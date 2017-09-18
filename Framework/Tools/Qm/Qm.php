<?php
namespace Framework\Tools\Qm;
use Framework\Engine\Services\ServiceProvider\ServicesProvider as sp;

class Qm {
	public static $table;//"table data model";;
	public static $db;
	public static $findSQL;
	public static $getSQL;
	public static $ownSQL;

	function __construct(){
		//echo "tes";
		//self::$table = "user";//explode("\\", __CLASS__);
		self::$findSQL = "SELECT * FROM ";
		
	}

	public static function db(){		
		if(self::$db == null){
			$DBF = sp::getInstanceOf("dbfactory");
			self::$db = $DBF->make();			
		}
		return self::$db;		
	}

	public static function finds($aa){
		self::find($aa);
	}
	
	//menghasilkan baris pertama
	public static function find($all=null){
		//echo "cari sesuatu ".$all." pada table::".self::$table;
		if(strtolower($all) == "all"){
			$all = 1;
			$sql = "SELECT * FROM user ";
			$result = self::db()->results($sql);
			return $result;
		}else{
			$all = 1;
			$sql = "SELECT * FROM user limit 1 ";
			$result = self::db()->results($sql); 
			//var_dump($result);
			return $result;
		}
	}
	//menghasilkan baris berdasarkan id
	public static function findById($id){

	}

	//menghasilkan 
	public static function findByName($Name){}
	public static function findBy(array $findBy){}


	public static function createQuery($sql){
		self::$ownSQL = $sql;
		return $this;
	}

	public static function getResult(){		
		return self::$db->query(self::$sql);
	}

	function __destruct(){

	}
}