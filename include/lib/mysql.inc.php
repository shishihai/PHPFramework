<?php
/**
 * @author scottshi
 * @dest define mysql class for all sql operations in the framework
 * @date 2015-12-14
 */
class MySql{
	protected $_link;
	protected $_dbconfig;
	protected $_sql;
	protected $_datapointer;
	private static $_instance = null;
	
	/**
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
	 * @desc destructor for mysql class
	 */
	public function __destruct(){
		mysql_close($this->_link);
	}
	
	/**
	 * @desc get mysql instance by using singleton pattern
	 */
	public static function getInstance(){       
		if(!isset(self::$_instance) || (self::$_instance instanceof self)){
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Query one record from database
	 * @param string $table:table name
	 * @param string $where:where condition 
	 * @param array/string $fields:selected fields
	 */
	public function fetchOne($table, $where = '', $fields = '*'){
		$this->fetchAll($table, $where = '',1, 0, $fields = '*');
	}
	
	/**
	 * Query records from database with parameters
	 * @param string $table:table name 
	 * @param string $where:where condition 
	 * @param int $limit:record number
	 * @param int $offset:record offset
	 * @param string $order:order by rule
	 * @param array/string $fields:selected fields
	 */
	public function fetchAll($table,$where = '',$limit = 10,$offset = 0 ,$order = '',$fields = '*'){
		if(is_array($fields)){
			$fields = implode(',', $fields);
		}
		$this->_sql = "SELECT {$fields} FROM '{$table}'";
		if($where){
			$this->_sql .= " WHERE {$where}";
		}
		if($order){
			$this->_sql .= " ORDER BY {$order}";
		}
		if($offset){
			$this->_sql .= " LIMIT {$offset},{$limit}";
		}else{
			$this->_sql .= " LIMIT {$limit}";
		}
		$resultset = array();
		$resultset = $this->_execSql($this->_sql);
		while($row = mysql_fetch_assoc($this->_datapointer)){
			$resultset[] = $row;
		}
		$this->_datapointer = null;
		return $resultset;
	}
	
	/**
	 * Query the number of record with specific condition
	 * @param string $table
	 * @param string $where
	 * @return:number of records
	 */
	public function getCount($table,$where){
		$this->_sql = "SELECT COUNT(*) as icount FROM {$table}";
		if($where){
			$this->_sql .= " WHERE {$where}";
		}
		$this->_execSql($this->_sql);
		$row = mysql_fetch_assoc($this->_datapointer);
		$iResult = $row["iCount"];
		$this->_datapointer = null;
		return $iResult;
	}
	
	/**
	 * insert into database
	 * @param string $table
	 * @param array $data
	 * @param bool $ignore
	 * @return:the number of affected rows
	 */
	public function insert($table,$data,$ignore = false){
		if(empty($table) || empty($data) || !is_array($data)){
			return -1;
		}
		$keys = array_keys($data);
		if($ignore){
			$this->_sql = "INSERT IGNORE INTO {$table} (".implode(",", $keys).") VALUES (";
		}else{
			$this->_sql = "INSERT INTO {$table} (".implode(",", $keys).") VALUES (";
		}
		foreach ($data as $val){
			$this->_sql .= "'".addslashes($val)."',";
		}
		$this->_sql = substr($this->_sql, 0,-1).")";
		
		$this->_execSql($this->_sql);
		$this->_datapointer = null;
		$result = mysql_insert_id($this->_link);
		return $result;
	}
	
	/**
	 * update database record
	 * @param string $table
	 * @param array $data
	 * @param string $where
	 * @return:the number of affected rows
	 */
	public function update($table,$data,$where = ''){
		if(empty($table) || empty($data) || !is_array($data)){
			return -1;
		}
		$tmp = array();
		
		foreach ($data as $key => $value){
			$tmp[] = "{$key} = '{$value}'";
		}
		
		$this->_sql = "UPDATE {$table} SET ".implode(",", $tmp);
		
		if($where){
			$this->_sql .= " WHERE {$where}";
		}
		$this->_execSql($this->_sql);	
		$result = mysql_affected_rows($this->_link);
		$this->_datapointer = null;
		return $result;
	}
	
	/**
	 * delete database record
	 * @param string $table
	 * @param string $where
	 * @return:the number of affected rows
	 */
	public function delete($table,$where = ''){
		if(empty($table)){
			return -1;
		}
		$this->_sql = "DELETE FROM {$table}";
		if($where){
			$this->_sql .= " WHERE {$where}";
		}
		$this->_execSql($this->_sql);
		$result = mysql_affected_rows($this->_link);
		$this->_datapointer = null;
		return $result;
	}
	
	/**
	 * exec complex sql statement
	 * @param string $sql
	 * @param string $type
	 * @return:depend on the sql type
	 */
	public function query($sql,$type='select'){
		$this->_sql = $sql;
		$this->_execSql($this->_sql);
		if($type == "select"){
			$result = array();
			while($row = mysql_fetch_assoc($this->_datapointer)){
				$result[] = $row;
			}
		}elseif ($type == "insert"){
			$result = mysql_insert_id($this->_link);
		}elseif ($type == "update"){
			$result = mysql_affected_rows($this->_link);
		}elseif ($type == "delete"){
			$result = mysql_affected_rows($this->_link);
		}else{
			return true;
		}
		$this->_datapointer = null;
		return $result;
	}
	
	/**
	 * @desc set mysql database
	 * @param string $sDatabase
	 */
	public function setDatabase($sDatabase){
		if(!mysql_select_db($sDatabase,$this->_link)){
			recordSysLog('Cannot use database '.$sDatabase);
		}
	}
	
	/**
	 * @desc set mysql charset
	 * @param string $sCharset
	 */
	public function setCharset($sCharset = 'UTF8'){
		$sSql = "set names {$sCharset}";
		$this->_execSql($sSql);
	}
	
	/**
	 * @desc execute sql statement
	 * @param string $sSql
	 */
	private function _execSql($sSql){
		try {
			$this->_datapointer = mysql_query($sSql,$this->_link);
		} catch (Exception $e) {
			recordSysLog('Invalid query:'.$sSql.' Error:'.mysql_errno($this->_link));
		}
	}
	
}