<?php
namespace Framework\Engine\Template; 
use Framework\Engine\Template\Parser;

class Viewer {
	public $html;
	public $content;
	public $data = [];
	public $tamplate;
	
	function __construct( Parser $parser, $tamplate = null,array $data=null){
		$this->set($tamplate);
		$this->data($data);
		$this->parser = $parser;
	}

	public function set($tamplate){
		$this->tamplate = $tamplate;
		return $this;
	}

	public function data($data){
		$this->data = $data;
		return $this;
	}

	public function render($kembalikan_string=false){
		$path = 'Application/Views/'.$this->tamplate.'.php';
		if (!file_exists($path)) die ("Tidak dapat memanggil file view : " . $this->tamplate);	

		if(sizeof($this->data) > 0){
			//echo "viewer says:: ada arr data... <br>";
			extract($this->data, EXTR_SKIP);
		}
		ob_start(); //memulai object buffer pada PHP
		
		if($kembalikan_string == true){
			$this->content = ob_get_contents();
			@ob_end_clean();
			return $this->content;
		}else{
			$this->content = ob_get_contents();
			$this->html = $this->content;
			@ob_end_clean();
			require ($path);
		}		
	}

	public function parse(){
		$tamplate = $this->render(true);
		$this->parser->parse($tamplate,$this->data,true);
	}
}

?>