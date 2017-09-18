<?php
namespace Application\Config; 
use Framework\Engine\Kopling\Http\Router;

$id="";
$route = [
			/*
			["GET","/user/${id}",function(array $id){
				$data = new \stdClass();
				$data->class = "user";
				$data->func = "show";
				$data->params = $id;
				$data->modul = null;

			//$data = ["class"=>"user","func"=>"index","params"=> []];
			return Router::getPage($data);
			//return require "application/views/hello.php";
			}],

			*/

			//struktur array routes
			//['request_method','uri','class','method']

			["GET","/","user","index"],
			["GET","/user","user","index"],
			["GET","/user/index","user","index"],
			["GET","/user/${id}","user","show"],
			["GET","/user/show","user","show"],
			["GET","/user/create","user","create"],
			["GET","/user/edit","user","edit"],
			["PUT","/user/update","user","update"],
			["DELETE","/user/delete","user","delete"],
			["POST","/user/store","user","index"],
			["GET","/user/store","user","index"],

			["GET","/jurnal","jurnal","index"],
			["GET","/jurnal/index","jurnal","index"],
			["GET","/jurnal/show","jurnal","show"],
			["GET","/jurnal/create","jurnal","create"],
			["GET","/jurnal/edit","jurnal","edit"],
			["GET","/jurnal/update","jurnal","update"],

			["GET","/product","product","index"],
			["GET","/product","product","index"],
			["GET","/product/show","product","show"],
			["GET","/product/create","product","create"],
			["GET","/product/edit","product","edit"],
			["GET","/product/update","product","update"],

		];

Router::setUri($route);


/*

Router::get("/user",		'index');
Router::get("/user/create",	'create');
Router::get("/user/show",	'show');
Router::get("/user/edit",	'edit');
Router::put("/user/update",	'update');
Router::post("/user",		'store');
Router::delete("/user/delete", 'delete');

Router::get("/jurnal",			'index');
Router::get("/jurnal/create",	'create');
Router::get("/jurnal/show",		'show');
Router::get("/jurnal/edit",		'edit');
Router::put("/jurnal/update", 	'update');
Router::post("/jurnal",			'store');
Router::delete("/jurnal/delete", 'delete');


Router::post("/product/create",'create');
Router::get("/product/show",	'show');
Router::get("/product",			'index');

*/
