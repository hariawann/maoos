<?php
namespace Framework\Engine\Gear;
use Framework\Engine\Gear\ModelInterface;
use Framework\Engine\Services\DI\ServiceLocator as sl;
use Application\config\ConfigApp as c;
use Framework\Engine\Database\Qmanager\Qm; 

abstract class Model implements ModelInterface{
	public $columnStructurs;
	static $containers;
	public $conf;
	protected static $sql;
	private $db;
	public static $dbStatic;
	static protected $objModel;
	public $isLoad=false;
	public $table;
	public $recordset;
	public $primary_key;
	public $foreign_key;
	public $myQuery;
	public $dataSave;
	public $queryManager;

	private $typeData = ["integer","int","boolean","bool","float","double","string","array","object","null"];

	function __construct(c $config, Qm $qm, $prop=null){
		$this->conf = $config;
		$this->queryManager = $qm;

		if($prop!=null){
			$this->initProp($prop);
		}		

		$this->setDriver();
		$this->setTable();
		$this->setKeys($this->table);
	}

	function __get($name){
		if(key_exists($name, $this->columnStructurs)){
		return $this->columnStructurs[$name];				
		}else{
			if(key_exists($name, $this->recordset)){
			return $this->recordset->{$name};				
			}else{
				echo "undifined column {$name} in table or columnStructurs ";
			}
		}
				
	}
	
	function __set($key,$val){
		if(key_exists($key, $this->columnStructurs)){
			$this->columnStructurs[$key] = $val;		
		}else{
			if(key_exists($key, $this->recordset)){
			$this->recordset->{$key} = $val;				
			}else{
				echo "undifined column {$key} in table or columnStructurs ";
			}
		}	
	}

	public function setTable(){
		 $objModel = new \ReflectionClass(get_called_class());
		 $this->table = strtolower(substr($objModel->getShortName(),0,-1));
		 //echo $this->table;

		 return $this->table;
	}

	public static function reflection(){
		 return self::$objModel = new \ReflectionClass(get_called_class());
	}

	public static function getInstanModel($name=null){
		$var = isset($name)? $name : get_called_class();
		return sl::get($var);
	}

	/**
	* 
	* @param $table for table name
	*/
	function setKeys($table=null){
		$sql = "SHOW KEYS FROM ".$table;
		$res = $this->db->results($sql);
		$this->foreign_key = [];
		foreach($res as $obj){
			if($obj->Key_name == "PRIMARY"){
				$this->primary_key = $obj->Column_name;
			}else{
				//mencari primary key dari table asal foreign_key
				$sql = "SHOW KEYS FROM ".$obj->Column_name." WHERE Key_name = 'PRIMARY' ";
				$res = $this->db->results($sql);
				$this->foreign_key[$obj->Column_name] = $res->Column_name;

			}
		}
	}

	static function prepare($data=null){		
		$model = self::getInstanModel();
		if($data != null) { $model->dataSave = $data; } 
		$model->isLoad = false;
		$model->setKeys($model->table);	
		$model->columnStructurs = $model->exec("describe ".$model->table);

		return $model;
	}

	/*	
		find($id); or find([$id,$id2,$id3,$id4,$id5]);
	*/

	static function find($id=null){
		$obj = self::prepare();
		$finder = sl::get("FindManager");
		$result = $finder->find($id,$obj);
		if($result->status){
			$obj->isLoad = true;
			return $result->data;
		}else{
			return $result->data;
		}
	}
	
	/*
		example :
	 
			$a = user::where([
				['name','<',$c],
				['name','>=',100],
				['name' => "alu"],
			]);
	 */
	function resolveINsql($data,$obj){
		$nilai = "";
		$L = count($data);
		foreach ($data as $key => $val) {
			$L -= 1;
			if($L == 0){

				$nilai .= $obj->escape($val);
			}else{
				$nilai .= $obj->escape($val).",";
			}
		}
		$nilai = "IN (".$nilai.")";
		return $nilai;
	}

