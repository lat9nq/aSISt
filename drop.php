<?php
	session_start();
	$db = new mysqli('localhost', 'username', 'password', 'asist');
	$course_number = explode("/", $_POST["course_number"])[0];
	$dept_mnemonic = explode("/", $_POST["dept_mnemonic"])[0];
	echo $course_number . "<br>";
	echo $dept_mnemonic;
	$computing_id = $_SESSION["computing_id"];
	$semester = "fall 2016";
	$query = "SELECT section_id FROM student_section WHERE course_number = $course_number " .
	"AND dept_mnemonic = '$dept_mnemonic' " .
	"AND student_id = '$computing_id';";
	$result = $db->query($query);
	$section_ids = array();
	while ($res = $result->fetch_row()) {
		array_push($section_ids, $res[0]);
	}
	print_r($section_ids);
	$waitlist_victims = array();
	foreach ($section_ids as $section_id) {
		$query = "SELECT student_id FROM student_section " .
		"WHERE course_number = $course_number AND dept_mnemonic = '$dept_mnemonic' " .
		"AND section_id = $section_id AND waitlist_timestamp = " .
		"(SELECT MIN(waitlist_timestamp) FROM " .
		"(SELECT waitlist_timestamp FROM student_section WHERE status = 2) as aliasname);";
		$result = $db->query($query);
		if ($res = $result->fetch_row()) {
			$waitlist_victims[$section_id] = $res[0];
		} else {
			$waitlist_victims[$section_id] = "NO WAITLIST";
		}
	}
	print_r($waitlist_victims);
	foreach ($waitlist_victims as $key => $victim) {
		if ($victim == "NO WAITLIST") {
			$query = "SELECT total_students from section " .
			"WHERE section_id = $key AND " .
			"course_number = $course_number " .
			"AND dept_mnemonic = '$dept_mnemonic' " .
			"AND semester = 'fall 2016';";
			echo $query;
			$result = $db->query($query);
			$count = $result->fetch_row()[0];
			echo "<br>$count<br>";
			$count--;
			$query = "UPDATE section " .
			"SET total_students = $count " .
			"WHERE section_id = $key AND " .
			"course_number = $course_number " .
			"AND dept_mnemonic = '$dept_mnemonic' " .
			"AND semester = 'fall 2016';";
			$db->query($query); 
		} else {
			$query = "UPDATE student_section SET status = 1 " .
			"WHERE student_id = '$victim' " .
			"AND section_id = $key " .
			"AND course_number = $course_number " .
			"AND dept_mnemonic = '$dept_mnemonic';";
			echo "<br>";
			echo $db->query($query) == false;
			echo $query;
			echo "<br>";
		}
	}
	$query = "DELETE FROM student_section WHERE student_id = '$computing_id' " .
	"AND course_number = $course_number " .
	"AND dept_mnemonic = '$dept_mnemonic';";
	echo $query;
	echo $db->query($query) == false;
?>