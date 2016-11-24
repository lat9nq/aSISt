<?php
	function brk($string) {
		echo "<br>" . $string . "<br>";
	}
	session_start();
	$db = new mysqli('localhost', 'username', 'password', 'asist2');
	$course_number = explode("/", $_POST["course_number"])[0];
	$dept_mnemonic = explode("/", $_POST["dept_mnemonic"])[0];
	echo $course_number . "<br>";
	echo $dept_mnemonic;
	$computing_id = $_SESSION["computing_id"];
	$semester = "fall 2016";
	$query = "SELECT section.section_key FROM section, student_section " .
	"WHERE section.course_number = $course_number " .
	"AND section.dept_mnemonic = '$dept_mnemonic' " .
	"AND section.semester = '$semester' " .
	"AND section.section_key = student_section.section_key " .
	"AND student_section.student_id = '$computing_id';";
	echo "<br>" . $query . "<br>";
	$result = $db->query($query);
	$section_keys = array();
	while ($res = $result->fetch_row()) {
		array_push($section_keys, $res[0]);
	}
	echo "section keys:";
	print_r($section_keys);
	$waitlist_victims = array();
	foreach ($section_keys as $section_key) {
		$query = "SELECT student_id FROM student_section " .
		"WHERE `status` = 2 AND section_key = $section_key " .
		"ORDER BY waitlist_timestamp ASC;";
		brk($query);
		/*
		$query = "SELECT student_id FROM student_section " .
		"WHERE section_key = $section_key AND waitlist_timestamp = " .
		"(SELECT MIN(waitlist_timestamp) FROM " .
		"(SELECT waitlist_timestamp FROM student_section WHERE status = 2) as aliasname);";
		*/
		$result = $db->query($query);
		if ($res = $result->fetch_row()) {
			$waitlist_victims[$section_key] = $res[0];
		} else {
			echo "here";
			$waitlist_victims[$section_key] = "NO WAITLIST";
		}
	}
	print_r($waitlist_victims);
	echo count($waitlist_victims);
	foreach ($waitlist_victims as $key => $victim) {
		if ($victim == "NO WAITLIST") {
			$query = "SELECT total_students from section " .
			"WHERE section_key = $key;";
			echo $query;
			$result = $db->query($query);
			$count = $result->fetch_row()[0];
			echo "<br>$count<br>";
			$count--;
			$query = "UPDATE section " .
			"SET total_students = $count " .
			"WHERE section_key = $key;";
			$db->query($query); 
		} else {
			$query = "UPDATE student_section SET status = 1 " .
			"WHERE student_id = '$victim' " .
			"AND section_key = $key;";
			echo "<br>";
			echo $db->query($query) == false;
			echo $query;
			echo "<br>";
		}
	}
	echo brk(count($section_keys));
	foreach ($section_keys as $key) {
		$query = "DELETE FROM student_section WHERE student_id = '$computing_id' " .
		"AND section_key = $key;";
		echo $query;
		echo $db->query($query) == false;
	}
	header("Location: classSchedule.php");
?>