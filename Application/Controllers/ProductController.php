<?php
namespace Application\Modules\User\Controllers;
use Framework\Engine\Gear\Controller;

class ProductController extends Controller{

	function __construct(){

		//setup dan object model products jika tanpa parameter
		//tetapi akan memebuat object model sesuai dengan parameter
		//tidak akan membuat object model jka parameter tidak sesuai dengan class model yang ada
		

	}

	//wajib ada //wajib menggunakan method GET
	function index(){		
	}
	//wajib ada DAN //wajib menggunakan method GET
	function show($id=null,$d=null){

		echo "show detail product by id ".$id." ".$d;
	}
	//wajib ada DAN //wajib menggunakan method GET
	function edit($id=null,$d=null){

		echo " edit product by id ".$id." ".$d." ".$_GET["aa"];
	}		

	//wajib menggunakan method GET
	function create(){
		echo "show form new product";
	}
	//wajib menggunakan method POST
	function store(){
		echo "save new product";
	}
	//wajib menggunakan method PUT
	function update(){
		echo "update data product";
	}
	//wajib menggunakan method DELETE
	function delete($id){
		echo "remove product by id ".$id;
	}

}