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
$collectionFb = $db->feedback;

$user = $collectionU->findOne(
    ['username' => $username,],
);
$oldFirstName = $user['FirstName']; 
$oldLastName = $user['LastName'];
$oldUsername = $username;
$oldEmail = $user['Email'];

$query = array('username'=>$_POST['newUsername']);
$query2 = array('Email'=>$_POST['newEmail']); 
$flag = 0;
$locat = 'profile.php';

if (isset($_POST['submit'])) {

    $newFirstName = $_POST['newFirstName'];
    $newLastName = $_POST['newLastName'];
    $newUsername = $_POST['newUsername'];
    $newEmail = $_POST['newEmail'];

    if ($newFirstName == $oldFirstName && $newLastName == $oldLastName && $newUsername == $oldUsername && $newEmail == $oldEmail) { // no update enculde email
        $flag = 1;
        echo '<script>window.location="' . $locat . '";</script>';
    } 
    
    else if ($newUsername == $oldUsername && $newEmail == $oldEmail && $flag == 0) { 
        $flag = 1;
        $userEdit = $collectionU->updateOne(
            ['FirstName' => $oldFirstName, 'LastName' => $oldLastName, 'username' => $oldUsername], // conditions 
            ['$set' => ['FirstName' => $newFirstName, 'LastName' => $newLastName],], // update
        );

        if ($userEdit->getMatchedCount()) {// edited successsfully
            $_SESSION['FirstName'] = $newFirstName;
            $_SESSION['LastName'] = $newLastName;
            echo '<script>alert("Profile Information Updated Successfully");</script>';
        } 
        else // failed 
            echo '<script>alert("Update Opreation Failed! Please Try Again!");</script>';
        echo '<script>window.location="' . $locat . '";</script>';
    } 
    
    else {
        if ($collectionU->findOne($query) && $newUsername != $oldUsername) { // if the new username name exist for the same user 
            $flag = 1;
            echo '<script>alert("Username Is Already Exist!");</script>';
            echo '<script>window.location="' . $locat . '";</script>';
        }
        if ($collectionU->findOne($query2) && $newEmail != $oldEmail) { // if the new email name exist for the same user 
            $flag = 1;
            echo '<script>alert("Email Is Already Exist!");</script>';
            echo '<script>window.location="' . $locat . '";</script>';
        } 
    }
    
    if ($flag == 0) {

        $userEdit = $collectionU->updateOne(
            ['FirstName' => $oldFirstName, 'LastName' => $oldLastName, 'username' => $oldUsername, 'Email' => $oldEmail], // conditions 
            ['$set' => ['FirstName' => $newFirstName, 'LastName' => $newLastName, 'username' => $newUsername, 'Email' => $newEmail],], // update 
        ); 

        $projectEdit = $collectionP->updateMany(
            ['username' => $oldUsername], // conditions 
            ['$set' => ['username' => $newUsername],], // update 
        ); 

        $fileEdit = $collectionF->updateMany(
            ['username' => $oldUsername], // conditions 
            ['$set' => ['username' => $newUsername],], // update 
        ); 
        if ($userEdit->getMatchedCount()) { // edited successsfully
            $_SESSION['FirstName'] = $newFirstName;
            $_SESSION['LastName'] = $newLastName;
            $_SESSION['username'] = $newUsername;
            $_SESSION['Email'] = $newEmail;
            echo '<script>alert("Profile Information Updated Successfully");</script>';
        }

        else // failed 
            echo '<script>alert("Update Opreation Failed! Please Try Again!");</script>';

       echo '<script>window.location="' . $locat . '";</script>';
    } 
}
