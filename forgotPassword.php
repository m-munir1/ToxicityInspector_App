<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include("Db.php");
$collectionU = $db->users;

// Include library files 
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if (isset($_POST['submit'])) {
    $collection = $db->users;
    $username = $_SESSION["username"];
    $email = $_POST['email'];
    $result = $collection->find();
    $flag = true;
    foreach ($result as $found) {
        if ($found['Email'] == $email) {  // check if this email is exist 
            $flag = false;
            $newPass = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
            break;
        }
    }
    if ($flag)
        echo '<script>window.location="signIn.php"; alert("This email is not exist, try with a different email");</script>';
    else {
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "ssl";
        $mail->Port = 465;

        $mail->mailer = "smtp";

        $mail->Username = 'toxicityinspector@gmail.com';
        $mail->Password = 'ttxalfaufqirmtjl';

        // Sender and recipient address
        $mail->SetFrom('toxicityinspector@gmail.com');
        $mail->addAddress($email);

        // Setting the subject and body
        $mail->IsHTML(true);
        $mail->Body = "Your new password is : $newPass";
        $mail->Subject = "New Password";
        if ($mail->send()) {
            echo '<script>window.location="signIn.php"; alert("Message sent successfully!");</script>';
            $changePass = $collectionU->updateOne(
                ['Email' => $email], // conditions 
                ['$set' => ['password' => md5($newPass)]]
            ); // updates 
        } else {
            echo '<script>window.location="signIn.php"; alert("Message could not be sent!");</script>';
        }
    }
}