	static function where($data){	
		$obj = self::prepare();
		$sql = "SELECT * FROM ".$obj->table." WHERE ";

		$length = count($data);
		$where = "";
		/*
		$loop = function($params, $Array,$obj) use (&$loop){
						$nilai = "";
						$kolom = "";
						$L = count($Array);
						foreach ($Array as $kunci => $isi) {
							if(is_string($kunci)){$kolom = $kunci; }
							$L -=1;
							if(is_array($isi)){
								$nilai = $loop($params,$isi,$obj);	
								return $kolom." = ". $nilai;						
							}
							else{
								if($L == 0){
									$nilai .= $obj->escape($isi);
								}else{
									$nilai .= $obj->escape($isi).",";
								}
								return $kolom." = ". $nilai;
							}									
						}
					};

		$funcWhere = function ($obj,$loop) use (&$params,&$value){
									
				if(is_integer($params)){
					$kolom = "";
					$nilai = "";
					$nilai = $loop($params,$value,$obj);

					return $nilai." ";


				}else{
					$kolom = $params;										
					$nilai = "";
					$nilai = $loop($params,$value,$obj);

					return $kolom." IN (".$nilai.")";
				}								
				
			};
		*/
		
		if(is_string($data)){			
			$where = $data;
		}
		elseif(is_array($data)){		
			
			if(!array_key_exists("data",$data)){
				foreach ($data as $key => $value) {
					$length -= 1;
					if(is_array($value)){
						$newdata = $obj->resolveINsql($value,$obj);
							$where .= $key." ".$newdata;
							if($length > 0){
								$where.=" AND ";
							}						
					}else{
						$where .=$key." = ".$obj->escape($value)." AND ";
					}	
				}
			}else{
				$sql = "SELECT ";
				if(is_array($data["data"])){
					foreach ($data["data"] as $key => $value) {
						$where .=$key." as '".$value."', ";
					}
				}else{
					$where .= $data["data"];
				}
			}

			
			$where = rtrim($where," AND ");
			$where = rtrim($where,",");
			$sql .= $where." FROM ".$obj->table;
			$where = "";
			if(array_key_exists("where",$data)){
				$sql .= " WHERE ";
				$index = 0;
				if(is_array($data["where"])){
					foreach ($data["where"] as $key => $value) {
						if(is_array($value)){
							$newdata = $obj->resolveINsql($value,$obj);

								if(array_key_exists("operator",$data)){
									if(count($data["operator"])>1 && $index < count($data["operator"])){					
										$where .= $key." ".$newdata;
										$where.=" ".$data["operator"][$index]." ";
									}else{									
										$where .= $key." ".$newdata;
										$where.=" ".$data["operator"][0]." ";
									}
								}else{									
									$where .= $key." ".$newdata;
									$where .=" AND ";
								}							
							
						}else{
							if(array_key_exists("operator",$data)){
								if(count($data["operator"])>1 && $index < count($data["operator"])){
									$where .=$key." = ".$obj->escape($value);
									$where.=" ".$data["operator"][$index]." ";
								}else{
									$where .=$key." = ".$obj->escape($value);
									$where.=" ".$data["operator"][0]." ";
								}
							}else{
								$where .=$key." = ".$obj->escape($value)." AND ";
							}
						}	
					}
					$index++;

					if(array_key_exists("operator",$data)){
						foreach ($data["operator"] as $key => $value) {
							$where = rtrim($where," ".$value." ");
						}
					}


				}else{
					$where = $data["where"];
				}
			}
		}

		$where = rtrim($where," AND ");
		$where = rtrim($where,",");
		$sql .= $where;

		$obj->myQuery = $sql;
		echo $obj->myQuery;
		$obj->recordset = $obj->db->results($sql);
		return $obj;
	}

