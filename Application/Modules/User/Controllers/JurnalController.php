<?php
use Framework\Engine\Gear\Controller;

class JurnalController extends Controller{

	function __construct(){
	}

	//wajib ada //wajib menggunakan method GET
	function index(){	
		$this->user->find(1000);
		$this->product->find(3585);
		$this->product->find(3585);
	}
	//wajib ada DAN //wajib menggunakan method GET
	function show($id=null,$d=null){

		echo "show detail jurnal by id ".$id." ".$d;
	}
	//wajib ada DAN //wajib menggunakan method GET
	function edit($id=null,$d=null){

		echo " edit jurnal by id ".$id." ".$d." ".$_GET["aa"];
	}		

	//wajib menggunakan method GET
	function create(){
		echo "show form new jurnal";
	}
	//wajib menggunakan method POST
	function store(){
		echo "save new user";
	}
	//wajib menggunakan method PUT
	function update(){
		echo "update data user";
	}
	//wajib menggunakan method DELETE
	function delete($id){
		echo "remove jurnal by id ".$id;
	}

	
}