<?php
//kita menggunakan konsep factory method untuk menciptakan driver
namespace Framework\Engine\Gear;
use Framework\Engine\Gear\AbstractFactoryMethod as FMDB;
use Application\config\ConfigApp as c;

class DBFactory extends FMDB {

	public $path = "Framework/Engine/Database/Driver/";
	public $config;
	public static $instance = null;

	function __construct(c $config){
		$this->config = $config;
		$this->path = $this->path;
	}

	function make($params=null){
		$sufix = "_driver";
		if($params == null){
			$params = $this->config->startUp["DB.PROVIDER"];
		}
		$db = null;
		if(file_exists($this->path.$this->config->startUp["DB.PROVIDER"].$sufix.".php")){
			$class = "\\".str_replace("/","\\", $this->path).$params.$sufix;
			if(self::$instance == null && self::$instance != $class){
				if(self::$instance instanceof $class){
					return self::$instance;
				}
				else
				{
					$obj = new $class($this->config);
					self::$instance = $obj;
					return $obj;
				}				
			}	else{
				return self::$instance;
			}	
			
		}
		else
		{
			echo "<h2>file driver <i>".$this->config->startUp["DB.PROVIDER"].
			"</i> not found <br></h2>";
		}
		
	}
}