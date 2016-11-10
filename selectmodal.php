<HTML>
	<HEAD>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
		integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
		<link rel="Stylesheet" href="style.css">
	</HEAD>
	<BODY>
		<button type="button" class="btn btn-toggle btn-circle.btn-lg" data-toggle="modal" data-target="#17339">hello!</button>
		<?php
			function createModal() {
				echo "<span style=\"margin:auto\"class=\"modal fade\" id=\"17339\" role=\"dialog\">";
				echo "<div class=\"modla-dialog modal-lg\">";
				echo "<div class=\"modal-content\">";
				echo "<div class=\"modal-header\">";
				echo "<button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>";
				echo "<h4 class=\"modal-title\">Select discussion</h4>";
				echo "</div>";
				echo "<div class=\"modal-body\">";
				echo "<div class=\"form-group\">";
				echo "<label for=\"17339disc\">Select a discussion:</label>";
				echo "<select multiple class=\"form-control\" id=\"17339disc\">";
				echo "<option>17514 | Laboratory | Th 9:30AM - 10:45AM | Olsson Hall 001</option>";
				echo "</select>";
				echo "</div>";
				echo "</div>";
				echo "<div class=\"modal-footer\">";
				echo "<button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Next ></button>";
				echo "</div>";
				echo "</div>";
				echo "</div>";
				echo "</span>";
			}
			createModal();
		?>
	</BODY>
</HTML>
