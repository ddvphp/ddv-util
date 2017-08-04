<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/9/25
 * Time: 下午12:32
 */

namespace DdvPhp\DdvUtil\Laravel\Proxy\Traits;

use Closure;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionParameter;
use ReflectionException;

trait Parameters
{
    /**
     * @var callable|array|string|Closure $handler
     */
    protected $handler;

    /**
     * 参数
     * @var array
     */
    protected $parameters;

    /**
     * 所有参数的反射
     * @var ReflectionParameter[] $reflectionParameters
     */
    protected $reflectionParameters = array();

    /**
     * 空参数反射
     * @var ReflectionParameter[] $reflectionParameters
     */
    protected $nullParameterReflections = array();

    /**
     * Parameters constructor.
     * @param callable|array|string|Closure $handler
     * @param $parameters
     * @throws ReflectionException
     */
    public function __construct($handler, $parameters)
    {
        // 存储监听
        $this->handler = $handler;
        // 参数存起来
        $this->parameters = &$parameters;
        unset($handler, $parameters);
        // 初始化
        $this->init();
    }

    /**
     * Notes:
     * User: yao
     * Date: 2018/9/18
     * Time: 15:49
     * @throws ReflectionException
     */
    protected function init()
    {
        // 存储参数
        $this->getReflectionParameters();
        // 空参数
        foreach ($this->reflectionParameters as $index => $parameter) {
            // 判断参数是否存在参数
            if (!isset($this->parameters[$index])){
                // 填充参数 为 null
                $this->parameters[$index] = null;
                // 填充反射
                $this->nullParameterReflections[$index] = $parameter;
            }
        };
        /**
         * 遍历所有没有设置的参数
         * 把$this设置进去
         */
        $this->mapNullParameters(function (&$value, $name, $index, ReflectionParameter $parameter) {
            $className = $parameter->getClass();
            $isSet = false;
            if (!empty($className)) {
                if ($className->isInstance($this)) {
                    // 返回控制器
                    $value = $this;
                    $isSet = true;
                }
            }
            unset($value, $name, $index, $className, $parameter);
            return $isSet;
        });
    }

    /**
     * 遍历空参数的反射
     * @param callable|array|string|Closure $handler
     * @return $this
     */
    public function mapNullParameters($hander)
    {
        foreach ($this->nullParameterReflections as $index => $parameter) {
            if (call_user_func_array($hander, [&$this->parameters[$index], $parameter->getName(), $index, $parameter])) {
                unset($this->nullParameterReflections[$index]);
            };
        }
        return $this;
    }

    /**
     * 遍历空参数的反射
     * @param callable|array|string|Closure $handler
     * @return $this
     */
    public function mapParameters($hander)
    {
        foreach ($this->reflectionParameters as $index => $parameter) {
            call_user_func_array($hander, [&$this->parameters[$index], $parameter->getName(), $index, $parameter]);
        }
        return $this;
    }

    /**
     * 把参数转数组
     * @return array
     */
    public function toArray()
    {
        $res = array();
        $this->mapParameters(function ($name, &$value, $parameter) use (&$res) {
            $res[$name] = $value;
        });
        return $res;
    }

    /**
     * 获取参数
     * @return array
     */
    public function &getParameters()
    {
        return $this->parameters;
    }

    /**
     * 获取一个反射参数数组
     * @param callable $handler
     * @return ReflectionParameter[] The parameters, as a ReflectionParameter objects.
     * @throws ReflectionException
     */
    public function getReflectionParameters()
    {
        if (!empty($this->reflectionParameters)) {
            return $this->reflectionParameters;
        }
        if (is_object($this->handler) && is_callable($this->handler)) {
            if ($this->handler instanceof Closure) {
                $this->reflectionParameters = (new ReflectionFunction($this->handler))->getParameters();
            }
        } elseif (is_string($this->handler)) {
            if (function_exists($this->handler)) {
                $this->reflectionParameters = (new ReflectionFunction($this->handler))->getParameters();
            } elseif (strpos($this->handler, '::') !== false) {
                list($className, $method) = explode('::', $this->handler, 2);
                if (method_exists($className, $method)) {
                    $this->reflectionParameters = (new ReflectionMethod($className, $method))->getParameters();
                }
            }
        } elseif (is_array($this->handler) && count($this->handler) === 2 && method_exists($this->handler[0], $this->handler[1])) {
            $this->reflectionParameters = (new ReflectionMethod($this->handler[0], $this->handler[1]))->getParameters();
        }
        return array();
    }
}
