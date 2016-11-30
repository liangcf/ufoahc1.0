<?php
/**
 * Created by PhpStorm.
 * User: AF
 * Date: 2016/9/20
 * Time: 14:38
 */
header("Content-Type:text/html;charset=utf-8");
function p($var){
    $phpRunMode=php_sapi_name();
    if(stristr('cli',$phpRunMode)){
        echo "\r\n\r\n------------------------------------------------------------\r\n";
    }else{
        echo '<hr style="border-top: 1px solid #008000">';
    }
    if(is_bool($var)){
        var_dump($var);
    }elseif(is_null($var)){
        var_dump($var);
    }else{
        print_r($var);
    }
    if(stristr('cli',$phpRunMode)){
        echo "------------------------------------------------------------\r\n\r\n";
    }else{
        echo '<hr style="border-top: 1px solid #008000">';
    }
}
function pe($var){
    $phpRunMode=php_sapi_name();
    if(stristr('cli',$phpRunMode)){
        echo "\r\n\r\n------------------------------------------------------------\r\n";
    }else{
        echo '<hr style="border-top: 1px solid #008000">';
    }
    if(is_bool($var)){
        var_dump($var);
    }elseif(is_null($var)){
        var_dump($var);
    }else{
        print_r($var);
    }
    if(stristr('cli',$phpRunMode)){
        echo "------------------------------------------------------------\r\n\r\n";
    }else{
        echo '<hr style="border-top: 1px solid #008000">';
    }
    exit;
}
function pd($var){
    $phpRunMode=php_sapi_name();
    if(stristr('cli',$phpRunMode)){
        echo "\r\n\r\n------------------------------------------------------------\r\n";
    }else{
        echo '<hr style="border-top: 1px solid #008000">';
    }
    var_dump($var);
    if(stristr('cli',$phpRunMode)){
        echo "------------------------------------------------------------\r\n\r\n";
    }else{
        echo '<hr style="border-top: 1px solid #008000">';
    }
}
function pde($var){
    $phpRunMode=php_sapi_name();
    if(stristr('cli',$phpRunMode)){
        echo "\r\n\r\n------------------------------------------------------------\r\n";
    }else{
        echo '<hr style="border-top: 1px solid #008000">';
    }
    var_dump($var);
    if(stristr('cli',$phpRunMode)){
        echo "------------------------------------------------------------\r\n\r\n";
    }else{
        echo '<hr style="border-top: 1px solid #008000">';
    }
    exit;
}
function currentTime(){
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
//返回微秒*100
function microsecond(){
    list($microsecond, $timeStamp) = explode(" ", microtime());
    $microsecond=(float)$microsecond;
    return $microsecond*1000*1000*100;
}
//extract
class runTime
{
    protected $beginTime;
    function __construct(){
        $this->beginTime=microsecond();
    }
    //返回毫秒
    public function finishTime(){
        $time=round(microsecond() - $this->beginTime);
        return $time/100000;
    }
}