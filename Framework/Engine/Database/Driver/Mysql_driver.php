<?php
namespace Framework\Engine\Database\Driver;
use Framework\Engine\Gear\DriverDBInterface as DDBI; 

class Mysql_driver implements DDBI{
	private $conn;
	private $server;
	private $user;
	private $password;
	private $database;
	private $port;

	function __construct($config){
		$this->server 	= $config->startUp['DB.HOST'];
		$this->user 	= $config->startUp['DB.USERNAME'];
		$this->password = $config->startUp['DB.PASSWORD'];
		$this->database = $config->startUp['DB.DBNAME'];
		$this->port 	= $config->startUp['DB.PORT'];
		$this->connect();
	}

	public function connect() {
		if(isset($this->conn)) return $this->conn;
		$this->conn = @mysql_connect($this->server,$this->user,$this->password, true) or die ("Unable connect to mysql."); 
		@mysql_select_db($this->database, $this->conn) or die("Unable to select your default database name.");
	}
	public function disconnect(){
		if(isset($this->conn)) @mysql_close($this->conn);
	}
	
	public function query($sql){
		$result = @mysql_query($sql, $this->conn);
		if (!$result) die (mysql_error());
		return $result;
	}
	
	public function results($query, $type = 'object'){
		$result = $this->query($query);
		$data=array();
		if($type == null || $type == 'array'){
			while ($row = @mysql_fetch_array($result)) {
				$data[] = $row;
			}
		}elseif ($type == 'object') {
			while ($row[] = @mysql_fetch_object($result)) {
				$data = $row;
			}
		}elseif ($type == 'field') {
			while ($row[] = @mysql_fetch_field($result)) {
				$data = $row;
			}
		}else{
			echo "tipe result data tidak sesuai, <br/>anda bisa menggunakan 'array' atau 'object' ";
			return $data = "tipe result belum ditentukan";
		}


		if(mysql_num_rows($result) == 1){
			$nett_result = $data[0];
			return $nett_result;
		}else{
			return $data;
		}
	}

	public function numRows($query){
		$result = $this->query($query);
		$data=mysql_num_rows($result);
		return $data;
	}

	public function isLastId($sql){
		$this->query($sql);
		return $this->conn->insert_id;
	}

	public function isAffected($sql){
		$this->query($sql);
		return mysql_affected_rows();
	}
}

?>