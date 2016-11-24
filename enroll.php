<?php

	function sufficient_grade($minimum, $grade) {
		$grades = array("A+" => -1, "A" => -1, "A-" => -1, "B+" => -1, "B" => -1, "B-" => -1, "C+" => 0, "C" => 1, "C-" => 2, "D+"  => 3, "D" => 4, "D-" => 5, "F" => 6);
		return $grades[$minimum] >= $grades[$grade];
	}

	session_start();
	$db = new mysqli('localhost', 'username', 'password', 'asist2');
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
	$semester = 'fall 2016';
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
	$query = "SELECT prereq_dept_mnemonic, prereq_course_number, minimum_grade " .
	"FROM prerequisites WHERE dept_mnemonic = '$dept_mnemonic' " .
	"AND course_number = $course_number;";
	
	echo "<br>" . $query . "<br>";
	
	$result =  $db->query($query);
	
	echo $result->num_rows;
	
	$all = $result->fetch_all();
	
	foreach ($all as $res) {
		echo "$res[0] $res[1] $res[2]<br>";
		$prereq_dept_mnemonic = $res[0];
		$prereq_course_number = $res[1];
		$grade = $res[2];
		$query = "SELECT student_section.grade " .
		"FROM student_section, section WHERE " .
		"section.dept_mnemonic = '$prereq_dept_mnemonic' AND " .
		"section.course_number = $prereq_course_number AND " .
		"section.semester <> '$semester' AND " .
		"section.section_key = student_section.section_key AND " . 
		"student_section.student_id = '$computing_id';";
		
		echo "<br>" . $query . "<br>";
		
		$best_grade = "F";
		$result = $db->query($query);
		while ($inner_res = $result->fetch_row()) {
			$best_grade = $inner_res[0];
		}
		if (!sufficient_grade($grade, $best_grade)) {
			$exception = " Error. You have not fulfilled the requirement \"$prereq_dept_mnemonic $prereq_course_number\" for $dept_mnemonic $course_number.";
		}
	}
	
	
	echo $exception;
	
	foreach ($section_numbers as $section_key) {
		$query = "select * from student_section where section_key = $section_key and student_id = '$computing_id';";
		$result = $db->query($query);
		
		// if there is a row containing the matching information given in the request already, reject it
		if ($result->fetch_row()) {
			$exception = " Error. You are already signed up for this course.<br>";
		}
		
		$query = "select status, total_students, capacity from section where section_key = $section_key;";
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
		foreach ($section_numbers as $section_key) {
			$query = "INSERT INTO student_section VALUES($section_key, '$computing_id', '" . date("Y-m-d H:i:s") . "', $waitlist, '?');";
			echo $db->query($query) == false;
			echo "<br>waitlist: " . $waitlist . "<br>";
			
			// if there is no waitlist, increase the size of total_students
			// if that makes it equal to the capacity, set the section status
			// to waitlisted (2)
			if ($waitlist == 1) {
				$query = "select total_students from section where section_key = $section_key;";
				$total_students = $db->query($query)->fetch_row()[0];
				$query = "select capacity from section where section_key = $section_key;";
				$capacity = $db->query($query)->fetch_row()[0];
				$total_students++;
				$query = "update section set total_students = $total_students where section_key = $section_key;";
				echo "increase query: " . $db->query($query) == false;
				if ($total_students >= $capacity) {
					$query = "update section set status = 2 where section_key = $section_key;";
					$db->query($query);
				}
			}
		}
	}
	echo $exception;
	$_SESSION["EXCEPTION"] = $exception;
	header("Location: searchResult.php");
?>