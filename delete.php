<?php

session_start();
include("Db.php");

if (!(isset($_SESSION["Role"])) || $_SESSION["Role"] != "User")
    echo '<script>window.location="signIn.php"; alert("You don\'t have access to the requested page!, Please sign in first.");</script>';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$username = $_SESSION["username"];
$collectionU = $db->users;
$collectionP = $db->projects;
$collectionF = $db->files;
$collectionR = $db->CommentsByAPI;

$name = $_GET['name'];
$ProjectName = $_GET['ProjectName'];

$deleteRes = $collectionP->deleteOne([ // delete the project 
    'ProjectName' => $ProjectName,
    'username' => $username,
]);
$allFiles = $collectionF->find(['ProjectName' => $ProjectName]);
$allFiles = $allFiles->toArray();
foreach ($allFiles as $file) {
    $deleteResults = $collectionR->deleteMany([ // delete all comments belong to this project by fetch each file  
        'fileID' => $file['_id']
    ]);
}
$deletefiles = $collectionF->deleteMany([ // delete all files belong to this project 
    'ProjectName' => $ProjectName,
    'username' => $username,
]);


if ($deleteRes->getDeletedCount()) // if the delete project and delete files opreations successfully done 
    echo '<script>alert("' . $ProjectName . ' Project Deleted Successfully");</script>';
else // error occured
    echo '<script>alert("' . $ProjectName . ' Delete Opreation Failed! Please Try Again!");</script>';

$locat = 'UserProjects.php?name=' . $name;
echo '<script>window.location="' . $locat . '";</script>';
