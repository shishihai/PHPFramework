<?php
class APIController extends BaseController{
	
	public function __construct($module, $controller, $action){
		parent::__construct($module, $controller, $action);
	}
	
	public function showApiJson($iRet,$sMsg,$jData = array()){
		$aOutputArr = array(
				'ret' => $iRet,
				'msg' => $sMsg,
				'data' => $jData
		);
		echo json_encode($aOutputArr);
		exit();
	}
}