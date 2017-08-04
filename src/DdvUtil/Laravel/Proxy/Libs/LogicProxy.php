<?php
/**
 * Created by PhpStorm.
 * User: sicmouse
 * Date: 2018/9/25
 * Time: 下午6:31
 */

namespace DdvPhp\DdvUtil\Laravel\Proxy\Libs;

use ReflectionException;
use DdvPhp\DdvUtil\Laravel\Exceptions\ProxyExceptions;
use DdvPhp\DdvUtil\Laravel\Proxy\Interfaces\Proxy;
use DdvPhp\DdvUtil\Laravel\Proxy\Interfaces\LogicProxy as LogicProxyInterface;

class LogicProxy implements LogicProxyInterface
{
    /**
     * @var Proxy
     */
    public $proxy;
    /**
     * @var string
     */
    protected $callCalssName;

    /**
     * LogicProxy constructor.
     * @param $callCalssName
     * @param $proxy
     * @throws ProxyExceptions
     */
    public function __construct($callCalssName, $proxy)
    {
        if (!($proxy instanceof Proxy)){
            throw new ProxyExceptions('must is Proxy');
        }
        $this->proxy = $proxy;
        $this->callCalssName = $callCalssName;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws ProxyExceptions
     * @throws ReflectionException
     */
    public function __call($name, $arguments)
    {
        if (empty($this->callCalssName) || empty($this->proxy)){
            throw new ProxyExceptions('调用环境不存在');
        }
        $handler = [$this->callCalssName, $name];
        if (!(is_callable($handler) || method_exists($this->callCalssName, $name))){
            unset($handler, $name, $arguments);
            throw new ProxyExceptions('方法不存在');
        }
        return $this->proxy->call($handler, $arguments, null, $this->callCalssName);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws ProxyExceptions
     * @throws ReflectionException
     */
    public static function __callStatic($name, $arguments)
    {
        throw new ProxyExceptions('不支持静态调用，请使用->调用');
    }
}