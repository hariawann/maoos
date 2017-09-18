<?php
namespace Framework\Engine\Services\Sanitasi;

class Saniter{
    function escape($var=null){
		if(is_null($var)){
			return null;
		}else{
			$esc = function($var){
				if(is_string($var)){
					$var =  "'".str_replace(["\"","'","''"],"",$var)."'";				
				}
				else if(is_integer($var)){
					$var =  str_replace(["'","\""], "",$var);
					$var = (int)$var;	
				}
				else if(is_float($var)){
					$var =  str_replace(["'","\""], "",$var);
					$var = (float)$var;
				}
				return $var;
			};

			if(is_array($var)){
				foreach($var as $v => $val){
					$var[$v]=$esc($val);
				}
			}else{
				 $var = $esc($var);
			}

			return $var;
		}
		
	}
}