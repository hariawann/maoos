<?php  
namespace Framework\Tools\Benchmark;

class Benchmark {
 
	public $marker = array();
	public $start;
	public $stop;

	function mark($name)
	{
		$this->marker["start"] = microtime();
	}

	function start(){
		$this->start = microtime();
	}

	function stop(){
		$this->stop = microtime();
	}

	function getStart(){
		return $this->start;
	}

	function getStop(){
		return $this->stop;
	}
 
	// Proses penghitungan waktu
	function elapsed_time($point1 = '', $point2 = '', $decimals = 4)
	{
		if ($point1 == '')
		{
			return '{elapsed_time}';
		}
 
		if ( ! isset($this->marker[$point1]))
		{
			return '';
		}
 
		if ( ! isset($this->marker[$point2]))
		{
			$this->marker[$point2] = microtime();
		}
 
		list($sm, $ss) = explode(' ', $this->marker[$point1]);
		list($em, $es) = explode(' ', $this->marker[$point2]);
 
		return number_format(($em + $es) - ($sm + $ss), $decimals);
	}

	function results($decimals = 4){
		list($sm, $ss) = explode(' ', $this->getStart());
		list($em, $es) = explode(' ', $this->getStop());
 
		return number_format(($em + $es) - ($sm + $ss), $decimals);
	}
 
	// Menghitung total memori yang digunakan
	function memory_usage()
	{
		return ( ! function_exists('memory_get_usage')) ? '0' : round(memory_get_usage()/1024/1024, 2).'MB';;
	}
}
?>
