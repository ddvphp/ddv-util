<?php
/**
 * Created by PhpStorm.
 * User: sicmouse
 * Date: 2018/9/25
 * Time: 下午1:42
 */

namespace DdvPhp\DdvUtil\Laravel\Proxy\Traits;

use Closure;

trait NextTransaction
{
    /**
     * 保存一个闭包
     * @var array|callable|Closure|string
     */
    protected $nextHander;

    /**
     * 传入一个闭包
     * NextTransaction constructor.
     * @param array|callable|Closure|string|null $nextHander
     */
    public function __construct($nextHander = null)
    {
        if (!is_null($nextHander)) {
            $this->nextHander = $nextHander;
        }
    }

    /**
     * 调用下一个事物
     * @return bool
     */
    public function __invoke()
    {
        return $this->next();
    }

    /**
     * 调用下一个事物
     * @return bool
     */
    public function next()
    {
        if (isset($this->nextHander)) {
            // 存储
            $hander = &$this->nextHander;
            // 释放
            unset($this->nextHander);
            // 调用
            return call_user_func($hander);
        }
        return false;
    }
}