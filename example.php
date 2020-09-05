<?php
include_once("netspeakapi.class.php");

//if we want access to API Shop and User Account functions.
$test = new netspeakapi();
$test->setAPIKey("KLUCZ API NETSPEAK");

//if we want access to slotted teamspeak server related functions
$test->setTeamSpeakAPIKey("KLUCZ API USŁUGI SLOTOWEJ");

$try = $test->shopCreatePayment(1, 'TEST');

print_r($try);
print_r($test->getElement("error", $try));
print_r($test->getElement("data", $try));

$try = $test->shopPaymentList();
print_r($try);

$try = $test->shopGetPaymentStatus('Test');
print_r($try);

$try = $test->shopDeletePayment('TEST');

print_r($try);
print_r($test->getElement("error", $try));
print_r($test->getElement("data", $try));
?>