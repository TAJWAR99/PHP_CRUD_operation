<?php
	require_once "connection.php";
	session_start();

	 echo "<h1>Profile information</h1><br>";
	 
	 $sql = "SELECT * FROM profile WHERE profile_id=:pid";
	 $stmt = $pdo->prepare($sql);
	 $stmt->execute(array(":pid" => $_GET['profile_id']));
	 $row = $stmt->fetch(PDO::FETCH_ASSOC);
	 
	 echo "First Name: ".$row['first_name']."<br>";
	 echo "Last Name: ".$row['last_name']."<br>";
	 echo "Email: ".$row['email']."<br>";
	 echo "Headline: ".$row['headline']."<br>";
	 echo "Summary: ".$row['summary']."<br>";
	 
	 $stmt2 = $pdo->prepare('SELECT * FROM position WHERE profile_id=:pid');
	 $stmt2->execute(array(":pid" => $_GET['profile_id']));
	 echo "<p>Position:</p>";
	 while($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)){
		echo ">>> ".$row2['year'].":".$row2['description']."<br>";
	 }
	 
	 $stmt3 = $pdo->prepare('SELECT * FROM education WHERE profile_id=:pid');
	 $stmt3->execute(array(":pid" => $_GET['profile_id']));
	 echo "<p>Education:</p>";
	 while($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)){
		$stmt4 = $pdo->prepare('SELECT name FROM institution WHERE institution_id=:insid');
	    $stmt4->execute(array(":insid" => $row3['institution_id']));
		$row4 = $stmt4->fetch(PDO::FETCH_ASSOC);
		echo ">>> ".$row3['year'].":".$row4['name']."<br>";
	 }
?>
<!DOCTYPE html>
<html>
<head>
<title>SK. ATIK TAJWAR SIHAN</title>
</head>
<body>
 <form method="post">
 <input type="button" onclick="location.href='index.php';return false;" value="Done">
 </form>
</body>
</html>