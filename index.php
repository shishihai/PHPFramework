<?php
//**********************************************************
// Create date: 2015/12/12								  //
// Author: scottshi										  //							
// Description: 所有请求入口； 请求转发到index.php上			  //
// Example:	http://a.qq.com/model/controller/action/?a=xx //
//**********************************************************

define("ROOT_PATH",realpath(dirname(__FILE__)));
require_once '/include/logic/functions.inc.php';

//设置时区
date_default_timezone_set('Asia/Shanghai');

//自动载入需要的类库文件
function __autoload($class){
	
	if(class_exists($class,false) || interface_exists($class,false)){
		return;
	}
	$aIncludePathArr = array(
		'include_logic' => ROOT_PATH.'/include/logic',
		'include_lib' => ROOT_PATH.'/include/lib',
		'model' => ROOT_PATH.'/model'
	);	
	
	$bLoadClass = false;
	foreach ($aIncludePathArr as $key=>$value){
		if($key == 'include_logic' || $key == 'include_lib'){
			if(file_exists("{$value}/{$class}.inc.php")){
				$bLoadClass = true;
				require_once "{$value}/{$class}.inc.php";
				break;
			}
		}elseif ($key == 'model'){
			if(file_exists("{$value}/{$class}.class.php")){
				$bLoadClass = true;
				require_once "{$value}/{$class}.class.php";
				break;
			}
		}else{
			echo "Incorrect autoload path";
			exit();
		}
	}
	if(!$bLoadClass){
		echo "Unable to load {$class}";
		exit();
	}
}

//定义程序分发逻辑
function run_framework_dispatcher(){
	
	//获取请求路径
	if (isset($_SERVER['PATH_INFO'])) {
		$path = $_SERVER['PATH_INFO'];
	} else {
		if ( ($pos = strpos($_SERVER['REQUEST_URI'], '?')) !== false ) {
			$path = substr($_SERVER['REQUEST_URI'], 0, $pos);
		} else {
			$path = $_SERVER['REQUEST_URI'];
		}
		$path = str_replace('/index.php', '', $path);
	}
	$request_uri = explode('/', trim($path, '/'));
	
	//配置controll目录下可访问的module白名单
	$aModuleWhiteList = array('app','api');
	if(empty($request_uri) || count($request_uri) < 2 || !in_array($request_uri[0], $aModuleWhiteList)){
		show_default_404();
	}
	
	//获取Module、Controller和Action值
	$sModule = htmlspecialchars(trim($request_uri[0]),ENT_QUOTES);
	$sController = htmlspecialchars(trim($request_uri[1]),ENT_QUOTES);
	$sAction = isset($request_uri[2])? htmlspecialchars(trim($request_uri[2]),ENT_QUOTES):'index';
	
	$sControllerClass = $sController.'Controller';
	$sActionMethod = $sAction.'Action';
	
	$sCtrlClassFilePath = ROOT_PATH.'/'.$sModule.'/'.$sControllerClass.'.php';
	if(file_exists($sCtrlClassFilePath)){
		//引入当前请求controller文件
		require_once ROOT_PATH.'/controll/BaseController.php';
		require_once ROOT_PATH.'/'.$sModule.'/'.strtoupper($sModule).'Controller.php';
		require_once $sCtrlClassFilePath;
		//检测类是否存在
		if(!class_exists($sControllerClass)){
			show_default_404();
		}
		$oContrller = new $sControllerClass($sModule,strtolower($sController),$sAction);
		//检测方法是否存在
		if(!method_exists($oContrller, $sActionMethod)){
			show_default_404();
		}
		//调用指定类的指定方法
		try {
			$oContrller->$sActionMethod();
		} catch (Exception $e) {
			echo 'Call Method Failed';
			exit();
		}				
	}else{
		show_default_404();
	} 	
}

function show_default_404(){
	echo '404 Not Fuound';
	exit();
}
//执行分发操作
run_framework_dispatcher();



