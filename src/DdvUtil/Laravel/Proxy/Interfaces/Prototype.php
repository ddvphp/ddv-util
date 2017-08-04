<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/9/17
 * Time: 下午8:12
 */

namespace DdvPhp\DdvUtil\Laravel\Proxy\Interfaces;

use DdvPhp\DdvUtil\Laravel\Exceptions\ProxyExceptions;

interface Prototype extends Request
{

    /**
     * 获取一个属性
     * @param null $key
     * @throws ProxyExceptions
     * @return mixed
     */
    public function getPrototype($key = null, $defaultValue = null);

    /**
     * 设置一个属性
     * @param $key
     * @param $value
     * @return $this
     */
    public function setPrototype($key, $value);

    /**
     * 复制属性
     * @param $key
     * @param $newKey
     * @throws ProxyExceptions
     */
    public function copyPrototype($key, $newKey);

    /**
     * @param $key
     * @return bool
     */
    public function hasPrototype($key);

    /**
     * 批量设置属性
     * @param array $input
     * @param bool $isReplace
     * @return $this
     */
    public function loadPrototype(array $input, $isReplace = false);
}