<?php
namespace DdvPhp\DdvUtil\String;
use \DdvPhp\DdvUtil\Arrays\Compare;
/**
 * Class Cors
 *
 * Wrapper around PHPMailer
 *
 * @package \DdvPhp\DdvUtil\String\Conversion
 */
class Conversion
{

  /**
   * 把数组中所有驼峰的key转小写下滑杠
   * @param array $array [数组]
   * @return array  [请求数组]
   */
  public static function humpToUnderlineByIndexArray ($array = array()){
    if (is_array($array)&&Compare::isIndexArray($array)) {
      foreach ($array as $key => $value) {
        $array[$key] = self::humpToUnderlineByIndexArray($value);
      }
      return $array;
    }else{
      return self::humpToUnderlineByArray($array);
    }
  }
  /**
   * 把数组中所有小写下滑杠的key转驼峰
   * @param array $array [数组]
   * @return array  [请求数组]
   */
  public static function underlineToHumpByIndexArray ($array = array()){
    if (is_array($array)&&Compare::isIndexArray($array)) {
      foreach ($array as $key => $value) {
        $array[$key] = self::underlineToHumpByIndexArray($value);
      }
      return $array;
    }else{
      return self::underlineToHumpByArray($array);
    }
  }
  /**
   * 把数组中所有key的首字符转换为大写
   * @param array $array [数组]
   * @return array  [请求数组]
   */
  public static function ucfirstByIndexArray ($array = array()){
    if (is_array($array)&&Compare::isIndexArray($array)) {
      foreach ($array as $key => $value) {
        $array[$key] = self::ucfirstByIndexArray($value);
      }
      return $array;
    }else{
      return self::ucfirstByArray($array);
    }
  }
  /**
   * 把数组中所有key的首字符转换为小写
   * @param array $array [数组]
   * @return array  [请求数组]
   */
  public static function lcfirstByIndexArray ($array = array()){
    if (is_array($array)&&Compare::isIndexArray($array)) {
      foreach ($array as $key => $value) {
        $array[$key] = self::lcfirstByIndexArray($value);
      }
      return $array;
    }else{
      return self::lcfirstByArray($array);
    }
  }
  /**
   * 把数组中所有的key的首字符转换为大写
   * @param array $array [数组]
   * @return array  [请求数组]
   */
  public static function ucfirstByArray($array = array()){

      foreach ($array as $key => $value) {
          $keyt = self::ucfirst($key);
          if ($keyt!==$key){
              unset($array[$key]);
              $array[$keyt] = $value;
          }
      }
      return $array;
  }
  /**
   * 把数组中所有的key的首字符转换为小写
   * @param array $array [数组]
   * @return array  [请求数组]
   */
  public static function lcfirstByArray($array = array()){

      foreach ($array as $key => $value) {
          $keyt = self::lcfirst($key);
          if ($keyt!==$key){
              unset($array[$key]);
              $array[$keyt] = $value;
          }
      }
      return $array;
  }
  /**
   * 把数组中所有驼峰的key转小写下滑杠
   * @param array $array [数组]
   * @return array  [请求数组]
   */
  public static function humpToUnderlineByArray ($array = array()){
    foreach ($array as $key => $value) {
      $keyt = self::humpToUnderline($key);
      if ($keyt!==$key){
        unset($array[$key]);
        $array[$keyt] = $value;
      }
    }
    return $array;
  }
  /**
   * 把数组中所有小写下滑杠转驼峰
   * @param array $array [数组]
   * @return array  [请求数组]
   */
  public static function underlineToHumpByArray ($array = array()){
    foreach ($array as $key => $value) {
      $keyt = self::underlineToHump($key);
        if ($keyt!==$key){
          unset($array[$key]);
          $array[$keyt] = $value;
        }
    }
    return $array;
  }
  /**
   * 把数组中所有小写中杠转驼峰
   * @param array $array [数组]
   * @return array  [请求数组]
   */
  public static function middleLineToHumpByArray ($array = array()){
    foreach ($array as $key => $value) {
      $keyt = self::middleLineToUpperCase($key);
      if ($keyt!==$key){
        unset($array[$key]);
        $array[$keyt] = $value;
      }
    }
    return $array;
  }
  /**
  * 首字符转换为大写：
  * @param string $str [需要转换的字符串]
  * @return string  [转换后的字符串]
  */
  public static function ucfirst ($str){
    return ucfirst($str);
  }
  /**
  * 首字符转换为小写：
  * @param string $str [需要转换的字符串]
  * @return string  [转换后的字符串]
  */
  public static function lcfirst ($str){
    return lcfirst($str);
  }
  /**
  * 驼峰转小写下滑杠
  * @param string $str [需要转换的字符串]
  * @return string  [转换后的字符串]
  */
  public static function humpToUnderline ($str){
    return preg_replace_callback(
      '([A-Z])',
      function ($matches) {
        return '_'.strtolower($matches[0]);
      },
      $str
    );
  }
  /**
  * 小写下滑杠转驼峰
  * @param string $str [需要转换的字符串]
  * @return string  [转换后的字符串]
  */
  public static function underlineToHump ($str = ''){
    return preg_replace_callback(
      '(\_\w)',
      function ($matches) {
        return strtoupper(substr($matches[0], 1));
      },
      $str
    );
  }
  /**
  * 小写中杠转驼峰
  * @param string $str [需要转换的字符串]
  * @return string  [转换后的字符串]
  */
  public static function middleLineToHump ($str = ''){
    return preg_replace_callback(
      '(\-\w)',
      function ($matches) {
        return strtoupper(substr($matches[0], 1));
      },
      $str
    );
  }
  public static function middleLineToUpperCaseByArray ($array = array()){
    return self::middleLineToHumpByArray($array);
  }
  public static function underlineToUpperCaseByArray ($array = array()){
    return self::underlineToHumpByArray($array);
  }
  public static function upperCaseToUnderlineByArray ($array = array()){
    return self::humpToUnderlineByArray($array);
  }  public static function upperCaseToUnderline ($str){
      return self::humpToUnderline($str);
  }
  public static function underlineToUpperCase ($str = ''){
      return self::underlineToHump($str);
  }
  public static function middleLineToUpperCase ($str = ''){
    return self::middleLineToHump($str);
  }
}