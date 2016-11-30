<?php

/**
 * Created by PhpStorm.
 * User: AF
 * Date: 2016/11/18
 * Time: 14:07
 */
namespace core\run;
class Run
{
    /* 拼装路由*/
    public function route($mode){
        if($mode===false){
            $this->handleException();
        }
        $action=array();
        $_reqUri=$_SERVER['REQUEST_URI'];
        $_index=strpos($_reqUri,'?');
        $_uri=$_index>0?substr($_reqUri,0,$_index):$_reqUri;
        if(stripos($_uri,'index.php')){
            throw new \Exception('访问出错！', 404);
        }
        if($_uri=='/'){
            $action=array('_module'=>'web','_controller'=>'index','_action'=>'index');
            return $action;
        }
        $pathArr=explode('/',trim($_uri,'/'));
        if(isset($pathArr[0])){
            $_module=$pathArr[0];
        }else{
            $_module='web';
        }
        if(isset($pathArr[1])){
            $controller=$pathArr[1];
        }else{
            $controller='index';
        }
        if(isset($pathArr[2])){
            $_action=$pathArr[2];
        }else{
            $_action='index';
        }

        $action=array('_module'=>$_module,'_controller'=>$controller,'_action'=>$_action);
        return $action;
    }
    /*判断是否缓存的函数*/
    public function actions($action,$modes){
        $_module=$action['_module'];
        $_controller=$action['_controller'];
        $_action=$action['_action'];
        $_url='/'.$_module.'/'.$_controller.'/'.$_action;
        $_url=strtolower($_url);
        $cache=GetConfigs::getRunConfigs();
        $cacheFlag=isset($cache['cache_flag'])?$cache['cache_flag']:false;
        $cacheTime=isset($cache['cache_time'])?$cache['cache_time']:0;
        if($cacheFlag===true&&$this->cache($_url)===true) {//开启缓存
            $cacheFile = __DIR__ . '/../../var/cache' . $_url.'.html';
            if (is_file($cacheFile)&&filemtime($cacheFile) + $cacheTime >= time()) {
                require $cacheFile;
                if($modes===true){
                    echo 'cache -- ';
                }
            }else{
                @unlink($cacheFile);
                $this->action($action);
                if($modes===true){
                    echo 'cache overtime -- ';
                }
            }
        }else{
            $this->action($action);
            if($modes===true){
                echo 'no cache -- ';
            }
        }
    }
    /*缓存视图跳转*/
    public function action($action){
        $returns=$this->actioning($action);
        $this->view($returns);
    }
    /* 视图*/
    public function view($whole){
        $view=new View();
        $view->view($whole);
    }
    /*缓存的类反射*/
    public function noCacheAction($action){
        return $this->actioning($action);
    }
    /*无缓试图*/
    public function noCacheView($whole){
        $view=new View();
        $view->noCacheView($whole);
    }
    /*类反射*/
    private function actioning($action){
        $_module=$action['_module'];
        $_controller=$action['_controller'];
        $_action=$action['_action'];
        $_url='/'.$_module.'/'.$_controller.'/'.$_action;
        /* 组装类路径 */
        $_className='app\\src\\indep\\'.strtolower($_module).'\\controller\\'.ucfirst($_controller).'Controller';
        $_funName=$_action.'Action';
        //throw new \Exception('测试异常');
        if(!class_exists($_className)){
            throw new \Exception("'Controller in not found'",404);
        }
        $_class=new \ReflectionClass($_className);
//        $_instance=$_class->newInstanceWithoutConstructor();//不通过构造函数
        $_instance=$_class->newInstanceArgs(); //通过构造函数
        if(!$_class->hasMethod($_funName)){
            throw new \Exception("'Action is not found'",404);
        }
        $_method=$_class->getMethod($_funName);
        $_beforeDispatch=$_class->getMethod("beforeDispatch");
        $whole['before']=$_beforeDispatch->invoke($_instance);

        $whole['data']=$_method->invoke($_instance);

        $_afterDispatch=$_class->getMethod("afterDispatch");
        $whole['after']=$_afterDispatch->invoke($_instance);
        /* 获取共同的头部或者尾部 */
        $_layMethod=$_class->getMethod("_getLayOut");
        $wholes['layout']=$_layMethod->invoke($_instance);
        /* 寻找视图文件的路径 */
        $wholes['view_dir']=$_url;
        return array('whole'=>$whole,'wholes'=>$wholes);
    }

    /**
     * 参考
     * @url:https://github.com/zhangbaitong/plume
     * -------------- 异常处理函数 ----------------
     **/

    //非开发环境全局处理error(warning)，exception,shutdown
    private function handleException(){
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
        set_error_handler(array($this,'error_function'));
        set_exception_handler(array($this,'exception_function'));
        register_shutdown_function(array($this,'shutdown_function'));
    }

    //1=>'ERROR', 2=>'WARNING', 4=>'PARSE', 8=>'NOTICE'
    public function error_function($errno, $errstr, $errfile, $errline, $errcontext){
        /*log*/
        \core\ufoahc\util\LogUtils::log('error_function','error_function',array('errno' => $errno,'errstr' => $errstr, 'errfile' => $errfile, 'errline' => $errline, 'errcontext' => $errcontext),__DIR__.'/../../var/systemlog');
        require __DIR__ . '/../../app/view/error/404.html';
        exit;
    }

    /**
     * @param $e \Exception
     */
    public function exception_function($e){
        /*log*/
        \core\ufoahc\util\LogUtils::log('exception_function','exception_function',$e->getMessage(),__DIR__.'/../../var/systemlog');
        switch ($e->getCode()){
            case 404:
                require __DIR__ . '/../../app/view/error/404.html';
                break;
            default:
                require __DIR__ . '/../../app/view/error/500.html';
        }
        exit;
    }

    public function shutdown_function(){
        $error = error_get_last();
        if(!empty($error)){
            \core\ufoahc\util\LogUtils::log('exception_function','exception_function',$error,__DIR__.'/../../var/systemlog');
        }
        exit;
    }

    /*对比缓存*/
    public function cache($cacheUrl){
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