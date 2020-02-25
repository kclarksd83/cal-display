<?php 
	require_once('config.php');
?>
<html>
<head>
<?php
	if(isset($_GET['room'])){
		$room = $_GET['room'];
		echo "<meta http-equiv=\"refresh\" content=\"0;url=book.php?room=$room\">";
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
			if(isset($_GET['time'])){
				$time = $_GET['time'];
				
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
				
				$url = "https://graph.microsoft.com/beta/users/$room@$tenantId";
				$arr = array(
					'headers' => array(
						'Authorization' => "Bearer $accessToken",
						'Content-Type' => 'application/json',
					)
				);
				$result = json_decode($guzzle->get($url,$arr)->getBody()->getContents());
				$roomName = $result->displayName;
				

				$startTime = date(DATE_ATOM);
				$endTime = date(DATE_ATOM,strtotime("+$time"));
				$url = "https://graph.microsoft.com/beta/users/$room@$tenantId/calendar/events";
				
				$arr = array(
					'headers' => array(
						'Authorization' => "Bearer $accessToken",
						'Content-Type' => 'application/json',
					),
					'json' => array(
						'subject' => "Impromptu Meeting",
						'start' => array(
							'dateTime' => "$startTime",
							'timeZone' => 'Pacific Standard Time',
						),
						'end' => array(
							'dateTime' => "$endTime",
							'timeZone' => 'Pacific Standard Time',
						),
						'location' => array(
							'displayName' => "$roomName",
							'locationUri' => "$room@$tenantId",
							'locationType' => "conferenceRoom",
							'uniqueId' => "$room@$tenantId",
							'uniqueIdType' => "directory"
						)
					)
				);
				#print_r($arr);
				$result = json_decode($guzzle->post($url,$arr)->getBody()->getContents());			
				
				
				echo "<h1>Please wait while your room is booked....</h1>";
			}			
		}
	?>
  </div>
</div>
</body>