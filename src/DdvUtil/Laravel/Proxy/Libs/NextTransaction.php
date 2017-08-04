<?php
/**
 * Created by PhpStorm.
 * User: sicmouse
 * Date: 2018/9/25
 * Time: 下午1:43
 */

namespace DdvPhp\DdvUtil\Laravel\Proxy\Libs;

use DdvPhp\DdvUtil\Laravel\Proxy\Traits\NextTransaction as NextTransactionTrait;
use DdvPhp\DdvUtil\Laravel\Proxy\Interfaces\NextTransaction as NextTransactionInterface;

class NextTransaction implements NextTransactionInterface
{
    use NextTransactionTrait;
}