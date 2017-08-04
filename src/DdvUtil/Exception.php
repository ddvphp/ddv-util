<?php

namespace DdvPhp\DdvUtil;

class Exception extends \DdvPhp\DdvException\Error
{
  // 魔术方法
  public function __construct( $message = 'Util Error', $errorId = 'UTIL_ERROR' , $code = '400', $errorData = array() )
  {
    parent::__construct( $message , $errorId , $code, $errorData );
  }
}