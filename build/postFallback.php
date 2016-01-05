<?php
/* 
Package: BathTime{r}
File: json.php 
(c) 2015 Brooke Dukes All Rights Reserved

This file is called if JavaScript is disabled and the user a user submits the Timer form in index.php. 

It works by checking if the POST data is avaliable and then does a curl request to the spark.io API

*/

//Check that all needed values have beem received:
if( ( isset( $_POST['m'] ) ) && ( isset( $_POST['deviceid'] ) ) && ( isset( $_POST['accesstoken'] ) ) ){
	
	//create shorthand versions for our data
	$m = isset( $_POST['m'] ) ? ( filter_var( $_POST['m'], FILTER_SANITIZE_NUMBER_INT ) ) : null;
	$d = isset( $_POST['d'] ) ? ( filter_var( $_POST['d'], FILTER_SANITIZE_STRING ) )    : null;
	$a = isset( $_POST['a'] ) ? ( filter_var( $_POST['a'], FILTER_SANITIZE_STRING ) )    : null;

	// Check if minutes value is a 1 or 2 digit number that is greater than 0 redirect on error
	if ( ( preg_match('/^[0-9]{1,2}$/', $m ) ) && ( $m > 0 ) ){
	
		//URL to the Spark API
 		$url = 'https://api.particle.io/v1/devices/' . $d . '/settimer';
 		//set our access token and argument
		$post = [
	    	'access_token' => $a,
			'args' => $m,
			];

		//curl all the thigs
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS,  http_build_query($post) );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                            'Content-Type: application/x-www-form-urlencoded'
                                            ));

		$response = curl_exec($ch);
		curl_close ($ch);

		//redirect to let the user know we're all good here
		header("Location: index.php?m=Timer%20Set!");
	}
	else{
		//redirect to let the user know that validation failed
		header("Location: index.php?e=Please%20enter%20a%20numeric%20value%20greater%20than%20zero.");
	}
}

// no POST data redictect with error instead 
else{
		header("Location: index.php?e=Access%20Denied");

}