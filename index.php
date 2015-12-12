<?php
//**********************************************************
// Create date: 2015/12/12								  //
// Author: scottshi										  //							
// Description: 所有请求入口； 请求转发到index.php上			  //
// Example:	http://a.qq.com/model/controller/action/?a=xx //
//**********************************************************

define("ROOT_PATH",realpath(dirname(__FILE__)));
require_once '/include/logic/functions.inc.php';

dete_default_timezone_set('Asia/Shanghai');