	static function like($data){	
		$obj = self::prepare();
		$sql = "SELECT * FROM ".$obj->table." WHERE ";

		$length = count($data);
		$where = "";
		
		
		if(is_string($data)){			
			$where = $data;
		}
		elseif(is_array($data)){		
			
			if(!array_key_exists("data",$data)){
				foreach ($data as $key => $value) {
					$length -= 1;
					if(is_array($value)){
						$newdata = $obj->resolveINsql($value,$obj);
							$where .= $key." ".$newdata;
							if($length > 0){
								$where.=" AND ";
							}						
					}else{
						$where .=$key." = ".$obj->escape($value)." AND ";
					}	
				}
			}else{
				$sql = "SELECT ";
				if(is_array($data["data"])){
					foreach ($data["data"] as $key => $value) {
						$where .=$key." as '".$value."', ";
					}
				}else{
					$where .= $data["data"];
				}
			}

			
			$where = rtrim($where," AND ");
			$where = rtrim($where,",");
			$sql .= $where." FROM ".$obj->table;
			$where = "";
			if(array_key_exists("where",$data)){
				$sql .= " WHERE ";
				$index = 0;
				if(is_array($data["where"])){
					foreach ($data["where"] as $key => $value) {
						if(is_array($value)){
							$newdata = $obj->resolveINsql($value,$obj);

								if(array_key_exists("operator",$data)){
									if(count($data["operator"])>1 && $index < count($data["operator"])){					
										$where .= $key." ".$newdata;
										$where.=" ".$data["operator"][$index]." ";
									}else{									
										$where .= $key." ".$newdata;
										$where.=" ".$data["operator"][0]." ";
									}
								}else{									
									$where .= $key." ".$newdata;
									$where .=" AND ";
								}							
							
						}else{
							if(array_key_exists("operator",$data)){
								if(count($data["operator"])>1 && $index < count($data["operator"]) ){
									$where .=$key." LIKE ".$obj->escape($value);
									$where.=" ".$data["operator"][$index]." ";
								}else{
									$where .=$key." LIKE ".$obj->escape($value);
									$where.=" ".$data["operator"][0]." ";
								}
							}else{
								$where .=$key." LIKE ".$obj->escape($value)." AND ";
							}
						}
						$index++;	
					}
					

					if(array_key_exists("operator",$data)){
						foreach ($data["operator"] as $key => $value) {
							$where = rtrim($where," ".$value." ");
						}
					}


				}else{
					$where = $data["where"];
				}
			}
		}

		$where = rtrim($where," AND ");
		$where = rtrim($where,",");
		$sql .= $where;

		$obj->myQuery = $sql;
		echo $obj->myQuery;
		$obj->recordset = $obj->db->results($sql);
		return $obj;
	}

	
	static function max($data){	
		$obj = self::prepare();
		$sql = "";

		$length = count($data);
		$where = "";

		if(is_string($data)){			
			$where = $data;
		}
		elseif(is_array($data)){		
			$sql = "SELECT MIN(";
			if(!array_key_exists("column",$data)){
				foreach ($data as $key => $value) {
					$length -= 1;
					if(is_array($value)){
						$newdata = $obj->resolveINsql($value,$obj);
							$where .= $key." ".$newdata;
							if($length > 0){
								$where.=" AND ";
							}
						
					}else{
						$where .=$key." = ".$obj->escape($value)." AND ";
					}	
				}
			}else{
				
				if(is_array($data["column"])){
					foreach ($data["column"] as $key => $value) {
						$where .=$key.") as '".$value."', ";
					}
				}else{
					$where .= $data["column"];
				}
			}
			
			$where = rtrim($where," AND ");
			$where = rtrim($where,",");
			$sql .= $where." FROM ".$obj->table;
			$where = "";
			if(array_key_exists("where",$data)){
				$sql .=" WHERE ";
				$index = 0;
				if(is_array($data["where"])){
					foreach ($data["where"] as $key => $value) {
						if(is_array($value)){
							$newdata = $obj->resolveINsql($value,$obj);
								if(array_key_exists("operator",$data)){
									if(count($data["operator"])>1 && $index < count($data["operator"])){					
										$where .= $key." ".$newdata;
										$where.=" ".$data["operator"][$index]." ";
									}else{									
										$where .= $key." ".$newdata;
										$where.=" ".$data["operator"][0]." ";
									}
								}else{									
									$where .= $key." ".$newdata;
									$where .=" AND ";
								}	
							
						}else{
							if(array_key_exists("operator",$data)){
									if(count($data["operator"])>1 && $index < count($data["operator"])){					
										$where .=$key." = ".$obj->escape($value);
										$where.=" ".$data["operator"][$index]." ";
									}else{									
										$where .=$key." = ".$obj->escape($value);
										$where.=" ".$data["operator"][0]." ";
									}
								}else{									
									$where .=$key." = ".$obj->escape($value);
									$where .=" AND ";
								}	

							
						}
						$index++;	
					}

					if(array_key_exists("operator",$data)){
						foreach ($data["operator"] as $key => $value) {
							$where = rtrim($where," ".$value." ");
						}
					}

				}else{
					$where = $data["where"];
				}
			}
		}

		$where = rtrim($where," AND ");
		$where = rtrim($where,",");
		$sql .= $where;

		$obj->myQuery = $sql;
		echo $obj->myQuery;
		$obj->recordset = $obj->db->results($sql);
		return $obj;
	}

