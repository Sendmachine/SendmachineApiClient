<?php
include_once 'SendmachineApiClient.php';
$username = "your_username";
$password = "your_password";

$sc = new SendmachineApiClient();
$sc->connect_api($username, $password);

//Get details about the current active package of the user
$resp = $sc->get_account_package_details();

//The SMTP user and password are also used for API Auth.
$resp = $sc->get_smtp_details();

//Reset smtp password. A new SMTP password will be generated.
$resp = $sc->reset_smtp_password();

//Get user details
$resp = $sc->get_user_details();

//Update user details
$data = array(
            'sex' => 'f',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'country' => '642',
            'phone_number' => '',
            'mobile_number' => '1111111111'
        );
$resp = $sc->update_user_details($data);

//Get countries with their corresponding IDs.
$resp = $sc->get_countries();

?>
