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

$user = $collectionU->findOne(
    ['username' => $username,],
);
$password = $user['password'];
$locat = 'profile.php';

if (isset($_POST['submit'])) {

    $newPassword = md5($_POST['newPassword']);
    $oldPassword = md5($_POST['oldPassword']);
    
    if($oldPassword == $password){

        $userEdit = $collectionU->updateOne(
            ['username' => $username], // conditions 
            ['$set' => ['password' => $newPassword],], // update 
        ); 

        if ($userEdit->getMatchedCount())// edited successsfully
            echo '<script>alert("Password Updated Successfully");</script>';
        
        else // failed 
            echo '<script>alert("Update Opreation Failed! Please Try Again!");</script>';

       echo '<script>window.location="' . $locat . '";</script>';
    }

    else{
        echo '<script>alert("Your Current Password does not Match! Please Try Again!");</script>';
        echo '<script>window.location="' . $locat . '";</script>';
    }
}
?>