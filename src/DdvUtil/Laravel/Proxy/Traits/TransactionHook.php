<?php
/**
 * Created by PhpStorm.
 * User: sicmouse
 * Date: 2018/9/25
 * Time: 下午1:35
 */

namespace DdvPhp\DdvUtil\Laravel\Proxy\Traits;

use Closure;

trait TransactionHook
{
    /**
     * 存储提交事物的钩子
     * @var callable[] $commitHooks
     */
    protected $commitHooks = array();

    /**
     * 存储回滚事物的钩子
     * @var callable[] $commitHooks
     */
    protected $rollBackHooks = array();

    /**
     * 存储事物完成的钩子
     * @var callable[] $commitHooks
     */
    protected $completedHooks = array();
    /**
     * 监听事物提交
     * @param callable|array|string|Closure $handler
     * @return $this
     */
    public function onCommit($handler)
    {
        $this->commitHooks[] = $handler;
        return $this;
    }

    /**
     * 监听事物结束
     * @param callable|array|string|Closure $handler
     * @return $this
     */
    public function onRollBack($handler)
    {
        $this->rollBackHooks[] = $handler;
        return $this;
    }

    /**
     * 监听事件在 Commit 或者 RollBack 后触发
     * @param callable|array|string|Closure $handler
     * @return $this
     */
    public function onCompleted($handler)
    {
        $this->completedHooks[] = $handler;
        return $this;
    }

    /**
     * 清空所以事物钩子
     * @return $this
     */
    public function clearTransactionHooks()
    {
        $this->completedHooks = $this->rollBackHooks = $this->commitHooks = array();
    }
}