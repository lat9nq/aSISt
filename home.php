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

    <?php
  //--------------------------------------------------------------------------
  // Fetch data from mysql database
  //--------------------------------------------------------------------------
    $db = new mysqli('localhost', 'username', 'password', 'asist2');
    if ($db->connect_error):
     die ("Could not connect to db: " . $db->connect_error);
   endif;

  ?>
<?php require ('header.php'); ?>


<div id="main-buttons">
  <button id="course-search" class="big-button" onClick="window.location = '/asist/searchResult.php'">Course Search</button><br>
  <?php if (!isset($_SESSION['instructor'])) { ?>
    <button id="class-schedule" class="big-button"  onClick="window.location = '/asist/classSchedule.php'">Class Schedule</button><br>
  <?php } else {
  ?>
    <button id="class-schedule" class="big-button"  onClick="window.location = '/asist/assignGrades.php'">Assign Grades</button><br>
  <?php } ?>
  <button id="personal-information" class="big-button"  onClick="window.location = '/asist/personalInfo.php'">Personal Information</button><br>
</div>
</div>
</div>
</body>
</html>
