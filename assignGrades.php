<?php
session_start();
if(!$_SESSION['computing_id'])  
{  
header("Location: login.php");//redirect to login page to secure the welcome page without login access.  
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
	<div class="container" id="page-wrap">

		<div class="header">
			<div style="float:left">
				<img id="logo" src="logo.png" width="100" height="100">
			</div>
			<div style="float:clear; display:inline-block; margin:1%;">
				<h3><i> aSISt </i></h3>
			</div>
		</div>

		<?php
//--------------------------------------------------------------------------
// Fetch data from mysql database
//--------------------------------------------------------------------------
		$db = new mysqli('localhost', 'username', 'password', 'asist2');
		if ($db->connect_error):
			die ("Could not connect to db: " . $db->connect_error);
		endif;

		function getPostArray($section, $students){
			$postArray=[];
			array_push($postArray,array("section"=>$section, "students"=>$students));
			print_r( $postArray);
			return $postArray;
		}

		?>


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

<?php

$session_id = $_SESSION['computing_id'];

$classes=[];
$titles=[];
$course_numbers=[];
$students=[];
$sections=[];

$classes_query = "select distinct section.section_key, section_id, dept_mnemonic, course_number, description from section inner join instructor_section on instructor_section.section_key=section.section_key and instructor_section.instructor_id='$session_id' and section_id<100 and semester='fall 2016'";
$classes_result = $db->query($classes_query);
if ($classes_result->num_rows>0){
	while ($row = $classes_result->fetch_array()){
		$class_array=array("class"=> $row["dept_mnemonic"]." ".$row["course_number"]." - ".$row["section_id"], "section"=>$row["section_key"]);
		array_push($classes, $class_array);
		$dept_mnemonic= $row["dept_mnemonic"];
		array_push($course_numbers,$row["course_number"]);
		array_push($sections,$row["section_key"]);
	}

	foreach($course_numbers as $course_number){
		$course_title_query = "select course_title from course as d join (select course_number, dept_mnemonic from section where semester='Fall 2016' and dept_mnemonic = '$dept_mnemonic' and course_number=$course_number) as r on d.course_number=r.course_number and d.dept_mnemonic=r.dept_mnemonic";
		$course_title = $db->query($course_title_query)->fetch_array()["course_title"];
		array_push($titles,$course_title);


		$student_query = "select * from student_section as d join (select section_key, course_number, dept_mnemonic from section where semester='Fall 2016' and dept_mnemonic = '$dept_mnemonic' and course_number=$course_number) as r on d.section_key=r.section_key";
		$student_result = $db->query($student_query);
		if ($student_result->num_rows>0){
			while ($row = $student_result->fetch_array()){
				$student_array=$row["student_id"];
				$section_key = $row["section_key"];
				$grade = $row["grade"];
				array_push($students, array("section"=>$section_key, "students"=>$student_array, "grade"=>$grade));
			}
		}


	}

} 


?>
<br/>
<center><h3>Assign Grades</h3></center><br/>

<?php if (isset($_SESSION["EXCEPTION"])) { ?>
<?php
$exception = $_SESSION["EXCEPTION"];

?><div style = "border: 1px solid #E7E7E7; font-size: 20px; border-radius: 5px; padding: 10px; background-color: #F8F8F8; color: #777777;">

<?php echo $_SESSION["EXCEPTION"]; unset($_SESSION["EXCEPTION"]); ?>
</div>
<?php } ?>


<table class="table table-striped" id="table">
	<thead>
		<tr>
			<th style="width:15%">Course Mnemonic</th>
			<th>Course Title</th>

			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php 
		$index=0;
		foreach($classes as $class){ ?>
		<tr>
			<td><?php 
			echo $class["class"]; ?>
		</td> 
		<td><?php
		echo $titles[$index]; 
		echo "<br>";
		?></td>
		<?php 
		foreach($students as $student){
			if ($student["section"]!=$class["section"]){ ?>
			<td><button type="button" class="btn btn-info btn-circle.btn-lg" disabled
				data-toggle="collapse" data-target=<?php echo "#demo".$class["section"]?> class="accordion-toggle"> Assign</button>
			</td> 
			<?php 
			break;
		} else {
			?>

			<td><button type="button" class="btn btn-info btn-circle.btn-lg"
				data-toggle="collapse" data-target=<?php echo "#demo".$class["section"]?> class="accordion-toggle"> Assign</button>
			</td> 
			<?php 
			break;
		}
	} ?>

</tr>
<tr>
	<td colspan="4" class="hiddenRow">
		<div class="accordian-body collapse" id=<?php echo "demo".$class["section"] ?>>
			<table class="table table-bordered" style="text-align:center;"> 
				<thead>
					<th style="text-align:center;">Student</th>
					<th style="text-align:center;">Grade</th>
					<th></th>

				</thead>

				<tbody>
					<tr>
						<form method="post" action="grade.php">
							<?php 


							foreach($students as $student){

								if ($student["section"]==$class["section"]){
									?>
									<tr>
										<td> <?php 
										echo $student["students"]; 

										?> </td>

										<td>
											<input type="hidden" name="section_key[]" value=<?php echo $class["section"]; ?> >
											<input type="hidden" name="student[]" value=<?php echo $student["students"]; ?> >

											<?php if ($student["grade"]!="?"){ ?>
											<input type="text" name="grade[]" value= <?php echo $student["grade"]; ?> >
											<?php } else { ?>
											<input type="text" name="grade[]" ?> 
											<?php } ?>
										</td>

									</td> 

								</td>
								</tr> <?php
							}
						}

						?>
						<tr>
							<td></td>
							<td><button type="submit" class="btn btn-block btn-info btn-circle.btn-lg"> Submit</button>
							</tr>

						</form>	

					</tr>
				</tbody>


			</table>
		</div>
	</td>
</tr>

<?php 
$index++;

}


?>

</tbody>
</table>

</div>
</div>
</body>
</html>
