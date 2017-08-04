<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/9/25
 * Time: 下午12:31
 */

namespace DdvPhp\DdvUtil\Laravel\Proxy\Traits;

use Closure;
use DdvPhp\DdvUtil\Laravel\Exceptions\ProxyExceptions;
use Throwable;
use ReflectionParameter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\ConnectionInterface;
use DdvPhp\DdvUtil\Laravel\Proxy\Libs\NextTransaction;
use DdvPhp\DdvUtil\Laravel\Proxy\Interfaces\NextTransaction as NextTransactionInterface;
use DdvPhp\DdvUtil\Laravel\Proxy\Libs\Parameters;

trait TransactionQuque
{
    use TransactionHook;


    /**
     * 默认事物完成后需要清空全部钩子
     * @var bool
     */
    protected $isClearHooksBeginAfter = true;

    /**
     * 默认事物完成前需要清空全部钩子
     * @var bool
     */
    protected $isClearHooksBeginBefore = true;

    /**
     * 事物队列
     * @var array[] $transactionQuque
     */
    protected $transactionQuque = array();

    /**
     * 运行队列的第几个事物
     * @var int
     */
    protected $runQuqueIndex = -1;

    /**
     * 是否正在运行事物
     * @var bool
     */
    protected $isRunTransactionQuque = false;

    /**
     * 事物链接
     * @var ConnectionInterface[] $transactionConnections
     */
    protected $transactionConnections = array();

    /**
     * 插入事物到队列
     * @param callable|array|string|Closure $transaction 队列事物
     * @param callable|array|string|Closure $ifCallable 队列是否启用
     * @return $this|static
     */
    public function pushTransactionQuque($transaction, $ifCallable = null)
    {
        $this->transactionQuque[] = array($transaction, $ifCallable);
        return $this;
    }

    /**
     * 触发事物
     * @param null $data
     * @throws Throwable
     * @return $this
     */
    public function beginTransactionQuque()
    {
        // 开启事物前清空钩子
        $this->isClearHooksBeginBefore && $this->clearTransactionHooks();
        // 清空事物链接
        $this->transactionConnections = array();
        // 标记正在运行事物
        $this->isRunTransactionQuque = true;
        /**
         * 记录错误
         * @var Throwable[]|\Exception[] $exceptions
         */
        $exceptions = array();
        // 试图运行
        try {
            // 设置从最后一个开始运行
            $this->runQuqueIndex = 0;
            // 试图运行队列中最后一个事物
            $this->callTransactionQuque();
        } catch (\Exception $e) {
            // 保存异常
            $exceptions[] = $e;
        } catch (Throwable $e) {
            // 保存异常
            $exceptions[] = $e;
        }
        foreach ($this->transactionConnections as $connection){
            empty($exceptions) ? $connection->commit() : $connection->rollBack();
        }

        if (empty($exceptions)) {
            /**
             * 遍历提交事物
             * @var callable $handler
             */
            foreach ($this->commitHooks as $handler) {
                try {
                    $this->call($handler);
                } catch (Throwable $exception) {
                    $exceptions[] = $exception;
                }
            }
        } else {
            /**
             * 遍历回滚事物
             * @var callable $fn
             */
            foreach ($this->rollBackHooks as $handler) {
                try {
                    $arguments = array();
                    $parameters = new Parameters($handler, $arguments);
                    $parameters->mapNullParameters(function (&$value, $name, $index, ReflectionParameter $parameter) use (&$exceptions) {
                        $class = $parameter->getClass();
                        $isHas = false;
                        if (!empty($class)) {
                            foreach ($exceptions as $exception) {
                                if ($class->isInstance($exception)) {
                                    // 返回控制器
                                    $value = $exception;
                                    $isHas = true;
                                    break;
                                }
                            }
                        }
                        // 释放内存
                        unset($class, $value, $name, $index, $parameter, $exceptions);
                        return $isHas;
                    });
                    $this->call($handler, $parameters);
                } catch (Throwable $exception) {
                    // 把最新异常，插入的异常数组的最前面
                    array_unshift($exceptions, $exception);
                }
            }
        }
        /**
         * 处理完成事件
         */
        foreach ($this->completedHooks as $handler) {
            try {
                $this->call($handler);
            } catch (Throwable $e) {
                $exceptions[] = $exception;
            }
        }
        // 标记为0
        $this->runQuqueIndex = 0;
        // 标记正在运行事物
        $this->isRunTransactionQuque = false;
        // 开启事物前清空钩子
        $this->isClearHooksBeginAfter && $this->clearTransactionHooks();
        // 清空队列
        $this->transactionQuque = array();
        // 清空事物链接
        $this->transactionConnections = array();
        /**
         * 如果存在异常就抛出异常
         */
        if (!empty($exceptions)) {
            throw $exceptions[0];
        }
        // 返回本身，链式调用
        return $this;
    }

