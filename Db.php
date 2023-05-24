<?php
include("vendor/autoload.php");

$client = new MongoDB\Client(
    'mongodb://toxicityuser:0zQT5wKf2%24cH@68.183.85.89:27017/?authMechanism=DEFAULT&authSource=toxicitydb'
);
$db = $client->Toxicity_Inspector;
?>