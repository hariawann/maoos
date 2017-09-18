<?php
namespace Framework\Engine\Services\DI;
use Framework\Engine\Services\DI\Container as container;
use Framework\Engine\Services\DI\IoC as ioc;

class ServiceLocator
{
	public static $services;	

	static function get($key=null){
		if(array_key_exists($key,self::$services)){
			return container::getInstan(self::$services[$key]);

		}elseif($key==null){
			return container::getInstan($key);
		}else{
			return container::getInstan($key);
		}
	}

	static function bind($key,$value){
		if(is_null(self::$services)){
			self::$services == array();
		}

		if(array_key_exists($key,self::$services)){
			echo "we have not services <strong>".$key."</strong> not found";
			exit;
		}else{
			self::$services[$key] = $value;
		}
	}

	static function listService(){
		foreach (self::$services as $key => $value) {
			echo $key ." => ".$value." <br>";
		}
	} 

	static function init(){
		require "Framework/Engine/Services/DI/defaultServicesList.php";
			self::$services = $defaultServices;
		require "Application/Config/addServices.php";
	}




}

