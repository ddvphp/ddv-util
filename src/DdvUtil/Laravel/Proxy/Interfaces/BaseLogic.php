<?php
/**
 * Created by PhpStorm.
 * User: sicmouse
 * Date: 2018/9/25
 * Time: 下午1:56
 */

namespace DdvPhp\DdvUtil\Laravel\Proxy\Interfaces;

use DdvPhp\DdvUtil\Laravel\Proxy\Interfaces\Proxy;
use DdvPhp\DdvUtil\Laravel\Proxy\Interfaces\LogicProxy;

interface BaseLogic
{

    /**
     * @param Proxy $proxy
     * @return static|LogicProxy
     */
    public static function proxy(Proxy $proxy);
}