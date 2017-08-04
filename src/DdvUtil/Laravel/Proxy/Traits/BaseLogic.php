<?php
/**
 * Created by PhpStorm.
 * User: sicmouse
 * Date: 2018/9/25
 * Time: 下午1:56
 */

namespace DdvPhp\DdvUtil\Laravel\Proxy\Traits;

use DdvPhp\DdvUtil\Laravel\Proxy\Interfaces\Proxy;
use DdvPhp\DdvUtil\Laravel\Proxy\Libs\LogicProxy;
use DdvPhp\DdvUtil\Laravel\Proxy\Traits\Proxy as ProxyLogicTrait;

trait BaseLogic
{
    /**
     * @param Proxy $proxy
     * @return static|LogicProxy
     */
    public static function proxy(Proxy $proxy){
        /**
         * 更新类调用作用域
         */
        return $proxy->getLogicProxyCallClass(get_called_class());
    }
}