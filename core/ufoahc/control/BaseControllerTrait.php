<?php
/**
 * Created by PhpStorm.
 * User: AF
 * Date: 2016/11/24
 * Time: 10:20
 */

namespace core\ufoahc\control;


trait BaseControllerTrait
{
    /**
     * 这里所有函数都可以联级操作
     * 以下代码内容参考
     * @url:https://github.com/zhangbaitong/plume
     */
    /************************************************************************************************************/
    private $context;
    public function __construct() {
        $this->context['response'] = array(
            'data' => null,
            'api' => false,
            'view' => null,
            'error' => false
        );
    }
    protected function msg($code, $msg){
        $this->context['response']['data'] = json_encode(array('code' => $code, 'msg' => $msg), JSON_UNESCAPED_UNICODE);
        return $this;
    }

    protected function result($data=array()){
        $this->context['response']['data'] = $data;
        return $this;
    }

    protected function error(){
        $this->context['response']['error'] = true;
        return $this;
    }


    protected function json(){
        $this->context['response']['data'] = json_encode($this->context['response']['data'], JSON_UNESCAPED_UNICODE);
        return $this;
    }

    protected function api(){
        $this->context['response']['api'] = true;
        return $this;
    }

    protected function view($view){
        $this->context['response']['view'] = $view;
        return $this;
    }

    protected function response(){
        return $this->context['response'];
    }
    /************************************************************************************************************/
}