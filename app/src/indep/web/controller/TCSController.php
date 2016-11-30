<?php
/**
 * Created by PhpStorm.
 * User: AF
 * Date: 2016/11/29
 * Time: 15:58
 */

namespace app\alone\web\controller;

use core\ufoahc\control\BaseController;

class TCSController extends BaseController
{
    public $_layOut='layout.pc';
    /**
     * 利用对象的方式给layout设置页面 set
     * @param string $_layMode
     */
    public function _setLayOut($_layMode='layout.pc'){
        $this->_layOut=$_layMode;
    }

    /**
     * get获取layout
     * @return string
     */
    public function _getLayOut(){
        return $this->_layOut;
    }
}