	static function min($data){	
		$obj = self::prepare();
		$sql = "";

		$length = count($data);
		$where = "";

		if(is_string($data)){			
			$where = $data;
		}
		elseif(is_array($data)){		
			$sql = "SELECT min(";
			if(!array_key_exists("column",$data)){
				foreach ($data as $key => $value) {
					$length -= 1;
					if(is_array($value)){
						$newdata = $obj->resolveINsql($value,$obj);
							$where .= $key." ".$newdata;
							if($length > 0){
								$where.=" AND ";
							}
						
					}else{
						$where .=$key." = ".$obj->escape($value)." AND ";
					}	
				}
			}else{
				
				if(is_array($data["column"])){
					foreach ($data["column"] as $key => $value) {
						$where .=$key.") as '".$value."', ";
					}
				}else{
					$where .= $data["column"];
				}
			}
			
			$where = rtrim($where," AND ");
			$where = rtrim($where,",");
			$sql .= $where." FROM ".$obj->table;
			$where = "";
			if(array_key_exists("where",$data)){
				$sql .=" WHERE ";
				$index = 0;
				if(is_array($data["where"])){
					foreach ($data["where"] as $key => $value) {
						if(is_array($value)){
							$newdata = $obj->resolveINsql($value,$obj);
								if(array_key_exists("operator",$data)){
									if(count($data["operator"])>1 && $index < count($data["operator"])){					
										$where .= $key." ".$newdata;
										$where.=" ".$data["operator"][$index]." ";
									}else{									
										$where .= $key." ".$newdata;
										$where.=" ".$data["operator"][0]." ";
									}
								}else{									
									$where .= $key." ".$newdata;
									$where .=" AND ";
								}	
							
						}else{
							if(array_key_exists("operator",$data)){
									if(count($data["operator"])>1 && $index < count($data["operator"])){					
										$where .=$key." = ".$obj->escape($value);
										$where.=" ".$data["operator"][$index]." ";
									}else{									
										$where .=$key." = ".$obj->escape($value);
										$where.=" ".$data["operator"][0]." ";
									}
								}else{									
									$where .=$key." = ".$obj->escape($value);
									$where .=" AND ";
								}	

							
						}
						$index++;	
					}

					if(array_key_exists("operator",$data)){
						foreach ($data["operator"] as $key => $value) {
							$where = rtrim($where," ".$value." ");
						}
					}

				}else{
					$where = $data["where"];
				}
			}
		}

		$where = rtrim($where," AND ");
		$where = rtrim($where,",");
		$sql .= $where;

		$obj->myQuery = $sql;
		echo $obj->myQuery;
		$obj->recordset = $obj->db->results($sql);
		return $obj;
	}

