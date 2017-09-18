<?php
namespace Framework\Tools\Api;
class RestAPI{
	public $acc_format = array('json','xml','html');
	public $acc_method = array('POST','PUT','GET','DELETE');
	public $response = array();
	public $request;
	private $method;
	private $default_format = "json";
	private static $codes = array(  
            100 => 'Continue',  
            101 => 'Switching Protocols',  
            200 => 'OK',  
            201 => 'Created',  
            202 => 'Accepted',  
            203 => 'Non-Authoritative Information',  
            204 => 'No Content',  
            205 => 'Reset Content',  
            206 => 'Partial Content',  
            300 => 'Multiple Choices',  
            301 => 'Moved Permanently',  
            302 => 'Found',  
            303 => 'See Other',  
            304 => 'Not Modified',  
            305 => 'Use Proxy',  
            306 => '(Unused)',  
            307 => 'Temporary Redirect',  
            400 => 'Bad Request',  
            401 => 'Unauthorized',  
            402 => 'Payment Required',  
            403 => 'Forbidden',  
            404 => 'Not Found',  
            405 => 'Method Not Allowed',  
            406 => 'Not Acceptable',  
            407 => 'Proxy Authentication Required',  
            408 => 'Request Timeout',  
            409 => 'Conflict',  
            410 => 'Gone',  
            411 => 'Length Required',  
            412 => 'Precondition Failed',  
            413 => 'Request Entity Too Large',  
            414 => 'Request-URI Too Long',  
            415 => 'Unsupported Media Type',  
            416 => 'Requested Range Not Satisfiable',  
            417 => 'Expectation Failed',  
            500 => 'Internal Server Error',  
            501 => 'Not Implemented',  
            502 => 'Bad Gateway',  
            503 => 'Service Unavailable',  
            504 => 'Gateway Timeout',  
            505 => 'HTTP Version Not Supported'  
        ); 

	function cek(){
		echo "cek cek cekc ke<br>";
	}

	function prepare($req=null){
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
	    $dataMerger = array($req,$_GET,$_POST);

	    if(!is_object($objJson)){
	      $arr = array();
	      parse_str($datas,$arr);
	      $dataMerger[]= $arr;
	      $obj = $merge($dataMerger);
	    }else{
	    	$dataMerger[] = (array)$objJson;
	    	$obj = $merge($dataMerger);
	    }

	    	//print_r("\nini obj json");
	    	//print_r($obj);

	    if(!is_null($obj)){
			$final_obj = array();
		    foreach ($obj as $key => $value) {
		    	if($value != "" || $value != null){
		    		$final_obj[$key] = $value;
		    	}
		    }
		    $this->request = (object)$final_obj;
	    }   
	    //print_r($this->request);  	 	  
        $this->setMethod(isset($this->request->format) ?
        $this->request->format : "");
	  	return $this;
	}

	function toJson($data){		
		header('Content-Type: application/json');
		echo (json_encode($data,JSON_PRETTY_PRINT));
	}

	function toXml2($datax){
		header("Content-Type: text/xml");
		//print_r($datax);
		$xml = new XMLWriter;
		$xml->openMemory();		
		$xml->setIndent(true);
		$xml->setIndentString("	");
		$xml->startDocument('1.0', 'UTF-8','yes');
		$xml->startElement('XML');
			foreach ($datax as $key => $value) {
				$xml->startElement($key);
				if($key == "header"){
					foreach ($datax[$key] as $k => $v) {				
						$xml->startElement($k);
						$xml->text($v);
						$xml->endElement();
					}
				}
				else{
					$key = "payload";
					$no=1;
					foreach ($datax[$key] as $k => $v) {						
						if(is_object($v)){
							$xml->startElement("data".$no);
							foreach ($v as $keyx => $valuex) {
								//var_dump($keyx);
								//$xml->startElement($keyx);
								print("<".$keyx.">");
								$xml->text($valuex);
								$xml->endElement();							
							}	
							$xml->endElement();
							$no++;
						}else{
							$xml->startElement($k);
							$xml->text($v);
							$xml->endElement();
							$no++;
						}	
					}
				}			

				$xml->endElement();
			}					
			
		$xml->endElement();
		echo $xml->outputMemory(true);
	}

