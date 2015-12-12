<?php
class BaseController{
	public $module;
	public $controller;
	public $action;
	public $aGPArr = array();
	
	public function __construct($module,$controller,$action){
		
		$this->module = $module;
		$this->controller = $controller;
		$this->action = $action;
		
		$this->aGPArr = array_merge($_GET,$_POST);
		
	}
	
	private function _initController(){
		
	}
	
	public function getVal($param,$type='string'){
		if(is_array($param)){
			foreach ($param as $key){
				$this->CVal($key, $type);
			}
		}else{
			$this->CVal($key, $type);
		}
	}
	
	public function CVal($key,$type){
		switch ($type){
			case 'string':
				break;
			case 'int':
				break;
			case 'bigint':
				break;
			case 'none':
				break;
			default:
				break;
		}
	}
}