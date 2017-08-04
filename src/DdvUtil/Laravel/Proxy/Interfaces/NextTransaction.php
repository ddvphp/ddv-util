<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/9/17
 * Time: 下午10:35
 */

namespace DdvPhp\DdvUtil\Laravel\Proxy\Interfaces;


interface NextTransaction
{
    /**
     * 调用下一个事物
     * @return bool
     */
    public function __invoke();

    /**
     * 调用下一个事物
     * @return bool
     */
    public function next();
}