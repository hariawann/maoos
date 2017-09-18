<?php
namespace Framework\Engine\Services\DI;
use Framework\Engine\Services\DI\IoC as ioc;

/**
* 
*/
class Container
{		
	private static $storeServices=array();

	static function has($key){
		if(array_key_exists($key,self::$services)){
			return true;
		}else{
			return false;
		}
	}

	static function getInstan($key){
		if(array_key_exists($key,self::$storeServices)){
			return self::$storeServices[$key];
		}else{
			$obj = ioc::get($key);
			self::$storeServices[$key] = $obj;
			return $obj;
		}
	}

	static function getValueService($key){
		if(array_key_exists($key,self::$services)){
			return self::$services[$key];
		}else{
			return $key;
		}		
		
	}	

	static function store($key,$services){
		self::$storeServices[$key] = $services;
	}

	static function listService(){
		foreach (self::$storeServices as $key => $value) {
			echo $key ."<br>";
		}
	} 

	static function set(){
		require "Framework/Engine/Services/DI/defaultServicesList.php";
			self::$services = $defaultServices;
		require "Application/Config/addServices.php";
	}

	static function bind($key,$value){
		if(array_key_exists($key,self::$services)){
			echo "Error: the name services <strong>".$key."</strong> already exsist";
		}else{
		self::$services[$key] = $value;
		}
	}
}