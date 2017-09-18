<?php
namespace Application\Controllers;
use Framework\Engine\Gear\Controller;
use Framework\Engine\Kopling\Http\Request as Req;
use Framework\Engine\Kopling\Http\Response as Res;
use Application\Models\Users as user;
use Framework\Engine\Services\DI\ServiceLocator as sl;


class UserController extends Controller{

	function __construct(){
		parent::__construct();
	}

	function index(){		
		$users = user::find();

		echo response()->json((object)[
			"message" => "succes",
			"data" => $users
			]);

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
	

	function show(Req $req, $id=null){
		$funcShow = function($idData){
			$a = user::find($idData);
			$notif = $a->first();
			if($notif){
			echo response()->json((object)[
				"message"=>"succes",
				"data"=> $notif
				]);
			//redirect('/user');
			}else{
				echo response()->json((object)[
					"message"=>"failed",
					"data"=>"fail!, cek again your input id to search data"
					]);
				//echo '<a href="'.baseUrl().'/user">back</a>';
			}
		};

		
		if(!is_null($id)){
			$funcShow($id);
		}else{
			$c = $req::getData();
			if(!is_null($c)){
				$funcShow($c->id);
			}else{
				echo response()->json((object)[
				"message"=>"failed",
				"data"=>"your id is null "]);
			}			
		}
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

	function testRaw(){
		$user = user::prepare();
		$user->exec("select * from user");
		var_dump($user->last());
	}

	function testWhere(){
		
		$user =  user::where([
			"data" => ["id"=>"idUser","nama"=>"namaUser"],
			"where" => ["id"=>[676,454,679,76],"nama" => "awan"],
			"operator" => ["OR","AND"]
		]);

		var_dump($user->get());
		//opsi 1  = $user =  user::where(["id"=>1000,"nama"=> ["awan1","aku"],"NAMA"=>"aku awan"]);
		//opsi 2  = $user =  user::where("id = 1000");
		//opsi 3  = $user =  user::where(["id"=>"1000"]);		
		/*
		opsi 4  = 
		$user =  user::where([
			"data" => ["id"=>"10000","nama"=>"hariawan"],
			"where" => ["id"=>1000,"nama"=> ["awan1","aku"],"NAMA"=>"aku awan"]
		]);
		opsi 5 
		$user =  user::where([
			"data" => ["id"=>"idUser","nama"=>"namaUser"],
			"where" => ["nama"=> "%awan%","username"=> "%wan%","id"=> "%wan%"],
			"operator" => ["OR","AND"] ----->>> tiap banyaknya operator yang dipakai sebanyak koma pada array where
		]);
		*/
	}

	function testLike(){
		
		$user =  user::like([
			"data" => ["id"=>"idUser","nama"=>"namaUser"],
			"where" => ["nama"=> "%awan%","username"=> "%wan%","id"=> "%wan%"],
			"operator" => ["OR","AND"]
		]);

		var_dump($user->get());
			
		/*
		opsi 4  = 
		$user =  user::where([
			"data" => ["id"=>"10000","nama"=>"hariawan"],
			"where" => ["id"=>1000,"nama"=> ["awan1","aku"],"NAMA"=>"aku awan"]
		]);
		*/
	}

	function testMax(){

		$user = user::max([
			"column" => ["level"=>"levelUser"],
			"where" => ["id"=>676,"nama"=>454,"username"=>454,"level"=>454,"password"=>454],
			"operator"=> ["OR","AND"]
		]);

		var_dump($user->get());
	}

	function testAvg(){
		$user = user::avg([
			"column" => ["level"=>"levelUser"],
			"where" => ["id"=>[676,454,679,76],"nama"=>"awan"]
		]);

		var_dump($user->get());
	}

	function testSum(){
		
		$user =  user::sum([
			"column" => ["level"=>"levelUser"],
			"where" => ["id"=>[676,454,679,76]]
		]);

		var_dump($user->get());
		//opsi 1  = $user =  user::where(["id"=>1000,"nama"=> ["awan1","aku"],"NAMA"=>"aku awan"]);
		//opsi 2  = $user =  user::where("id = 1000");
		//opsi 3  = $user =  user::where(["id"=>"1000"]);		
		/*
		opsi 4  = 
		$user =  user::where([
			"data" => ["id"=>"10000","nama"=>"hariawan"],
			"where" => ["id"=>1000,"nama"=> ["awan1","aku"],"NAMA"=>"aku awan"]
		]);
		*/
	}


	
	function store(Req $req){
		$data = $req::getData();
		if(!is_null($data)){
			$a = user::prepare($data);			
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
					"data"=> " fail!, cek again your input data"
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
		if(!is_null($data)){
			$a = user::find($data->id);
			$a->dataSave = $data;
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
	function delete(Req $req, $id=null){
		$funcDelete = function($idData){
			$a = user::find($idData);			
			if(count($a) > 0){				
				$a = user::prepare($a);
				$a->delete();
				echo "halooooo";
				
				$notif = $a->delete();
			
				if($notif){
				echo response()->json((object)[
					"message"=>"succes",
					"data"=>"OK!, delete data is completed for key : ".implode(",",$idData)
					]);
				//redirect('/user');
				}else{
					echo response()->json((object)[
						"message"=>"failed",
						"data"=>"fail!, cek again your input id to delete data"
						]);
					//echo '<a href="'.baseUrl().'/user">back</a>';
				}
			}else{
				echo response()->json((object)[
					"message"=>"failed",
					"data"=>"data with parameter id ".$idData." not found"
					]);
			}			
				
		};
		//lewat parameter fucntion
		if(!is_null($id)){
			$id = explode(",",$id);
			//var_dump($id);
			$funcDelete($id);
		}
		//lewat query string
		else{
			$c = $req::getData("id");
			if(!is_null($c)){
				$id = $c;
				//var_dump($id);
				$funcDelete($id);
				
			}else{
				echo response()->json((object)[
				"message"=>"failed",
				"data"=>"your id is null "]);	
			}
		}
		

			/*
		$a = user::find($id);
		$notif = $a->delete();
		if($notif){
			redirect('/user');
		}else{
			echo '<a href="'.baseUrl().'/user">back</a>';
		}

		*/
	}

}