	static function avg($data){	
		$obj = self::prepare();
		$sql = "";

		$length = count($data);
		$where = "";

		if(is_string($data)){			
			$where = $data;
		}
		elseif(is_array($data)){		
			$sql = "SELECT AVG(";
			if(!array_key_exists("column",$data)){
				foreach ($data as $key => $value) {
					$length -= 1;
					if(is_array($value)){
						$newdata = $obj->resolveINsql($value,$obj);
							$where .= $key." ".$newdata;
							if($length > 0){
								$where.=" AND ";
							}
						
					}else{
						$where .=$key." = ".$obj->escape($value)." AND ";
					}	
				}
			}else{
				
				if(is_array($data["column"])){
					foreach ($data["column"] as $key => $value) {
						$where .=$key.") as '".$value."', ";
					}
				}else{
					$where .= $data["column"];
				}
			}
			
			$where = rtrim($where," AND ");
			$where = rtrim($where,",");
			$sql .= $where." FROM ".$obj->table;
			$where = "";
			if(array_key_exists("where",$data)){
				$sql .=" WHERE ";
				$index = 0;
				if(is_array($data["where"])){
					foreach ($data["where"] as $key => $value) {
						if(is_array($value)){
							$newdata = $obj->resolveINsql($value,$obj);
								if(array_key_exists("operator",$data)){
									if(count($data["operator"])>1 && $index < count($data["operator"])){					
										$where .= $key." ".$newdata;
										$where.=" ".$data["operator"][$index]." ";
									}else{									
										$where .= $key." ".$newdata;
										$where.=" ".$data["operator"][0]." ";
									}
								}else{									
									$where .= $key." ".$newdata;
									$where .=" AND ";
								}	
							
						}else{
							if(array_key_exists("operator",$data)){
									if(count($data["operator"])>1 && $index < count($data["operator"])){					
										$where .=$key." = ".$obj->escape($value);
										$where.=" ".$data["operator"][$index]." ";
									}else{									
										$where .=$key." = ".$obj->escape($value);
										$where.=" ".$data["operator"][0]." ";
									}
								}else{									
									$where .=$key." = ".$obj->escape($value);
									$where .=" AND ";
								}	

							
						}
						$index++;	
					}

					if(array_key_exists("operator",$data)){
						foreach ($data["operator"] as $key => $value) {
							$where = rtrim($where," ".$value." ");
						}
					}

				}else{
					$where = $data["where"];
				}
			}
		}

		$where = rtrim($where," AND ");
		$where = rtrim($where,",");
		$sql .= $where;

		$obj->myQuery = $sql;
		echo $obj->myQuery;
		$obj->recordset = $obj->db->results($sql);
		return $obj;
	}

	static function sum($data){	
		$obj = self::prepare();
		$sql = "";

		$length = count($data);
		$where = "";

		if(is_string($data)){			
			$where = $data;
		}
		elseif(is_array($data)){		
			$sql = "SELECT SUM(";
			if(!array_key_exists("column",$data)){
				foreach ($data as $key => $value) {
					$length -= 1;
					if(is_array($value)){
						$newdata = $obj->resolveINsql($value,$obj);
							$where .= $key." ".$newdata;
							if($length > 0){
								$where.=" AND ";
							}
						
					}else{
						$where .=$key." = ".$obj->escape($value)." AND ";
					}	
				}
			}else{
				
				if(is_array($data["column"])){
					foreach ($data["column"] as $key => $value) {
						$where .=$key.") as '".$value."', ";
					}
				}else{
					$where .= $data["column"];
				}
			}
			
			$where = rtrim($where," AND ");
			$where = rtrim($where,",");
			$sql .= $where." FROM ".$obj->table;
			$where = "";
			if(array_key_exists("where",$data)){
				$sql .=" WHERE ";
				$index = 0;
				if(is_array($data["where"])){
					foreach ($data["where"] as $key => $value) {
						if(is_array($value)){
							$newdata = $obj->resolveINsql($value,$obj);
								if(array_key_exists("operator",$data)){
									if(count($data["operator"])>1 && $index < count($data["operator"])){					
										$where .= $key." ".$newdata;
										$where.=" ".$data["operator"][$index]." ";
									}else{									
										$where .= $key." ".$newdata;
										$where.=" ".$data["operator"][0]." ";
									}
								}else{									
									$where .= $key." ".$newdata;
									$where .=" AND ";
								}	
							
						}else{
							if(array_key_exists("operator",$data)){
									if(count($data["operator"])>1 && $index < count($data["operator"])){					
										$where .=$key." = ".$obj->escape($value);
										$where.=" ".$data["operator"][$index]." ";
									}else{									
										$where .=$key." = ".$obj->escape($value);
										$where.=" ".$data["operator"][0]." ";
									}
								}else{									
									$where .=$key." = ".$obj->escape($value);
									$where .=" AND ";
								}	

							
						}
						$index++;	
					}

					if(array_key_exists("operator",$data)){
						foreach ($data["operator"] as $key => $value) {
							$where = rtrim($where," ".$value." ");
						}
					}

				}else{
					$where = $data["where"];
				}
			}
		}

		$where = rtrim($where," AND ");
		$where = rtrim($where,",");
		$sql .= $where;

		$obj->myQuery = $sql;
		echo $obj->myQuery;
		$obj->recordset = $obj->db->results($sql);
		return $obj;
	}

