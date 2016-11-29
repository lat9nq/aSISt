<?php

function brk($whatever) {
	echo "<br>" . $whatever . "<br>";
}

function grade_translator($grade, $units) {
	$grade_map = array(
		"A+" => 4.0, "A" => 4.0, "A-" => 3.7, "B+" => 3.3, "B" => 3.0,
		"B-" => 2.7, "C+" => 2.3, "C" => 2.0, "C-" => 1.7, "D+" => 1.3,
		"D" => 1.0, "D-" => 0.7, "F" => 0.0
	);
	return $grade_map[$grade] * $units;
}


session_start();
if(!$_SESSION['computing_id'])  
{  
    header("Location: login.php");//redirect to login page to secure the welcome page without login access.  
  }  
  ?>
  <!DOCTYPE HTML>
  <html>
  <head>
    <title>aSISt</title>
    
    <style type="text/css">
    
    table { border: none; border-collapse: collapse;}
    table td { border-left: 1px solid #000; }
    table td:first-child { border-left: none; } 

    </style>
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" 

    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-

    awesome.min.css">
    <link rel="stylesheet" 

    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 

    crossorigin="anonymous">
    <link rel="Stylesheet" href="style.css">
    
    <style type="text/css">

    </style>
  </head>
  <body>
   <?php require('header.php'); ?>

<?php
$db = new mysqli('localhost', 'username', 'password', 'asist2');
if ($db->connect_error):
 die ("Could not connect to db: " . $db->connect_error);
endif;

$session_id = $_SESSION['computing_id'];

//student view
if (!isset($_SESSION['instructor'])){
  $query = "select * from student where computing_id='$session_id'";
  $result = $db->query($query);
  if ($result->num_rows>0){
    $row = $result->fetch_array();
    $computing_id = $row["computing_id"];
    $name = $row["first_name"] ." ". $row["last_name"];
    $dob = $row["date_of_birth"];
    $phone = $row["primary_phone"];
    $permanent_address = $row["permanent_home_address"];
    $mailing_address = $row["current_mailing_address"];
    $year = $row["year"];
    $career= $row["career"];
    $school_id = $row["school_id"];
    $school_result = $db->query("select school_name from school where school_id = $school_id");
    $school_name = $school_result->fetch_array()["school_name"];
  }
  
  $query = "SELECT student_section.grade, course.units FROM student_section, section, course WHERE student_id = '$session_id' AND grade <> '?' " .
  "AND student_section.section_key = section.section_key " .
  "AND section.course_number = course.course_number AND section.dept_mnemonic = course.dept_mnemonic;";
  $result = $db->query($query);
  
  $total_units = 0;
  $acquired_units = 0;
  
  while ($res = $result->fetch_row()) {
  	$grade = $res[0];
  	$units = $res[1];
  	$total_units += $units;
  	$acquired_units += grade_translator($grade, $units);
  }
  
  $gpa = "No GPA";
  if ($total_units > 0) {
  	$gpa = $acquired_units / $total_units;
  }

  //major/minor
  $query = "select department.name, student_department.major, department.dept_mnemonic from student_department join department where student_department.computing_id='$session_id' and student_department.dept_mnemonic=department.dept_mnemonic";
  $result = $db->query($query);
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_array()) {
      $major_num= $row["major"];
      $minor="";
      $dept_name = $row["name"];
      if ($major_num==1){
        $major = $dept_name;
        $major_mnemonic = $row["dept_mnemonic"];
      }
      if ($major_num==0) {
        $minor = $dept_name;
      }

    }
  }
  
  $query = "SELECT student_section.grade, course.units FROM student_section, section, course " .
  "WHERE student_id = '$session_id' AND grade <> '?' " .
  "AND student_section.section_key = section.section_key " .
  "AND section.course_number = course.course_number " .
  "AND section.dept_mnemonic = course.dept_mnemonic " .
  "AND section.dept_mnemonic = '$major_mnemonic';";
    
  $result = $db->query($query);
  
  $total_units = 0;
  $acquired_units = 0;
  
  while ($res = $result->fetch_row()) {
  	$grade = $res[0];
  	$units = $res[1];
  	$total_units += $units;
  	$acquired_units += grade_translator($grade, $units);
  }
  
  $major_gpa = "No Major GPA";
  if ($total_units > 0) {
  	$major_gpa = $acquired_units / $total_units;
  }
  
  $query = "SELECT student_section.grade, course.dept_mnemonic, course.course_number, course.description, section.semester " .
  "FROM student_section, section, course WHERE student_id = '$session_id' " .
  "AND student_section.section_key = section.section_key " .
  "AND section.course_number = course.course_number AND section.dept_mnemonic = course.dept_mnemonic " .
  "ORDER BY course.dept_mnemonic, course.course_number ASC;";
    
  $result = $db->query($query);
  $course_array = array();
  while ($res = $result->fetch_row()) {
  	array_push(
  		$course_array, array(
  			"grade" => ($res[0] == "?") ? "Pending" : $res[0], "dept_mnemonic" => $res[1],
  			"course_number" => $res[2], "description" => $res[3],
  			"semester" => ucfirst($res[4])
  		)
  	);
  }

