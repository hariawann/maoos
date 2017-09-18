<?php 
namespace Framework\Engine\Kopling\Http;

class Response {
	public $data;

	function __construct($data=null){
		$this->data = $data;
	}

	static function make(){
		
	}

	function json($data=null){	
		if(!is_null($this->data)){
			$dataX = $this->data;
		}else{
			$dataX = $data;
		}
		header('Content-Type: application/json');
		return json_encode($dataX,JSON_PRETTY_PRINT);
	}

}