	function _OR($data){
		$length = count($data);
		$where = "";
		/*
		if(is_string($data)){
			$where = $data;
		}else{
			foreach($data as $params => $value){			
				$length -= 1;
				if(count($value) > 2){
					if($length == 1){
						$where .= " OR ".$value[0]." ".$value[1]." ".$this->escape($value[2]);
					}else{
						$where .= " OR ".$value[0]." ".$value[1]." ".$this->escape($value[2]);
					}
				}
				else{
					foreach($value as $key => $val){
						if($length == 1){
							if(is_array($val)){
								$where .= " OR ".$key." IN (".implode(",",$this->escape($val)).")";	
							}else{
								$where .= " OR ".$key." = ".$this->escape($val);		
							}

						}else{
							$where .= " OR ".$key." = ".$this->escape($val);
						}
					}				
				}		
			}
		}
		*/
		
		if(is_string($data)){
			/*
				example :			 
				$a = user::where('id = 1000');
			 */
			$where = " OR ".$data;
		}else{
			/*
				example :
			 
			$a = user::where([
						['name','<',$c],
						['name','>=',100],
						['name' => "alu"],
					]);
			 */
			foreach($data as $params => $value){
				$length -= 1;
				if(is_array($value)){
					if(count($value) > 2){
						if($length == 1){
							$where .= " OR ".$value[0]." ".$value[1]." ".$this->escape($value[2]);
						}else{
							$where .= " OR ".$value[0]." ".$value[1]." ".$this->escape($value[2]);
						}
					}
					else{

						foreach($value as $key => $val){
							if($length == 1){
								if(is_array($val)){
									$where .= " OR ".$key." IN (".implode(",",$this->escape($val)).")";	
								}else{
									$where .= " OR ".$key." = ".$this->escape($val);		
								}

							}else{
								$where .= " OR ".$key." = ".$this->escape($val);
							}
						}				
					}
				}
				else{
					/*
						example :
					 
						$a = user::where([
									'id' => 1000, 'nama' =>[1000,1002]
									]);
					 */
					if($length == 1){
						if(is_array($value)){
							$where .= " OR ".$params." IN (".implode(",",$this->escape($value)).")";	
						}else{
							$where .= " OR ".$params." = ".$this->escape($value);		
						}
					
					}else{
						if(is_array($value)){
							$where .= " OR ".$params." IN (".implode(",",$this->escape($value)).")";	
						}else{
							$where .= " OR ".$params." = ".$this->escape($value);	
						}
						
					}
				}			
			}
		}


		$this->myQuery .= $where;
		echo $this->myQuery;
		return $this;
	}

