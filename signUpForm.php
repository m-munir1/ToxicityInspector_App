<?php
session_start();
include("Db.php");

$collection = $db->users;
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $userExist = $collection->findOne(['username' => $username]);
    if ($userExist['username'] == $username) { // check if this username already exist 
        echo '<script>window.location="signIn.php"; alert("This username is already exist, Change the username or sign in");</script>';
    }
    $email = $_POST['email'];
    $userExist = $collection->findOne(['Email' => $email]);
    if ($userExist['Email'] == $email) { // check if this username already exist 
        echo '<script>window.location="signIn.php"; alert("This email is already used, Change the email Or sign in");</script>';
    }
    $date = $_POST['date'];
    $year = substr($date, 0, 4);
    $year = date("Y") - $year;
    if ($year < 18) { // check user's age
        echo '<script>window.location="index.php"; alert("Sorry, our website users\' age should be more than 18 years old, due the hate speech and sensetive content");</script>';
    } else {

        $insertOneResult = $collection->insertOne([
            'username' => $username,
            'FirstName' => $_POST['first_name'],
            'LastName' => $_POST['last_name'],
            'Email' => $email,
            'DateOfBirth' => $date,
            'password' => md5($_POST['password']),
            'educationLevel' => $_POST['educationLevel'],
            'Gender' => $POST['Gender'],

        ]);
        $fname = $_POST['first_name'];
        if ($insertOneResult->getInsertedCount()) {
            $userInfo = $collection->findOne(['username' => $username]);
            $_SESSION['userID'] = $userInfo['_id'];
            $_SESSION['Role'] = "User"; // set session attribute role
            $_SESSION['username'] = $username; // set session attribute username
            $_SESSION['FirstName'] = $_POST['first_name']; // set session attribute FirstName
            $_SESSION['LastName'] = $_POST['last_name']; // set session attribute LastName
            $_SESSION['DateOfBirth'] = $_POST['DateOfBirth']; // set session attribute DateOfBirth
            $_SESSION['Email'] = $_POST['Email']; // set session attribute LastNaEmailme
            $_SESSION['educationLevel'] = $_POST['educationLevel']; // set session attribute educationLevel
            $_SESSION['Gender'] = $_POST['Gender']; // set session attribute Gender

            echo '<script>window.location="userHome.php?name=' . $fname . '"; alert("Signed Up successfully!");</script>';
        } else
            echo '<script>window.location="signup.php"; alert("Error on registerd record! Please try again");</script>';
    }
} else
    echo '<script>window.location="index.php";</script>';



?>