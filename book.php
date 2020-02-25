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
				if(isset($_GET['room'])){
					$room=$_GET['room'];
					
					if(isset($_GET['header'])){
						$header = $_GET['header'];
					}else{
						$header = "Today in $room:";
					}
					
					
					$header = str_ireplace("Today",date("F j"),$header);
					
					
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
					$filteredRoomName = str_ireplace("DESC - ","",$roomName);
					$header = str_ireplace($room,$filteredRoomName,$header);
					echo "<h1 class='floatleft'>$header</h1>";

					$allEvents = array();
					$startDate = date(DATE_W3C, strtotime("Today"));
					$endDate = date(DATE_W3C, strtotime("Tomorrow"));
					
					$url = "https://graph.microsoft.com/beta/users/$room@$tenantId/calendar/calendarView?startDateTime=$startDate&endDateTime=$endDate&select=subject,start,end,organizer,location,id";
					
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
						$eventid = $event->id;
						
						if($end > gmdate(DATE_W3C)){
							echo "<!--$end is gt than " . gmdate(DATE_W3C) . ", $subject should display-->\n";
							$allEvents[]=array($subject,$start,$end,$organizer,$eventid);
						}else{
							echo "<!--$end is lt than " . gmdate(DATE_W3C) . ", $subject should not display-->\n";
						}
					}

					if($allEvents){
						array_multisort(array_column($allEvents,2),$allEvents);
						echo "<table class=\"today\">";
						foreach($allEvents as $event){
							#print_r($event);
							$organizer = $event[3];
							$subject=str_replace($organizer,"",$event[0]);
							
							$start = str_replace(":00","",date("g:ia",strtotime($event[1]. " UTC")));
							$end = str_replace(":00","",date("g:ia",strtotime($event[2]. " UTC")));
							if($subject=="Impromptu Meeting"){
								echo str_replace(" - $roomName","","<tr><td>$start-$end</td><td>$subject - $organizer<span class=\"floatright\"><a href=\"cancel.php?room=$room&eventid=" . $event[4] . "\">&#10060;</a></span></td></tr>");
							}else{
								echo str_replace(" - $roomName","","<tr><td>$start-$end</td><td>$subject - $organizer</td></tr>");
							}
						}
						echo "</table>";
					}else{
						echo "<h1 style=\"clear: both;\">No events today</h1>";
					}
					echo "<div id=\"formdiv\">";
					echo "<h1 class=\"floatleft\">Book an impromptu meeting:</h1>";
					echo "<div style=\"clear: both;\">";
					echo "<form style=\"clear: both;\" id=\"bookform\" name=\"bookform\" action=\"book-post1.php\" method=\"GET\">";
					
					#if(!isset($_GET['time'])){
						#echo "<input type=\"hidden\" name=\"submitted\" value=\"true\" />";
						echo "<input type=\"hidden\" name=\"room\" value=\"" . $_GET['room'] . "\" />";
						echo "<input class=\"bigbutton\" name=\"time\" type=\"submit\" value=\"15 Minutes\"  />";
						echo "<input class=\"bigbutton\" name=\"time\" type=\"submit\" value=\"30 Minutes\"  />";
						echo "<input class=\"bigbutton\" name=\"time\" type=\"submit\" value=\"45 Minutes\"  />";
						echo "<input class=\"bigbutton\" name=\"time\" type=\"submit\" value=\"60 Minutes\"  />";
						echo "</form>";
						echo "</div>";
					#}
					echo "</div>";
				}
			?>
		</div>
	</div>
</body>