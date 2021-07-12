<?php
	require_once "connection.php";
	session_start();
	if(!isset($_SESSION['username'])){
		die("Not logged in");
	}
	
	$sql = "SELECT * FROM profile WHERE profile_id=:pid";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array(":pid" => $_GET['profile_id']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	if($row === false){
		error_log("Bad input");
		header("Location:index.php");
		return;
	}
	echo "<h1>Deleting Profile</h1><br>";
	echo "First name: ".$row['first_name']."<br>";
	echo "Last name: ".$row['last_name']."<br>";
	
	if(isset($_POST['del'])){
		$sql = "DELETE FROM profile WHERE profile_id=:pid";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array(":pid" => $_GET['profile_id']));
		$_SESSION['success'] = "Profile deleted";
		header("Location:index.php");
		return;
	}
	
?>
<!DOCTYPE html>
<html>
<head>
<title>SK. ATIK TAJWAR SIHAN</title>
</head>
<body>
	<form method="post">
	<input type="submit" name="del" value="Delete">
	<input type="button" onclick="location.href='index.php';return false;" value="Cancel">
	</form>
</body>
</html>