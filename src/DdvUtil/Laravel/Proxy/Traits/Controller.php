<?php
/**
 * Created by PhpStorm.
 * User: sicmouse
 * Date: 2018/9/25
 * Time: 下午7:32
 */

namespace DdvPhp\DdvUtil\Laravel\Proxy\Traits;

use DdvPhp\DdvUtil\Laravel\Proxy\Libs\Proxy as ProxyLib;

trait Controller
{
    /**
     * @return ProxyLib
     */
    public function getProxy(){
        $proxy = $this->createProxy();

        return $proxy;
    }

    /**
     * @return ProxyLib
     */
    public function createProxy(){
        return new ProxyLib();
    }
}