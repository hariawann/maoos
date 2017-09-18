<?php
//kita menggunakan konsep factory method untuk menciptakan driver
namespace Framework\Engine\Gear;
use Framework\Engine\Gear\AbstractFactoryMethod as FMDB;
use Framework\Boots\Booting;

class ModelFactory extends FMDB {

	public $path = "Application/Models/";
	public $config;
	static $instance = [];

	function __construct(){
		$this->config = Booting::getConfigENVI();
		$this->path = $this->path;
	}

	function make($params=null){
		if($params == null){
			//prefer dengan whoops library
			echo "<h3> you must insert name model</h3> cek your controller on function controller::make( 'name_model' );";
		}
		else
		{
			if(file_exists($this->path.ucfirst($params)."s".".php")){
				$class = "\\".str_replace("/","\\", $this->path).ucfirst($params)."s";


				if(array_key_exists($params,self::$instance) == false){
					//echo "membuat objek $params ... <br>";
					$obj = new $class();
					self::$instance[$params] = $obj;
					return $obj;

				}	else{
					//echo "ga usah membuat objek $params ... <br>";
					return self::$instance[$params];
				}	
				
			}else{
				echo "Model <b>$params</b> ini  belum ada, mungkin belum dibuat sebelumnya  ... <br>cobalah buat satu model ini ";
			}
		}		
	}
}