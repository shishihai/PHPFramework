<?php
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
