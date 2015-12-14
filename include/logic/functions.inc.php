<?php

function loadConfig($item = 'common'){
	$sPath = ROOT_PATH.'/cfg/';
	if(file_exists($sPath.$item.'.cfg.php')){
		require $sPath.$item.'.cfg.php';		
	}else{
		require $sPath.'common.cfg.php';	
	}
	return $G_CONFIG[$item];
}

/**
 * @author scottshi
 * @desc check the current environment in the context
 * @date 2015-12-14
 */
function isTestEnv(){
	return false;
}

function GBK2UTF8($str)
{
	if (is_array($str)) {
		foreach ($str as $value) {
			$value = GBK2UTF8($value);
		}
		return $str;
	} else if (is_string($str)) {
		$str = iconv("GBK", "UTF-8//IGNORE", $str);
		return $str;
	} else {
		return $str;
	}
}

function UTF82GBK($str)
{
	if (is_array($str)) {
		foreach ($str as $value) {
			$value = UTF82GBK($value);
		}
		return $str;
	} else if (is_string($str)) {
		$str = iconv("UTF-8", "GBK//IGNORE", $str);
		return $str;
	} else {
		return $str;
	}
}

function GetClientIp(){
	$ip = $_SERVER['REMOTE_ADDR'];
	if (isset($_SERVER["HTTP_X_REAL_IP"]) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_REAL_IP'])) {
		$ip = $_SERVER['HTTP_X_REAL_IP'];
	} else if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
		foreach ($matches[0] AS $xip) {
			if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
				$ip = $xip;
				break;
			}
		}
	}
	return $ip;
}

function GetServerIp(){
	return $_SERVER['SERVER_ADDR'];
}

function ipToLong($ip){
}

function longToIp($str){
}

function redirectURL($sURL){
	header("Location:{$sURL}");
}

/**
 * @author scottshi
 * @desc record log with log info and log level
 * @param string $sMsg
 * @param int $sLevel
 * @date 2015-12-13
 */
function recordUserLog($sMsg,$sLevel=ERRORLOG){
	try {
		$this->sLogPath = ROOT_PATH.'/log/user/'.date('Ym',time()).'/'.date('Ymd').'.log';
		if(! ($file = fopen($this->sLogPath, 'a+'))){
			echo 'Unable to open log file';
			exit();
		}
		$sLogInfo =date('Y-m-d H:i:s').'|'. GetClientIp().'|'.$sLevel.'|'.$sMsg.'\n';
		fwrite($file, $sLogInfo);
		fclose($file);
	} catch (Exception $e) {
		echo 'Unexpected error in recordLog';
		exit();
	}	
}

/**
 * @author scottshi
 * @desc record log with log info and log level
 * @param string $sMsg
 * @param int $sLevel
 * @date 2015-12-13
 */
function recordSysLog($sMsg,$sLevel=ERRORLOG){
	try {
		$this->sLogPath = ROOT_PATH.'/log/sys/'.date('Ym',time()).'/'.date('Ymd').'.log';
		if(! ($file = fopen($this->sLogPath, 'a+'))){
			echo 'Unable to open log file';
			exit();
		}
		$sLogInfo =date('Y-m-d H:i:s').'|'.$sLevel.'|'.$sMsg.'\n';
		fwrite($file, $sLogInfo);
		fclose($file);
	} catch (Exception $e) {
		echo 'Unexpected error in recordLog';
		exit();
	}	
}
