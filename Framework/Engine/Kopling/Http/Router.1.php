<?php 
namespace Framework\Engine\Kopling\Http;
use Framework\Boots\Booting;
use Framework\Engine\Kopling\Http\Request as req;
use Application\Config\ConfigApp as c;
use Framework\Engine\Services\DI\ServiceLocator as sl;

/**
*
* class Routes bertugas memetakan jalur yang tepat untuk ditampilan sesuai permintaan pengguna
*
* author : Hariawan
*
*/
class Router1 {
	public static $rutes = []; //["verb","uri","action"]
	public static $verbs = ['GET','POST','PUT','DELETE'];
	public $conf;
	public static $confStatic;

	function __construct(c $conf, Request $req){
		$this->conf = $conf;
		self::$confStatic = $conf;
	}

	static function setUri(array $uris){
		//array_push(self::$rutes, $uris);
		self::$rutes = $uris;
	}

	static function getUri(){
		return self::$rutes;
	}

	function grab(){	
		

		$domains = explode(",",$this->conf->startUp["APPS.DOMAIN"]);
		if(in_array($_SERVER["HTTP_HOST"],$domains)){
			$domain = $_SERVER["HTTP_HOST"];
		}else{
			$domain = "localhost";
		}	

		$bruto_url = $_SERVER["REQUEST_SCHEME"]."://".
			$domain.$_SERVER["REQUEST_URI"];

		$net_url =  isset($bruto_url) ? $bruto_url : false;

		if($net_url)
		{
			//format HMVC [modules][controller ++model++views]
			//format MVC controler ++model++views
			$isModules = self::$confStatic->startUp["APPS.MODULAR"];
			$net_url 	= parse_url($net_url);
			$array_path = explode('/',ltrim($net_url['path'],'/'));

			if($isModules == "true"){ 
				if($domain == "localhost" || $domain == "127.0.0.1"){
					$modul 		= ucfirst($array_path[1]);
					$class 		= isset($array_path[2]) ? $array_path[2] : $this->conf->startUp["APPS.BASEPAGE"];
					$func 		= isset($array_path[3]) ? $array_path[3] : $this->conf->startUp["APPS.FUNC"];
					$params		= isset($array_path[3]) ? array_slice($array_path,4) : null;
				}else{
					$modul 		= ucfirst($array_path[0]);
					$class 		= isset($array_path[1]) ? $array_path[1] : $this->conf->startUp["APPS.BASEPAGE"];
					$func 		= isset($array_path[2]) ? $array_path[2] : $this->conf->startUp["APPS.FUNC"];
					$params		= isset($array_path[2]) ? array_slice($array_path,3) : null;
				}
				
			}else{
				
				if($domain == "localhost" || $domain == "127.0.0.1"){
					$class 		= $array_path[1];
					$func 		= isset($array_path[2]) ? $array_path[2] : $this->conf->startUp["APPS.FUNC"];
					$params		= isset($array_path[2]) ? array_slice($array_path,3) : null;
				}else{
					$class 		= $array_path[0];
					$func 		= isset($array_path[1]) ? $array_path[1] : $this->conf->startUp["APPS.FUNC"];
					$params		= isset($array_path[1]) ? array_slice($array_path,2) : null;
				}


			}		

			if(isset($net_url['query'])){
				parse_str($net_url['query'],$array_query_string);
				req::setQueryGET($array_query_string);	
				//var_dump($array_query_string);
			}else{
				$array_query_string = [];
				//var_dump($array_query_string);
			}
			$data = [
				"class"=>$class,
				"func"=>$func,
				"params"=>$params,
				"modul"=>isset($modul)?$modul:null
				];
			self::getPage( $data);
		}
		else
		{
			echo "whoopppsss something went wrong";
			//error whoopsss plugin
		}
	}

	static function getPage(array $dataPage){

		extract($dataPage);
		if(is_callable($func)){
			
			call_user_func_array($func,$params);
		}
		else
		{

			//format HMVC [modules][controller ++model++views]
			//format MVC controler ++model++views
			$isModules = self::$confStatic->startUp["APPS.MODULAR"];

			if($isModules == "true"){ 
				$pathName = "Application/Modules/".$modul."/Controllers/";
			}else{
				$pathName = "Application/Controllers/";
			}


			$sufix 		= "Controller";
			if($class==""){$class = self::$confStatic->startUp["APPS.BASEPAGE"];}
			$controller =  ucfirst($class).$sufix;
			$filename = $pathName. $controller .".php";

			if(file_exists($filename)){
				require_once $filename;
				$class = "Application\Controllers\\".$controller;
				//echo "tes";
				if (class_exists($controller) == true) {
					$obj_controller = new $controller;
				}
				else
				{
					$obj_controller = new $class;
				}
				

				if(!method_exists($obj_controller,$func)){
				//var_dump($func);
					//echo " function $func tidak ada<br>";
					if(!empty($func)){
						$params = array($func);
						$action = "show";
					}else{
						$action = self::$confStatic->startUp["APPS.FUNC"];
					}
				}else { $action = $func; }
				//var_dump($func);

				$reflector = sl::get()->bluePrint($obj_controller);

				$par = $reflector->getMethod($action)->getParameters();
				$dependencies = array();

				foreach ($par as $key => $value) {
					$nameClass = $value->getClass();
					if(!is_null($nameClass)){	
						$dependencies[]= sl::get($nameClass->name);
					}else{
						$dependencies = array_merge($dependencies,$params);
					}
				}

				//var_dump($dependencies);

				call_user_func_array([$obj_controller,$action],$dependencies);
			}
			else
			{
				echo "WHHOOOPSSS .. page ini belum ada";
			}

			
		}		
	}

}