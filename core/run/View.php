<?php
/**
 * Created by PhpStorm.
 * User: AF
 * Date: 2016/11/26
 * Time: 14:06
 */

namespace core\run;


class View
{
    /*返回页面的数据*/
    public $data;
    /*返回页面的全局变量*/
    public $overall;
    /*有缓存的视图*/
    public function view($whole){
        $this->initHeader();
        $this->data=$data=$whole['whole']['data']['data'];
        if(isset($whole['whole']['data']['api'])&&$whole['whole']['data']['api']===true){
            $this->renderAPI($data);
        }
        $this->overall=$overall=$whole['whole'];
        if($whole['whole']['data']['view']!==null){
            $cacheUrl=strtolower($whole['whole']['data']['view']);
            $_url='/app/view/content'.$whole['whole']['data']['view'];
        }else{
            $cacheUrl=strtolower($whole['wholes']['view_dir']);
            $_url='/app/view/content'.$whole['wholes']['view_dir'];
        }
        $_layMode=$whole['wholes']['layout'];
        $_tmpDir=__DIR__.'/../..';
        $_content=$_tmpDir.strtolower($_url).'.phtml';
        $_layUrl='/app/view/layout/'.$_layMode.'.phtml';
        $_layOutFile=$_tmpDir.strtolower($_layUrl);
        if(is_file($_content)&&is_file($_layOutFile)){
            $cache=GetConfigs::getRunConfigs();
            $cacheFlag=isset($cache['cache_flag'])?$cache['cache_flag']:false;
            if($cacheFlag===true&&$this->cache($cacheUrl)===true){//开启缓存
                $cacheDirArr=explode('/',$cacheUrl);//print_r($cacheDirArr);die;
                $cacheFileDir=__DIR__.'/../../var/cache/'.$cacheDirArr[1].'/'.$cacheDirArr[2];
                if(!is_dir($cacheFileDir)){
                    mkdir($cacheFileDir,0777,true);
                }
                $cacheFile=$cacheFileDir.'/'.strrchr($cacheUrl,'/').'.html';
                ob_start();//2.启动ob缓存
                require $_layOutFile;
                $ob_str = ob_get_contents();
                //3.把ob_str保存到一个静态文件页面，取文件名有讲究：1.唯一标识该新闻 2.利于seo
                file_put_contents($cacheFile, $ob_str);
            }else{
                require $_layOutFile;
            }
        }else{
            throw new \Exception('View is not found:'.$_content,404);
        }
    }
    /*无缓存视图*/
    public function noCacheView($whole){
        $this->initHeader();
        $this->data=$data=$whole['whole']['data']['data'];
        if(isset($whole['whole']['data']['api'])&&$whole['whole']['data']['api']===true){
            $this->renderAPI($data);
        }
        $this->overall=$overall=$whole['whole'];
        if($whole['whole']['data']['view']!==null){
            $cacheUrl=strtolower($whole['whole']['data']['view']);
            $_url='/app/view/content'.$whole['whole']['data']['view'];
        }else{
            $cacheUrl=strtolower($whole['wholes']['view_dir']);
            $_url='/app/view/content'.$whole['wholes']['view_dir'];
        }
        $_layMode=$whole['wholes']['layout'];
        $_tmpDir=__DIR__.'/../..';
        $_content=$_tmpDir.strtolower($_url).'.phtml';
        $_layUrl='/app/view/layout/'.$_layMode.'.phtml';
        $_layOutFile=$_tmpDir.strtolower($_layUrl);
        if(is_file($_content)&&is_file($_layOutFile)){
            require $_layOutFile;
        }else{
            throw new \Exception('View is not found:'.$_content,404);
        }
    }
    /*加载css方法*/
    public function appendCss($var){
        echo '<link type="text/css" rel="stylesheet" href="'.$var.'"/>';
    }
    /*加载js的方法*/
    public function appendJs($var){
        echo '<script type="text/javascript" src="'.$var.'"></script>';
    }

    private function initHeader(){
        header('Content-Type: text/html; charset=UTF-8');
    }

    private function apiHeader(){
        header('Content-Type: application/json; charset=UTF-8');
    }
    private function renderAPI($actionResult){
        $this->apiHeader();
        print_r($actionResult);
        exit;
    }
    /*对比缓存*/
    private function cache($cacheUrl){
        $cache=GetConfigs::getAppConfigs();
        $caches=isset($cache['cache'])?$cache['cache']:array();
        if(empty($caches)){
            return false;
        }else{
            foreach($caches as $_key=>$cacheRow){
                if($cacheRow==$cacheUrl){
                    return true;
//                    break;
                }
            }
            return false;
        }
    }
}