	function _AND($data){
		$length = count($data);
		$where = "";
		/*
		if(is_string($data)){
			$where = $data;
		}else{
			foreach($data as $params => $value){			
				$length -= 1;
				if(count($value) > 2){
					if($length == 1){
						$where .= " OR ".$value[0]." ".$value[1]." ".$this->escape($value[2]);
					}else{
						$where .= " OR ".$value[0]." ".$value[1]." ".$this->escape($value[2]);
					}
				}
				else{
					foreach($value as $key => $val){
						if($length == 1){
							if(is_array($val)){
								$where .= " OR ".$key." IN (".implode(",",$this->escape($val)).")";	
							}else{
								$where .= " OR ".$key." = ".$this->escape($val);		
							}

						}else{
							$where .= " OR ".$key." = ".$this->escape($val);
						}
					}				
				}		
			}
		}
		*/
		
		if(is_string($data)){
			/*
				example :			 
				$a = user::where('id = 1000');
			 */
			$where = " AND ".$data;
		}else{
			/*
				example :
			 
			$a = user::where([
						['name','<',$c],
						['name','>=',100],
						['name' => "alu"],
					]);
			 */
			foreach($data as $params => $value){

				$length -= 1;
				if(is_array($value)){
					if(count($value) > 2){
						if($length == 1){
							$where .= " AND ".$value[0]." ".$value[1]." ".$this->escape($value[2]);
						}else{
							$where .= " AND ".$value[0]." ".$value[1]." ".$this->escape($value[2]);
						}
					}
					else{

							if($length == 1){
								if(is_array($value)){
									echo "satu";
									var_dump($value);
									$where .= " AND ".$params." IN (".implode(",",$this->escape($value)).")";	
								}else{
									$where .= " AND ".$params." = ".$this->escape($value);		
								}

							}else{
								if(is_array($value)){
									var_dump($value);
									$where .= " AND ".$params." IN (".implode(",",$this->escape($value)).")";	
								}else{
									$where .= " AND ".$params." = ".$this->escape($value);		
								}

							}			
					}
				}
				else{
					/*
						example :
					 
						$a = user::where([
									'id' => 1000, 'nama' =>[1000,1002]
									]);
					 */

					if($length == 1){
						if(is_array($value)){
							$where .= " AND ".$params." IN (".implode(",",$this->escape($value)).")";	
						}else{

							$where .= " AND ".$params." = ".$this->escape($value);		
						}
					
					}else{
						if(is_array($value)){
							$where .= " AND ".$params." IN (".implode(",",$this->escape($value)).")";	
						}else{

							$where .= " AND ".$params." = ".$this->escape($value);	
						}
						
					}
				}			
			}
		}


		$this->myQuery .= $where;
		echo $this->myQuery;
		return $this;
	}
	/*
	function _AND($data){
		$length = count($data);
		$where = "";
		foreach($data as $params => $value){
			
				$length -= 1;

			if(count($value) > 2){
				if($length == 1){
					$where .= " AND ".$value[0]." ".$value[1]." ".$this->escape($value[2]);
				}else{
					$where .= " AND ".$value[0]." ".$value[1]." ".$this->escape($value[2]);
				}
			}
			else{
				foreach($value as $key => $val){
					if($length == 1){
						if(is_array($val)){
							$where .= " AND ".$key." IN (".implode(",",$this->escape($val)).")";	
						}else{
							$where .= " AND ".$key." = ".$this->escape($val);		
						}

					}else{
						$where .= " AND ".$key." = ".$this->escape($val);
					}
				}				
			}			
		}		

		$this->myQuery .= $where;
		return $this;
	} */

	public static function insert($dataSave=null){	

		$model = self::getInstanModel();
		if(!is_null($dataSave)){

			$model->dataSave = $dataSave;
		}
		$model->isLoad = false;
		$model->exec("describe ".$model->table);
		if($model->save()){
			return true;
		}else{
			return false;
		}
		
	}

	public static function create(){
		$model = self::getInstanModel();
		$model->isLoad = false;
		$model->exec("describe ".$model->table);

		foreach($model->recordset as $prop ){
			$model->columnStructurs[$prop->Field] = "";
		}

	}

	function exec($sql){
		$db= sl::get("dbfactory");
		$db = $db->make();
		return $db->results($sql);
	}

