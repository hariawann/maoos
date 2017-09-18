<?php
namespace Application\Models;
use Framework\Engine\Gear\Model;

class Products extends Model{
	public $id;
	public $username;
	public $nama;
	public $password;
	public $email;
	public $dibuat;
	public $dirubah;
	public $level;


	function __construct(){
		parent::__construct([
							"id"		=> "",
							"username" 	=> "",
							"nama"		=> "",
							"password"	=> "",
							"email"		=> "",
							"dirubah"	=> "",
							"dibuat"	=> "",
							"level"		=> "",
							]);
	}

	function make(){
		
	}
}