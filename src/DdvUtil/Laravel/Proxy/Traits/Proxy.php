<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/9/25
 * Time: 下午12:31
 */

namespace DdvPhp\DdvUtil\Laravel\Proxy\Traits;


use Closure;
use DdvPhp\DdvUtil\Laravel\Proxy\Libs\LogicProxy;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionException;
use Illuminate\Http\Request as HttpRequest;
use DdvPhp\DdvUtil\Laravel\Exceptions\ProxyExceptions;
use DdvPhp\DdvUtil\Laravel\Proxy\Libs\Parameters;
use DdvPhp\DdvUtil\Laravel\Proxy\Interfaces\Parameters as ParametersInterface;

trait Proxy
{
    // 使用请求模块
    use Request;
    // 使用属性模块
    use Prototype;
    // 使用事物队列模块
    use TransactionQuque;

    /**
     * @var LogicProxy[] $cacheLogicProxy
     */
    protected $cacheLogicProxy = [];
    /**
     * @var array
     */
    protected $cacheObjCall = [];

    /**
     * 调用
     * @param array|callable|Closure|string $handler
     * @param array|ParametersInterface $arguments
     * @param ParametersInterface $parameters
     * @param string|null $callCalssName
     * @return mixed
     * @throws ProxyExceptions
     * @throws ReflectionException
     */
    public function call($handler, $arguments = null, $parameters = null, $callCalssName = null){
        /**
         * 字符串类调用强转数组
         * 'AA\BB::bb' 转为 ['AA\BB', 'cc']
         */
        if (is_string($handler) && strpos($handler, '::') !== false) {
            // 拆分数组
            list($className, $method) = explode('::', $handler, 2);
            // 如果是可以调用的回调
            if (is_callable(array($className, $method)) || method_exists($className, $method)) {
                // 重写回调
                $handler = array($className, $method);
            }
            // 释放内存
            unset($className, $method);
        }
        /**
         * 试图提取调用的类名
         */
        $className = is_array($handler) && isset($handler[0]) ? $handler[0] : null;
        /**
         * 判断类名是否存在
         */
        if ($className && is_string($className) && class_exists($className)) {
            /**
             * 试图反射方式
             */
            $reflectionMethod = new ReflectionMethod($className, $handler[1]);
            /**
             * 判断是否为静态方法
             * 是的话不需要实例化
             */
            if (!$reflectionMethod->isStatic()) {

                if (!(isset($this->cacheObjCall[$className]) && $this->cacheObjCall[$className] instanceof $className)){
                    $carguments = array();
                    $cparameters = new Parameters([$className, '__construct'], $carguments);
                    $this->argumentsInitByReflectionParameter($carguments, $cparameters);
                    $this->cacheObjCall[$className] = new $className(...$cparameters->getParameters());
                    unset($carguments, $cparameters);
                }
                // 实例化
                $handler = array($this->cacheObjCall[$className], $handler[1]);
            }
            unset($reflectionMethod);
        }
        /**
         * 调用类作用域
         * @var string $newClassScope
         */
        $newClassScope = empty($callCalssName)? get_called_class() : $callCalssName;
        /**
         * 如果第二个参数传入了第三个参数的内容，交换内容
         */
        if (is_object($arguments) && $arguments instanceof ParametersInterface){
            $t = &$parameters;
            unset($parameters);
            $parameters = &$arguments;
            unset($arguments);
            if (is_array($t)){
                $arguments = &$t;
            }else{
                $arguments = $parameters->getParameters();
            }
            unset($t);
        }
        /**
         * 确保第二个参数是一个数组
         */
        if (!is_array($arguments)){
            $arguments = array();
        }
        /**
         * 必须有一个解析参数的参数
         */
        if (!(isset($parameters)&&$parameters instanceof ParametersInterface)){
            $parameters = new Parameters($handler, $arguments);
        }
        $this->argumentsInitByReflectionParameter($arguments, $parameters);
        return call_user_func_array(
            Closure::bind(
                function ($handler, $arguments){
                    return call_user_func_array($handler, $arguments);
                },
                null,
                $newClassScope),
            [$handler, $arguments]
        );
    }

