<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
		table,td{
			border: 1px solid;
			padding: 10px;

		}

	</style>
</head>
<body>
	<h1>hello gengs</h1>
	<h2>Edit Data User <?php echo isset($user->nama) ? $user->nama : null;  ?></h2>
	<form method="Post" action="<?php echo baseUrl();?>/user/update">
		id<input name="id" value="<?php echo isset($user->id) ? $user->id : null;  ?>" type="text" readonly></br>
		nama<input name="nama" value="<?php echo isset($user->nama) ? $user->nama : null;  ?>" type="text"></br>
		username<input name="username" value="<?php echo isset($user->username) ? $user->username : null;  ?>" type="text"></br>
		password<input name="password" value="<?php echo isset($user->password) ? $user->password : null;  ?>" type="password"></br>
		email<input name="email" value="<?php echo isset($user->email) ? $user->email : null;  ?>" type="email"></br>
		level<input name="level" value="<?php echo isset($user->level) ? $user->level : null;  ?>" type="number"></br>		
		tanggal ubah<input name="dirubah" value="<?php mytime()->now()  ?>" type="date" readonly>
		<button type="submit">post now</button>
	</form>
	</br>
	<a href="<?php echo baseUrl().'/user'; ?>">kembali</a>
</body>
</html>