<!-- This php uses a SQL function. It's called $query2 below, I don't know why but it won't run when I called it here, so just manually run it in phpmyadmin-->

<?php
session_start();

//Global variables
$computing_id;
$major = array();

// Check if session logged in
if(!$_SESSION['computing_id'])  
{  
    header("Location: login.php"); 
} else {

  $computing_id = $_SESSION['computing_id'];


  function build_table($computing_id, $major) {
	$db = new mysqli('localhost', 'username', 'password', 'asist2');
    if ($db->connect_error):
     die ("Could not connect to db: " . $db->connect_error);
    endif;
/*
    $query1 = "DROP FUNCTION IF EXISTS chronological;";

	$result = $db->query($query1) or die ("Invalid: " . $db->error);

	$query2 = //"DELIMITER $$ 
	CREATE FUNCTION chronological (semester varchar(20)) RETURNS varchar(5) NOT DETERMINISTIC
	BEGIN 
    DECLARE year varchar(4); 
    DECLARE semVal varchar(15); 
    DECLARE newSem varchar(5);
    SET year = (SELECT RIGHT(semester, 4));  
    SET semVal = SUBSTRING(semester, 1, LENGTH(semester)-5);

    IF (semVal = 'winter') THEN
    	SET newSem =  CONCAT(year,'A');
    ELSEIF (semVal ='spring') THEN
    	SET newSem = CONCAT(year,'B');
    ELSEIF (semVal ='summer') THEN
    	SET newSem = CONCAT(year,'C');
    ELSEIF (semVal = 'fall') THEN
    	SET newSem = CONCAT(year,'D');
    END IF;
    
	 RETURN newSem;
	    
	END";

	$result = $db->query($query2) or die ("Invalid: " . $db->error);
*/

	$query3 = "SELECT * FROM (student_section INNER JOIN (section INNER JOIN course ON section.course_number = course.course_number AND section.dept_mnemonic = course.dept_mnemonic) " . 
	"ON section.section_key = student_section.section_key) " . 
	"WHERE student_id = '"  . $computing_id . "' " . 
	"GROUP BY student_section.section_key ORDER BY chronological(semester) ASC;";

	$result = $db->query($query3) or die ("Invalid: " . $db->error);

	// Keep track
	$currSem = "";
	$curCred = 0.0;
	$curGrd = 0.0;

	$cumCred = 0.0;
	$cumGrd = 0.0;

	$majorCred = 0.0;
	$majorGrd = 0.0;

	// Build table by row read
	echo "<table width=100%>";
	while ($row = $result->fetch_assoc()) {
		if ($currSem == "") {
			$currSem = $row['semester'];
			echo "<tr><td colspan='6'><center><b>" . ucfirst($currSem) . "</b></center></td></tr>";
		}
		if ($currSem != $row['semester']) {

			echo "<tr><td> Current Credits </td><td> " . number_format((float)$curCred, 1, '.','')
			. "</td><td> Grade Points</td><td> " . $curGrd 
			. "</td><td> GPA </td><td>" . ($curGrd/$curCred);

			echo "<tr><td> Cummulative Credits </td><td> " . number_format((float)$cumCred, 1, '.','')
			. "</td><td> Grade Points</td><td> " . $cumGrd 
			. "</td><td> GPA </td><td>" . ($cumGrd/$cumCred)
			. "</td></tr><tr><td colspan='6'> <span style='visibility:hidden'>take up space</span> </td></tr>";

			// reset values
			$curGpa = 0.0; $curGrd = 0.0; $curCred = 0.0;
			// Print new semester header
			$currSem = $row['semester'];
			echo "<tr><td colspan='6'><center><b>" . ucfirst($currSem) . "<b/></center></td></tr>";
		}
		echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td></tr>";

		echo "<tr valign='top'><td width='25%'> " . $row['dept_mnemonic'] 
			. " </td><td width='50px'>" . $row['course_number'] 
			. "</td><td colspan='2'>" . $row['course_title']
			. "</td><td width='50px'>" . (($row['grade'] != "?") ? $row['grade'] : " ")
			. "</td><td width='50px'>" . number_format((float)$row['units'], 1, '.', '')
			. "</td></tr>";


			// Only factor in grades that count towards credits/gpa
			if ($row['grade'] != "?" && $row['grade'] != "F") {
				$curCred += (float) $row['units'];
				$cumCred += (float) $row['units'];
				$cumGrd += (letterToGP($row['grade']) * $row['units']);
				$curGrd += (letterToGP($row['grade']) * $row['units']);
			}

			//calculate major gpa
			if ( in_array($row['dept_mnemonic'], $major) && $row['grade'] != "?") {
				$majorCred += (float) $row['units'];
				$majorGrd += (letterToGP($row['grade']) * $row['units']);
			}

		//echo json_encode($row);
		//echo "<br>";
	}

	echo "<tr><td colspan='6'> <span style='visibility:hidden'>.</span> </td></tr>";
	echo "<tr><td>Cumulative GPA: </td><td>" . $cumGrd/$cumCred . "</td><td id='majorGPA'>Major GPA: </td><td>" . $majorGrd/$majorCred . "</td></tr>";
	echo "</table>";
	
	}
}

