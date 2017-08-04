<?php
namespace DdvPhp\DdvUtil\Arrays;
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2017/8/4
 * Time: 下午4:50
 */

class Compare
{
    /**
     * 把数组中所以驼峰的key转小写下滑杠
     * @param array $array [数组]
     * @return array  [请求数组]
     */
    public static function isIndexArray ($array = array()){
        return array_keys($array) === range(0, count($array) - 1);
    }
    public static function isAssocArray($array = array()){
        return !self::isIndexArray($array);
    }
}