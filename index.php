<?php
//**********************************************************
// Create date: 2015/12/12								  //
// Author: scottshi										  //							
// Description: ����������ڣ� ����ת����index.php��			  //
// Example:	http://a.qq.com/model/controller/action/?a=xx //
//**********************************************************

define("ROOT_PATH",realpath(dirname(__FILE__)));
require_once '/include/logic/functions.inc.php';

//����ʱ��
date_default_timezone_set('Asia/Shanghai');

//�Զ�������Ҫ������ļ�
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

//�������ַ��߼�
function run_framework_dispatcher(){
	
	//��ȡ����·��
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
	
	//����controllĿ¼�¿ɷ��ʵ�module������
	$aModuleWhiteList = array('app','api');
	if(empty($request_uri) || count($request_uri) < 2 || !in_array($request_uri[0], $aModuleWhiteList)){
		show_default_404();
	}
	
	//��ȡModule��Controller��Actionֵ
	$sModule = htmlspecialchars(trim($request_uri[0]),ENT_QUOTES);
	$sController = htmlspecialchars(trim($request_uri[1]),ENT_QUOTES);
	$sAction = isset($request_uri[2])? htmlspecialchars(trim($request_uri[2]),ENT_QUOTES):'index';
	
	$sControllerClass = $sController.'Controller';
	$sActionMethod = $sAction.'Action';
	
	$sCtrlClassFilePath = ROOT_PATH.'/'.$sModule.'/'.$sControllerClass.'.php';
	if(file_exists($sCtrlClassFilePath)){
		//���뵱ǰ����controller�ļ�
		require_once ROOT_PATH.'/controll/BaseController.php';
		require_once ROOT_PATH.'/'.$sModule.'/'.strtoupper($sModule).'Controller.php';
		require_once $sCtrlClassFilePath;
		//������Ƿ����
		if(!class_exists($sControllerClass)){
			show_default_404();
		}
		$oContrller = new $sControllerClass($sModule,strtolower($sController),$sAction);
		//��ⷽ���Ƿ����
		if(!method_exists($oContrller, $sActionMethod)){
			show_default_404();
		}
		//����ָ�����ָ������
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
//ִ�зַ�����
run_framework_dispatcher();



