<?php

session_start();
include("Db.php");

if (!(isset($_SESSION["Role"])) || $_SESSION["Role"] != "User")
    echo '<script>window.location="signup.php"; alert("Error on registerd record! Please try again");</script>';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$username = $_SESSION["username"];
$collectionF = $db->files;
$collectionP = $db->projects;
$collectionR = $db->CommentsByAPI;
$name = $_GET['name'];
$ProjectName = $_GET['ProjectName'];
$FileName = $_GET['FileName'];

$FileInfo = $collectionF->findOne([ // to retrive the file language 
    'FileName' => $FileName,
    'ProjectName' => $ProjectName,
    'username' => $username,
]);

$deleteRes = $collectionF->deleteOne([ // delete the file
    'FileName' => $FileName,
    'ProjectName' => $ProjectName,
    'username' => $username,
]);



if ($FileInfo['Languages'] == 'English') { // if the file language is english then decrement the total number of english files for the project 
    $project = $collectionP->findOne(['username' => $username, 'ProjectName' => $ProjectName]);
    $count = $project['NumberOfEnglishFiles'] - 1;
    $percent = $project['OverallToxicity'] - $FileInfo['ToxicityLevel']; // remove this file's toxicity from the project's OverallToxicity
    $PeojectDec = $collectionP->updateOne(
        ['ProjectName' => $ProjectName, 'username' => $username],
        ['$set' => ['NumberOfEnglishFiles' => $count, 'OverallToxicity' => $percent]]
    );
}

if ($FileInfo['Languages'] == 'Arabic') { // if the file language is arabic then decrement the total number of arabic files for the project 
    $project = $collectionP->findOne(['username' => $username, 'ProjectName' => $ProjectName]);
    $count = $project['NumberOfArabicFiles'] - 1;
    $percent = $project['OverallToxicity'] - $FileInfo['ToxicityLevel']; // remove this file's toxicity from the project's OverallToxicity
    $PeojectDec = $collectionP->updateOne(
        ['ProjectName' => $ProjectName, 'username' => $username],
        ['$set' => ['NumberOfArabicFiles' => $count, 'OverallToxicity' => $percent]]
    );
}

if ($deleteRes->getDeletedCount() && $PeojectDec->getMatchedCount()) // if the delete file, delete comments and decrement opreations successfully done 
    echo '<script>alert("' . $FileName . ' File Deleted Successfully");</script>';
else // error on one of them or both 
    echo '<script>alert("' . $FileName . ' File Opreation Failed! Please Try Again!");</script>';

$locat = 'ProjectPage.php?name=' . $name . '&ProjectName=' . $ProjectName;
echo '<script>window.location="' . $locat . '";</script>';