	function escape($var=null){
		if(is_null($var)){
			return null;
		}else{
			$esc = function($var){
				if(is_string($var)){
					$var =  "'".str_replace(["\"","'","''"],"",$var)."'";				
				}
				else if(is_integer($var)){
					$var =  str_replace(["'","\""], "",$var);
					$var = (int)$var;	
				}
				else if(is_float($var)){
					$var =  str_replace(["'","\""], "",$var);
					$var = (float)$var;
				}
				return $var;
			};

			if(is_array($var)){
				foreach($var as $v => $val){
					$var[$v]=$esc($val);
				}
			}else{
				 $var = $esc($var);
			}

			return $var;
		}
		
	}

	function table(){
		return trim(get_called_class(),"s");
	}
	
	function initProp(array $prop){
		$prop = array_change_key_case($prop,CASE_LOWER);
		$this->columnStructurs = $prop;
	}

	/**
	 * @Void => set value propertis of model
	 * @param $prop => key of array propertis, $value => value of propertis by key
	 */

	function setProp($prop,$value){
		if(!array_key_exists($prop, $this->columnStructurs)){
			$this->columnStructurs[$prop] = $value;	
		}else{
			$this->columnStructurs[$prop] = $value;	
		}
	}

	/**
	 * @return get list data arrayor one data propertis of model
	 * @param $prop nullable or by key of name propertis
	 */
	function getProp($prop=null){
		if($prop==null){
			return $this->columnStructurs;
		}else{
			if(array_key_exists($prop, $this->columnStructurs)){
				return $this->columnStructurs[$prop];	
			}else{
				echo "undifined this ".$prop;
			}
		}
		
	}

	

	/**
	 * @void cahnges data type propertis to match the data type in the table
	 * @param array($data => $typeData)
	 */
	function setTypeProp(array $data){
		foreach ($data as $key => $value) {
			if(in_array($value, $this->typeData)){
				setType($this->columnStructurs[$key],strtolower($value));
			}
		}
	}

	/**
	 * @void setting 
	 * @param array($data => $typeData)
	 */
	public function setDriver(){
		if(is_null($this->db)){
			$DBF = sl::get("dbfactory");
			$this->db = $DBF->make();
			self::$dbStatic = $this->db;			
		}
		return $this->db;	
	}


//GEETING DATA //
/**
 *
 *
 * 
 */
	
	function get($sql=null){
		if(! $sql==null){
			$this->exec($sql);
		}		
		$length = count($this->recordset);
		if($length == 1){
			return [$this->recordset];
		}else{
			return $this->recordset;
		}
	}

	function count(){
		return count($this->recordset);
	}

	function cursor($position=null){
		if(count($this->recordset) == 1){
			return $this->recordset;
		}else{
			return $this->recordset[$position];
		}
	}

	function first($offset=null){
		$length = count($this->recordset);
		if($length == 0){
			return $length;
		}else{
			if(is_null($offset)){
				return $this->recordset;
			}else{
				return array_slice($this->recordset,0,$offset,true);
			}
		}			
	}

	function last($offset=null){
		$length = count($this->recordset);
		if(is_null($offset)){
			if($length == 1){
				return $this->recordset;
			}else{
				return $this->recordset[$length-1];
			}
		}else{
			if($length == 1){
				return $this->recordset;
			}else{
				return array_slice($this->recordset,-$offset,$offset,true);
			}
		}
	}

	function chunck($offset=null,$option=false){
		$length = count($this->recordset);
		if(is_null($offset)){
			if($length == 1){
				return [$this->recordset];
			}else{
				return $this->recordset[0];
			}
		}else{
			if($length == 1){
				return [$this->recordset];
			}else{
				return array_chunk($this->recordset,$offset,$option);
			}
		}
	}

//DELETION//
/**
 * 
 * 
 */
	function delete(){
		return $this->queryManager->delete($this);
	}	

	function save(){		
		
		if($this->isLoad){
			//update data 			
			return $this->queryManager->update($this);
		}else{
			//simpan data
			return $this->queryManager->save($this);
		}
	}

	/**
	 * @Void => init environtment propertis of model
	 * @param $prop => key of array propertis, $value => value of propertis by key
	 */


	
}