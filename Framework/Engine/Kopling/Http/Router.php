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
class Router {
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
		//cari dulu server metode 

		$domains = explode(",",$this->conf->startUp["APPS.DOMAIN"]);
		if(in_array("".$_SERVER["HTTP_HOST"],$domains)){
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
			$modeHMVC = self::$confStatic->startUp["APPS.MODULAR"];
			$net_url 	= parse_url($net_url);
			$array_path = explode('/',ltrim($net_url['path'],'/'));

			$pageComponent=[];
			if($modeHMVC == "true"){ 
				//jika dijalankan pada mode lokal atau online
				if($domain == "localhost" || $domain == "127.0.0.1"){
					$pageComponent['modul']		= ucfirst($array_path[1]);
					$pageComponent['class'] 	= isset($array_path[2]) ? $array_path[2] : $this->conf->startUp["APPS.BASEPAGE"];
					$pageComponent['func'] 		= isset($array_path[3]) ? $array_path[3] : $this->conf->startUp["APPS.FUNC"];
					$pageComponent['params'] 	= isset($array_path[3]) ? array_slice($array_path,4) : null;			

				}
				//jika tidak mode local
				else{
					$pageComponent['modul'] 		= ucfirst($array_path[0]);
					$pageComponent['class'] 		= isset($array_path[1]) ? $array_path[1] : $this->conf->startUp["APPS.BASEPAGE"];
					$pageComponent['func'] 			= isset($array_path[2]) ? $array_path[2] : $this->conf->startUp["APPS.FUNC"];
					$pageComponent['params']		= isset($array_path[2]) ? array_slice($array_path,3) : null;
				}
				
			}else{
				//jika mode local
				if($domain == "localhost" || $domain == "127.0.0.1"){
					$pageComponent['modul'] 		= "";
					$pageComponent['class'] 		= $array_path[1];
					$pageComponent['func'] 			= isset($array_path[2]) ? $array_path[2] : $this->conf->startUp["APPS.FUNC"];
					$pageComponent['params']		= isset($array_path[2]) ? array_slice($array_path,3) : null;
				}
				//jika tidak mode lokal
				else{
					$pageComponent['modul'] 		= "";
					$pageComponent['class'] 		= $array_path[0];
					$pageComponent['func'] 			= isset($array_path[1]) ? $array_path[1] : $this->conf->startUp["APPS.FUNC"];
					$pageComponent['params'] 		= isset($array_path[1]) ? array_slice($array_path,2) : null;
				}
			}		
			$pageComponent = (object) $pageComponent;


			if(isset($net_url['query'])){

				parse_str($net_url['query'],$array_query_string);
				req::setQueryGET($array_query_string);	
				//var_dump($array_query_string);
			}else{
				$array_query_string = [];
				//var_dump($array_query_string);
			}

			if($pageComponent->class==""){$pageComponent->class = self::$confStatic->startUp["APPS.BASEPAGE"];}
			req::setParamsFunc((array)$pageComponent->params);
			self::setupPage(self::$rutes,$pageComponent);			
		}
		else
		{
			echo "whoopppsss something went wrong";
			//error whoopsss plugin
		}
	}

	static function setupPage($storedUri, $pageComponent, $online=true) {
		$requestUri = "/".$pageComponent->class."/".$pageComponent->func;
		$verb = $_SERVER['REQUEST_METHOD'];
		$a = new \stdClass();
		$sortedUri = array();
		foreach($storedUri as $keyStoreUri => $valUri){
			//jika request uri ada dalam koleksi rute pada konfigurasi
			if($valUri[1] === $requestUri){
				if($valUri[3] != $pageComponent->func){
					$pageComponent->class; 					
					$sortedUri[$valUri[0]] = $valUri;
					//echo "+++ tidak ditemukan ".$valUri[1]." = ".$requestUri." +++   ";
					//var_dump($pageComponent);
					return self::getPage($pageComponent);
					
				}else{
					$pageComponent->params;
					$pageComponent->class; 
					$sortedUri[$valUri[0]] = $valUri;
					//echo "+++ ditemukan ".$valUri[1]." = ".$requestUri." +++   ";
					//var_dump($pageComponent);
					return self::getPage($pageComponent);
					
				}				
			}else{
					
					//var_dump($pageComponent);
					return self::getPage($pageComponent);
			}
		}	
		
	}

	static function getPage($dataPage){
		if(is_callable($dataPage->func)){
			
			call_user_func_array($dataPage->func,$dataPage->params);
		}
		else
		{

			//format HMVC [modules][controller ++model++views]
			//format MVC controler ++model++views
			$isModules = self::$confStatic->startUp["APPS.MODULAR"];

			if($isModules == "true"){ 
				$pathName = "Application/Modules/".$dataPage->modul."/Controllers/";
			}else{
				$pathName = "Application/Controllers/";
			}

			$sufix 		= "Controller";
			$controller =  ucfirst($dataPage->class).$sufix;
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
				

				if(!method_exists($obj_controller,$dataPage->func)){
				//var_dump($func);
					//echo " function $func tidak ada<br>";
					if(!empty($dataPage->func)){
						$dataPage->params = array($dataPage->func);
						$action = "show";
					}else{
						$action = self::$confStatic->startUp["APPS.FUNC"];
					}
				}else { $action = $dataPage->func; }
				//var_dump($func);

				$reflector = sl::get()->bluePrint($obj_controller);

				$par = $reflector->getMethod($action)->getParameters();
				$dependencies = array();

				foreach ($par as $key => $value) {
					$nameClass = $value->getClass();
					if(!is_null($nameClass)){	
						$dependencies[]= sl::get($nameClass->name);
					}else{
						$dependencies = array_merge($dependencies,$dataPage->params);
					}
				}

				//var_dump($dependencies);

				call_user_func_array([$obj_controller,$action],$dependencies);
			}
			else
			{
				echo json_encode((object)["message"=>"failed","data"=>"page not found on the sistem"]);
			}
		}		
	}

}