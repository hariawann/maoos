<?php  
namespace Framework\Tools\TIme;

class Time {
 
	public $timezone;

    function __constructor(){
        $this->setTimeZone();
    }

    function setTimeZone(){
        date_default_timezone_get('Asia/Jakarta');
    }
    function now(){
        echo date("Y-m-d H:i:s");
    }


}
?>
