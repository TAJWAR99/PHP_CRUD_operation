<?php
	require_once "connection.php";
	$term = $_GET['term'];
	$stmt = $pdo->prepare('SELECT name FROM institution WHERE name LIKE:prefix');
	$stmt->execute(array(":prefix" => $term."%"));
	$retval = array();
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		$retval[] = $row['name'];
	}
	echo json_encode($retval,JSON_PRETTY_PRINT);
?>