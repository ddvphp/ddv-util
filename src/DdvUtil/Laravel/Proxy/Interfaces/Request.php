<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/9/17
 * Time: 下午8:10
 */

namespace DdvPhp\DdvUtil\Laravel\Proxy\Interfaces;

use DdvPhp\DdvUtil\Laravel\Exceptions\ProxyExceptions;
use Illuminate\Http\Request as HttpRequest;

interface Request
{

    /**
     * 设置一个请求
     * @param HttpRequest $request
     * @return $this
     */
    public function setRequest(HttpRequest $request);

    /**
     * 获取一个请求
     * @return HttpRequest|null
     * @throws ProxyExceptions
     */
    public function getRequest();
}