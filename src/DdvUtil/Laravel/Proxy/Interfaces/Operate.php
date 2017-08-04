<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/9/18
 * Time: 上午10:18
 */

namespace DdvPhp\DdvUtil\Laravel\Proxy\Interfaces;

use DdvPhp\DdvUtil\Laravel\Exceptions\ProxyExceptions;

interface Operate
{
    /**
     * 获取操作者uid
     * @throws ProxyExceptions
     * @return string
     */
    public function getOperateUid();

    /**
     * 设置操作者uid
     * @param $uid
     * @return mixed
     */
    public function setOperateUid($uid);

    /**
     * 获取操作的站点
     * @throws ProxyExceptions
     * @return string
     */
    public function getOperateSiteId();

    /**
     * 设置操作的站点
     * @throws ProxyExceptions
     * @param $siteId
     * @return mixed
     */
    public function setOperateSiteId($siteId);
}