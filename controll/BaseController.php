<?php
class BaseController{
	
	public $module;
	public $controller;
	public $action;
	
	public $aGPArr = array();
	
	public $sLogPath;
	
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
				$this->filterVal($key, $type);
			}
		}else{
			$this->filterVal($key, $type);
		}
	}
	
	public function filterVal($key,$type){
		switch ($type){
			case 'string':
				$this->aGPArr[$key] = isset($this->aGPArr[$key])? htmlspecialchars(trim($this->aGPArr[$key]),ENT_QUOTES):'';
				break;
			case 'int':
				$this->aGPArr[$key] = isset($this->aGPArr[$key])? intval(trim($this->aGPArr[$key])):'';
				break;
			case 'bigint':
				$this->aGPArr[$key] = isset($this->aGPArr[$key])? (string)doubleval(trim($this->aGPArr[$key])):'';
				break;
			case 'none':
				$this->aGPArr[$key] = isset($this->aGPArr[$key])? trim($this->aGPArr[$key]):'';
				break;
			default:
				$this->aGPArr[$key] = isset($this->aGPArr[$key])? trim($this->aGPArr[$key]):'';
				break;
		}
	}
	
	public function initLog(){
		$this->sLogPath = ROOT_PATH.'/'.'log'.'/'.date('Ym',time()).'/'.date('Ymd').'.log';
		if(!file_exists($this->sLogPath)){
			
		}
	}
	public function recordLog(){
	
	}
}