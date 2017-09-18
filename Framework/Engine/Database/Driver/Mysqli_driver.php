<?php
namespace Framework\Engine\Database\Driver;
use Framework\Engine\Gear\DriverDBInterface as DDBI; 

class Mysqli_driver implements DDBI{
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

		$this->conn 	= new \mysqli($this->server,$this->user,$this->password,$this->database);
	}

	public function connect() {
		if(isset($this->conn)) return $this->conn;
		// Check connection
		if($this->conn->connect_error){
			die("Unable connect to MySqli ".$this->conn->connect_error);
		}
	}
	public function disconnect(){
		if(isset($this->conn)) @mysqli_close($this->conn);
	}
	public function query($sql){
		$result = $this->conn->query($sql);
		if (!$result) die ($this->conn->error);
		return $result;
	}
	public function results($query, $type = 'object'){
		$result = $this->query($query);
		$data=array();
		if($type == null || $type == 'array'){
			while ($row = $result->fetch_array()) {
				$data[] = $row;
			}
		}elseif ($type == 'object') {
			while ($row = $result->fetch_object()) {
				$data[] = $row;
			}
		}elseif ($type == 'field') {
			while ($row = $result->fetch_field()) {
				$data[] = $row;
			}
		}else{
			echo "tipe result data tidak sesuai, <br/>anda bisa menggunakan 'array' atau 'object' ";
			return $data = "tipe result belum ditentukan";
		}


		if(mysqli_num_rows($result) == 1){
			$nett_result = $data[0];
			return $nett_result;
		}else{
			return $data;
		}


	}

	public function numRows($query){
		$result = $this->query($query);
		$data=mysqli_num_rows($result);
		return $data;
	}

	public function isAffected($sql){
		$this->query($sql);
		return $this->conn->affected_rows;
	}

	public function isLastId($sql){
		$this->query($sql);
		return $this->conn->insert_id;
	}
}

?>