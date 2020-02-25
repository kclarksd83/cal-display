<?php 
	require_once('config.php');
?>
<html>
<head>
	<link rel="stylesheet" href="style.css" />
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
					$filter = $defaultFilter;
				}
				if(isset($_GET['header'])){
					$header = $_GET['header'];
				}else{
					$header = $defaultHeader;
				}
				
				$header = str_ireplace("Today",date("F j"),$header);
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



				$url = "https://graph.microsoft.com/beta/users/$lookupUser/findRooms";
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
						$startDate = date(DATE_W3C, strtotime("Today"));
						$endDate = date(DATE_W3C, strtotime("Tomorrow"));
						
						$url = "https://graph.microsoft.com/beta/users/$roomAddress/calendar/calendarView?startDateTime=$startDate&endDateTime=$endDate&select=subject,start,end,organizer";

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
							if($end > gmdate(DATE_W3C)){
								echo "<!--$end is gt than " . gmdate(DATE_W3C) . ", $subject should display-->\n";
								$allEvents[]=array($roomName,$subject,$start,$end,$organizer);
							}else{
								echo "<!--$end is lt than " . gmdate(DATE_W3C) . ", $subject should not display-->\n";
							}
						}
					}
				}


				if($allEvents){
					array_multisort(array_column($allEvents,2),$allEvents);
					echo "<table class=\"today\">";
					echo "<thead style=\"border-bottom: 2px solid black; text-align: left;\"><tr><th style=\"padding-left: 20px;\">Time</th><th style=\"padding-left: 20px;\">Room</th><th></th></thead>";
					foreach($allEvents as $event){
						#print_r($event);
						$roomName=explode(" - ",$event[0])[1];
						$organizer = $event[4];
						$subject=str_replace($organizer,"",$event[1]);
						
						$start = str_replace(":00","",date("g:ia",strtotime($event[2]. " UTC")));
						$end = str_replace(":00","",date("g:ia",strtotime($event[3]. " UTC")));

						echo str_replace(" - " . $event[0],"","<tr><td>$start-$end</td><td>$roomName</td><td>$subject - $organizer</td></tr>");
					}
					echo "</table>";
				}else{
					echo "<h1>No events today</h1>";
				}

			?>
		</div>
	</div>
</body>