<?php
namespace app\src\indep\mobile\controller;

use core\ufoahc\control\BaseController;

class IndexController extends BaseController
{
    public function _getLayOut(){
        return 'layout.mobile';
    }
    public function indexAction(){

        return $this->result(array('time'=>date('Y-m-d H:i:')))->response();
    }
}
