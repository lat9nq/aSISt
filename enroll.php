<?php
	session_start();
	$db = new mysqli('localhost', 'username', 'password', 'asist');
	$section_numbers = array();
	print_r($_POST);
	foreach ($_POST as $key => $value) {
		if (strpos($value, "|")) {
			$exploded = explode("|", $value);
			array_push($section_numbers, $exploded[0]);
		}
	}
	echo "SECTION NUMBERS:<br>";
	print_r($section_numbers);
	echo "<br>";
	print_r($_SESSION);
	$exp = explode("?", $_POST["course"]);
	
	// this communicates the course data
	$dept_mnemonic = $exp[0];
	$course_number = $exp[1];
	$expected = explode("/", $exp[2])[0];
	$computing_id = $_SESSION["computing_id"];
	echo "<br>%%%%%% $expected %%%%%%%<br>";
	
	// this string contains any errors caused by trying to enroll
	$exception = "Congratulations. You have enrolled in $dept_mnemonic $course_number.";
	if ($expected != count($section_numbers)) {
		$exception = " Error. You did not choose enough sections. Please pick one from each category.<br>";
		echo "<br>NOT ENOUGH SECTIONS<br>";
	} else {
		echo "<br>SUCCESS, SUFFICIENT SECTIONS CHOSEN<br>";
	}
	foreach ($section_numbers as $section_id) {
		$query = "select * from student_section where section_id = $section_id and student_id = '$computing_id' and course_number = $course_number and dept_mnemonic = '$dept_mnemonic';";
		$result = $db->query($query);
		
		// if there is a row containing the matching information given in the request already, reject it
		if ($result->fetch_row()) {
			$exception = " Error. You are already signed up for this course.<br>";
		}
		
		$query = "select status, total_students, capacity from section where section_id = $section_id and course_number = $course_number and dept_mnemonic = '$dept_mnemonic' and semester = 'fall 2016'";
		$result = $db->query($query);
		$status = $result->fetch_row()[0];
		// 0 == closed, 1 == open, 2 == waitlist; yes we are very smart
		
		// if it's closed, go Dikembe Mutombo on this fool
		if ($status == 0) {
			$exception = " Error. One of the sections you signed up for has closed.<br>";
		// if there's a waitlist, signal the exception and also tell the subsequent script to put this person on the waitlist
		} else if ($status == 2 and !strpos($exception, "Error")) {
			$exception = " Alert: Waitlisted. You will be waitlisted for course $dept_mnemonic $course_number, as there aren't enough seats left in one of the sections you chose.<br>";
		}
	}
	if (!strpos($exception, "Error")) {
		// determine the status of the relationship between the student and the table
		$waitlist = strpos($exception, "Waitlisted") ? 2 : 1;
		
		// insert the student into each section
		foreach ($section_numbers as $section_id) {
			$query = "INSERT INTO student_section VALUES($section_id, '$dept_mnemonic', $course_number, '$computing_id', '" . date("Y-m-d H:i:s") . "', $waitlist, '?');";
			echo $db->query($query) == false;
			echo "<br>waitlist: " . $waitlist . "<br>";
			
			// if there is no waitlist, increase the size of total_students
			// if that makes it equal to the capacity, set the section status
			// to waitlisted (2)
			if ($waitlist == 1) {
				$query = "select total_students from section where section_id = $section_id and dept_mnemonic = '$dept_mnemonic' and course_number = $course_number and semester = 'fall 2016'";
				$total_students = $db->query($query)->fetch_row()[0];
				$query = "select capacity from section where section_id = $section_id and dept_mnemonic = '$dept_mnemonic' and course_number = $course_number and semester = 'fall 2016'";
				$capacity = $db->query($query)->fetch_row()[0];
				$total_students++;
				$query = "update section set total_students = $total_students where section_id = $section_id and dept_mnemonic = '$dept_mnemonic' and course_number = $course_number and semester = 'fall 2016'";
				echo "increase query: " . $db->query($query) == false;
				if ($total_students >= $capacity) {
					$query = "update section set status = 2 where section_id = $section_id and dept_mnemonic = '$dept_mnemonic' and course_number = $course_number and semester = 'fall 2016'";
					$db->query($query);
				}
			}
		}
	}
	echo $exception;
	$_SESSION["EXCEPTION"] = $exception;
	header("Location: searchResult.php");
?>