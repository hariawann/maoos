<?php 
namespace Framework\Engine\Database\Qmanager;
use Framework\Engine\Services\DI\ServiceLocator as sl;
use Application\config\ConfigApp as c;

class Find {

	private $select="SELECT ";
	private $from="FROM ";
	private $where="";
	private $config;
	private $db;
	

	function __construct(){
		$dbfactory = sl::get("dbfactory");
		$this->db = $dbfactory->make();
	}

    function find($id=null,$objectModel,$columns=null){
		//jika id tidak terdefinisi
		
		$this->from .= $objectModel->table;
		if($columns!=null && is_array($colums)){
			$this->select = "SELECT ".implode(",",$columns);
		}else{
			foreach($objectModel->columnStructurs as $key => $val){
				$this->select .= $objectModel->table.".".$val->Field." as '".$val->Field."', ";
			}
		}

		if(count($objectModel->foreign_key) > 0){
			foreach($objectModel->foreign_key as $key => $val){
				$this->from .= " join ".$key." on ".$objectModel->table.".".$key." = ".$key.".".$val;
				$columnFK = $objectModel->exec("describe ".$key);

				foreach($columnFK as $fkKey => $fkVal){
					$this->select .= "".$key.".".$fkVal->Field." as '".$key."_".$fkVal->Field."', ";
				}
			}

		}
		if($id != null){			
			if(is_array($id)){
				$this->where = " WHERE ".$objectModel->table.".".$objectModel->primary_key." IN (".implode(",",$id).")";
				echo $this->where;
			}else{
				$this->where = " WHERE ".$objectModel->table.".".$objectModel->primary_key." = ".$id;
			}			
		}

		
		$sql = substr($this->select,0,(strlen($this->select)-2))." ".$this->from.$this->where;
		$objectModel->recordset = $objectModel->exec($sql);
		
		if($objectModel->count() == 1){
			$aa = new \ReflectionObject($objectModel);

			foreach($objectModel->columnStructurs as $key => $val){
				
				if($aa->hasProperty($val->Field)){
					$objectModel->{$val->Field}  = $objectModel->recordset->{$objectModel->table.$val->Field};
				}
			}

			return (object)["status"=>true,"data"=>$objectModel];
		}
		elseif($objectModel->count() == 0){
			return (object)["status"=>false,"data"=>false];
		}
		else{
			return (object)["status"=>true,"data"=>$objectModel->recordset];
		}



        /*
		$obj = self::prepare();
		$sql = "SELECT * FROM ".$obj->table;
		if(is_array($id)){
			$arr = array();
			foreach($id as $key => $val){
				$arr[$key] = $obj->escape($val);
			}
			$id = $arr;
			$obj->recordset = array();
			$sql .= " WHERE ".$obj->primary_key." IN (".implode(",",$id).")";
			$obj->recordset = $obj->db->results($sql);
			//echo $sql;
		}		
		else if(!is_null($id)){
			$id = $obj->escape($id);
			if($id != $obj->primary_key){
				$sql .= " WHERE ".$obj->primary_key." = ".$id;
				$obj->recordset = $obj->db->results($sql);
				//echo $sql;
			}			
		}else{	
			$obj->recordset = $obj->db->results($sql);
			//echo $sql;
		}	
		$obj->isLoad=true;
        return $obj;
        
        */
	}
}