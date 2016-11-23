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

<?php
$db = new mysqli('localhost', 'username', 'password', 'asist2');
if ($db->connect_error):
 die ("Could not connect to db: " . $db->connect_error);
endif;

$session_id = $_SESSION['computing_id'];
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
  <td><?php echo $computing_id?>@virginia.edu</td>   
  <td><b>School</b></td>
  <td><?php echo $school_name ?></td>
  <td></td>
  <td></td>
</tr>
<tr>
  <td><b>Permanent Address</b></td>
  <td><?php echo $permanent_address?></td>   
  <td></td>
  <td></td>
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
  
</table>
