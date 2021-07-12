<?php
	session_start();
	require_once "connection.php";
	if(!isset($_SESSION['username']) && !isset($_SESSION['password'])){
		if(isset($_POST['Login']) && (empty($_POST['email']) || empty($_POST['pass']))){
			echo "<p style='color:red'>"."Email and Password is required";
		}
		elseif(isset($_POST['Login']) && !filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)){
				echo "<p style='color:red'>"."Warning:Email format is incorrect";
		}
		elseif(isset($_POST['Login']) && isset($_POST['email']) && isset($_POST['pass'])){
			$sql = "SELECT email,password FROM users WHERE email=:em and password=:pass";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(
				':em' => $_POST['email'],
				':pass' => $_POST['pass']));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if($row == false){
				error_log("Login failed:".$_POST['email']);
				echo "<p style='color:red'>"."Incorrect password";
			}else{
				$user = $_POST['email'];
				$pass = $_POST['pass'];
				$_SESSION['username'] = $user;
				$_SESSION['password'] = $pass;
				error_log("Login successful:".$_POST['email']);
				header("Location:index.php");
				return;
			}			
		}
	}else{
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
	<h1>Please Log In</h1><br>
	<form method="post">
	<p>Email:<input type="text" name="email" id="addr"></p>
	<p>Password:<input type="text" name="pass" id="id_1723"></p>
	<input type="submit" onclick="return doValidate();" name="Login" value="Log In">
	<input type="button" onclick="location.href='index.php';return false;" name="canc" value="cancel">
	</form>
	<script src="log.js"></script>
</body>
</html>