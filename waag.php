<?php 
	date_default_timezone_set("America/Vancouver"); 
	require('vendor/autoload.php');
	$tenantId="sd83.bc.ca";
	$clientId = "c8e0629b-b11c-4db8-8738-eeb1a6b3291b";
	$clientSecret = file_get_contents("../../calendardisplay-includes/clientsecret.txt");
	$guzzle = new \GuzzleHttp\Client();
	
?>
<html>
<head>
	<link rel="stylesheet" href="waag.css" />
	<script src="jquery-3.4.1.min.js"></script>
	<script src="jquery-ui.js"></script>
	<script>
		$(function(){
			var $page = $('#page_content');
			var $pageheight = $('#container').height();
			var $contentheight = $('#page_content').height();
			var $scale = $pageheight/$contentheight
			if($scale >1){$scale = 1;}
			$('#page_content').attr('style', 'transform: scale(' + $scale + ');');
		});
    </script>
</head>
<body>
	<div id="container">
		<div id="page_content" >
			<?php
				if(isset($_GET['filter'])){
					$filter = $_GET['filter'];
				}else{
					$filter = "room-esc-";
				}
				if(isset($_GET['add'])){
					$add = $_GET['add'];
				}else{
					$add = 0;
				}
				if(date("l")=="Monday"){
					$startDate = strtotime("Today");
				}else{
					$startDate = strtotime("Last monday");
				}
				
				
				if(isset($_GET['header'])){
					$header = $_GET['header'];
				}else{
					if($add==0){
						$header = "DESC This Week at a Glance:";
					}else{
						if($add==1){
							$header = "DESC Next Week at a Glance:";
						}else{
							$header = "DESC Week at a Glance:";
						}
						$startDate = strtotime("+$add Weeks",$startDate);
					}
				}
				
				$endDate = strtotime("+1 Week",$startDate);
				
				
				
				echo "<h1 class='floatleft'>$header</h1>";
				
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



				$url = "https://graph.microsoft.com/beta/users/365admin@sd83bcca.onmicrosoft.com/findRooms";
				$result = json_decode($guzzle->get($url, [
					'headers' => [
						'Authorization' => "Bearer $accessToken"
					],
				])->getBody()->getContents());

				$allEvents = array();

				foreach($result->value as $room){
					$roomName = $room->name;
					$roomAddress = $room->address;
					
					if(preg_match("/$filter/",$roomAddress)){
						$startDateString = date(DATE_W3C, $startDate);
						$endDateString = date(DATE_W3C, $endDate);
						
						$url = "https://graph.microsoft.com/beta/users/$roomAddress/calendar/calendarView?startDateTime=$startDateString&endDateTime=$endDateString&select=subject,start,end,organizer";

						$events = json_decode($guzzle->get($url, [
							'headers' => [
								'Authorization' => "Bearer $accessToken"
							],
						])->getBody()->getContents());
						
						foreach($events->value as $event){
							$subject=$event->subject;
							$start = $event->start->dateTime;
							$end = $event->end->dateTime;
							$organizer = $event->organizer->emailAddress->name;
							#echo "<p>$subject $start $organizer</p>";
							$allEvents[]=array($roomName,$subject,$start,$end,$organizer);
						}
					}
				}


				if($allEvents){
					$activeDays = array();
					array_multisort(array_column($allEvents,2),$allEvents);
					$firstDay=1;
					
					foreach($allEvents as $event){
						#print_r($event);
						$roomName=explode(" - ",$event[0])[1];
						$organizer = $event[4];
						$subject=str_replace($organizer,"",$event[1]);
					
						$startDay = date("l,  F j", strtotime($event[2]. " UTC"));
						
						if(!in_array($startDay,$activeDays)){
							$activeDays[]=$startDay;
							if($firstDay){
								$firstDay=0;
							}else{
									echo "</table>";
							}
							#echo "<h2>$startDay</h2>";
							
							echo "<table class=\"today\">";
							
							echo "<thead><tr><th colspan=\"5\"><h2>$startDay</h2></th></tr><tr><th>Time</th><th>Organizer</th><th>Room</th><th>Subject</th></tr></thead>";
						}
						
						$start = str_replace(":00","",date("g:ia",strtotime($event[2]. " UTC")));
						$end = str_replace(":00","",date("g:ia",strtotime($event[3]. " UTC")));

						echo str_replace(" - " . $event[0],"","<tr><td>$start-$end</td><td>$organizer</td><td>$roomName</td><td>$subject</td></tr>");
					}
					echo "</table>";
				}else{
					echo "<h1>No events this week</h1>";
				}
			?>
		</div>
	</div>
</body>