<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/9/13
 * Time: 下午7:12
 */

namespace DdvPhp\DdvUtil\Laravel\Proxy\Traits;

use DdvPhp\DdvUtil\Laravel\Exceptions\ProxyExceptions;
use Illuminate\Http\Request as HttpRequest;

trait Prototype
{
    /**
     * 获取一个属性
     * @param null $key
     * @throws ProxyExceptions
     * @return mixed
     */
    public function getPrototype($key = null, $defaultValue = null)
    {
        if (empty($key)) {
            throw new ProxyExceptions('key must not empty', 'MUST_INPUT_KEY');
        }
        /**
         * @var HttpRequest $request
         */
        $request = $this->getRequest();
        if ($request->has($key)) {
            return $request->input($key);
        } else {
            if (func_num_args() > 1) {
                return $defaultValue;
            } else {
                throw new ProxyExceptions('prototype [' . $key . '] not defined', 'PROTOTYPE_NOT_DEFINED');
            }
        }
    }

    /**
     * 设置一个属性
     * @param $key
     * @param $value
     * @return $this
     */
    public function setPrototype($key, $value)
    {
        /**
         * @var HttpRequest $request
         */
        $this->loadPrototype(array($key => $value));
        return $this;
    }

    /**
     * 复制属性
     * @param $key
     * @param $newKey
     * @throws ProxyExceptions
     */
    public function copyPrototype($key, $newKey){
        $this->setPrototype($newKey, $this->getPrototype($key));
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasPrototype($key){
        return (bool)$this->getRequest()->has($key);
    }

    /**
     * 批量设置属性
     * @param array $input
     * @param bool $isReplace
     * @return $this
     */
    public function loadPrototype(array $input, $isReplace = false)
    {
        /**
         * @var HttpRequest $request
         */
        $request = $this->getRequest();
        if ($isReplace) {
            $request->replace($input);
        } else {
            $request->merge($input);
        }
        // 释放内存
        unset($request);
        return $this;
    }
}