<?php
namespace Framework\Engine\Database\Qmanager;
use Framework\Engine\Database\Qmanager;
use Framework\Engine\Services\Sanitasi\Saniter;
use Framework\Engine\Services\DI\ServiceLocator as sl;

class Qm {
	public $saniter;

	function __construct(Saniter $saniter){
		$this->saniter = $saniter;		
	}

	
	function realData($datas){
		$length = count($datas);
		if($length == 0){
			echo "data is empty, nothing to do with this data";
		}else{
			$data = "";
			$index = 0;

			foreach($datas as $prop=>$val){
				if(($length-$index) == 1 ){
					$data .= $prop."=". $this->saniter->escape($val);
				}else{
					$data .= $prop."=".$this->saniter->escape($val).",";
				}
				$index++; 
			}
		}
		return $data;
	}


	function save($model){
		//new store data
		$sql = "INSERT INTO ".$model->table." (";
		$column="";
		
		foreach($model->columnStructurs as $prop => $val){
			if(is_object($val)){
				$column .= $val->Field.",";
			}else{
				$column .= $prop.",";
			}				
		}	

		$column = rtrim($column,",");
		$sql .= $column.") VALUES ";
		
		$form = "";					
		if(! is_null($model->dataSave)){

			$form .= "(";

			foreach($model->dataSave as $index => $val){
				if(is_array($val)){
					$form .="(";
					$str = "";
					foreach($val as $key => $value){
						$str.= $this->saniter->escape($value).",";
					}
					$form .= rtrim($str,",");
					$form .="),";
				}
				elseif (is_object($val)) {
					$val = (array) $val;
					$form .="(";
					$str = "";
					foreach($val as $key => $value){
						$str.= $this->saniter->escape($value).",";
					}
					$form .= rtrim($str,",");
					$form .="),";

				}
				else{			
					$form .= $this->saniter->escape($val).",";	
				}	
			}
		}else{
			$form .="(";
			$str = "";
			foreach($model->columnStructurs as $key => $value){
				$str.= $this->saniter->escape($value).",";
			}
			$form .= rtrim($str,",");
			$form .="),";
		}			
		$form = substr($form,0,(strlen($form)-4));
		$form .= ")";
		$sql .= str_ireplace(["((","))"],["(",")"],$form);

		$db = sl::get("dbfactory")->make();
		return $db->query($sql);
	}
	
	function delete($model){

		$id = array();
		foreach($model->dataSave as $datas => $val){
			$id[] = $val->{$model->primary_key};
		}
		$sql = "DELETE FROM ".$model->table;
		$length  = count($id);
		if($length > 0){		
			if(is_array($id)){
				
				if($length == 1 ){

					$sql .= " WHERE ".$model->primary_key." = ".$id[0];
					echo $sql;
				}
				else
				{
					$sql .= " WHERE ".$model->primary_key." IN (".implode(",",$id).")";
					echo $sql;
					
				}	
				$db = sl::get("dbfactory")->make();
				return $db->query($sql);	
			}
			else{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	function update($model){
		$sql = "UPDATE ".$model->table." SET ";
		$pkValue = $model->{$model->primary_key};
		$str="";
		if(! is_null($model->dataSave)){
			foreach($model->dataSave as $index => $val){

				if(is_array($val)){
					foreach($val as $key => $value){
						if($model->{$key} != $value){
							$str.= $key."=".$this->saniter->escape($value).",";
						}						
					}
				}else{	
					if($model->{$index} !== $val){
						$str.= $index."=".$this->saniter->escape($val).", ";
					}		
				}	
			}

		}else{
			unset($model->columnStructurs->{$model->primary_key});
			foreach($model->columnStructurs as $key => $value){
				$str.= $key."=".$this->saniter->escape($value).",";
			}
			$sql .= $str; 
		}	
		
		$str= substr($str,0,(strlen($str)-2));
		$sql .= $str." WHERE ".$model->primary_key."=".$pkValue;
		//echo $sql;
		$db = sl::get("dbfactory")->make();
		return $db->query($sql);
	}

	function join(){}
}