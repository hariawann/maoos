<?php
use Framework\Engine\Kopling\Http\Response;

function response($data=null){
	return new Response($data);
}

function toHtml($datax){
		if(is_object($datax)){
			$datax=(array)$datax;
		}

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
		return $html;
	}