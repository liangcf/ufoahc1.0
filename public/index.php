<?php
/**
 * Created by PhpStorm.
 * User: AF
 * Date: 2016/7/20
 * Time: 15:32
 * url: http://git.oschina.net/liangcf/ufoahc
 * url: https://github.com/liangcf/ufoahc
 */
if (stristr('cli',php_sapi_name()) && is_file(__DIR__.parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

require  '../core/run/AutoLoadClass.php';
//自动加载类 '\core\run\AutoLoadClass::loader'
spl_autoload_register('\core\run\AutoLoadClass::loader',true,true);

//如果不启用上述的spl_autoload_register自动加载类可以使用composer，执行composer dump-autoload后注销spl_autoload_register 放开如下的引入即可
//require '../vendor/autoload.php';
//执行开始
\core\Application::run();