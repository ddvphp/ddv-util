<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/9/25
 * Time: 上午11:02
 */

namespace DdvPhp\DdvUtil\Laravel\Proxy\Interfaces;

use Closure;
use ReflectionException;
use DdvPhp\DdvUtil\Laravel\Exceptions\ProxyExceptions;
use DdvPhp\DdvUtil\Laravel\Proxy\Interfaces\Parameters;

interface Proxy extends Prototype, TransactionQuque
{
    /**
     * 调用
     * @param array|callable|Closure|string $handler
     * @param array|Parameters $arguments
     * @param Parameters $parameters
     * @param string|null $callCalssName
     * @return mixed
     * @throws ProxyExceptions
     * @throws ReflectionException
     */
    public function call($handler, $arguments = null, $parameters = null, $callCalssName = null);

    /**
     * @param $callClass
     * @return mixed
     */
    public function getLogicProxyCallClass($callClass);
}