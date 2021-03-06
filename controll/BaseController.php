<?php
class BaseController{
	
	public $module;
	public $controller;
	public $action;
	
	public $aGPArr = array();	
	public $sLogPath;
	
	/**
	 * @author scottshi
	 * @desc constructor of Basecontroller
	 * @param string $module
	 * @param string $controller
	 * @param string $action
	 * @date 2015-12-13
	 */
	public function __construct($module,$controller,$action){
		
		$this->module = $module;
		$this->controller = $controller;
		$this->action = $action;		
		$this->aGPArr = array_merge($_GET,$_POST);		
	}
	
	/**
	 * @author scottshi
	 * @desc get parameter value by GET or POST
	 * @param array/string $param
	 * @param string $type
	 * @date 2015-12-13
	 */
	public function getVal($param,$type='string'){
		if(is_array($param)){
			foreach ($param as $key){
				$this->filterVal($key, $type);
			}
		}else{
			$this->filterVal($key, $type);
		}
	}
	/**
	 * @author scottshi
	 * @desc filter parameter value by GET or POST
	 * @param string $key
	 * @param string $type
	 * @date 2015-12-13
	 */
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
	
	
}