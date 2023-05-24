<?php
session_start();

include("Db.php");
if (isset($_POST['submit']) && (isset($_SESSION["Role"])) &&  $_SESSION["Role"] == "User") {
    $collection = $db->projects;
    $name = $_GET['name'];
    $query=array('username'=> $_SESSION['username'],'ProjectName'=>$_POST['ProjectName']);
    
    if ($collection->findOne($query)) { // check if the project name is already exist 
        echo '<script>window.location="userProjects.php?name=' . $name . '"; alert("This project name is already exist!");</script>';
    } else {
        $insertOneResult = $collection->insertOne([
            'userID'=> $_SESSION['userID'],
            'username' => $_SESSION['username'],
            'ProjectName' => $_POST['ProjectName'],
            'ProjectDesc' => $_POST['ProjectDesc'],
            'ProjectStatus' => 'not started', // as initial value 
            'OverallToxicity' => 0, // as initial value 
            'NumberOfEnglishFiles' => 0, // as initial value 
            'NumberOfArabicFiles' => 0, // as initial value 
        ]);

        if ($insertOneResult->getInsertedCount()) { // if the project inserted successfully 
            echo '<script>window.location="UserProjects.php?name='. $name. '"; alert("Project Created Successfully!");</script>';
        } else
            echo '<script>window.location="UserProjects.php?name=' . $name . '"; alert("Error! Please try to create a new project again");</script>';
    }
} else
    echo '<script>window.location="UserProjects.php?name=' . $name . '";</script>';
?>