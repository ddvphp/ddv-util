ddv-util
===================

Installation - 安装
------------

```bash
composer require ddvphp/ddv-util
```

Usage - 使用
-----

### 1、驼峰转小写下滑杠 `humpToUnderline`

```php

$a = \DdvPhp\DdvUtil\String\Conversion::humpToUnderline('userLoginTest');
var_dump($a);

```
#### 1.1、驼峰转小写下滑杠 `humpToUnderlineByArray`

```php

$a = \DdvPhp\DdvUtil\String\Conversion::humpToUnderlineByArray([
	'userLoginTest'=>'11',
	'userLoginTest1'=>'12'
]);
var_dump($a);

```


### 2、小写下滑杠转驼峰 `underlineToHump`

```php

$a = \DdvPhp\DdvUtil\String\Conversion::underlineToHump('user_login_test');
var_dump($a);

```


### 3、小写中杠转驼峰 `middleLineToHump`

```php

$a = \DdvPhp\DdvUtil\String\Conversion::middleLineToHump('user-login-test');
var_dump($a);

```

