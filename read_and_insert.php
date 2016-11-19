<?php
	$GLOBALS["num"] = 1;
	$GLOBALS["letter1"] = 0x61;
	$GLOBALS["letter2"] = 0x61;
	function nextID() {
		$num = $GLOBALS["num"];
		$letter1 = $GLOBALS["letter1"];
		$letter2 = $GLOBALS["letter2"];
		$ret = strval($num) . chr($letter1) . chr($letter2);
		if (chr($letter2) == "z" && chr($letter1) == "z") {
			$num++;
			$letter1 = 0x61;
			$letter2 = 0x61;
		} else if (chr($letter2) == "z") {
			$letter1++;
			$letter2 = 0x61;
		} else {
			$letter2++;
		}
		$GLOBALS["num"] = $num;
		$GLOBALS["letter1"] = $letter1;
		$GLOBALS["letter2"] = $letter2;
		return $ret;
	}
	
	function logInstructors($list) {
		$instructor_set = array();
		for ($i = 0; $i < count($list); $i++) {
			$in = $list[$i];
			if (property_exists($in, "instructor_first") && !array_key_exists($in->instructor_first . " " . $in->instructor_last, $instructor_set)) {
				$id = strtolower($in->instructor_first[0]) . strtolower($in->instructor_last[0]) . nextID();
				$new_last = $in->instructor_last;
				if (strpos($list[$i]->instructor_last, "+")) {
					$new_last = str_replace("+", "", $in->instructor_last);
					$new_last = str_replace("1", "", $new_last);
					$new_last = str_replace("2", "", $new_last);
				}
				if (!strcmp($in->instructor_first, "Staff")) {
					$new_last = "Staff";
					$id = "staff";
				}
				$temp = array("password" => strtolower($in->instructor_first), "department" => $in->dept_mnemonic, "computing_id" => $id, "instructor_first" => $in->instructor_first, "instructor_last" => $new_last, "dept_mnemonic" => $in->dept_mnemonic);
				$instructor_set[$in->instructor_first . " " . $in->instructor_last] = $temp;
			}
		}
		foreach ($instructor_set as $key => $value) {
			//print_r($value);
			//echo "<br>";
		}
		return $instructor_set;
	}
	
	function parseCourses($list) {
		$course_set = array();
		for ($i = 0; $i < count($list); $i++) {
			$course = $list[$i];
			if (!property_exists($course, "instructor_first") && !array_key_exists($course->dept_mnemonic . $course->number, $course_set)) {
				$course_set[$course->dept_mnemonic . $course->number] = array("name" => $course->name, "number" => $course->number, "dept_mnemonic" => $course->dept_mnemonic, "units" => -1);
			}
		}
		for ($i = 0; $i < count($list); $i++) {
			
			$section = $list[$i];
			
			if (property_exists($section, "instructor_first") ) {
				$id = $section->dept_mnemonic . $section->course_number;
				if ($course_set[$id]["units"] == -1) {
					$course_set[$id]["units"] = $section->units;
				}
			}
		}
		foreach ($course_set as $key => $value) {
			if ($value["units"] == -1) {
				$value["units"] = 3;
				$course_set[$key] = $value;
			}
		}
		return $course_set;
	}
	
	function parseDepartments($list) {
		$department_set = array();
		$id = 0;
		for ($i = 0; $i < count($list); $i++) {
			$dept = $list[$i];
			if (!array_key_exists($dept->dept_mnemonic, $department_set)) {
				$department_set[$dept->dept_mnemonic] = array("dept_mnemnonic" => $dept->dept_mnemonic, "id" => $id++);
			}
		}
	}
	
	function parseTimeSlots($list) {
		$timeslot_set = array();
		$id = 0;
		for ($i = 0; $i < count($list); $i++) {
			$ts = $list[$i];
			if (property_exists($ts, "start_time")) {
				if (!array_key_exists($ts->start_time . $ts->end_time, $timeslot_set)) {
					$timeslot_set[$ts->start_time . $ts->end_time] = array("start_time" => $ts->start_time, "end_time" => $ts->end_time, "id" => $id++);
				}
			}
		}
		return $timeslot_set;
	}
	
	function getDepartments() {
		return array("SWAH" => "Swahili", "CS" => "Computer Science", "ECE" => "Electrical Engineering", "CPE" => "Computer Engineering", "ANTH" => "Anthropology", "ASTR" => "Astronomy");
	}
	
	function parseBuildings($list) {
		$building_set = array();
		$id = 0;
		for ($i = 0; $i < count($list); $i++) {
			$b = $list[$i];
			if (property_exists($b, "building")) {
				if (!array_key_exists($b->building, $building_set)) {
					$building_set[$b->building] = array("building_id" => $id++, "building_name" => $b->building);
				}
			}
		}
		return $building_set;
	}
	
	function writeDatabase($instructor_set, $course_set, $timeslot_set, $list, $building_set) {
		$db = new mysqli("localhost", "username", "password", "asist");
		$depts = getDepartments();
		foreach ($depts as $key => $value) {
			$db->query("INSERT INTO department VALUES('" . $key . "', '" . $value . "');");
		}
		echo $db->query("INSERT INTO school VALUES(1, 'College');") == false;
		echo $db->query("INSERT INTO school VALUES(2, 'Engineering');") == false;
		foreach ($instructor_set as $key => $value) {
			$query = "INSERT INTO instructor VALUES('" . $value["computing_id"] . "', '" . $value["password"] . "', '" . $value["instructor_first"] . "', middle_name, '" . $value["instructor_last"] . "', '" . $value["dept_mnemonic"] . "');";
			$db->query($query);
		}
		foreach ($course_set as $key => $value) {
			$query = "INSERT INTO course VALUES('" . $value["dept_mnemonic"] . "', " . $value["number"] . ", '" . $value["name"] . "', '', " . $value["units"] . ", 1);";
			$db->query($query);
		}
		foreach ($timeslot_set as $key => $value) {
			if (strpos($value["start_time"], ":")) {
				$query = "INSERT INTO timeslot VALUES(" . $value["id"] . ", '" . $value["start_time"] . "', '" . $value["end_time"] . "');";
				$db->query($query);
			}
		}
		foreach ($building_set as $key => $value) {
			$query = "INSERT INTO building VALUES(" . $value["building_id"] . ", '" . $value["building_name"] . "', 'CS');";
			$db->query($query);
		}
		/*
	section_id INT,
    dept_mnemonic VARCHAR(4),
    course_number INT,
    building_id INT,
    room VARCHAR(6),
    section_title VARCHAR(50),
    time_id INT,
    semester VARCHAR(20),
    capacity INT,
    total_students INT,
    days VARCHAR(10),
    description VARCHAR(30),
    status TINYINT,
    	*/
		for ($i = 0; $i < count($list); $i++) {
			$s = $list[$i];
			if (property_exists($s, "status")) {
				if (strpos($s->start_time, ":") and strlen($s->building) > 1) {
					$time_id = $timeslot_set[$s->start_time . $s->end_time]["id"];
					$building_id = $building_set[$s->building]["building_id"];
					$status = ($s->status == "Closed") ? 0 : 1;
					$computing_id = $instructor_set[$s->instructor_first . " " . $s->instructor_last]["computing_id"];
					$query = "INSERT INTO section VALUES(" . $s->number . ", '" . $s->dept_mnemonic . "', " . $s->course_number . ", " . $building_id
					. ", '" . $s->room_number . "', '', " . $time_id . ", '" . $s->semester . " " . $s->year . "', " . $s->capacity . ", " . $s->enrollment
					. ", '" . $s->days . "', '" . $s->description . "', " . $status . ");";
					$db->query($query);
					
					$query = "INSERT INTO instructor_section VALUES('" . $computing_id . "', '" . $s->dept_mnemonic . "', " . $s->course_number . ", " . $s->number . ");";
					$db->query($query);
				}
			}
		}
	}
	
	$results = file_get_contents("results.txt");
	$list = json_decode($results);
	//$db = new mysqli("localhost", "username", "password", "asist");
	$instructors = array();
	for ($i	= 0; $i < count($list); $i++) {
		if (property_exists($list[$i], "units")) {
			$list[$i]->units = str_replace(" ", "", $list[$i]->units);
			if (preg_match_all("/[0-9]+Units/", $list[$i]->units)) {
				$list[$i]->units = str_replace("Units", "", $list[$i]->units);
			}
			if (preg_match_all("/-/", $list[$i]->units)) {
				$list[$i]->units = substr($list[$i]->units, 0, 1);
			}
		}
	}
	$timeslot_set = parseTimeSlots($list);
	$instructor_set = logInstructors($list);
	$course_set = parseCourses($list);
	$buildings = parseBuildings($list);
	//writeDatabase($instructor_set, $course_set, $timeslot_set, $list, $buildings);
?>