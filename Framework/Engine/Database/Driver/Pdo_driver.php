<?php
namespace Framework\Engine\Database\Driver;
use Framework\Engine\Gear\DriverDBInterface as DDBI; 
use PDO;

class Pdo_driver implements DDBI{
	private $conn;
	private $server;
	private $user;
	private $password;
	private $database;	
	private $port;

	public function __construct($config){
		$this->server 	= $config->startUp['DB.HOST'];
		$this->user 	= $config->startUp['DB.USERNAME'];
		$this->password = $config->startUp['DB.PASSWORD'];
		$this->database = $config->startUp['DB.DBNAME'];
		$this->port 	= $config->startUp['DB.PORT'];
		$this->connect();

	}

	public function connect() {
		if(isset($this->conn)) return $this->conn;
		try{
		    $this->conn = new PDO (
			"mysql:host=".$this->server.";dbname=".$this->database, 
			$this->user, 
			$this->password
			);
		}catch(PDOException $e){
		    echo __LINE__.$e->getMessage();
		}
	}

	public function disconnect(){
		if(isset($this->conn)) {
			$this->conn = null;
		}
	}

	public function query($sql){
		$result = array();
		try{
		    $try_query = $this->conn->prepare($sql) or die($this->conn->errorInfo());
        	    $result = $try_query->execute();
		}catch(PDOException $e){
		    echo __LINE__.$e->getMessage();
		}
		return $result;
	}

	public function results($query, $type = 'object'){
		$result = $this->conn->prepare($query);
		$result->execute();
		$data = array();
		if($type == null || $type == 'array'){
			
			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
				$data[] = $row;
			}
		}elseif ($type == 'object') {
			while ($row = $result->fetch(PDO::FETCH_OBJ)) {
				$data[] = $row;
			}
		}elseif ($type == 'field') {
			while ($row = $result->fetch(PDO::FETCH_OBJ)) {
				$data[] = $row;
			}
		}else{
			echo "tipe result data tidak sesuai, <br/>anda bisa menggunakan 'array' atau 'object' ";
			return $data = "tipe result belum ditentukan";
		}
		if($result->rowCount() == 1){
			$nett_result = $data[0];
			return $nett_result;
		}else{
			return $data;
		}
	}

	public function numRows($sql){
		$res = $this->conn->prepare($sql);
		$res->execute();
		$data=$res->rowCount();
		return $data;
	}

	public function isAffected($sql){		
		return $this->conn->exec($sql);
	}


}

?>