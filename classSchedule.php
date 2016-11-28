<?php
session_start();
function brk() {
	echo "<br>";
}
if(!$_SESSION['computing_id'])  
{  
    header("Location: login.php");//redirect to login page to secure the welcome page without login access.  
  }  
  ?>
<?php
	$computing_id = $_SESSION["computing_id"];
	$db = new mysqli('localhost', 'username', 'password', 'asist2');
	$query = "SELECT distinct section.course_number, section.dept_mnemonic, course.course_title," .
	"section.room, building.building_name, timeslot.start_time, timeslot.end_time, " .
	"instructor.first_name, instructor.last_name, section.days, section.section_id, " .
	"section.semester, section.section_key, student_section.status, student_section.grade " .
	
	"FROM student_section, section, instructor_section, building, instructor, course, timeslot" .
	" WHERE student_section.section_key = section.section_key " .
	
	"AND section.semester = 'fall 2016' " .
	"AND student_section.student_id = '$computing_id' " .
	"AND instructor_section.section_key = section.section_key " .
	"AND instructor_section.instructor_id = instructor.computing_id " .
	"AND building.building_id = section.building_id " .
	"AND course.course_number = section.course_number " .
	"AND course.dept_mnemonic = section.dept_mnemonic " .
	"AND timeslot.time_id = section.time_id;";
	$result = $db->query($query);
	$ultimate_array = array();
	while ($res = $result->fetch_row()) {
		$query = "SELECT student_id FROM student_section " .
		"WHERE `status` = 2 AND section_key = $res[12] " .
		"ORDER BY waitlist_timestamp ASC;";
		$_result = $db->query($query);
		$waitlist_position = 0;
		$index = 1;
		while ($waitlist_pos = $_result->fetch_row()) {
			if ($waitlist_pos[0] == $computing_id) {
				$waitlist_position = $index;
				break;
			}
			$index++;
		}
		$status = "";
		if ($res[14] != "?") {
			$status = $res[14];
		} else if ($res[13] == 1) {
			$status = "Enrolled";
		} else if ($res[13] == 2) {
			$status = "Waitlisted ($waitlist_position)";
		}
		
		$grade_disabled = ($res[14] == "?") ? "" : "disabled";
		
		$temp_array = array("course" => $res[1] . " " . $res[0], "course_title" => $res[2],
			"room" => $res[4] . " " . $res[3], "time" => $res[9] . " " . $res[5] . " - " . $res[6],
			"instructor_name" => $res[7] . " " . $res[8], "course_number" => $res[0],
			"dept_mnemonic" => $res[1], "section_id" => $res[10], "section_key" => $res[12],
			"status" => $status, "grade-disabled" => $grade_disabled
		);
		//print_r($temp_array);
		array_push($ultimate_array, $temp_array);
	}
?>
<!DOCTYPE HTML>
<html>
<head>
	<title>Home</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<script type="text/javascript">

		var currCourse;

		// Modal with confirmation for course drop
		// Dynamically fetches course mnemonic and title for modal display
		$(document).on('click', '#tryDrop', function(event) {

			// Selects the mnemonic from the same row the button was clicked
			var mnem = $($(this).parent().parent().children()[0]);
			// Selects the course title from the same row the button was clicked
			var title = $($(this).parent().parent().children()[1]);

			// Set modal to reflect selected course to drop
			$("#mod-title").html(title.html());
			$("#mod-mnem").html(mnem.html());

			console.log(mnem.html());
			console.log(title.html());

			currCourse = $(this).parent().parent();
			console.log(currCourse.html());
		});


		$(document).ready(function(){
			// Drops course from table
			$(document).on('click', '#drop', function(event) {
				// var dropping = confirm("Are you sure you want to drop course " + mnem.html() + "?");
				//console.log(mnem);

				// if (dropping == true) {
				currCourse.remove();
				// }
				noSched(); //show no schedule message if no courses enrolled
			});
		});


		// Hide table if no courses
		function noSched() {
			var tbody = $("#table tbody");
			if (tbody.children().length == 0) {
				$("#table").hide();
				$('#noCourse').show();
				console.log("No courses left");
			} else {
				$("#table").show();
				$('#noCourse').hide();
			}
		}

	</script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
	integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<link rel="Stylesheet" href="style.css">
</head>
<body>
	<div class="container" id="page-wrap">

		<div class="header">
			<div style="float:left">
				<img id="logo" src="logo.png" width="100" height="100">
			</div>
			<div style="float:clear; display:inline-block; margin:1%;">
				<h3><i> aSISt </i></h3>
			</div>
		</div>


	<nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
        data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a href="/asist/home.php" class="navbar-brand">aSISt</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="/asist/home.php">Home </a></li>
        <li><a href="/asist/searchResult.php">Course Search </a></li>
        <?php 
        if (!isset($_SESSION['instructor'])){
        ?>
          <li><a href="/asist/classSchedule.php">Class Schedule </a></li>
        <?php 
        } else { ?>
          <li><a href="/asist/assignGrades.php">Assign Grades </a></li>
        <?php
        }
        ?>
        <li><a href="/asist/personalInfo.php">Personal Information </a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="/asist/home.php">Signed in as <?php echo $_SESSION['computing_id'];?></a></li>
        <li><a href="/asist/logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

	<br/>
	<center><h3>Class Schedule</h3></center><br/>


	<table class="table table-striped" id="table">
		<thead>
			<tr>
				<th style="width:15%">Course Mnemonic</th>
				<th>Course Title</th>
				<th>Instructor</th>
				<th>Meeting Time</th>
				<th>Location</th>
				<th>Status</th>
				<th></th>
			</tr>
		</thead>
		<tbody>

			<!-- first search result -->
			<?php foreach ($ultimate_array as $section) { ?>
			<tr>
				<td><?php echo $section["course"]; ?></td>
				<td><?php echo $section["course_title"]; ?></td>
				<td><?php echo $section["instructor_name"]; ?></td>
				<td><?php echo $section["time"] ?></td>
				<td><?php echo $section["room"] ?></td>
				<td><?php echo $section["status"] ?></td>
				<td>
					<form method = "post" action = "drop.php">
						<input type = "hidden" name = "course_number" value = <?php echo $section["course_number"] ?>/>
						<input type = "hidden" name = "dept_mnemonic" value = <?php echo $section["dept_mnemonic"] ?>/>
						<input type="submit" class="btn btn-danger btn-circle.btn-lg" id="tryDrop" value = "Drop"
							<?php echo $section["grade-disabled"]; ?>/> <!--data-toggle="modal" data-target="#17339"--> 
					</form>
				</td>
			</tr>
			<?php } ?>
			
		</tbody>
	</table>

<!--
		<div class="modal fade" id="17339" role="dialog">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Course Drop</h4>
					</div>
					<div class="modal-body">
						<p>Are you sure you want to drop this?</p>
						<p><b><span id="mod-mnem"></span></b>
						<br><span id="mod-title"></span></p>
					</div>
					<div class="modal-footer">
						<button type="button" id="drop" class="btn btn-danger" data-dismiss="modal">Drop</button>
					</div>
				</div>
			</div>
		</div> 
-->

	<div id="noCourse" hidden>
		<center><p id="msg"> You are not currently enrolled in any courses. 
			<br>Go to <a href="/searchResult.html">Class Search</a> to find courses.
				</p>
		</center>
	</div>

</div>
</body>




</html>
