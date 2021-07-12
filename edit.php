<?php
	require_once "connection.php";
	//require_once "util.php";
	session_start();
	if(!isset($_SESSION['username'])){
		die("Not logged in");
	}
	
	$sql ="SELECT * FROM profile WHERE profile_id=:id";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array(":id" => $_GET['profile_id']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	if($row === false){
		error_log("Bad value");
		header("Location:index.php");
		return;
	}
	$input1 = $row['first_name'];
	$input2 = $row['last_name'];
	$input3 = $row['email'];
	$input4 = $row['headline'];
	$input5 = $row['summary'];
	
	$positions = loadPos($pdo, $_GET['profile_id']);
	$educations = loadEdu($pdo, $_GET['profile_id']);
	
	if(isset($_SESSION['username'])){
		echo "Editing Profile for ".$_SESSION['username'];
	}else{
		die("Not logged in");
	}
	
	if(isset($_POST['save']) && isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email'])){
		if(strlen($_POST['first_name'])<1 || strlen($_POST['last_name'])<1 || strlen($_POST['email'])<1 || strlen($_POST['headline'])<1 || strlen($_POST['summary'])<1){
			echo "<p style='color:red'>"."All fields are required";
		}
		elseif(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)){
			echo "<p style='color:red'>"."Email address must contain @";
		}
		else{
			
			$msg = validatePos();
			if(is_string($msg)){
				$_SESSION['err'] = "error";
				echo "<p style='color:red'>".$msg;
			}else{
				$sql_2 = "UPDATE profile SET first_name=:fnm,last_name=:lnm,email=:em,headline=:hd,summary=:sum WHERE profile_id=:pid";
				$stmt_2 = $pdo->prepare($sql_2);
				$stmt_2->execute(array(
				':pid' => $_GET['profile_id'],
				':fnm' => $_POST['first_name'],
				':lnm' => $_POST['last_name'],
				':em' => $_POST['email'],
				':hd' => $_POST['headline'],
				':sum' => $_POST['summary']));
				
				$profile_id = $pdo->lastInsertId();
			
				$stat = $pdo->prepare('DELETE FROM position WHERE profile_id = :pro_id');
				$stat->execute(array(':pro_id' => $_GET['profile_id']));
				$stat2 = $pdo->prepare('DELETE FROM education WHERE profile_id = :pro_id');
				$stat2->execute(array(':pro_id' => $_GET['profile_id']));
				
				$rank = 1;
				for($i=1; $i<=9; $i++) {
					
				  if ( ! isset($_POST['year'.$i]) ) continue;
				  if ( ! isset($_POST['desc'.$i]) ) continue;

				  $year = $_POST['year'.$i];
				  $desc = $_POST['desc'.$i];
				  
				  $stmt = $pdo->prepare('INSERT INTO Position
					(profile_id, rank, year, description)
					VALUES ( :pid, :rank, :year, :desc)');

				  $stmt->execute(array(
				  ':pid' => $_GET['profile_id'],
				  ':rank' => $rank,
				  ':year' => $year,
				  ':desc' => $desc));

					$rank++;
				}
				$rank2 = 1;
				for($j=1; $j<=9; $j++) {
					
				  if ( ! isset($_POST['year'.$j]) ) continue;
				  if ( ! isset($_POST['school'.$j]) ) continue;

				  $year2 = $_POST['year'.$j];
				  $school = $_POST['school'.$j];
				  
				  $stmt1 = $pdo->prepare("SELECT * FROM institution WHERE name=:nm");
				  $stmt1->execute(array(":nm" => $school));
				  $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
				  if($row1 == false){
					  $stmt2 = $pdo->prepare("INSERT INTO institution(name) VALUES(:name)");
					  $stmt2->execute(array(":name" => $school));
					  $institutionid = $pdo->lastInsertId();
					  $stmt = $pdo->prepare('INSERT INTO education(profile_id,institution_id, rank, year)
					  VALUES ( :pid,:insid, :rank, :year)');

					  $stmt->execute(array(
					  ':pid' => $profile_id,
					  ':insid' => $institutionid,
					  ':rank' => $rank2,
					  ':year' => $year2));
				  }else{
					  $stmt = $pdo->prepare('INSERT INTO education(profile_id,institution_id,rank, year)
					  VALUES ( :pid,:insid,:rank, :year)');

					  $stmt->execute(array(
					  ':pid' => $_GET['profile_id'],
					  ':insid' => $row1['institution_id'],
					  ':rank' => $rank2,
					  ':year' => $year2));
				  }
				  

				$rank2++;
				}
	        }
			if(!isset($_SESSION['err'])){
				$_SESSION['success'] = "Profile added";
				header("Location:index.php");
				return;
			}
			else{
				unset($_SESSION['err']);
			}
		}
	}
	function validatePos() {
	  for($i=1; $i<=9; $i++) {
		if ( ! isset($_POST['year'.$i]) ) continue;
		if ( ! isset($_POST['desc'.$i]) ) continue;

		$year = $_POST['year'.$i];
		$desc = $_POST['desc'.$i];
		
		if ( strlen($year) == 0 || strlen($desc) == 0 ) {
		  return "All fields are required";
		}

		if ( ! is_numeric($year) ) {
		  return "Position year must be numeric";
		}
	  }
	  return true;
	}
?>
<!DOCTYPE html>
<html>
<head>
<title>SK. ATIK TAJWAR SIHAN</title>
<script
  src="https://code.jquery.com/jquery-3.2.1.js"
  integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
  crossorigin="anonymous">
</script>
</head>
<body>
	<form method="post">
	<p>First Name:<input type="text" name="first_name" size="40" value="<?= htmlentities($input1) ?>"></p><br>
	<p>Last Name:<input type="text" name="last_name" size="40" value="<?= htmlentities($input2) ?>"></p><br>
	<p>Email:<input type="text" name="email" size="40" value="<?= htmlentities($input3) ?>"></p><br>
	<p>Headline:</p>
	<p><input type="text" name="headline" size="40" value="<?= htmlentities($input4) ?>"></p><br>
	<p>Summary:</p>
	<textarea name="summary" rows="10" cols="30"><?php echo $row['summary'] ?></textarea><br>
	
	<?php 
	$pos = 0;
    // Make some checks for the PHP dynamically generated HTML elements......  
    if(!$positions){ // If there's no loaded value from database;
        
        // Create the Position button & DIV element;
        echo('<p>Position: <input type="submit" id="addPos" value="+"></p>' . "\n");
        echo('<div id="position_field">' . "\n");
        echo('</div>');
        
    }else{
        
        echo('<p>Position: <input type="submit" id="addPos" value="+"></p>' . "\n");
        echo('<div id="position_field">' . "\n");
        foreach($positions as $position){
            $pos++;
            echo('<div id="position' . $pos . '">' . "\n");
            echo('<p>Year: <input type="text" name="year' . $pos . '"');
            echo(' value="' . $position['year'] . '" />' . "\n");
            echo('<input type="button" value="-"');
            echo('onclick="$(\'#position' . $pos .'\').remove(); return false;">' . "\n");
            echo("</P>\n");
			echo "<p>Description:</p><br>";
            echo('<textarea name="desc' . $pos . '" rows="8" cols="80">' . "\n");
            echo(htmlentities($position['description']) . "\n");
            echo("\n</textarea>\n</div>\n");
            echo('</div>');
        }
        
    }
	$pos2 = 0;
    // Make some checks for the PHP dynamically generated HTML elements......  
    if(!$educations){ // If there's no loaded value from database;
        
        // Create the Position button & DIV element;
        echo('<p>Education: <input type="submit" id="addEdu" value="+"></p>' . "\n");
        echo('<div id="education_field">' . "\n");
        echo('</div>');
        
    }else{
        
        echo('<p>Education: <input type="submit" id="addEdu" value="+"></p>' . "\n");
        echo('<div id="education_field">' . "\n");
        foreach($educations as $education){
			
			$stmt1 = $pdo->prepare("SELECT * FROM institution WHERE institution_id=:id");
			$stmt1->execute(array(":id" => $education['institution_id']));
			$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
			
            $pos2++;
            echo('<div id="education' . $pos2 . '">' . "\n");
            echo('<p>Year: <input type="text" name="year' . $pos2 . '"');
            echo(' value="' . $education['year'] . '" />' . "\n");
            echo('<input type="button" value="-"');
            echo('onclick="$(\'#education' . $pos2 .'\').remove(); return false;">' . "\n");
            echo("</P>\n");
			echo "<p>School:</p><br>";
            echo('<input type="text" size="40" name="school' . $pos2 . '"');
			echo('value="'.$row1['name'].'"/>' . "\n");
            echo('</div>');
        }
        
    }
	?>
	<input type="hidden" name="id" value="<?= $_GET['profile_id'] ?>">
	<input type="submit" name="save" value="Save">
	<input type="button" onclick="location.href='index.php';return false;" value="Cancel">
	</form>
	<script>
	countpos=0;
	countedu=0;
	$(document).ready(function(){
	    console.log("Document Ready");
		$('#addPos').click(function(event){
			event.preventDefault();
			if(countpos>=9){
				alert("Maximum limit reached");
				return;
			}
			countpos++;
			console.log("Adding position");
			$('#position_field').append(
			'<div id="position'+countpos+'">\
			<p>Year:<input type="text" name="year'+countpos+'" value=""/>\
			<input type="button" value="-" onclick="$(\'#position'+countpos+'\').remove();\
			return false;"></p>\
			Description:<br>\
			<textarea name="desc'+countpos+'" rows="8" cols="80"></textarea>)\
			<\div>');
		});
		$('#addEdu').click(function(event){
			event.preventDefault();
			if(countedu>=9){
				alert("Maximum limit reached");
				return;
			}
			countedu++;
			console.log("Adding education");
			$('#education_field').append(
			'<div id="education'+countedu+'">\
			<p>Year:<input type="text" name="year'+countedu+'" value=""/>\
			<input type="button" value="-" onclick="$(\'#education'+countedu+'\').remove();\
			return false;"></p>\
			School:<input type="text" name="school'+countedu+'" size="40">\
			<\div>');
		});
	});
	</script>
</body>
</html>