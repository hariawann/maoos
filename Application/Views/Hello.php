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
	<h2>Data User</h2>
	<table cellspacing="0">
		<tr>
			<td>id</td>
			<td>nama</td>
			<td>username</td>
			<td>email</td>
			<td>bergabung</td>
		</tr>
		<?php foreach ($user as $userx => $value) { ?>
		<tr>
			<td><?php echo $value->id; ?></td>
			<td><?php echo $value->nama; ?></td>
			<td><?php echo $value->username; ?></td>
			<td><?php echo $value->email; ?></td>
			<td><?php echo $value->dibuat; ?></td>
		</tr>
		<?php } ?>
	</table>

	<h2>Data Jurnal</h2>
	<table cellspacing="0">
		<tr>
			<td>id</td>
			<td>hari</td>
			<td>tanggal</td>
			<td>debet</td>
			<td>kredit</td>
		</tr>
		<tr>
			<td><?php echo $jurnal->id; ?></td>
			<td><?php echo $jurnal->hari; ?></td>
			<td><?php echo $jurnal->tgl; ?></td>
			<td><?php echo $jurnal->debet; ?></td>
			<td><?php echo $jurnal->kredit; ?></td>
		</tr>
	</table>

</body>
</html>