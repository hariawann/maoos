<?php
function load($classname){
	$classname 	= ltrim($classname,'\\');
	$filename	= '';
	$namespace	= '';
	if($lastNsPos 	= strpos($classname,'\\')){
		$namespace 	= substr($classname,0,$lastNsPos);
		$classname	= substr($classname,$lastNsPos + 1);
		$filename	= str_replace('\\',DIRECTORY_SEPARATOR,$namespace).DIRECTORY_SEPARATOR;
	} 

	$filename	= $namespace.DIRECTORY_SEPARATOR.str_replace('_',DIRECTORY_SEPARATOR, $classname).'.php';
	require $filename;
}

spl_autoload_register('load');