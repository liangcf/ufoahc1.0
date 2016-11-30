<?php
header('Content-Type: text/html; charset=UTF-8');
//define('_SHELL_DIR',__DIR__);
const _SHELL_DIR=__DIR__;
require _SHELL_DIR.'/db/MysqliStmt.php';
require _SHELL_DIR.'/util/HttpUtils.php';
require _SHELL_DIR.'/util/UuidUtils.php';
require _SHELL_DIR.'/util/LogUtils.php';
require _SHELL_DIR.'/util/QrCodeUtils.php';
require _SHELL_DIR.'/util/OtherUtils.php';
/* 开发测试平时使用的打印函数 */
require _SHELL_DIR . '/../../var/function.php';

//require _SHELL_DIR.'/db/MysqliQuery.php';
//require _SHELL_DIR . '/util/image.php';