    /**
     * @param $parameters
     * @param $arguments
     * @throws ProxyExceptions
     */
    protected function argumentsInitByReflectionParameter(&$arguments, ParametersInterface $parameters){

        /**
         * 请求输入数据
         * @var array $input
         */
        $input = $this->getRequest()->input();
        /**
         * 遍历空参数，试图从请求输入中获取
         */
        $parameters->mapNullParameters(function (&$value, $name, $index, ReflectionParameter $parameter) use (&$input) {
            $isHas = false;
            if (!empty($name) && isset($input[$name])) {
                // 试图获取输入值
                $data = &$input[$name];
                // 反射类
                $reflectionClass = $parameter->getClass();
                // 判断是否为数组
                if ($parameter->isArray()) {
                    // 传入的值要求是数组，所以强转数组
                    $value = is_array($data) ? $data : array($data);
                    $isHas = true;
                } elseif (empty($reflectionClass)) {
                    // 不转换
                    $value = $data;
                    $isHas = true;
                } elseif (is_object($data) && $reflectionClass->isInstance($data)){
                    $value = $data;
                    $isHas = true;
                }
                unset($data);
            }
            unset($name, $value, $input, $parameter);
            return $isHas;
        });

        unset($input);


        /**
         * 通过反射类
         * 类名获取
         */
        $parameters->mapNullParameters(function (&$value, $name, $index, ReflectionParameter $parameter) {
            // 反射类
            $reflectionClass = $parameter->getClass();
            if (!empty($reflectionClass)) {
                /**
                 * 判断是否期待的类是 自己 代理本身
                 */
                if ($reflectionClass->isInstance($this)) {
                    // 返回代理
                    $value = $this;
                } elseif ($reflectionClass->isInstance($this->getRequest())) {
                    // 请求对象
                    $value = $this->getRequest();
                } else {
                    $value = app($reflectionClass->getName());
                }
            }
            unset($value, $name, $index, $parameter);
            return !empty($reflectionClass);
        });
        /**
         * 赋予默认值
         */
        $parameters->mapNullParameters(function (&$value, $name, $index, ReflectionParameter $parameter) {
            if ($parameter->isDefaultValueAvailable()){
                $value = $parameter->getDefaultValue();
            }
            unset($name, $value, $index, $parameter);
        });


        /**
         * 通过检测
         */
        $parameters->mapParameters(function (&$value, $name, $index, ReflectionParameter $parameter) {
            $className = $parameter->getClass();
            if (empty($className)) {
                if (method_exists($parameter, 'hasType') && method_exists($parameter, 'getType')) {
                    $type = $parameter->getType();
                    if ($parameter->hasType() && $type->isBuiltin()) {
                        switch ($type->getName()) {
                            case 'int':
                                $value = is_numeric($value) ? (int)$value : 0;
                                break;
                            case 'float':
                                $value = is_numeric($value) ? (float)$value : 0;
                                break;
                            case 'bool':
                                $value = (bool)$value;
                                break;
                            case 'string':
                                $value = (is_string($value) || is_numeric($value)) ? (string)$value : '';
                                break;
                            case 'array':
                                $value = isset($value) ? (is_array($value) ? $value : array($value)) : array();
                                break;
                        }
                    }
                }
            }
        });
        $arguments = $parameters->getParameters();
        unset($arguments, $parameters);
    }

    /**
     * @param $callClass
     * @return LogicProxy
     * @throws ProxyExceptions
     */
    public function getLogicProxyCallClass($callClass){
        if (!(isset($this->cacheLogicProxy[$callClass]) && $this->cacheLogicProxy[$callClass] instanceof LogicProxy)){
            $this->cacheLogicProxy[$callClass] = new LogicProxy($callClass, $this);
        }
        return $this->cacheLogicProxy[$callClass];
    }

    public function destroy()
    {
        $this->cacheLogicProxy = $this->cacheObjCall = [];
    }
}