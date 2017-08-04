<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/9/13
 * Time: 下午4:14
 */

namespace DdvPhp\DdvUtil\Laravel\Proxy\Traits;

use DdvPhp\DdvUtil\Laravel\Exceptions\ProxyExceptions;
use Illuminate\Http\Request as HttpRequest;

trait Request
{

    protected $request;
    /**
     * 设置一个请求
     * @param HttpRequest $request
     * @return $this
     */
    public function setRequest(HttpRequest $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * 获取一个请求
     * @return HttpRequest|null
     * @throws ProxyExceptions
     */
    public function getRequest()
    {
        if (function_exists('request')) {
            return request();
        } elseif (function_exists('app')) {
            return app(HttpRequest::class);
        } else {
            return new HttpRequest();
        }
    }

}