//advisor
  $query = "select * from `instructor` INNER JOIN `advisor` on instructor_id = computing_id and student_id='$session_id'";
  $result = $db->query($query);
  if ($result->num_rows>0){
    $row = $result->fetch_array();
    $advisor= $row["first_name"]." ".$row["last_name"];
  } 
}

//instructor view
else {

  $query = "select * from instructor where computing_id='$session_id'";
  $result = $db->query($query);
  if ($result->num_rows>0){
    $row = $result->fetch_array();
    $computing_id = $row["computing_id"];
    $name = $row["first_name"] ." ". $row["last_name"];
    $dept_id = $row["dept_mnemonic"];
    $dept_result = $db->query("select name from department where dept_mnemonic = '$dept_id'");
    $dept_name = $dept_result->fetch_array()["name"];
  }

  //advisees
  $advisees = [];
  $classes = [];
  $query = "select * from student INNER JOIN advisor on student_id = computing_id and instructor_id='$session_id'";
  $result = $db->query($query);
  if ($result->num_rows>0){
    while ($row = $result->fetch_array()){
      array_push($advisees,$row["first_name"]." ".$row["last_name"]);
    }
  } else {
    array_push($advisees,"None");
  }

  $classes_query = "select distinct dept_mnemonic, course_number, semester from section inner join instructor_section on instructor_section.section_key=section.section_key and instructor_section.instructor_id='$session_id' and semester='Fall 2016'";
  $classes_result = $db->query($classes_query);
  if ($classes_result->num_rows>0){
    while ($row = $classes_result->fetch_array()){
      array_push($classes,$row["dept_mnemonic"]." ".$row["course_number"]);
    }
  } 

}
?>

<br/>
<center><h3>Personal Information</h3></center><br/>

<h4><center><?php echo $name ?></center></h4>

<table class="table table-bordered"> 
  <thead>
    <th colspan='2' width="50%"><center>Contact</center></th>
    <th colspan='2'><center>Academic</center></th>
  </thead>
</tr>

<?php
//student view
if (!isset($_SESSION['instructor'])){
  ?>

  <tr>
    <td><b>Primary Phone Number</b></td>
    <td><?php echo $phone?></td>   
    <td><b>Year</b></td>
    <td><?php echo $year ?></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td><b>Date of Birth</b></td>
    <td><?php echo $dob?></td>   
    <td><b>Career</b></td>
    <td><?php echo $career ?></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td><b>Email Address</b></td>
    <td><?php echo $computing_id?>@virginia.edu </td>   
    <td><b>School</b></td>
    <td><?php echo $school_name ?></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td><b>Permanent Address</b></td>
    <td><?php echo $permanent_address?></td>   
    <td><b>Major</b></td>
    <td><?php echo $major ?></td>
  </tr>
  <tr>
    <td><b>Current Mailing Address</b></td>
    <td><?php echo $mailing_address?></td>   
    <td><b> Minor </b></td>
    <td> <?php 
    if ($minor==""){
      echo "None";
    } else {
      echo $minor;
    }
    ?> </td>
  </tr>
  <tr>   
    <td></td>
    <td></td>
    <td><b> Advisor </b></td>
    <td> <?php echo $advisor ?></td>
  </tr>
  <tr>   
    <td></td>
    <td></td>
    <td><b> GPA </b></td>
    <td> <?php echo number_format((float) $gpa, 2, '.', ''); ?></td>
  </tr>
  <tr>   
    <td></td>
    <td></td>
    <td><b> Major GPA </b></td>
    <td> <?php echo number_format((float) $major_gpa, 2, '.', ''); ?></td>
  </tr>
  
  <?php } else { 

    //instructor view

    ?>
    <tr>
      <td><b>Email Address</b></td>
      <td><?php echo $computing_id?>@virginia.edu</td>   
      <td><b>Department</b></td>
      <td><?php echo $dept_name ?></td>
      <td></td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td><b>Advisees</b></td>
      <td>
        <?php 
        foreach($advisees as $advisee){
          echo $advisee;
          echo "<br>";
        }
        ?>
      </td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td><b>Current Classes</b></td>
      <td>
        <?php
        foreach($classes as $class){
          echo $class;
          echo "<br>";
        }
        ?>
      </td>
    </tr>
    <?php
  }
  ?>
  

</table>
<?php if (!isset($_SESSION['instructor'])){
  ?>
<center><h4>Course History</h4></center>

<center><table class="table table-bordered" style = "width: 50%;"> 
  <thead>
    <th><center>Course</center></th>
    <th><center>Semester</center></th>
    <th><center>Grade</center></th>
  </thead>
<?php foreach ($course_array as $course) { ?>
	<tr>
		<td><?php echo $course["dept_mnemonic"] . " " . $course["course_number"]; ?></td>
		<td><?php echo $course["semester"]; ?></td>
		<td><?php echo $course["grade"]; ?></td>
	</tr>
<?php } ?>
</table></center>
<?php } ?>