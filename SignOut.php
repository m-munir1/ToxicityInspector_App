<?php
session_start();
if (isset($_SESSION["Role"])) {
    session_destroy();
    echo '<script>window.location="signIn.php"; alert("Signed Out Successfully!");</script>';
} else {
        echo '<script>alert("Error on registerd record! Please try again");</script>';
}
?>