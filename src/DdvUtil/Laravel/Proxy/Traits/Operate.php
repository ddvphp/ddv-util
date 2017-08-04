<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/9/17
 * Time: 下午11:39
 */

namespace DdvPhp\DdvUtil\Laravel\Proxy\Traits;

use DdvPhp\DdvUtil\Laravel\Exceptions\ProxyExceptions;

trait Operate
{
    /**
     * 获取操作者uid
     * @throws ProxyExceptions
     * @return string
     */
    public function getOperateUid()
    {
        return $this->getPrototype('operateUid');
    }

    /**
     * 设置操作者uid
     * @param $uid
     * @return mixed
     */
    public function setOperateUid($uid)
    {
        return $this->setPrototype('operateUid', $uid);
    }

    /**
     * 获取操作的站点
     * @throws ProxyExceptions
     * @return string
     */
    public function getOperateSiteId()
    {
        return $this->getPrototype('operateSiteId');
    }

    /**
     * 设置操作的站点
     * @throws ProxyExceptions
     * @param $siteId
     * @return mixed
     */
    public function setOperateSiteId($siteId)
    {
        return $this->setPrototype('operateSiteId', $siteId);
    }

    /**
     * 获取操作语言id
     * Date: 2018/9/19
     * Time: 17:28
     * @return mixed
     */
    public function getLanguageId()
    {
        return $this->getPrototype('languageId');
    }

    /**
     * 设置操作的语言ID
     * @throws ProxyExceptions
     * @param $siteId
     * @return mixed
     */
    public function setLanguageId($languageId)
    {
        return $this->setPrototype('languageId', $languageId);
    }


}