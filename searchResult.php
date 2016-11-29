<?php
session_start();
if(!$_SESSION['computing_id'])  
{  
    header("Location: login.php");//redirect to login page to secure the welcome page without login access.  
  }  
  ?>
  
  <?php
  		function no_sections($array) {
  			foreach ($array as $course) {
  				if (count($course["sections"]) > 0) {
  					return false;
  				}
  			}
  			return true;
  		}
  		
  		function disassemble($array) {
  			$sectoids = array();
  			foreach ($array as $section) {
  				if (!isset($sectoids[$section["course_component"]])) {
  					$sectoids[$section["course_component"]] = array();
  				}
  				array_push($sectoids[$section["course_component"]], $section);
  			}
  			return $sectoids;
  		}
  	?>
  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="Stylesheet" href="style.css">

    <title>aSISt</title>

  </head>
  <body>

    <div class="container-fluid" style="margin-right:0%;margin-left:3%">
     <div class="row">
      <div class="col-md-2" id="search">
       <center><h3>Search</h3></center><br>
       <form name="course_search_form" action="searchResult.php" method="post">
        <div class="form-group">
        <?php
        	$dept_input = "";
        	if (isset($_POST["dept_input"])) {
        		$dept_input = $_POST["dept_input"];
        	}
        	$course_input = "";
        	if (isset($_POST["course_input"])) {
        		$course_input = $_POST["course_input"];
        	}
        	$year = "2016";
        	$season = "Fall";
        	//print_r($_POST);
        	if (isset($_POST["year_select_input"])) {
        		$year = $_POST["year_select_input"];
        	}
        	if (isset($_POST["season_select_input"])) {
        		$season = $_POST["season_select_input"];
        	}
        ?>
        	
         <input type="text" class="form-control" placeholder="Department" name="dept_input" value = "<?php echo $dept_input; ?>"/>
       </div>
       <div class="form-group">
         <input type="text" class="form-control" placeholder="Course Number" name="course_input" value = "<?php echo $course_input; ?>"/>
       </div>
       <div class="form-group">
					<!--
						<input type="text" class="form-control" placeholder="Semester" name="semester_input" />
					-->
					<select name="season_select_input">
						<option value="fall" <?php echo ($season == 'fall') ? 'selected' : ''; ?>>Fall</option>
						<option value="spring" <?php echo ($season == 'spring') ? 'selected' : ''; ?>>Spring</option>
					</select>
					<select name="year_select_input">
						<option value="2016" <?php echo ($year == '2016') ? 'selected' : ''; ?>>2016</option>
						<option value="2015" <?php echo ($year == '2015') ? 'selected' : ''; ?>>2015</option>
					</select>
				</div>
				<button type="submit" class="btn btn-success btn-block">Search</button>
			</form>
		</div>

    <!-- header and nav bar -->
    <div class="col-md-2" id="page-wrap-search">

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

          <li class="dropdown">
          <a href="/asist/classSchedule.php" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true">
              My Courses <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              <li><a href="/asist/classSchedule.php">Current Semester</a></li>
              <li><a href="/asist/courseHistory.php">Course History</a></li>
              <li class="divider"></li>
              <li><a href="/asist/transcript.php">Transcript Summary</a></li>
            </ul>
          </li>
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

    <!-- end nav bar -->

    
<?php if (isset($_SESSION["EXCEPTION"])) { ?>
	<?php
		$exception = $_SESSION["EXCEPTION"];
		if (strpos($exception, "Error") != false or strpos($exception, "Waitlisted" != false)) { ?>
  			<div style = "font-size: 20px; border-radius: 5px; padding: 10px; background-color: #FF5151; color: white;">
  		<?php } else { ?>
  			<div style = "border: 1px solid #E7E7E7; font-size: 20px; border-radius: 5px; padding: 10px; background-color: #F8F8F8; color: #777777;">
  		<?php } ?>
  		<?php echo $_SESSION["EXCEPTION"]; unset($_SESSION["EXCEPTION"]); ?>
  	</div>
<?php } ?>


    


	<?php
    	$db = new mysqli('localhost', 'username', 'password', 'asist2');
    	if ($db->connect_error):
    		die ("Could not connect to db: " . $db->connect_error);
    	endif;

