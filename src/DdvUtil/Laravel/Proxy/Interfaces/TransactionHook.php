<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/9/16
 * Time: 下午3:39
 */

namespace DdvPhp\DdvUtil\Laravel\Proxy\Interfaces;

use Closure;

interface TransactionHook
{
    /**
     * 监听事物提交
     * @param callable|array|string|Closure $handler
     * @return $this
     */
    public function onCommit($handler);

    /**
     * 监听事物结束
     * @param callable|array|string|Closure $handler
     * @return $this
     */
    public function onRollBack($handler);

    /**
     * 监听事件在 Commit 或者 RollBack 后触发
     * @param callable|array|string|Closure $handler
     * @return $this
     */
    public function onCompleted($handler);

    /**
     * 清空所以事物钩子
     * @return $this
     */
    public function clearTransactionHooks();
}