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

$name = $_GET['name'];
$oldProjectName = $_GET['ProjectName'];
$project = $collectionP->findOne(['username' => $username, 'ProjectName' => $oldProjectName]);
$oldDes = $project['ProjectDesc']; // retrive description 
$flag = 0;
if (isset($_POST['submit'])) {
    $newProjectName = $_POST['eName'];
    $newDes = $_POST['eDes'];
    if ($newProjectName == $oldProjectName && $oldDes == $newDes) { // if the project name and the discription are same (no update)
        $flag = 1;
        $locat = 'UserProjects.php?name=' . $name;
        echo '<script>window.location="' . $locat . '";</script>';
    } else if ($newProjectName == $oldProjectName && $flag == 0) { // if the project name same but the discription changed
        $flag = 1;
        $InfoEdit = $collectionP->updateOne(
            ['ProjectName' => $oldProjectName, 'ProjectDesc' => $oldDes, 'username' => $username], // conditions 
            ['$set' => ['ProjectDesc' => $newDes]]
        ); // updates 
        $locat = 'UserProjects.php?name=' . $name;
        echo '<script>window.location="' . $locat . '";</script>';
    } else {
        $checkExist = $collectionP->find(['username' => $username]);
        foreach ($checkExist as $check) { // if the new project name exist for the same user 
            if ($check['ProjectName'] == $newProjectName) {
                $flag = 1;
                echo '<script>alert("Project Name Is Already Exist!");</script>';
                $locat = 'UserProjects.php?name=' . $name;
                echo '<script>window.location="' . $locat . '";</script>';
            }
        }
    }
    if ($flag == 0) {
        $InfoEdit = $collectionP->updateOne(
            ['ProjectName' => $oldProjectName, 'ProjectDesc' => $oldDes, 'username' => $username], // conditions 
            ['$set' => ['ProjectName' => $newProjectName, 'ProjectDesc' => $newDes]]
        ); // updates 
        $fileEdit = $collectionF->updateMany(
            ['ProjectName' => $oldProjectName,'username' => $username], // conditions 
            ['$set' => ['ProjectName' => $newProjectName]]
        ); // updates 
        $feedbackFileEdit = $collectionFb->updateMany(
            ['ProjectName' => $oldProjectName,'username' => $username], // conditions 
            ['$set' => ['ProjectName' => $newProjectName]]
        ); // updates
        if ($InfoEdit->getMatchedCount()) // edited successsfully
            echo '<script>alert("Project Information Updated Successfully");</script>';
        else // failed 
            echo '<script>alert("Update Opreation Failed! Please Try Again!");</script>';

        $locat = 'UserProjects.php?name=' . $name;
        echo '<script>window.location="' . $locat . '";</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Toxicity Inspector - Edit Project Information</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">
    <script type="text/javascript" src="https://cdn.weglot.com/weglot.min.js"></script>
    <script>
        Weglot.initialize({
            api_key: 'wg_7971b0c8d0752818fdd77c7810fb22808'
        });
    </script>
</head>

<body>
    <main>
        <div class="container">

            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                            <div class="card mb-3">

                                <div class="card-body">

                                    <div class="pt-4 pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">Edit Project</h5>
                                    </div>

                                    <form class="row g-3 needs-validation" action="#" method="post" novalidate>

                                        <div class="col-12">
                                            <label for="yourPassword" class="form-label">Project Name:</label>
                                            <input type="text" value="<?php echo $oldProjectName; ?>" name="eName" class="form-control" required>
                                        </div>

                                        <div class="col-12">
                                            <label for="yourPassword" class="form-label">Project Description:</label>
                                            <input type="text" value="<?php echo $oldDes; ?>" name="eDes" class="form-control" required>
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-primary w-100" name="submit" type="submit">Edit</button>
                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </section>

        </div>
    </main><!-- End #main -->
    <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="credits">
      <div>Contact Us
        <div class="social-links mt-3">
          <a href="mailto: toxicityinspector@gmail.com"><i class="bi bi-envelope-fill"></i></a>
          <a href="https://twitter.com/" target="_blank" class="twitter"><i class="bi bi-twitter"></i></a>
          <a href="https://facebook.com/" target="_blank" class="facebook"><i class="bi bi-facebook"></i></a>
          <a href="https://instagram.com/" target="_blank" class="instagram"><i class="bi bi-instagram"></i></a>
          <a href="https://linkedin.com/" target="_blank" class="linkedin"><i class="bi bi-linkedin"></i></a>
        </div>
        Phone: +966 555555555<br>
        King Saud University, Riyadh
    </div>
    <div class="copyright">
      <br>
      &copy; Copyright <strong><span>Toxicity Inspector</span></strong>. All Rights Reserved
    </div>
  </footer><!-- End Footer -->

  <style>

    /*--------------------------------------------------------------
# Footer
--------------------------------------------------------------*/
.footer {
  padding: 20px 0;
  font-size: 14px;
  transition: all 0.3s;
  border-top: 1px solid #cddfff;
}

.footer .copyright {
  text-align: center;
  color: #012970;
}

.footer .credits {
  padding-top: 5px;
  text-align: center;
  font-size: 13px;
  color: #012970;
}


  </style>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/chart.js/chart.min.js"></script>
    <script src="assets/vendor/echarts/echarts.min.js"></script>
    <script src="assets/vendor/quill/quill.min.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>

</body>

</html>