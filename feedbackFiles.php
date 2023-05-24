<?php
session_start();
include("Db.php");
$collectionF = $db->files;
$collectionU = $db->users;
$collectionP = $db->projects;

if (!(isset($_SESSION["Role"])) || $_SESSION["Role"] != "User")
    echo '<script>window.location="signIn.php"; alert("You don\'t have access to the requested page!, Please sign in first.");</script>';


$username = $_SESSION["username"];
$name = $_SESSION["FirstName"];
$FileName = $_GET["FileName"];
$ProjectName = $_GET["ProjectName"];
$file = $collectionF->findOne( //get the file info
    [
        'username' => $username,
        'ProjectName' => $ProjectName,
        'FileName' => $FileName,
    ],
);
$feedbackLevel = $file['FeedbackLevel'];
if (isset($_POST['TrainFeedback'])) {


    $feedbackArray = $_POST['TrainFeedback'];
    $commentsArray = $_POST['commentsTrain'];
    $labelsArray = $_POST['labelsTrain']; // array of old labels
    $feedbackFile = $_POST['feedbackFileTrain'];

    $rows = array();
    $rows[] = ['index', 'comment_text', 'toxic'];
    for ($i = 0; $i < count($commentsArray); $i++) {

        if ($feedbackArray[$i] == "2") { //no feedback then set the toxic to the old label
            if ($labelsArray[$i] == '1') {
                $rows[] = [$i, $commentsArray[$i], "1"];
            } elseif ($labelsArray[$i] == '0') {
                $rows[] = [$i, $commentsArray[$i], "0"];
            }
        } elseif ($feedbackArray[$i] == "1") { //if the given feedback is toxic
            $rows[] = [$i, $commentsArray[$i], "1"];
        } elseif ($feedbackArray[$i] == "0") { //if the given feedback is non-toxic
            $rows[] = [$i, $commentsArray[$i], "0"];
        }
    }

    $path = $feedbackFile;
    $fp = fopen($path, 'w'); // open in write only mode (write at the start of the file)
    foreach ($rows as $row) {
        fputcsv($fp, $row);
    }
    fclose($fp);
}
if (isset($_POST['TestFeedback'])) {
    $feedbackArrayTest = $_POST['TestFeedback'];
    $commentsArrayTest = $_POST['Testcomments'];
    $labelsArrayTest = $_POST['Testlabels']; // array of old labels
    $feedbackFileTest = $_POST['feedbackFileTest'];
    $rowsTest = array();
    $rowsTest[] = ['index', 'comment_text', 'toxic'];

    for ($i = 0; $i < count($commentsArrayTest); $i++) {
        if ($feedbackArrayTest[$i] == "2") { //no feedback then set the toxic to the old label
            if ($labelsArrayTest[$i] == '1') {
                $rowsTest[] = [$i, $commentsArrayTest[$i], "1"];
            } elseif ($labelsArrayTest[$i] == '0') {
                $rowsTest[] = [$i, $commentsArrayTest[$i], "0"];
            }
        } elseif ($feedbackArrayTest[$i] == "1") { //if the given feedback is toxic
            $rowsTest[] = [$i, $commentsArrayTest[$i], "1"];
        } elseif ($feedbackArrayTest[$i] == "0") { //if the given feedback is non-toxic
            $rowsTest[] = [$i, $commentsArrayTest[$i], "0"];
        }
    }


    $path = $feedbackFileTest;
    $fp = fopen($path, 'w'); // open in write only mode (write at the start of the file)
    foreach ($rowsTest as $row) {
        fputcsv($fp, $row);
    }
    fclose($fp);

    $feedbackUpdate = $collectionF->updateOne(
        ['ProjectName' => $ProjectName, 'username' => $username, 'FileName' => $FileName,],
        ['$set' => ['FeedbackLevel' => $feedbackLevel + 1]]
    );
}
