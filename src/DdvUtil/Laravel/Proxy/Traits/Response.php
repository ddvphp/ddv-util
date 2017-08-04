<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/9/16
 * Time: 下午3:44
 */

namespace DdvPhp\DdvUtil\Laravel\Proxy\Traits;

use Closure;

trait Response
{
    // 结果集合
    protected $responses = array();
    // 处理闭包
    protected $toResponse = null;

    /**
     * @param $key
     * @param $value
     */
    public function setResponse($key, $value)
    {
        if ($key === static::RESPONSES_DATA_ARRAY_MERGE) {
            // 如果$key为false就是需要强制重写所有输出
            if (is_array($value)) {
                $this->responses = array_merge($this->responses, $value);
            } else {
                $this->responses = $value;
            }
        } else if ($key === static::RESPONSES_DATA_ARRAY_REPLACE) {
            // 如果$key为true就是需要强制重写所有输出
            $this->responses = $value;
        } else if (isset($key) && (is_string($key) || is_numeric($key))) {
            // 把返回数据保存起来
            $this->responses[(string)$key] = $value;
        }
        // 释放内存
        unset($key, $value);
        //
        return $this;
    }

    /**
     * @param Closure $fn
     * @return $this
     */
    public function setToResponseClosure(Closure $fn)
    {
        $this->toResponse = $fn;
        return $this;
    }

    /**
     * @return $this
     */
    public function clearToResponseClosure()
    {
        $this->toResponse = null;
        return $this;
    }

    /**
     * 获取返回结果
     * @return mixed|array
     */
    public function getResponse()
    {
        /**
         * @var Closure $toResponse
         */
        $toResponse = $this->toResponse;

        if ($toResponse instanceof Closure) {
            $res = $toResponse($this->responses, $this);
        } else {
            $res = empty($this->responses) ? null : $this->responses;
        }
        return $res;
    }

    /**
     * 获取分页数据和数据库数据
     * @return array $res [分页数据和查询数据]
     */
    public function toArray()
    {
        return ['data' => $this->getResponse()];
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

}