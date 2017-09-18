<?php
namespace Framework\Engine\Template;
class Parser{
	public $tamplate;
	public $data;
	public $return;


	var $l_delim = '{';
	var $r_delim = '}';
	var $object;

	function __construct($tamplatex=null, $datax=null, $returnx = TRUE){
		$this->tamplate = $tamplatex;
		$this->data = $datax;
		$this->return = $returnx;
	}

	function setTemplate($tamplatex=null, $datax=null, $returnx = TRUE){
		$this->tamplate = $tamplatex;
		$this->data = $datax;
		$this->return = $returnx;
		
	}

	function parse()
	{		
		if ($this->tamplate == ''){
			echo "tak ada tamplate yang diproses";
			return FALSE;
		}
		foreach ($this->data as $key => $val){
			if (is_array($val)){
				$this->tamplate = $this->_parse_pair($key, $val, $this->tamplate);		
			}
			else{
				$this->tamplate = $this->_parse_single($key, (string)$val, $this->tamplate);
			}
		}

		echo $this->tamplate;
	}

	function set_delimiters($l = '{', $r = '}'){
		$this->l_delim = $l;
		$this->r_delim = $r;
	}

	function _parse_single($key, $val, $string){
		return str_replace($this->l_delim.$key.$this->r_delim, $val, $string);
	}

	function _parse_pair($variable, $data, $string){	
		if (FALSE === ($match = $this->_match_pair($string, $variable))){
			return $string;
		}

		$str = '';
		foreach ($this->data as $row){
			$temp = $match['1'];
			foreach ($row as $key => $val){
				if ( ! is_array($val)){
					$temp = $this->_parse_single($key, $val, $temp);
				}
				else{
					$temp = $this->_parse_pair($key, $val, $temp);
				}
			}
			
			$str .= $temp;
		}
		
		return str_replace($match['0'], $str, $string);
	}

	function _match_pair($string, $variable){
		if ( ! preg_match("|".$this->l_delim . $variable . $this->r_delim."(.+?)".$this->l_delim . '/' . $variable . $this->r_delim."|s", $string, $match))	{
			return FALSE;
		}
		return $match;
	}

}