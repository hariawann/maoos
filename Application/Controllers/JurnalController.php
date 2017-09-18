<?php
namespace Application\Modules\User\Controllers;
use Framework\Engine\Gear\Controller;
use Framework\Engine\Kopling\Http\Request as Req;
use Framework\Engine\Kopling\Http\Response as Res;
use Application\Models\Users as user;
use Framework\Engine\Services\DI\ServiceLocator as sl;


class JurnalController extends Controller{

	function __construct(){
		parent::__construct();
	}

	function index(){	
		$b = sl::get("bmark");
		$b->start();	
		$users = user::find();
		echo response()->json((object)[
			"message" => "succes",
			"data" => $users->get()
			]);
		$b->stop();
		echo $b->results()." Detik";

		/*
		view('Home')
		->data(['users'=>$users->get()])
		->render();

		//$a = user::find(1000);
		
		
		view('Home')
		->data(['users'=>$users->get()])
		->render();

		 
		$a = user::insert([
			["id"=>1008,
			"nama"=>"winda", 
			"username"=>"winda",
			"password"=>"sdgdsg",
			"email"=>"winda@gmail.com",
			"level"=>"1",
			"dibuat"=>"",
			"dirubah"=>""],

			["id"=>1009,
			"nama"=>"winda", 
			"username"=>"winda",
			"password"=>"sdgdsg",
			"email"=>"winda@gmail.com",
			"level"=>"1",
			"dibuat"=>"",
			"dirubah"=>""]
			]);

			var_dump($a);


			ALTERNATIF //
		*/
		



	/*
	
	[
						"id" 	=> [1000,1002,1004],
						"nama"	=> ["hariawan","hari awan"],
						"email"	=>	["awanhari52@gmail.com"]
					]

	$a = user::where([
						"id" 	=> [1000,1002,1004],
						"nama"	=> ["hariawan","hari awan"],
						"email"	=>	"awanhari52@gmail.com"
					]);

					$a = user::where([
						'name2' => ["alu","ayam"],
						'name3' => "alu",
						//['name3' => "alu"],
						//['name4' => ["alu","ayam"]],
						//['name5' => "alu"],
						//['name6 > "asfsa"'],
					]);

		//var_dump($a->get());
*/
		//var_dump($a);
		//view("Hello",["user"=>$data_user,"jurnal"=>$data_jurnal],true);
	}
	

	function show($id){

		$b = sl::get("bmark");
		$b->start();
		$a = user::find($id)->first();
		echo toHtml($a);
		$b->stop();
		echo $b->results()." Detik";
	}

	function edit($id=null){
		$user=user::find($id);
		view('Edit')
		->data(['user'=>$user->first()])
		->render();
	}	

	function create(Req $req){
		view('Create')
		->render();
		
	}

	function store(Req $req){
		$data = $req::getData();

		if(!is_null($data)){
			$a = user::prepare();
			$a->id = $data->id;
			$a->nama = isset($data->nama) ? $data->nama : "" ;
			$a->username = $data->username;
			$a->password = $data->password;
			$a->email = $data->email;
			$a->level = $data->level;
			$a->dibuat = $data->dibuat;
			$a->dirubah = "";

			$notif = $a->save();
			if($notif){
			echo response()->json((object)[
				"message"=>"succes",
				"data"=>"OK!, save data is completed"
				]);
			//redirect('/user');
			}else{
				echo response()->json((object)[
					"message"=>"failed",
					"data"=>"fail!, cek again your input data"
					]);
				//echo '<a href="'.baseUrl().'/user">back</a>';
			}
		}else{
			echo response()->json((object)[
				"message"=>"failed",
				"data"=>"your data is null"]);
		}	
		
		
	}
	
	function update(Req $req){
		$data = $req::getData();
		if(is_null($data)){
			$a = user::find($data->id);
			$a->nama = $data->nama;
			$a->username = $data->username;
			$a->password = $data->password;
			$a->email = $data->email;
			$a->level = $data->level;
			$a->dirubah = $data->dirubah;
			$notif = $a->save();
			
			if($notif){
			echo response()->json((object)[
				"message"=>"succes",
				"data"=>"OK!, update data is completed"
				]);
			//redirect('/user');
			}else{
				echo response()->json((object)[
					"message"=>"failed",
					"data"=>"fail!, cek again your input to update data"
					]);
				//echo '<a href="'.baseUrl().'/user">back</a>';
			}

		}else{
			echo response()->json((object)[
				"message"=>"failed",
				"data"=>"your data is null"]);
		}

		/*
		
		if($notif){
			redirect('/user/edit/'.$data->id);
		}else{
			echo '<a href="'.baseUrl().'/edit/'.$data->id.'">back</a>';
		}
		*/
	}

	//wajib menggunakan method DELETE
	function delete($id){
		$a = user::find($id);
		$notif = $a->delete();
		if($notif){
			redirect('/user');
		}else{
			echo '<a href="'.baseUrl().'/user">back</a>';
		}
	}

}