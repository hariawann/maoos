<?php

use Application\Config\ConfigApp as c;
use Framework\Tools\Time\Time as t;

/**
* this tool is used get base url apps 
*@return give the value of base url
*
*/
function baseUrl(){
	//initializing
	$c = new c;
	$domains = explode(",",$c->startUp["APPS.DOMAIN"]);

	//get valid domain on list allowed to visit
	if(in_array($_SERVER["HTTP_HOST"],$domains)){
		$domain = $_SERVER["HTTP_HOST"];
	}else{
		$domain = "localhost";
	}

	//if not defined own base url
	$baseUrl = $_SERVER["REQUEST_SCHEME"]."://".$domain;
	if($c->startUp["APPS.URL"] != null){
		$baseUrl = $c->startUp["APPS.URL"]; 
	}

	return $baseUrl;
}

/**
* this tool is used to move page to other page, it can not used in the loop redirect
*@param $uri URI address formar /uri1 or /uri1/uri2
*
*/
function redirect($uri=null){
	$str = baseUrl().$uri;
	header("Location:$str");
}

function mytime(){
	return new t;
}

