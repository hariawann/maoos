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
	<h2>Daftar User</h2>
	<table cellspacing="0">
		<tr>
			<td>id</td>
			<td>nama</td>
			<td>username</td>
			<td>email</td>
			<td>bergabung</td>
			<td>ubah profil</td>
			<td>opsi</td>
		</tr>
		<?php foreach ($users as $user => $value) { ?>
		<tr>
			<td><?php echo $value->id; ?></td>
			<td><?php echo $value->nama; ?></td>
			<td><?php echo $value->username; ?></td>
			<td><?php echo $value->email; ?></td>
			<td><?php echo $value->dibuat; ?></td>
			<td><?php echo $value->dirubah; ?></td>
			<td><a href="<?php echo baseUrl();echo'/user/edit/'.$value->id; ?>">Edit</a> | 
			<a href="<?php echo baseUrl();echo'/user/delete/'.$value->id; ?>">Delete</a></td>
		</tr>
		<?php } ?>
	</table>
	</br>
	<a href="<?php echo baseUrl().'/user/create'; ?>">tambah</a>

	

</body>
</html>