<?php
namespace Framework\Engine\Gear;
use Framework\Engine\Gear\ModelFactory;
use Framework\Engine\Gear\ControllerInterface;
use Framework\Engine\Template\Viewer;
Use Framework\Engine\Services\ServiceProvider\ServicesProvider as SP;

abstract class Controller extends Viewer implements ControllerInterface{
	public $modelIn;
	public $model_instance = [];
	public $container;

	function __construct(){
		$this->allowCrossDomain();
	}

	public function __get($modelIn){
		if(array_key_exists($modelIn, $this->model_instance)){
			//echo "ga usah bikin baru <br>";
			return $this->model_instance[$modelIn];
		}else{
			$this->modelIn = strtolower(trim($modelIn));
			$this->make($this->modelIn);
			$this->model_instance[$modelIn]->table = $this->modelIn;
			//echo "sedang bikin baru $this->modelIn tableName:: ".$this->model_instance[$modelIn]->table."<br> ";
			return $this->model_instance[$modelIn];
		}
		
	}

	/**
	 * get services from service provider
	 * @param  string $name_service [description]
	 * @return object       [description]
	 */
	function sp($name_service){
		return SP::getInstanceOf($name_service);
	}	

	public function allowCrossDomain(){
		
		if (isset($_SERVER['HTTP_ORIGIN'])) {
		    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
		    header('Access-Control-Allow-Credentials: true');
		    header('Access-Control-Max-Age: 86400'); // cache for 1 day
		}

		// Access-Control headers are received during OPTIONS requests
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

		    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
			   header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

		    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
			   header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

		    exit(0);
		}
	}							

	public function make( $modelIn=null){
		if($modelIn == null){
			$modelIn = $this->modelIn;
		}
		if(array_key_exists($modelIn, $this->model_instance)){
			//echo "ga usah bikin model baru ".$modelIn."<br>";
			return $this->model_instance[$modelIn];
		}else{
			//$this->model_instance[$modelIn] = $modelIn;
			//echo "bikin model baru ".$modelIn."<br>";
			$ModelFactory = new ModelFactory();
			$this->model_instance[$modelIn] = $ModelFactory->make($modelIn);	
			return $this->model_instance[$modelIn];
		}
				
	}
}