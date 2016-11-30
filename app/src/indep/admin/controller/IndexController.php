<?php
namespace app\src\indep\admin\controller;

use core\ufoahc\control\BaseController;

class IndexController extends BaseController
{
    /*重写*/
    public function _getLayOut(){
        return 'layout.admin';
    }

    public function indexAction(){
        print_r($this->getConfigValue('my_array'));
        return $this->result(array('time'=>date('Y-m-d H:i:')))->response();
    }
}
