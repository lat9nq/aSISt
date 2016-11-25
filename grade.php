<?php
	session_start();
	$db = new mysqli('localhost', 'username', 'password', 'asist2');
	$session_id = $_SESSION["computing_id"];
	
	$grade_array = $_POST["grade"];
	$student_array = $_POST["student"];
	$section_key_array=$_POST["section_key"];
	$index=0;

	foreach($section_key_array as $section_key){
		$grade = $grade_array[$index];
		
		if ( !in_array($grade, array("A+","A","A-","B+","B","B-","C+","C","C-","D+","D","D-","F"), true ) ) {
			$exception="Error. Invalid grade entered. Please try again.";
			break;
		} else{
			$exception = "Congratulations. You have successfully assigned grades.";
		}
		$student = $student_array[$index];
		$query = "update student_section set grade='$grade' where student_id = '$student' and section_key='$section_key'";
		$db->query($query);
		$index+=1;
	}

	$_SESSION["EXCEPTION"] = $exception;
	header("Location:assignGrades.php");
    
?>