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

  $classes_query = "select distinct dept_mnemonic, course_number from section inner join instructor_section on instructor_section.section_key=section.section_key and instructor_section.instructor_id='$session_id'";
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
    <th><center>Contact</center></th>
    <th></th>
    <th><center>Academic</center></th>
    <th></th>
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
    <td><b> Advisor </b></td>
    <td> <?php echo $advisor ?></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td><b>Current Mailing Address</b></td>
    <td><?php echo $mailing_address?></td>   
    <td></td>
    <td></td>
    <td></td>
    <td></td>
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