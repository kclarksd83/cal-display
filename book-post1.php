<html>
<head>
<?php
	if(isset($_GET['room'])){
		if(isset($_GET['time'])){
			$room=$_GET['room'];
			$time=$_GET['time'];
			echo "<meta http-equiv=\"refresh\" content=\"0;url=book-post2.php?room=$room&time=$time\" />";
		}
	}
?>
	<link rel="stylesheet" href="style.css" />

</head>
<body>
<div id="container">
	<div id="page_content" >
	<h1>Please wait while your room is booked...</h1>
  </div>
</div>
</body>