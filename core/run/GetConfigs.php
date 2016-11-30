<?php
/**
 * Created by PhpStorm.
 * User: AF
 * Date: 2016/11/18
 * Time: 14:11
 */

namespace core\run;


class GetConfigs
{
    /**
     * 读取配置文件
     * @return mixed
     */
    static public function getAppConfigs(){
        $data=include __DIR__.'/../../config/application.config.php';
        return $data;
    }

    /**
     * 读取数据库配置文件
     * @return mixed
     */
    static public function getRunConfigs(){
        $data=include __DIR__.'/../../config/run.config.php';
        return $data;
    }
}