<?php
session_start();
if(!$_SESSION['computing_id'])  
{  
    header("Location: login.php");//redirect to login page to secure the welcome page without login access.  
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
         <input type="text" class="form-control" placeholder="Department" name="dept_input" />
       </div>
       <div class="form-group">
         <input type="text" class="form-control" placeholder="Course Number" name="course_input" />
       </div>
       <div class="form-group">
					<!--
						<input type="text" class="form-control" placeholder="Semester" name="semester_input" />
					-->
					<select name="season_select_input">
						<option value="fall">Fall</option>
						<option value="spring">Spring</option>
					</select>
					<select name="year_select_input">
						<option value="2016">2016</option>
						<option value="2015">2015</option>
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
            <li><a href="/asist/classSchedule.php">Class Schedule </a></li>
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

    <br/>
    <center><h3>Search Results</h3></center><br/>



    <table class="table table-striped">
      <thead>
        <tr>
          <th style="width:20%">Course Number</th>
          <th>Course Title</th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>


        <?php
        $db = new mysqli('localhost', 'username', 'password', 'asist');
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
    while ($course_row = $result->fetch_array()){
      $course = $course_row["course_number"];
      array_push($courses,$course);
    }
  } else {
    array_push($courses,$course_number);
  }

    //for the Learn More toggle
  $index=1;

  foreach($courses as $course_number){

   $sections = array();

   $query = "select * from section where semester='$semester' and dept_mnemonic='$dept' and course_number=$course_number";
   $result = $db->query($query);

   $credit_query = "select units from course as d join (select course_number, dept_mnemonic from section where semester='$semester' and dept_mnemonic = '$dept' and course_number=$course_number) as r on d.course_number=r.course_number and d.dept_mnemonic=r.dept_mnemonic";
   $credit_result = $db -> query($credit_query);
   $credits = $credit_result->fetch_array()["units"];

   $course_title_query = "select course_title from course as d join (select course_number, dept_mnemonic from section where semester='$semester' and dept_mnemonic = '$dept' and course_number=$course_number) as r on d.course_number=r.course_number and d.dept_mnemonic=r.dept_mnemonic";
   $course_title = $db->query($course_title_query)->fetch_array()["course_title"];

   if ($result->num_rows>0){
    while ($row = $result->fetch_array()){
      $section = $row["section_id"];
      $course_component = $row["description"]." (".$credits.")";
      $status_num = $row["status"];
        //0 is closed, 1 is open, 2 is waitlisted
      if ($status_num==0){
        $status="Closed";
      } else if ($status_num==1){
        $status = "Open";
      } else if ($status_num==2){
        $status = "Waitlisted";
      }
      $enrollment = $row["total_students"]."/".$row["capacity"];

      $instructor_id_query = "select instructor_id from instructor_section where section_id = $section and dept_mnemonic='$dept' and course_number=$course_number";
      $instructor_id = $db->query($instructor_id_query)->fetch_array()["instructor_id"];
      $instructor_name_query = "select first_name,last_name from instructor where computing_id = '$instructor_id'";
      $instructor_row = $db->query($instructor_name_query)->fetch_array();
      $instructor = $instructor_row["first_name"]." ".$instructor_row["last_name"];

      $time_id = $row["time_id"];
      $time_query = "select start_time,end_time from timeslot where time_id=$time_id";
      $time_row = $db->query($time_query)->fetch_array();
      $time = $row["days"]." ".substr($time_row["start_time"],0,-3)."-".substr($time_row["end_time"],0,-3);

      $building_id = $row["building_id"];
      $building_query = "select building_name from building where building_id = $building_id";
      $building = $db->query($building_query)->fetch_array()["building_name"]." ".$row["room"];

      array_push($sections, array($section, $course_component, $status, $enrollment, $instructor, $time, $building) );
    }
  }


  ?>

  <!-- search results -->
  <tr>
    <td>
      <?php 
                //course_number
      echo $dept." ".$course_number
      ?>
    </td>

    <td>
      <?php 
                //course_title
      echo $course_title
      ?>
    </td>

    <td style="width:15%">
      <button type="button" class="btn btn-info btn-circle.btn-lg" data-toggle="collapse" data-target=<?php echo "#demo".$index ?> class="accordion-toggle"> Learn More</button>
    </td>
    <td>
      <button type="button" class="btn btn-success btn-circle.btn-lg" >Add</button>
    </td>
  </tr>
  <tr >

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

          <?php foreach($sections as $section) { ?>

          <tbody>
            <tr>
              <td>
                <?php
                                    // section ID
                echo $section[0];
                ?>
              </td>
              <td>
                <?php 
                                    // course component (Ex. Lecture(3))
                echo $section[1] 
                ?>
              </td>
              <td>
                <?php
                                    //status
                echo $section[2]
                ?>
              </td>
              <td>
                <?php
                                    //enrollment
                echo $section[3]
                ?>
              </td>
              <td>
                <?php
                                    //instructor
                echo $section[4]
                ?>
              </td>
              <td>
                <?php
                                    //time
                echo $section[5]
                ?>
              </td>
              <td>Thornton Hall E316</td>
              <td><button type="button" class="btn btn-success btn-circle.btn-lg"
               data-toggle="modal" data-target="#17339">Add</button></td>

               <span style="margin:auto"class="modal fade" id="17339" role="dialog">
                 <div class="modla-dialog modal-lg">
                  <div class="modal-content">
                   <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Select discussion</h4>
                  </div>
                  <div class="modal-body">
                    <div class="form-group">
                      <label for="17339disc">Select a discussion:</label>
                      <select multiple class="form-control" id="17339disc">
                       <option>17514 | Laboratory | Th 9:30AM - 10:45AM | Olsson Hall 001</option>
                     </select>
                   </div>
                 </div>
                 <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Next ></button>
                </div>
              </div>
            </div>
          </span> <!-- modal -->

        </tr>
      </tbody>
      <?php
      $index=$index+1;
            } //end foreach section
            ?>

          </table>


        </div> 
      </td>
    </tr>

    <?php
  }
  ?>
</tbody>
</table>

</div>
</div>

</body>
</html>
