<?php
include_once("netspeakapi.class.php");

//jeżeli chcemy mieć dostęp do funkcji API Shop oraz Konta Użytkownika.
$test = new netspeakapi();
$test->setAPIKey("KLUCZ API NETSPEAK");

//jeżeli chcemy mieć dostęp do funkcji związanych z serwerem slotowym.
$test->setTeamSpeakAPIKey("KLUCZ API USŁUGI SLOTOWEJ");

$try = $test->shopCreatePayment(1, 'TEST');

print_r($try);
print_r($test->getElement("error", $try));
print_r($test->getElement("data", $try));

$try = $test->shopDeletePayment('TEST');

print_r($try);
print_r($test->getElement("error", $try));
print_r($test->getElement("data", $try));
?>