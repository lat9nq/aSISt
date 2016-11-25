<?php
if(!$_SESSION['computing_id'])  
{  
    header("Location: login.php");//redirect to login page to secure the welcome page without login access.  
  }  

$home = "/asist/";
$search = "/asist/searchResult.php";
$schedule = "/asist/classSchedule.php";
$history = "/asist/courseHistory.php";
$transcript = "/asist/transcript.php";

?>

  <style>
      ul.nav li.dropdown:hover > ul.dropdown-menu {
        display: block;    
    }
  </style>

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
        <a href="<?php echo $home; ?>" class="navbar-brand">aSISt</a>
      </div>

      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
          <li><a href="<?php echo $home; ?>">Home </a></li>
          <li><a href="<?php echo $search; ?>">Course Search </a></li>
          <li class="dropdown">
            <a href="<?php echo $schedule; ?>" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true">
              My Courses <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              <li><a href="<?php echo $schedule; ?>">Current Semester</a></li>
              <li><a href="<?php echo $history; ?>">Course History</a></li>
              <li class="divider"></li>
              <li><a href="<?php echo $transcript; ?>">Transcript Summary</a></li>
            </ul>
          </li>
          <li><a href="/asist/personalInfo.html">My Info</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <li><a href="/asist">Signed in as <?php echo $_SESSION['computing_id'];?></a></li>
          <li><a href="/asist/login.html">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>