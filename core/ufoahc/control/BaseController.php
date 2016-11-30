<?php
/**
 * Created by PhpStorm.
 * User: SAMSUNG-R428
 * Date: 16-1-25
 */

namespace core\ufoahc\control;

use core\run\GetConfigs;

abstract class BaseController
{
	use BaseControllerTrait;

	public $_layOut='layout.pc';

	/**
	 * 获取application.config.php配置文件中的配置项
	 * @param $configKey
	 * @return null
	 */
	protected function getConfigValue($configKey){
		$config=GetConfigs::getAppConfigs();
		if(isset($config[$configKey])){
			return $config[$configKey];
		}else{
			return null;
		}
	}

	/**
	 * 获取db.config.php配置文件中的配置项
	 * @param $configKey
	 * @return null
	 */
	protected function getDbConfigValue($configKey){
		$config=GetConfigs::getRunConfigs();
		if(isset($config[$configKey])){
			return $config[$configKey];
		}else{
			return null;
		}
	}

	/**
	 * 获取get参数
	 * @param string $key
	 * @param null $default
	 * @return null|string
	 */
	protected function getParameter($key,$default=null){
		if(isset($_GET[$key])){
			return trim($_GET[$key]);
		}else{
			return $default;
		}
	}

	/**
	 * 获取post参数
	 * @param string $key
	 * @param null $default
	 * @return null|string
	 */
	protected function postParameter($key,$default=null){
		if(isset($_POST[$key])){
			return trim($_POST[$key]);
		}else{
			return $default;
		}
	}

	/**
	 * 判断是否是post请求
	 * @return bool
	 */
	protected function isPost() {
		if ($_SERVER['REQUEST_METHOD'] != "POST") {
			return false;
		}
		return true;
	}

	/**
	 * 设置session节点
	 * @param $key
	 * @param $value
	 */
	protected function setSessionValue($key, $value) {
		if(!isset($_SESSION)){
			session_start();
		}
		$_SESSION[$key] = $value;
	}

	/**
	 * 获取session节点下存储的值
	 * @param $key
	 * @param null $default
	 * @return null string
	 */
	protected function getSessionValue($key, $default=null) {
		if(!isset($_SESSION)){
			session_start();
		}
		return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
	}

	/**
	 * 删除某个session节点的值
	 * @param $key
	 */
	protected function unsetSession($key) {
		if(!isset($_SESSION)){
			session_start();
		}
		if (isset($_SESSION[$key])) {
			unset($_SESSION[$key]);
		}
	}

	/**
	 * 设置某个session节点的时间
	 * @param $key
	 * @param $value
	 * @param $expire
	 */
	protected function setValAndExpire($key, $value, $expire) {
		ini_set('session.gc_maxlifetime', $expire);
		session_set_cookie_params($expire);
		if(!isset($_SESSION)){
			session_start();
		}
		$_SESSION[$key] = $value;
	}
	/**
	 * 日志记录工具
	 * @param string $file 日志名称
	 * @param string $message 日志描述
	 * @param null $context 日志内容
	 * @param string $dir 日志路径
	 * @return int
	 */
	protected function log($file,$message,$context,$dir=__DIR__.'/../../../var/logs'){
		$dir=rtrim($dir,'/');
		$dir=$dir.'/'.date('Ymd').'/';
		if(!is_dir($dir)){
			mkdir($dir,0777,true);
		}
		if(!is_string($context)){
			$context=json_encode($context);
		}
		$fileName=$dir.$file.'.log';
		$date=date('Y-m-d H:i:s');
		$log='['.$date.'] - '.$message.' - '.$context."\r\n\r\n";
		return file_put_contents($fileName, $log,FILE_APPEND);
	}
	/**
	 * 密码处理
	 * @param $pwd
	 * @return string
	 */
	protected function passwordProcessing($pwd){
		$pwdMd5=md5($pwd);
		$pwdMd5=md5(substr($pwdMd5,0,-3));
		$pwdMd5=md5(substr($pwdMd5,3));
		return md5($pwdMd5);
	}

	/**
	 * 获取当前时间
	 * @return bool|string
	 */
	protected function getTime(){
		return date('Y-m-d H:i:s');
	}

	/**
	 * 获取id
	 * @param null $prefix
	 * @return string
	 */
	protected function id($prefix=null){
		return strtolower(md5(uniqid($prefix . mt_rand(), true)));
	}

	/**
	 * 图片base64解码
	 * @param $imgInfo
	 * @param $path
	 * @return bool|string
	 */
	protected function imgBase64Decode($imgInfo,$path){
		try {
			$base64Info=substr(strstr($imgInfo,','),1);
			$imgInfo=base64_decode($base64Info);
			$fileName=$this->id().'.png';
			if (!is_dir($path)){
				mkdir($path, 0777,true);
			}
			$path=$path.$fileName;
			file_put_contents($path,$imgInfo);	//返回的是字节数
			return $fileName;
		}catch(\Exception $e) {
			echo '图片base64解码异常：'.$e->getMessage();
			return false;
		}
	}

	/**
	 * 调用请求的Action前需要执行的动作
	 */
	public function beforeDispatch(){
		//默认什么也不做，各子类可重写该方法
	}

	/**
	 * 调用请求的Action后需要执行的动作
	 */
	public function afterDispatch(){
		// 默认什么也不做，各子类可重写该方法
	}

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