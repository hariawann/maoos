<?php
namespace Application\Models;
use Framework\Engine\Gear\Model;

class Jurnals extends Model{

	function __construct(){
		parent::__construct();
				
		$this->initProp([ "id"		=> "",
						"hari" 		=> "",
						"tgl"		=> "",
						"debet"		=> "",
						"kredit"	=> "",
						"uraian"	=> "",
						"updates"	=> "",
						"submitter"	=> ""	]);

		$this->setTypeProp(["id"=>"int"]);	
	}




}