// source: http://www.virginia.edu/registrar/acadrecord.html
function letterToGP($letter) {
	if ( strcmp($letter,"A+") == 0 || strcmp($letter,"A") == 0) {
		return 4.0;
	} elseif ( strcmp($letter,"A-") == 0) {
		return 3.7;
	} elseif ( strcmp($letter,"B+") == 0) {
		return 3.3;
	} elseif ( strcmp($letter,"B") == 0) {
		return 3.0;
	} elseif ( strcmp($letter,"B-") == 0) {
		return 2.7;
	} elseif ( strcmp($letter,"C+") == 0) {
		return 2.3;
	} elseif ( strcmp($letter,"C") == 0) {
		return 2.0;
	} elseif ( strcmp($letter,"C-") == 0) {
		return 1.7;
	} elseif ( strcmp($letter,"D+") == 0) {
		return 1.3;
	} elseif ( strcmp($letter,"D") == 0) {
		return 1.0;
	} elseif ( strcmp($letter,"D-") == 0) {
		return 0.7;
	} else {
		return 0.0;
	}
}

function transcriptHeader($computing_id) {
 $db = new mysqli('localhost', 'username', 'password', 'asist2');
    if ($db->connect_error):
     die ("Could not connect to db: " . $db->connect_error);
    endif;

 $query = "SELECT * FROM (student JOIN school ON student.school_id = school.school_id) WHERE computing_id = '" . $computing_id . "';";
 $result = $db->query($query) or die ("Invalid: " . $db->error);
 $row = $result->fetch_assoc();

 echo "<h3>" . $row['first_name'] . " " . $row['middle_name'] . " " . $row['last_name'] . "</h3>";
 echo "<h4>" . $row['school_name'] . " " . $row['career'] . "</h4>";

}

function fetchMajorMinor($computing_id, $major) {
 $db = new mysqli('localhost', 'username', 'password', 'asist2');
    if ($db->connect_error):
     die ("Could not connect to db: " . $db->connect_error);
    endif;

 $query = "SELECT * FROM (student_department JOIN department ON student_department.dept_mnemonic = department.dept_mnemonic) WHERE computing_id = '" . $computing_id . "' ORDER BY major DESC;";
 $result = $db->query($query) or die ("Invalid: " . $db->error);
 while ( $row = $result->fetch_assoc() ) {

 	echo "<br>" . (($row['major'] == 1) ? 'Majoring in ' : 'Minoring in ') . $row['name'];
 	if ( $row['major'] == 1 ) { 
 		array_push($major, $row['dept_mnemonic']);
 	}
 }

 return $major;

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
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
  integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <link rel="Stylesheet" href="style.css">
</head>
<body>
<?php require ('header.php'); ?>
	<center><h3>Sample Transcript Report</h3></center><br/>
<!-- Hover over to see explanation -->
  <div style="text-align: right;">
    <a href="#" data-toggle="tooltip" data-placement="top" data-html="true" 
    data-title="This page will display all the information that would be present on your transcript.
     <br><br>However, note that in no way does this replace an official transcript.
     Talk to your school's registrar office to learn more. ">What am I seeing?</a>
    <script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();   
    });
    </script>
   </div>
   <!-- Build Transcript page -->
   <div id="transcript" style="width:80%; margin: 0 auto; display: block; padding: 60px;">
   	<style>
   		#transcript {font-family: Georgia, serif; padding: 20px;}
   		#transcript:hover {background-color: white;}
   	</style>
   	<center>
   		<?php transcriptHeader($computing_id); 
   			$major = fetchMajorMinor($computing_id, $major); // Stores majors into an array
   			?>
   	</center>
   	<br/>
   	<center>
   <?php build_table($computing_id, $major); ?>
	</center>
   </div>
</body>
</html>