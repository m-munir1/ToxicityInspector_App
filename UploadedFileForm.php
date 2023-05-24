<?php

session_start();
include("Db.php");
if (isset($_POST['submit']) && (isset($_SESSION["Role"])) &&  $_SESSION["Role"] == "User") {

    $username = $_SESSION["username"];
    $collectionP = $db->projects;
    $collectionF = $db->files;
    $name = $_GET['name'];
    $results = $collectionF->find(['username' => $username, 'ProjectName' => $_POST['ProjectName']]);
    $flag = false;
    foreach ($results as $result) {
        if ($result['FileName'] === $_POST['FileName']) {
            $flag = true;
            echo '<script>window.location="ProjectPage.php?name=' . $name . '&ProjectName=' . $_POST['ProjectName'] . '"; alert("This file name is already exist!");</script>';
        }
    }
    if ($flag == false) {
        $insertOneResult = $collectionF->insertOne([
            'userID' => $_SESSION['userID'],
            'ProjectName' => $_POST['ProjectName'],
            'FileName' => $_POST['FileName'],
            'UploadedFile' => null,
            'Languages' => $_POST['Languages'],
            'username' => $username,
            'ToxicityLevel' => 0, // as initial value before checking ,
            'FeedbackLevel' => 1,
            'Model' => 'None',
            'F1_Scores' => [0,0,0,0,0,0],
        ]);

        $fileInfo = $collectionF->findOne(['username' => $username, 'ProjectName' => $_POST['ProjectName'], 'FileName' => $_POST['FileName']]);
        $fileID = $fileInfo['_id']; // retrive id to store file in directory

        if (is_dir('Uploads/')) { // if directory is exist 
            $uploaddir = 'Uploads/';
            $file = $_FILES['UploadedFile']['name'];
            $uploadfile = $uploaddir . $fileID . '.csv';
            $temp = $_FILES['UploadedFile']['tmp_name'];
            move_uploaded_file($temp, $uploadfile);
        } else {  // if directory is not exist 
            $uploaddir = 'Uploads/';
            $file = $_FILES['UploadedFile']['name'];
            umask(mask);
            mkdir($uploaddir, 0775);
            $uploadfile = $uploaddir . $fileID . '.csv';
            $temp = $_FILES['UploadedFile']['tmp_name'];
            move_uploaded_file($temp, $uploadfile);
        }

        $EditDir = $collectionF->updateOne( // store file in directory with file id added
            ['username' => $username, 'ProjectName' => $_POST['ProjectName'], 'FileName' => $_POST['FileName']], // conditions 
            ['$set' => ['UploadedFile' => $uploadfile]]
        ); // updates 
        $lanFlag = false;
        $command = escapeshellcmd('/Users/hourianalthunayangmail.com/opt/anaconda3/bin/python langDetection.py "' . $fileID . '" ');
        $output = shell_exec($command);
        $language = substr($output, 0, 2);

        // fine the project to increment the number of files based on the file's language 
        if ($_POST['Languages'] == 'English') { // if file's language is english
            $project = $collectionP->findOne(['username' => $username, 'ProjectName' => $_POST['ProjectName']]);
            $count = $project['NumberOfEnglishFiles'] + 1;

            if ($language == 'ar')
                $lanFlag = true;
            else {
                $FileIncrement = $collectionP->updateOne(
                    ['ProjectName' => $_POST['ProjectName'], 'username' => $username],
                    ['$set' => ['NumberOfEnglishFiles' => $count]]
                );
            }
        } else if ($_POST['Languages'] == 'Arabic') { // if file's language is arabic
            $project = $collectionP->findOne(['username' => $username, 'ProjectName' => $_POST['ProjectName']]);
            $count = $project['NumberOfArabicFiles'] + 1;

            if ($language == 'en')
                $lanFlag = true;
            else{
                $FileIncrement = $collectionP->updateOne(
                    ['ProjectName' => $_POST['ProjectName'], 'username' => $username],
                    ['$set' => ['NumberOfArabicFiles' => $count]]
                );
            }
        }
        $command = escapeshellcmd('/Users/hourianalthunayangmail.com/opt/anaconda3/bin/python split.py "' . $fileID . '" ');
        $output = shell_exec($command);

        if (!($lanFlag) && $insertOneResult->getInsertedCount() && $FileIncrement->getMatchedCount() && $EditDir->getMatchedCount()) { // // if the insert file, increment number of files and directory update opreations successfully done 
            echo '<script>window.location="ProjectPage.php?ProjectName=' . $_POST['ProjectName'] . '&name=' . $name . '"; alert("File Uploaded Successfully!");</script>';
       // count number of comments in train and test files
        } else if ($lanFlag) { // 
            $deleteRes = $collectionF->deleteOne([ // delete the file
                'FileName' => $_POST['FileName'],
                'ProjectName' => $_POST['ProjectName'],
                'username' => $username,
                '_id' => $fileID
            ]);
            echo '<script>window.location="ProjectPage.php?name=' . $name . '&ProjectName=' . $_POST['ProjectName'] . '"; alert("The choosen language does not match the file language!");</script>';
        } else // error on one of them or both 
            echo '<script>window.location="ProjectPage.php?name=' . $name . '&ProjectName=' . $_POST['ProjectName'] . '"; alert("Error On Uploading The File! Please try again");</script>';
    }
} else
    echo '<script>window.location="ProjectPage.php?name=' . $name . '&ProjectName=' . $_POST['ProjectName'] . '"</script>';
