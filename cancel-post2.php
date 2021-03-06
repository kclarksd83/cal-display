<?php 
	require_once('config.php');	
?>
<html>
<head>
<?php
	if(isset($_GET['room'])){
		$room = $_GET['room'];
		echo "<meta http-equiv=\"refresh\" content=\"5;url=book.php?room=$room\">";
	}
	
?>
	<link rel="stylesheet" href="style.css" />

</head>
<body>
<div id="container">
	<div id="page_content" >
    <?php
		if(isset($_GET['room'])){
			$room=$_GET['room'];	
			if(isset($_GET['eventid'])){
				$eventid= $_GET['eventid'];
				
				$url = 'https://login.microsoftonline.com/' . $tenantId . '/oauth2/v2.0/token';
				$token = json_decode($guzzle->post($url, [
					'form_params' => [
						'client_id' => $clientId,
						'client_secret' => $clientSecret,
						'scope' => 'https://graph.microsoft.com/.default',
						'grant_type' => 'client_credentials',
					],
				])->getBody()->getContents());
				$accessToken = $token->access_token;
				
				$url = "https://graph.microsoft.com/beta/users/$room@$tenantId/events/$eventid";
				$endTime = date(DATE_ATOM);
				$arr = array(
					'headers' => array(
						'Authorization' => "Bearer $accessToken",
						'Content-Type' => 'application/json',
					),
					'json' => array(
						'end' => array(
							'dateTime' => "$endTime",
							'timeZone' => 'Pacific Standard Time',
						)
					)
				);
				$result = json_decode($guzzle->patch($url,$arr)->getBody()->getContents());
				#print_r($result);
				echo "<h1>Ending Meeting....</h1>";
			}			
		}
	?>
  </div>
</div>
</body>