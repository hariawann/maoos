<?php
namespace Application\Modules\User\Controllers;
use Framework\Engine\Gear\Controller;
use Framework\Engine\Kopling\Http\Request as Req;
use Framework\Engine\Kopling\Http\Response as Res;
use Application\Modules\User\Models\Users as user;
use Framework\Engine\Services\DI\ServiceLocator as sl;


class UserController extends Controller{

	function index(){
		$users = user::find();

		view('Home')
		->data(['users'=>$users->get()])
		->render();

		//$a = user::find(1000);
		/*
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
			
			$data = $req::getData();
			$a = user::prepare();

			$a->id = $data->id;
			$a->nama = $data->nama;
			$a->username = $data->username;
			$a->password = $data->password;
			$a->email = $data->email;
			$a->level = $data->level;
			$a->dibuat = $data->dibuat;
			$a->dirubah = "";

			$notif = $a->save();

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
		$a = user::prepare();

		$a->id = $data->id;
		$a->nama = $data->nama;
		$a->username = $data->username;
		$a->password = $data->password;
		$a->email = $data->email;
		$a->level = $data->level;
		$a->dibuat = $data->dibuat;
		$a->dirubah = "";

		$notif = $a->save();
		
		if($notif){
			redirect('/user');
		}else{
			echo '<a href="'.baseUrl().'/user">back</a>';
		}
	}
	
	function update(Req $req){
		$data = $req::getData();
		$a = user::find($data->id);
		$a->nama = $data->nama;
		$a->username = $data->username;
		$a->password = $data->password;
		$a->email = $data->email;
		$a->level = $data->level;
		$a->dirubah = $data->dirubah;
		$notif = $a->save();
		if($notif){
			redirect('/user/edit/'.$data->id);
		}else{
			echo '<a href="'.baseUrl().'/edit/'.$data->id.'">back</a>';
		}
		
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