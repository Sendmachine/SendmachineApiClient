<?php

require_once '../SendmachineApiClient.php';

$username = "your_username";
$password = "your_password";

try {
	$sc = new SendmachineApiClient($username, $password);
	
    $details = array();
    
	/*
	 * send email
	 */
	$response = $sc->mail->send($details);
    print_r($response);
	
} 
catch(Sendmachine_Error $ex){
	
	echo $ex->getMessage(); //error details
	echo $ex->getSendmachineStatus(); //error status
}