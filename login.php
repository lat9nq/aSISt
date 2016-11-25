<?php
session_start();
?>

<!DOCTYPE HTML>
<html>
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

  <div class="container" style="text-align:center">
    <div class = "login-form">
      <form class = "form-signin" role = "form" 
      action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); 
      ?>" method = "post">
      <legend><h2>Welcome</h2></legend><br/>
      <input type = "text" class = "form-control" 
      name = "computing_id" placeholder = "Computing ID" 
      required autofocus></br>
      <input type = "password" class = "form-control"
      name = "password" placeholder = "Password" required>
      <br/>
      <button class = "btn btn-info btn-lg btn-primary btn-block" type = "submit" 
      name = "login">Login</button>
    </form>
  </div>
</div>

<!-- Modal 1 -->
<div class="modal fade" id="errorIDModal" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Error!</h4>
      </div>
      <div class="modal-body">
        <p>Invalid computing ID</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Try Again</button>
      </div>
    </div>

  </div>
</div>

<!-- Modal 1 -->
<div class="modal fade" id="errorPasswordModal" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Error!</h4>
      </div>
      <div class="modal-body">
        <p>Invalid password</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Try Again</button>
      </div>
    </div>

  </div>
</div>

</body>
</html>

<?php


$db = new mysqli('localhost', 'username', 'password', 'asist2');
if ($db->connect_error):
 die ("Could not connect to db: " . $db->connect_error);
endif;

$query = "select * from student";
$result = $db->query($query);
if ($result->num_rows>0){
  $row = $result->fetch_array();
  $computing_id = $row["computing_id"];
  $password = $row["password"];
}


if(isset($_POST['login']))  
{  

 $computing_id=$_POST['computing_id'];  
 $password=$_POST['password'];  

 $check_student="select * from student where computing_id='$computing_id'and password='$password'";
 $check_instructor="select * from instructor where computing_id='$computing_id'and password='$password'";

 $run_student=mysqli_query($db,$check_student);
 $run_instructor=  mysqli_query($db,$check_instructor);

 if(mysqli_num_rows($run_student) or mysqli_num_rows($run_instructor))  
 {  
  echo "<script>window.open('home.php','_self')</script>";  
  
  $_SESSION['computing_id']=$computing_id; 
  if (mysqli_num_rows($run_instructor)){
    $_SESSION['instructor']='yes';
  }
  
}  
else  
{ 
  $check_ID_student="select * from student where computing_id='$computing_id'";
  $check_ID_instructor = "select * from instructor where computing_id='$computing_id'";
  $check_password_student = "select * from student where password='$password'";
  $check_password_instructor = "select * from instructor where password='$password'";
  $result_ID_student = $db->query($check_ID_student);
  $result_ID_instructor = $db->query($check_ID_instructor);
  $result_password_student = $db->query($check_password_student);
  $result_password_instructor = $db->query($check_password_instructor);
  if ($result_ID_student->num_rows==0 and $result_ID_instructor->num_rows==0)
  {  
    ?>
    <script>$('#errorIDModal').modal('show');</script>

    <?php
  } else if ($result_password_student->num_rows==0 and $result_password_instructor->num_rows==0){
    ?>
    <script>$('#errorPasswordModal').modal('show');</script>
    <?php
  }
}  
}  
?>