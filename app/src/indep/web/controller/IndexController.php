<?php
namespace app\src\indep\web\controller;

use app\src\indep\web\service\UsersService;
use core\ufoahc\control\BaseController;

class IndexController extends BaseController
{
    public function indexAction(){
//        echo "My name is " , get_class($this) , "\n";
//        print_r($_SERVER);
        $tt=$_SERVER['REQUEST_TIME'];
        //测试二维码生成工具
        $patch=$_SERVER['DOCUMENT_ROOT'];
        /*$res=QRCodeUtils::createQRCode(UuidUtils::uuid(),'test 二维码',$patch.'/test',false,$patch.'/img/logo_1.png');
        $res=stristr($res,'/test/');
        p($res);
        echo '<img src="'.$res.'">';*/
        //获取配置文件方法
       /* $res1=$this->getConfigValue('my_array');
        p($res1);*/
        //实例化service
        $usersService=new UsersService();
        //根据id查询数据
        $res2=$usersService->getById('a0acd183542b0d2ab2d52291171aef0b');
//        p($res2);
        //查询所有
        $res3=$usersService->tGetAll();
        //p($res3);
        //更多查询 MysqliAbstractImp.php
        //设置模板
//        $this->_setLayOut('layout2');
        //测试日志工具
//        $this->log('liangchaofu','这是测试的内容','错误的内容');

        $time_request = microtime(true) - $tt;

        $ret3=$usersService->like();

//        p($usersService->count());
//        p($usersService->min());
//        p($usersService->max());
//        p($usersService->avg());
//        p($usersService->sum());
//
//        p($ret3);
        /*1*/
        $this->result(array('a'=>date('H-yd H:i:s'),'my_yes'=>$res2,'ret'=>$ret3));
        return $this->response();
        /*2*/
//        return $this->result(array('a'=>date('H-yd H:i:s'),'my_yes'=>$res2))->response();
        /*3*/
//        return array('a'=>date('H-yd H:i:s'),'my_yes'=>$res2);
    }

    public function indexaAction(){
        /* 接口方式*/
        $this->api();
        if(!$this->isPost()){
            $this->msg(-10,'不是post请求')->response();
        }
        $usersService=new UsersService();
        //根据id查询数据
        $res2=$usersService->getById('a0acd183542b0d2ab2d52291171aef0b');
        $this->msg(0,$res2);
        return $this->response();
    }
}
