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
                  <li><a href="/asist/home.php">Signed in as ~~name~~</a></li>
                  <li><a href="/asist/login.php">Logout</a></li>
              </ul>
          </div>
      </div>
  </nav>

	<br/>
	<center><h3>Class Schedule</h3></center><br/>


	<table class="table table-striped">
		<thead>
			<tr>
				<th style="width:15%">Course Mnemonic</th>
				<th>Course Title</th>
				<th>Instructor</th>
				<th>Meeting Times</th>
				<th>Location</th>
				<th></th>
			</tr>
		</thead>
		<tbody>

			<!-- first search result -->
			<tr>
				<td>CS 1010</td>
				<td>Introduction to Information Technology</td>
				<td>Craig Dill</td>
				<td>TuTh 2:00-3:15 PM</td>
				<td>Thornton Hall E316</td>
				<td>
					<button type="button" class="btn btn-danger btn-circle.btn-lg">Drop</button>
				</td>
			</tr>
			

		</tbody>
	</table>

</div>
</body>




</html>