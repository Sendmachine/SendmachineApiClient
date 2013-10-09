<?php
include_once 'SendmachineApiClient.php';
$username = "your_username";
$password = "your_password";

$sc = new SendmachineApiClient();
$sc->connect_api($username, $password);


//add new sender
$resp = $sc->add_new_sender('email@example.ro');

//get sender list
$resp = $sc->get_sender_list($status = 'all', $type = 'email', $group = 'none', $limit = null, $offset=null);

?>