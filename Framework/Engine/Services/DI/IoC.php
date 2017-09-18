<?php 
namespace Framework\Engine\Services\DI;
use Framework\Engine\Services\DI\Container as con;
/**
* 
*/
class IoC
{	
	private $reflector;
	public 	$services;

	static function get($className=null){
		if($className==null){
			return new self;
		}else{
			return (new self)->__get($className);
		}
		
	}

	function __get($key){	
		return $this->resolve($key);
	}

	function bluePrint($class=null){
		if(!is_null($class)){
			return new \ReflectionClass($class);
		}
		
	}

	function resolve($className){

			//var_dump($className);echo "<br>";
			$reflector = $this->bluePrint($className);

			if(!$reflector->isInstantiable()){
				throw new Exception("class not found",1);
			}

			$constructor = $reflector->getConstructor();
			$method = $reflector->getMethods();

			//echo $constructor ."<br>";
			if(is_null($constructor)){
				return new $className;
			}else{
				$parameters = $constructor->getParameters();
				//var_dump($parameters)."<br>";
				$dependencies = $this->getDependencies($parameters);

				//echo "buat objek ".$className."<br>";
				$obj = $reflector->newInstanceArgs($dependencies);
				//var_dump($obj);

				return $obj;
			}
	}

	function resolveFunc($funcName){
		//var_dump($className);echo "<br>";
		$reflector = $this->bluePrint($className);

		if(!$reflector->isInstantiable()){
			throw new Exception("class not found",1);
		}

		$constructor = $reflector->getConstructor();
		//echo $constructor ."<br>";
		if(is_null($constructor)){
			return new $className;
		}else{
			$parameters = $constructor->getParameters();
			//var_dump($parameters)."<br>";
			$dependencies = $this->getDependencies($parameters);

			//echo "buat objek ".$className."<br>";
			$obj = $reflector->newInstanceArgs($dependencies);
			//var_dump($obj);

			return $obj;
		}
	}


	function getDependencies($parameters){
		$dependencies = [];
		foreach ($parameters as $parameter){

			$dependency = $parameter->getClass();

			if(!is_null($dependency)){
				//echo "butuh ".$dependency->name."<br>";
				//var_dump($dependency);
				$dependencies[] = $this->resolve($dependency->name);				
			}
			else
			{				
				$dependencies[] = $parameter->getDefaultValue();
			}
		}

		return $dependencies;
	}

	/**
	 * [resolveNonClass description]
	 * @param  ReflectionParameter $parameter [description]
	 * @return mixed                         [description]
	 */
	function resolveNonClass(ReflectionParameter $parameter){
		if($parameter->isDefaultValueAvalailable()){
			return $parameter->getDefaultValue();
		}

		throw new Exception("cannot resolve the unkwon", 1);
		
	}
}

