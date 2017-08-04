<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/9/18
 * Time: 下午1:11
 */

namespace DdvPhp\DdvUtil\Laravel\Proxy\Interfaces;

use Closure;
use ReflectionParameter;
use ReflectionException;

interface Parameters
{

    /**
     * Parameters constructor.
     * @param callable|array|string|Closure $handler
     * @param $parameters
     */
    public function __construct($handler, $parameters);

    /**
     * 获取参数
     * @return array
     */
    public function getParameters();

    /**
     * 把参数转数组
     * @return array
     */
    public function toArray();

    /**
     * 获取一个反射参数数组
     * @param callable $handler
     * @return ReflectionParameter[] The parameters, as a ReflectionParameter objects.
     * @throws ReflectionException
     */
    public function getReflectionParameters();

    /**
     * 遍历空参数的反射
     * @param callable|array|string|Closure $handler
     * @return $this
     */
    public function mapParameters($hander);

    /**
     * 遍历空参数的反射
     * @param callable|array|string|Closure $handler
     * @return $this
     */
    public function mapNullParameters($hander);
}