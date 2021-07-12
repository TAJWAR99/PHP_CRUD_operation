<?php
	require_once "connection.php";
	session_start();
	if(isset($_SESSION['success'])){
		echo "<p style='color:green'>".$_SESSION['success'];
		unset($_SESSION['success']);
	}
?>
<!DOCTYPE html>
<html>
<head>
<title>SK. ATIK TAJWAR SIHAN</title>
</head>
<body>
	<h1>Chuck Severance's Resume Registry</h1>
	<br>
	<?php 
	if(!isset($_SESSION['username'])){ ?>
	<a href="login.php">Please log in</a>
	<br><br>
	<table border="1">
	<?php
		echo "<tr><td>";
		echo "<b>Name</b>";
		echo "</td><td>";
		echo "<b>Headline</b>";
		echo "</td></tr>";
		
		$stmt = $pdo->query("SELECT profile_id,first_name,headline FROM profile");
		while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
			$fname =  $row['first_name'];
			$hdline = $row['headline'];
			echo "<tr><td>";
			echo ('<a href="view.php?profile_id='.$row['profile_id'].'">'.$fname.'</a>');
			echo "</td><td>";
			echo $hdline;
			echo "</td></tr>";
			
		}
	?>
	</table>
	<?php }else{ ?>
	<a href="logout.php">Logout</a>
	<br><br>
	<table border="1">
	<?php
		echo "<tr><td>";
		echo "<b>Name</b>";
		echo "</td><td>";
		echo "<b>Headline</b>";
		echo "</td><td>";
		echo "<b>Action</b>";
		echo "</td></tr>";
		
		$stmt = $pdo->query("SELECT profile_id,first_name,headline FROM profile");
		while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
			$fname =  $row['first_name'];
			$hdline = $row['headline'];
			echo "<tr><td>";
			echo ('<a href="view.php?profile_id='.$row['profile_id'].'">'.$fname.'</a>');
			echo "</td><td>";
			echo $hdline;
			echo "</td><td>";
			echo ('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a>');
			echo "  ";
			echo ('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
			echo "</td></tr>";
		}
	?>
	</table>
	<br><br>
	<a href="add.php">Add New Entry</a>
	<?php } ?>
</body>
</html>