<?php

	date_default_timezone_set("America/Vancouver"); ####Replace with your local timezone
	$tenantId="example.com"; ####Replace with your primary domain name
	$clientId = "replace-with-application-id-from-azuread-enterprise-application-config"; ####Replace with the "Application ID" from the AzureAD from the AzureAD admin center, under Enterprise Applications.
	
	$lookupUser = "username@example.onmicrosoft.com"; ####Replace with the email address of a user in your org. (Doesn't need a password, this is only to be able to look up the list of resource calendars)
	
	#Don't put your client secret in this file! Include it in a file that is OUTSIDE of your web root folder!
	$clientSecret = file_get_contents("../replace/with/path/to/clientsecret.txt"); ####Replace with the path to your clientsecret.txt file.
	#Don't put your client secret in this file! Include it in a file that is OUTSIDE of your web root folder!
	
	$defaultFilter="room-"; ####Replace with whatever prefix your room calendar email addresses use.
	$defaultHeader="Today at Example Corp:";####Replace with what you want the default header to be.
	
	
	
	####Don't edit below this line.
	require('vendor/autoload.php');
	$guzzle = new \GuzzleHttp\Client();
?>