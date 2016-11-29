<!-- This also uses chronological() function from MySQL -->

<?php
session_start();

//Global variables
$computing_id;

// Check if session logged in
if(!$_SESSION['computing_id'])  
{  
    header("Location: login.php"); 
}
else {
  $computing_id = $_SESSION['computing_id'];

  function build_table($computing_id) {

    $db = new mysqli('localhost', 'username', 'password', 'asist2');
    if ($db->connect_error):
     die ("Could not connect to db: " . $db->connect_error);
    endif;

    // Sort by waitlist_timestamp, e.g. when the course was added
    // Probably should be sorted by semester/alphabetically by default but haven't found best way to do this yet
    $query = "SELECT * FROM (student_section INNER JOIN (section INNER JOIN course ON section.course_number = course.course_number AND section.dept_mnemonic = course.dept_mnemonic) " . 
        "ON student_section.section_key = section.section_key) " . 
        "WHERE student_id = '"  . $computing_id . "' " .  
        " ORDER BY chronological(semester) ASC; ";
        // Returns a query containing the following data, where in [index range]
        // [0-4] section_key, student_id, waitlist, completion_status, grade, 
        // [5-9] section_id, section_key, dept_mnemonic, course_number, building_id,
        // [10-14] room, section_title, time_id, semester, capacity,
        // [15-19] total_students, days, type, enroll-status, dept_mnemonic,
        // [20-24] course_number, course_title, description, units, school_id

    $result = $db->query($query) or die ("Invalid: " . $db->error);
    if ($result->num_rows > 0) {
      // From these courses, get the ones for the current semester
      $rows = $result->fetch_all();
      //echo json_encode($rows);

      foreach ($rows as $row) {
        $mnemonic = $row[7];
        $course_num = $row[8];
        $status = $row[3];
        $grade = $row[4];
        $semester = $row[13];
        $course_title = $row[21];
        $credits = $row[23];


        if ($grade == '?') {
          $grade = "N/A";
          $status = 0;
        }


        //echo $x . "<br>";       
        // echo $section . " " . $mnemonic . " " . $course_num . " " . $wailist_pos . " " . $building_id ." " . $room . " " . $time_id . " " . $days . "<br><br>"; 
        
        //BUILD TABLE!!! This gets placed in the HTML
        // if ($semester) {
        //   echo '<div class="h-divider">';
        // }
        echo '<tr>';
        echo '<td>' . $mnemonic . " " .$course_num . "</td>";
        echo '<td>' . $course_title . "</td>";
        echo '<td>' . ucfirst($semester) . '</td>';
        echo '<td>' . $grade . '</td>';
        echo '<td>' . $credits . '</td>';
        echo '<td>' . status_to_gliph($status) . '</td>';
        echo '</tr>';

      } // end foreach

    }// endif
  }

  function status_to_gliph($status) {
      if ($status == 0) {
        return '<span class="glyphicon glyphicon-hourglass">';
      } else if ($status == 1) {
        return '<span class="glyphicon glyphicon-ok">';
      }
  }

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
  <script type="text/javascript">
    $(document).ready(function() {
        $['.tbody']
    });
  </script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
  integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <link rel="Stylesheet" href="style.css">
</head>
<body>
<?php require ('header.php'); ?>
	<center><h3>Course History</h3></center><br/>

  <!-- Hover over to see status symbol key -->
  <div style="text-align: right;">
    <a href="#" data-toggle="tooltip" data-placement="top" data-html="true" 
    data-title="<table width='100'>
    <tr><td>&#10003;</td><td>Completed</td></tr>
    <tr><td>&#8987;</td><td>In Progress</td></tr>
  </table>">What's "status?"</a>

    <script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();   
    });
    </script>
  </div>

  <!-- Build Course History Table -->
  <table class="table table-sm table-hover">
  	<thead>
  		<tr>
	  		<td> Course </span></td>
	  		<td> Description </td>
	  		<td> Term </span></td>
	  		<td> Grade </td>
	  		<td> Credits </td>
	  		<td> Status </span></td>
  		</tr>
  	</thead>
  	<tbody>
      <?php build_table($computing_id); ?> 

<!--   		<tr>
  				<td>STS 1500</td>
				<td>Sci Tech and Contemporary Issues</td>
				<td>Fall 2016</td>
				<td>A</td>
				<td>3</td>
				<td><span class="glyphicon glyphicon-ok"></span></td>
  		</tr>
  		<tr>
  				<td>CS 1010</td>
				<td>Introduction to Information Technology</td>
				<td>Fall 2016</td>
				<td></td>
				<td>3</td>
				<td><span class="glyphicon glyphicon-hourglass"></span></td>
  		</tr>
  		<tr>
  				<td>CLAS 1010</td>
				<td>Some Random College Course</td>
				<td>Fall 2016</td>
				<td></td>
				<td>3</td>
				<td><span class="glyphicon glyphicon-hourglass"></td>
  		</tr> -->
  	</tbody>
  </table>
 
</body>
</html>