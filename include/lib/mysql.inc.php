<?php
/**
 * @author scottshi
 * @dest define mysql class for all sql operations in the framework
 * @date 2015-12-14
 */
class MySql{
	protected $_link;
	protected $_dbconfig;
	private static $_instance = null;
	
	/**
	 * @author scottshi
	 * @desc constructor for mysql class
	 * @param string $sEnvType
	 */
	private function __construct($sEnvType='release'){
		
		if($sEnvType == 'test'){
			$this->_dbconfig = loadConfig('DBTest');
		}elseif($sEnvType == 'release'){
			$this->_dbconfig = loadConfig('DB');
		}else{
			if(isTestEnv()){
				$this->_dbconfig = loadConfig('DBTest');
			}else{
				$this->_dbconfig = loadConfig('DB');
			}
		}
		$sHost = $this->_dbconfig['host'];
		$sPort = $this->_dbconfig['port'];
		$sUser = $this->_dbconfig['user'];
		$sPassword = $this->_dbconfig['password'];
		$this->_link = mysql_connect("{$sHost}:{$sPort}",$sUser,$sPassword);
		if(!$this->_link){
			recordSysLog('Unable to connect to Mysql');
		}
		$sDatabase = $this->_dbconfig['database'];
		$this->setDatabase($sDatabase);
		$sCharset = $this->_dbconfig['charset'];
		$this->setCharset($sCharset);
	}
	
	/**
	 * @author scottshi
	 * @desc get mysql instance by using singleton pattern
	 * @date 2015-12-14
	 */
	public static function getInstance(){
		if(!isset(self::$_instance) || (self::$_instance instanceof self)){
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * @author scottshi
	 * @desc set mysql database
	 * @param string $sDatabase
	 */
	public function setDatabase($sDatabase){
		if(!mysql_select_db($sDatabase,$this->_link)){
			recordSysLog('Cannot use database '.$sDatabase);
		}
	}
	
	/**
	 * @author scottshi
	 * @desc set mysql charset
	 * @param string $sCharset
	 */
	public function setCharset($sCharset = 'UTF8'){
		$sSql = "set names {$sCharset}";
		$this->_execSql($sSql);
	}
	
	/**
	 * @author scottshi
	 * @desc execute sql statement
	 * @param string $sSql
	 */
	private function _execSql($sSql){
		try {
			mysql_query($sSql,$this->_link);
		} catch (Exception $e) {
			recordSysLog('Invalid query:'.$sSql.' Error:'.mysql_errno($this->_link));
		}
	}
	
}