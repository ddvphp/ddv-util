<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/9/16
 * Time: 下午3:50
 */

namespace DdvPhp\DdvUtil\Laravel\Proxy\Interfaces;

use Closure;

interface Response
{
    // 强制合并
    const RESPONSES_DATA_ARRAY_MERGE = false;
    // 强制替换
    const RESPONSES_DATA_ARRAY_REPLACE = true;

    /**
     * @param $key
     * @param $value
     */
    public function setResponse($key, $value);

    /**
     * @param Closure $fn
     * @return $this
     */
    public function setToResponseClosure(Closure $fn);

    /**
     * @return $this
     */
    public function clearToResponseClosure();

    /**
     * 获取返回结果
     * @return mixed|array
     */
    public function getResponse();

    /**
     * 获取分页数据和数据库数据
     * @return array $res [分页数据和查询数据]
     */
    public function toArray();

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize();

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0);

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function __toString();

}