// assuming that $dept and $course_number gotten from search
		if(!empty($_POST["dept_input"])) {
    		$dept = $_POST["dept_input"];
      	} else {
    		$dept= "";
      	}
      	if (!empty($_POST["course_input"])){
      	 	$course_number = $_POST["course_input"];
     	} else {
      		$course_number="";
    	}
    	if (!empty($_POST["season_select_input"])){
     		$semester = $_POST["season_select_input"] . ' ' . $_POST["year_select_input"];
   		} else {
    		$semester="Fall 2016";
  		}

  		$courses = array();

  		if ($course_number==""){
    		//the search query was only for department, but nothing for course_number and semester were entered
        	//~ $query = "select course_number from course where dept_mnemonic='$dept'";
    		$query = "select distinct d.course_number from course as d join (select course_number, dept_mnemonic from section where semester='$semester' and dept_mnemonic = '$dept') as r on d.course_number=r.course_number and d.dept_mnemonic=r.dept_mnemonic";
    		$result = $db->query($query);
    		// get all the courses which correspond to this query
    		while ($course_row = $result->fetch_array()){
      			$course = $course_row["course_number"];
      			array_push($courses,$course);
    		}
  		} else {
    		array_push($courses,$course_number);
  		}

    	//for the Learn More toggle
  		$index=1;
		$ultimate_array = array();
		// loop over all courses and get the data for their sections
  		foreach($courses as $course_number){
   			$sections = array();

			// get data for each section in the current course
   			$query = "select * from section where semester='$semester' and dept_mnemonic='$dept' and course_number=$course_number";
   			$result = $db->query($query);

   			$credit_query = "select units from course as d join (select course_number, dept_mnemonic from section where semester='$semester' and dept_mnemonic = '$dept' and course_number=$course_number) as r on d.course_number=r.course_number and d.dept_mnemonic=r.dept_mnemonic";
   			$credit_result = $db->query($credit_query);
   			$credits = $credit_result->fetch_array()["units"];

   			$course_title_query = "select course_title from course as d join (select course_number, dept_mnemonic from section where semester='$semester' and dept_mnemonic = '$dept' and course_number=$course_number) as r on d.course_number=r.course_number and d.dept_mnemonic=r.dept_mnemonic";
   			$course_title = $db->query($course_title_query)->fetch_array()["course_title"];

			$current_course_array = array("credits" => $credits, "course_number" => $course_number, "course_title" => $course_title,
				"dept_mnemonic" => strtoupper($dept), "sections" => array());
			
   			if ($result->num_rows > 0){
   				// here we loop over all the sections to get the various data
   				// including that referenced in other tables
    			while ($row = $result->fetch_array()){
      				$section = $row["section_id"];
      				$section_key = $row["section_key"];
      				$course_component = $row["description"]." (".$credits.")";
      				$status_num = $row["status"];
        			//0 is closed, 1 is open, 2 is waitlisted
        			//this is a very good database strategy
      				if ($status_num==0){
        				$status="Closed";
      				} else if ($status_num==1){
        				$status = "Open";
      				} else if ($status_num==2){
        				$status = "Waitlisted";
      				}
      				$enrollment = $row["total_students"]."/".$row["capacity"];

					// get the instructor id, so you can then use that to find the instructor's name
      				$instructor_id_query = "select instructor_id from instructor_section where section_key = $section_key;";
      				$instructor_id = $db->query($instructor_id_query)->fetch_array()["instructor_id"];
      
      				// here we find the instructor's first and last names
      				// and concatenate then in variable $instructor (unique to this particular section)
      				$instructor_name_query = "select first_name,last_name from instructor where computing_id = '$instructor_id'";
      				$instructor_row = $db->query($instructor_name_query)->fetch_array();
      				$instructor = $instructor_row["first_name"]." ".$instructor_row["last_name"];

					// select the timeslot for this section
      				$time_id = $row["time_id"];
      				$time_query = "select start_time,end_time from timeslot where time_id=$time_id";
      				$time_row = $db->query($time_query)->fetch_array();
      				$time = $row["days"]." ".substr($time_row["start_time"],0,-3)."-".substr($time_row["end_time"],0,-3);
		
					// select the building for this section
      				$building_id = $row["building_id"];
      				$building_query = "select building_name from building where building_id = $building_id";
      				$building = $db->query($building_query)->fetch_array()["building_name"]." ".$row["room"];
 					$section_array = array("section" => $section, "section_key" => $section_key, "course_component" => $course_component, "status" => $status, "enrollment" => $enrollment, "instructor" => $instructor, "time" => $time, "building" => $building);
      				array_push($current_course_array["sections"], $section_array);
      				//this is what used to be here, the above code is more maintainable
      				//array_push($sections, array($section, $course_component, $status, $enrollment, $instructor, $time, $building) );
    			}
  			}
  			array_push($ultimate_array, $current_course_array);
  		}
	?>
	<!-- 	This whole thing used to be one giant for loop; to accommodate error handling
			I split it into two parts; the part above is for gathering the data, and the
			one below is for displaying it. This allows the page to look at all the data
			that was gathered and tell whether or not we actually got results. If not,
			it prints an annoying message. The CSS didn't work after I made this change, 
			so I fixed it but there may be other problems. I also improved the readability
			of the code by indexing the arrays by strings rather than integers, for
			example $section["semester"] holds the semester value, rather than $section[2]
			which no one can remember. ==Rowyn
	-->
	
	<?php if (count($ultimate_array) == 0 || no_sections($ultimate_array)) { ?>
  			<p style = "text-align: left; font-size: 30px;">
  				<center><h3>No search results.</h3></center>
  			</p>
  		<br/>
  	<?php } else { ?>
    		<center><h3>Search Results</h3></center>
			
  			<table class="table table-striped">
  			<?php foreach ($ultimate_array as $current_course) { ?>
  				<!--
      			<thead>
        			<tr>
          				<th style="width:20%">Course Number</th>
          				<th>Course Title</th>
          				<th></th>
          				<th></th>
        			</tr>
      			</thead>
      			-->
      			
      			<tbody>
  					<tr>
    					<td style = "width: 10em"><?php echo $current_course["dept_mnemonic"]." ".$current_course["course_number"];?></td>
    					<td style = "width: 40em;"><?php echo $current_course["course_title"]; ?></td>
    					<td style = "width: 5em;">
    						<!-- 
    							important buttons
    						-->
      						<button type="button" class="btn btn-info btn-circle.btn-lg"
      							data-toggle="collapse" data-target=<?php echo "#demo".$index ?> class="accordion-toggle"> Learn More</button>
    					</td>
    					<td>
    						<?php $disabled = (strtolower($semester) == 'fall 2016') ? "" : "style='display:none'"; 
      						
                  if (!isset($_SESSION['instructor'])){ ?>
                  <button type="button" class="btn btn-success btn-circle.btn-lg" data-toggle = "modal" data-target = <?php echo "#modal" . $index?> <?php echo $disabled ?>>Add</button>
      						<?php } ?>
                  
      						<!-- giant modal -->
      						<span style="margin:auto"class="modal fade" id=<?php echo "modal" . $index ?> role="dialog">
                 				<div class="modal-dialog modal-lg">
                  					<div class="modal-content">
                  						<form method = "post" action = "enroll.php" id = <?php echo "enrollment-form" . $index?>>
                  						
                   						<div class="modal-header">
                    						<button type="button" class="close" data-dismiss="modal">&times;</button>
                    						<h4 class="modal-title">Enroll in <?php echo $current_course["dept_mnemonic"] . " " .
                    							$current_course["course_number"]; ?></h4>
                  						</div>
                  							<div class="modal-body">
                    						<div class="form-group">
                    							<?php $section_disassembly = disassemble($current_course["sections"]); ?>
                    							<input type = "hidden" name = "course" value =
                  									<?php echo $current_course["dept_mnemonic"]."?".$current_course["course_number"]."?".count($section_disassembly); ?>/>
                    							<?php foreach ($section_disassembly as $key => $value) { ?>
                      								<label for=<?php echo "#modal" . $index ?>>Select a <?php echo $key ?>:</label>
                      								<select multiple class="form-control" form = <?php echo "enrollment-form".$index ?> name = <?php echo $key; ?> id=<?php echo "modal" . $index; ?> >
                      									<?php foreach($value as $sect) { ?>
                      										<!-- 	an option to choose one of each section of a given type
                      												i.e. you need a lab and a lecture, but only one of each
                      										-->
                       										<option>
                       											<?php echo $sect["section_key"]; ?> |
                       											<?php echo $sect["section"]; ?> |
                       											<?php echo $sect["course_component"]; ?> |
                       											<?php echo $sect["instructor"]; ?> |
                       											<?php echo $sect["time"]; ?> |
                       											<?php echo $sect["building"]; ?>
                       										</option>
                       									<?php } ?>
                     								</select>
                     							<?php } ?>
                   							</div>
                 						</div>
                 							<div class="modal-footer">
                  							<!-- <input type="submit" class="btn btn-default" data-dismiss="modal">Enroll ></button> -->
                  							<input type = "submit" class="btn btn-default" value = "Enroll"/>
                						</div>
                						</form>
              						</div>
            					</div>
          					</span>
    					</td>
  					</tr>
 					<tr>
    					<td colspan="4" class="hiddenRow">
      						<div class="accordian-body collapse" id=<?php echo "demo".$index ?>>
        						<table class="table table-bordered"> 
          							<thead>
            							<tr>
              								<th>Section</th>
              								<th>Course Component</th>
              								<th>Status</th>
              								<th>Enrollment</th>
             								<th>Instructor</th>
              								<th>Meeting Times</th>
              								<th>Location</th>
              								<th></th>
            							</tr>
          							</thead>

          							<?php foreach ($current_course["sections"] as $section) { ?>
          							<tbody>
           								<tr>
											<td><?php echo $section["section"]; ?></td>
              								<td><?php echo $section["course_component"]; ?></td>
              								<td><?php echo $section["status"]; ?></td>
              								<td><?php echo $section["enrollment"]; ?></td>
              								<td><?php echo $section["instructor"]; ?></td>
              								<td><?php echo $section["time"]; ?></td>
              								<td><?php echo $section["building"]; ?></td>
        								</tr>
      								</tbody>
      <?php $index++; } ?> <!-- end foreach section -->

          </table>
        </div> 
      </td>
    </tr>
</tbody>
<?php } } ?>
</table>

</div>
</div>
</body>
</html>
