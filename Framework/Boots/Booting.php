<?php
namespace Framework\Boots;
use Framework\Engine\Kopling\Http\Router as R;
use Framework\Tools\Benchmark\Benchmark as b;
use Application\Config\ConfigApp as c;
use Framework\Engine\Services\DI\ServiceLocator as sl;

class Booting{
	public $statusEngine;
	public static $configENVI;
	public $configApp;
	public $router;

	function __construct(c $config, R $router){
		$this->configApp = $config->startUp;
		$this->router = $router;
		$this->statusEngine = $this->configApp["APPS.DEV"];
	}

	function run(){
		$this->init($this->statusEngine);		
	}

	public function init($option){
		if($option == "false"){
			sl::init();
			$this->dispacth();
		}
		else
		{	
			echo "<h2>this application is under construction</h2>";
		}
	}

	public function dispacth(){
		$this->router->grab();
	}
}

