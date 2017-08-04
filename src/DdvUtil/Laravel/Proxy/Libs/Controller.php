<?php
/**
 * Created by PhpStorm.
 * User: sicmouse
 * Date: 2018/9/25
 * Time: 下午7:33
 */

namespace DdvPhp\DdvUtil\Laravel\Proxy\Libs;

use DdvPhp\DdvUtil\Laravel\Proxy\Traits\Controller as ControllerTrait;
use DdvPhp\DdvUtil\Laravel\Proxy\Interfaces\Controller as ControllerInterface;

class Controller implements ControllerInterface
{
    use ControllerTrait;
}