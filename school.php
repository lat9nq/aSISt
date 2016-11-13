<?php
	$db = new mysqli("localhost", "username", "password", "asist");
	echo $db->query("INSERT INTO school VALUES(1, 'College');") == false;
	echo $db->query("INSERT INTO school VALUES(2, 'Engineering');") == false;
?>