    /**
     * 插入需要开启事物的model
     * @param Model[]|Model|array|string $model
     * @return $this
     * @throws ProxyExceptions
     * @throws \Exception
     */
    public function pushTransactionModel($model)
    {
        if (!$this->isRunTransactionQuque){
            // 并没有运行事物，所以无需启动事物
            return $this;
        }
        if (is_array($model)){
            foreach ($model as $m){
                $this->pushTransactionModel($m);
            }
            return $this;
        } elseif (is_object($model)){
            if ($model instanceof Model){
                $connection = $model->getConnection();
            }else{
                throw new ProxyExceptions('must extends abstract Model', 'MUST_EXTENDS_ABSTRACT_MODEL');
            }
        }elseif (is_string($model) && class_exists($model) && method_exists($model, 'getConnection')){
            /**
             * ConnectionInterface $connection
             */
            $connection = (new $model())->getConnection();
        }
        if (is_object($connection) && method_exists($connection,'beginTransaction')){
            if (!in_array($connection, $this->transactionConnections, true)){
                $connection->beginTransaction();
                $this->transactionConnections[] = $connection;
            }
            return $this;
        }else{
            throw new ProxyExceptions('Invalid connection', 'INVALID_CONNECTION');
        }
    }

    /**
     * @throws \ReflectionException
     */
    protected function callTransactionQuque()
    {
        /**
         * 提前第 {$this->runQuqueIndex} 个事物
         */
        if ($this->runQuqueIndex < count($this->transactionQuque) && isset($this->transactionQuque[$this->runQuqueIndex])) {
            /**
             * 获取到监听器
             * @var callable $handler
             * @var callable $ifCallable
             */
            list($handler, $ifCallable) = $this->transactionQuque[$this->runQuqueIndex];

            $nextTransaction = new NextTransaction(function () {
                if ($this->runQuqueIndex < count($this->transactionQuque)) {
                    $this->runQuqueIndex++;
                    $this->callTransactionQuque();
                    return true;
                } else {
                    return false;
                }
            });

            if (!empty($ifCallable)) {
                if (false === $this->call($ifCallable)) {
                    $nextTransaction();
                    return;
                }
            }

            /**
             * 标记是否参数中存在 下一个事物的参数
             */
            $isUseNextTransaction = false;


            $arguments = array();
            $parameters = new Parameters($handler, $arguments);
            $parameters->mapNullParameters(function (&$value, $name, $index, ReflectionParameter $parameter) use  (&$nextTransaction, &$isUseNextTransaction) {
                // 参数中填充 上一个事物
                $isHas = false;
                $reflectionClass = $parameter->getClass();
                if (!empty($reflectionClass)) {
                    if ($reflectionClass->implementsInterface(NextTransactionInterface::class) || $reflectionClass->isInstance($nextTransaction)) {
                        // 返回控制器
                        $value = $nextTransaction;
                        $isUseNextTransaction = $isHas = true;
                    }
                }
                // 释放内存
                unset($value, $name, $index, $parameter, $reflectionClass, $nextTransaction, $isUseNextTransaction);
                return $isHas;
            });
            /**
             * 调用当前事物
             */
            $this->call($handler, $parameters);

            if ($isUseNextTransaction === false){
                $nextTransaction();
            }

            // 释放内存
            unset($handler, $ifCallable, $nextTransaction);
        }
        // 释放内存
        unset($index);
    }
}