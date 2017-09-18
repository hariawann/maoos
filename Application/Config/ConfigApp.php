<?php
namespace Application\Config;

class ConfigApp
{
	public $startUp;

	function __construct($null=null){
		$this->startUp = $this->readFileConfig();
	}

	function readFileConfig(){		
		$path ="Application/Config/ENVI.mao";
		$handle = fopen($path, "r");
		$arr = [];
		if($handle){
			while(!feof($handle)){
				$a = [];
				$buffer = fgets($handle);
				$a = explode("=", $buffer);
				if($a !== null){
					$arr[$a[0]] = trim($a[1]);
				}				 
			}
		}else{ echo "tidak bisa dibaca" ;}
		fclose($handle);
		return $this->startUp = $arr;
	}
}