<?php

session_start();

include("Db.php");

$collectionU = $db->users;
$Role = $_POST['Role'];
$username = $_POST['username'];
$password = md5($_POST['password']);

if (isset($_POST['submit'])) {
    $user = $collectionU->findOne(['username' => $username]);
    if ( $user['username'] == $username && $user['password'] == $password) { // check if the username and password match
        $_SESSION['userID']= $user['_id'];
        $_SESSION['Role'] = "User";
        $_SESSION["username"] = $user['username'];
        $_SESSION['FirstName'] = $user['FirstName'];
        $_SESSION['LastName'] = $user['LastName'];
        $_SESSION['DateOfBirth'] = $user['DateOfBirth'];
        $_SESSION['Email'] = $user['Email'];
        $_SESSION['educationLevel'] = $user['educationLevel']; 
        $_SESSION['Gender'] = $user['Gender']; 
        echo '<script>window.location="userHome.php?name=' . $user['FirstName'] . '"; alert("Signed In Successfully!");</script>';
    } else
        echo '<script>window.location="signIn.php"; alert("There is no account associated with the given information, You can re-check your input or sign-up now!");</script>';
}
?>
