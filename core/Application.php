<?php

/**
 * Created by PhpStorm.
 * User: AF
 * Date: 2016/11/15
 * Time: 21:04
 */
namespace core;
use core\run\GetConfigs;

class Application
{
    /**
     * 入口函数 默认有缓存
     * @throws \Exception
     */
    static public function run(){
        $dev=GetConfigs::getRunConfigs();
        $mode=$dev['mode'];
        if($mode==='dev'){
            /*开发加载常用的打印函数，开发完毕关闭*/
            include __DIR__.'/../var/function.php';
            $modes=true;
            $runTime=$_SERVER['REQUEST_TIME'];
        }else{
            $modes=false;
        }
        $_route=new \core\run\Run();
        $action=$_route->route($modes);
        $_route->actions($action,$modes);
        if($modes===true){
            echo 'run-time : '.(microtime(true)-$runTime);
        }
    }
    /*不取用缓存的函数*/
    static public function noCacheRun(){
        $dev=GetConfigs::getRunConfigs();
        $mode=$dev['mode'];
        if($mode==='dev'){
            /*开发加载常用的打印函数，开发完毕关闭*/
            include __DIR__.'/../var/function.php';
            $modes=true;
            $runTime=$_SERVER['REQUEST_TIME'];
        }else{
            $modes=false;
        }
        $_route=new \core\run\Run();
        $action=$_route->route($modes);
        $data=$_route->noCacheAction($action);
        $_route->noCacheView($data);
        if($modes===true){
            echo 'run-time : '.(microtime(true)-$runTime);
        }
    }
}