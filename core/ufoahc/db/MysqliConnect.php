<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/6/17
 * Time: 11:39
 */

namespace core\ufoahc\db;


use core\run\GetConfigs;

abstract class MysqliConnect extends MysqliImp
{
    /**
     * @throws \Exception
     */
    function __construct(){
        $defaultDb=GetConfigs::getRunConfigs();
        $config=$defaultDb['default_db'];
        $this->setConnectInit($config);
    }
}