<?php
/**
 * Created by PhpStorm.
 * User: sicmouse
 * Date: 2018/9/25
 * Time: 下午7:31
 */

namespace DdvPhp\DdvUtil\Laravel\Proxy\Interfaces;

use DdvPhp\DdvUtil\Laravel\Proxy\Interfaces\Proxy as ProxyLogicInterface;

interface Controller
{
    /**
     * @return ProxyLogicInterface
     */
    public function getProxy();

    /**
     * @return ProxyLogicInterface
     */
    public function createProxy();

}