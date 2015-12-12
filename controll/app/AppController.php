<?php
class AppController extends BaseController{
	public function __construct(){
		parent::__construct($module, $controller, $action);
	}
	
	public function initSmarty(){
		$this->view = new Smarty();
		$this->view -> template_dir = ROOT_PATH . "/app/templates/";
		$this->view -> compile_dir = ROOT_PATH . "/app/templates_c/";
		$this->view -> left_delimiter = '<{';
		$this->view -> right_delimiter = '}>';
		$this->view -> caching = false;
	}
	
	public function assign($key, $value){
		$this->view->assign($key, $value);
	}
	public function display($tpl){
		$this->view->display($tpl);
	}
	
	public function showAppJson($iRet,$sMsg,$jData = array()){
		$aOutputArr = array(
				'ret' => $iRet,
				'msg' => $sMsg,
				'data' => $jData
		);
		echo 'var '.$this->controller.'_'.$this->action.' = '.json_encode($aOutputArr).';/*****/';
		exit();
	}
	
	public function checkAppLogin(){
	
	}
}