	function toHtml($datax){
		$html = "<!DOCTYPE html>";
		$html .= "<html>";
		$html .= "<head>";
		$html .= "<title> Response API";
		$html .= "</title>";
		$html .= "</head>";
		$html .= "<body>";
			foreach ($datax as $key => $value) {
				if($key == "header"){
					$html .= "<div>Header : </div><br>";
					foreach ($datax[$key] as $k => $v) {
						$html .= $k." <input type='text' name='".
						$k."' value='".$v."'>  <br>";
					}
				}
				else{
					$key = "payload";
					$no=1;
					$html .= "<br><div>Data : </div>";
					foreach ($datax[$key] as $k => $v) {						
						if(is_object($v)){
							$html .= "<br><div>Data".$no."</div>";
							foreach ($v as $keyx => $valuex) {					
								if(!is_numeric($keyx)){
									$html .= $keyx." <input type='text' name='".
									$keyx."' value='".$valuex."'>  <br>";
								}
							}	
							$no++;
						}else{							
							$html .= "<div>Data".$no."</div><br>";
							$html .= $k." <input type='text' name='".
							$k."' value='".$v."' >  <br>";					
							$no++;
						}
					}
				}			
			}					
			
		$html .= "</body>";
		$html .= "</html>";
		header("Content-Type: text/html");
		echo $html;
	}

	function toXml($datax){
		$xml="<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>";
		$xml.="<XMLdata>";
			foreach ($datax as $key => $value) {
				if($key == "header"){
					$xml .= "<".$key.">";
					foreach ($datax[$key] as $k => $v) {
						$xml .="<".$k.">".$v."</".$k.">";
					}
					$xml .= "</".$key.">";
				}
				else{
					$key = "payload";
					$no=1;
					$xml .= "<".$key.">";
					foreach ($datax[$key] as $k => $v) {					
						if(is_object($v)){
							$xml .= "<Data".$no.">";
							foreach ($v as $keyx => $valuex) {	
								if(!is_numeric($keyx)){
									$xml .= "<".$keyx.">".$valuex."</".$keyx.">";
								}
							}
							$xml .= "</Data".$no.">";	
							$no++;
						} /* else{	
							$xml .= "<Data".$no.">";
							$xml .= "<".$k.">".$v."</".$k.">";	
							$xml .= "</Data".$no.">";				
							$no++;
						} */
					}
					$xml .= "</".$key.">";
				}			
			}				
			
		$xml .= "</XMLdata>";		
		header("Content-Type: text/xml");
		echo $xml;
	}

	function getMethod(){
		return $this->method;
	}

	function setMethod($name_method=null){
		if($name_method == null || isset($name_method) == ""){			
			$this->method = strtoupper($_SERVER['REQUEST_METHOD']);
		}else{
			if(in_array(strtoupper($name_method), $this->acc_method)){
			 $this->method = $_SERVER['REQUEST_METHOD'] = strtoupper($name_method);
			}else{
				$this->method = strtoupper($_SERVER['REQUEST_METHOD']);	
			}
		}
		return $this;		
	}

	function addResponse($field=null,$data=null){
		$this->response[$field] = $data;
		return $this;
	}

	function response($data){	
		
		$this->response = $data;
		return $this;
	}

	function proses($data){
		foreach (self::$codes as $key => $value) {
				if($key == $data['code']->code){	
					$c 		= (string)$key;
					$d 		= $value;
					$ld 	= $data['code']->long_desc;
					$sts 	= $data['code']->status;
					$t 		= $data['code']->times;	
				}
			}
			if(count($data['data']) < 1){
				$data_send = array( 
				'header'	=> array('code' => $c,'desc' => $d, 'long_desc' => $ld,'status' => $sts,'times' => $t),
				);
			}else{
				$data_send = array( 
				'header'	=> array('code' => $c,'desc' => $d, 'long_desc' => $ld,'status' => $sts,'times' => $t),
				'payload'	=> (array)$data['data']
				);
			}
			return 	$data_send;	
		}

	function send(){
		$p = $this->proses($this->response);
		$pre="to";
		$format = function(){
			if(!in_array(isset($this->request->format) ? $this->request->format : null, $this->acc_format)){
				return $this->default_format;
			}else{		
				return $this->request->format;
			}
		};
		call_user_func_array(array($this,$pre.ucfirst($format())), array($p));

	}
}