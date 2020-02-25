<html>
<head>
<?php
	if(isset($_GET['room'])){
		if(isset($_GET['eventid'])){
			$eventid=$_GET['eventid'];
			$room=$_GET['room'];
			echo "<meta http-equiv=\"refresh\" content=\"0;url=cancel-post2.php?room=$room&eventid=$eventid\" />";
		}
	}
?>
	<link rel="stylesheet" href="style.css" />
</head>
<body>
<div id="container">
	<div id="page_content" >
	<h1>Ending Meeting...</h1>
  </div>
</div>
</body>