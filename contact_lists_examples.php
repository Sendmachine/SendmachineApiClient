<?php
include_once 'SendmachineApiClient.php';
$username = "your_username";
$password = "your_password";

$sc = new SendmachineApiClient();
$sc->connect_api($username, $password);

//get all contact lists
$resp = $sc->get_all_contact_lists();

//get a single contact list
$list_id = 5;
$resp = $sc->get_a_single_contact_list($list_id);

//create a new contact list
$resp = $sc->create_new_contact_list("list name", array('email@example.com', 'email1@example.com'));

//add contacts list to a existing list
$resp = $sc->edit_contacts_list($list_id, array('email2@example.com', 'email3@example.com'), 'subscribe');

//delete contact list
$resp = $sc->delete_contact_list($list_id);
?>