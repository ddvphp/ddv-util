<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/9/25
 * Time: 下午12:14
 */

namespace DdvPhp\DdvUtil\Laravel\Proxy\Interfaces;

use Closure;
use DdvPhp\DdvUtil\Laravel\Exceptions\ProxyExceptions;
use Illuminate\Database\Eloquent\Model;
use Throwable;

interface TransactionQuque extends TransactionHook
{

    /**
     * 插入事物到队列
     * @param callable|array|string|Closure $transaction 队列事物
     * @param callable|array|string|Closure $ifCallable 队列是否启用
     * @return $this|static
     */
    public function pushTransactionQuque($transaction, $ifCallable = null);

    /**
     * 触发事物
     * @param null $data
     * @throws Throwable
     * @return $this
     */
    public function beginTransactionQuque();

    /**
     * 插入需要开启事物的model
     * @param Model[]|Model|array|string $model
     * @return $this
     * @throws ProxyExceptions
     */
    public function pushTransactionModel($model);
}