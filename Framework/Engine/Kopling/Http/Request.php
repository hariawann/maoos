<?php 
namespace Framework\Engine\Kopling\Http;

class Request {
	protected static $POST=[];
	protected static $GET=[]; 
	static $paramsFunc=[];

	
	public static function cekInArray($data,$type){
		if($type == "POST"){
			$array_data = self::$POST;
		}else{
			$array_data = self::$GET;
		}

		if($array_data != null){
			if(array_key_exists($data,$array_data)){
				return true;
			}
			else{
				echo "Your key ( <b>".$data."</b> ) not found in the Request GET field list";
			}
		}
		else{
			echo "Your key ( <b>".$data."</b> ) not found in the Request GET field list";
		}
	}
	
	public static function setParamsFunc(array $data){
		self::$paramsFunc = $data;
	}

	public static function getData($req=null){
		
		$merge = function(array $data){
			$res_array = array();
			//print_r($data);
			foreach ($data as $key => $value){
				if (count($data[$key]) > 0){
					$res_array = array_merge($res_array,$data[$key]);
				}
			}
			if(!empty($res_array))
			return $res_array;
		};
		$datas = file_get_contents('php://input');
	    $objJson = json_decode($datas);
	    //print_r("ini ada content parameter");
	    //print_r(count($req));

	    //print_r("ini parameter func");
	    //print_r($req); 
	    //jika parameter id ada nilai maka 
	    //print_r("ini data GET");
	    //print_r($_GET);

	    $dataMerger = array($_GET,$_POST,self::$POST,self::$GET);

		//var_dump($dataMerger);

	    if(!is_object($objJson)){
	      $arr = array();
	      parse_str($datas,$arr);	  
	      $dataMerger[]= $arr;
	      $obj = $merge($dataMerger);
		  
		  

	    }else{
	    	$dataMerger[] = (array)$objJson;
	    	$obj = $merge($dataMerger);

			
	    }

		//var_dump($obj);
	    	//print_r("\nini obj json");
	    	//print_r($obj);

	    if(!is_null($obj)){
			$request = "";
			$final_obj = array();
		    foreach ($obj as $key => $value) {
		    	if($value != "" || $value != null){
		    		$final_obj[$key] = $value;
		    	}
		    }

			if(count($final_obj) > 0){
				$request = (object)$final_obj;		
				$request->params = self::$paramsFunc;
			}else{					
				$request = (object)$final_obj;				
				$request->params = self::$paramsFunc;
			}		    
	    }  

		if(!is_null($req)){
				return @$request->{$req};
		}else{
			return $request;
		}

		
	  	
	}

	public static function input($keyData, $filter = true){	
		if($_SERVER["REQUEST_METHOD"] == "POST"){
			self::$POST = $_POST;
			$array_data = self::$POST;
		}
		else if($_SERVER["REQUEST_METHOD"] == "GET"){
			$array_data = self::$GET;
		}
		if( self::cekInArray($keyData,"GET") ){			
			if($filter){
				return self::filter($array_data[$keyData]);
			}
			else{
				return $array_data[$keyData];
			}
		}
			
	}
	
	public static function filter($val){
		$final_var 	= htmlentities(strip_tags(filter_var(
			htmlspecialchars($val,ENT_QUOTES), FILTER_SANITIZE_STRING)));
		return $final_var;
	}

	public static function setQueryGET($data){
		self::$GET = $data;

		//var_dump(self::$GET);
	}

	public static function setQueryPOST($data){
		self::$POST = $data;
	}


}