<?php
/* 
	
Package: BathTime{r}
File: setAccess.php 
(c) 2015 Brooke Dukes All Rights Reserved

This file is called when a user submits the access form with their Device ID and Access Token in index.php. 
*/

//output POST data into variables 
$deviceID = isset( $_POST['d'] ) ? ( filter_var( $_POST['d'], FILTER_SANITIZE_STRING  ) ) : null;
$accessToken = isset( $_POST['a'] ) ? ( filter_var( $_POST['a'], FILTER_SANITIZE_STRING  ) ) : null;

	//if post data exist
	if ( ( isset( $deviceID ) ) && (isset($accessToken) ) ){ 
		setcookie("deviceid", $deviceID, time()+60*60*24*30);  /* expire in 60 days */
		setcookie("accesstoken", $accessToken, time()+60*60*24*30);  /* expire in 60 days */

		//redirect with success
		header("Location: index.php?m=Device%20ID%20and%20Access%20Token%20set%20as%20a%20cookie.%20You%20may also%20bookmark%20this%20URL%20for%20future%20use.&a=".$accessToken."&d=".$deviceID);
	}
	
	else{
		//redirect with error
		header("Location: index.php?e=Please%20enter%20both%20a%20Device%20ID%20and%20Access%20Token%20and%20try%20again.");
	}