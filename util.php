<?php
	require_once "connection.php";
	function loadPos($pdo,$profile_id){
		$sql = "SELECT * FROM position WHERE profile_id=:pid ORDER BY rank";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array(':pid' => $profile_id));
		$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $positions;
	}
	function loadEdu($pdo,$profile_id){
		$sql2 = "SELECT * FROM education WHERE profile_id=:pid ORDER BY rank";
		$stmt2 = $pdo->prepare($sql2);
		$stmt2->execute(array(':pid' => $profile_id));
		$educations = $stmt2->fetchAll(PDO::FETCH_ASSOC);
		return $educations;
	}

?>