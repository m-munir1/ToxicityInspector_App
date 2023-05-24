<?php
session_start();
include("Db.php");

$username = $_SESSION["username"];

if (!(isset($_SESSION["Role"])) || $_SESSION["Role"] != "User")
    echo '<script>window.location="signup.php"; alert("Error on registerd record! Please try again");</script>';

$collectionF = $db->files;
$collectionU = $db->users;
$collectionP = $db->projects;
$collectionR = $db->CommentsByAPI ;

$name = $_GET['name'];
$ProjectName = $_GET['ProjectName'];
$FileName = $_GET['FileName'];

$file = $collectionF->findOne( //get the file info
    ['username' => $username,
    'ProjectName' => $ProjectName,
    'FileName' => $FileName,],
  );
  
$fileID = $file['_id'];
$comment = $_GET['comment'];
$index = $_GET['index'];
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Favicons -->
    <link href="assets/img/logo.png" rel="icon">
    <link href="assets/img/logo.png" rel="apple-touch-icon">

    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Toxicity Inspector - <?php echo $FileName . ' file'; ?></title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <a href="userHome.php?name=<?php echo $name; ?>" accesskey="h"></a>


    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/userHome.css" rel="stylesheet">
    <script type="text/javascript" src="https://cdn.weglot.com/weglot.min.js"></script>
    <script>
        Weglot.initialize({
            api_key: 'wg_7971b0c8d0752818fdd77c7810fb22808'
        });
    </script>
</head>

<body>

    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">

        <div class="d-flex align-items-center justify-content-between">
            <a href="userHome.php?name=<?php echo $_GET['name']; ?>" class="logo d-flex align-items-center">
                <img src="assets/img/logo.png" alt="Logo">
                <span class="d-none d-lg-block">Toxicity Inspector</span>
            </a>
            <i class="bi bi-list toggle-sidebar-btn"></i>
        </div><!-- End Logo -->

        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">

                <li class="nav-item dropdown pe-3">

                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                        <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $name . ' ' . $_SESSION['LastName']; ?></span> <!-- php -->
                    </a><!-- End Profile Iamge Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li>
              <a class="dropdown-item d-flex align-items-center" href="profile.php">
                <i class="bi bi-person"></i>
                <span>My Profile</span>
              </a>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>
             <li>
                            <a class="dropdown-item d-flex align-items-center" href="SignOut.php">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Sign Out</span>
                            </a>
                        </li>

                    </ul><!-- End Profile Dropdown Items -->
                </li><!-- End Profile Nav -->

            </ul>
        </nav><!-- End Icons Navigation -->

    </header><!-- End Header -->

    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">

        <ul class="sidebar-nav" id="sidebar-nav">

            <li class="nav-item">
                <a class="nav-link collapsed" href="userHome.php?name=<?php echo $name; ?>">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li><!-- End Dashboard Nav -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="profile.php">
                    <i class="bi bi-person"></i>
                    <span>Profile</span>
                </a>
            </li><!-- End Profile Page Nav -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="uploadFiles.php?name=<?php echo $name; ?>">
                    <i class="bi bi-file-earmark"></i>
                    <span>Upload Your Files</span>
                </a>
            </li><!-- End upload Page Nav -->

            <li class="nav-item">
                <a class="nav-link" href="UserProjects.php?name=<?php echo $name; ?>">
                    <i class="bi bi-menu-button-wide"></i>
                    <span>Projects</span>
                </a>
            </li><!-- End projects Nav -->

            <li class="nav-item">
                <a class="nav-link collapsed" href="SignOut.php">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Sign Out</span>
                </a>
            </li><!-- End Sign Out Page Nav -->

        </ul>

    </aside><!-- End Sidebar-->

    <main id="main" class="main">

    <div class="pagetitle">
      <h1>Each Comment's Toxicity Interpretation Level</h1> <!-- link it----------- -->
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="userHome.php?name=<?php echo $name; ?>">Home</a></li> <!-- link it----------- -->
          <li class="breadcrumb-item"><a href="UserProjects.php?name=<?php echo $name; ?>">Projects</a></li>
          <li class="breadcrumb-item"><a href="ProjectPage.php?name=<?php echo $name; ?>&ProjectName=<?php echo $ProjectName; ?>"><?php echo $ProjectName; ?></a></li>
          <li class="breadcrumb-item"><a href="FilePage.php?ProjectName=<?php echo $ProjectName; ?>&FileName=<?php echo $FileName; ?>&name=<?php echo $name; ?>"><?php echo $FileName; ?></a></li>
          <li class="breadcrumb-item"><a href="Interpretation.php?name=<?php echo $name; ?>&ProjectName=<?php echo $ProjectName; ?>&FileName=<?php echo $FileName; ?>">Interpretation of Toxicity Level</a></li>
          <li class="breadcrumb-item active">Each Comment's Toxicity Interpretation Level</li> <!-- link it----------- -->
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <a href="FilePage.php?ProjectName=<?php echo $ProjectName; ?>&FileName=<?php echo $FileName; ?>&name=<?php echo $name; ?>">
            <button class="btn btn-primary" role="button">
                <span><i class="bi bi-arrow-left-circle"></i></span> Back to your file
            </button>
        </a>
        <a href="Uploads/<?php echo $fileID.$index ?>.html" download>
        <button class="btn btn-primary float-end" role="button">
                <span><i class="bi bi-download"></i></span> Download
        </button>
        </a>

        <?php     
        $command = escapeshellcmd('/Users/hourianalthunayangmail.com/opt/anaconda3/bin/python IntereptToxicity.py "' . $fileID . '" "' . $index . '" "' . $comment . '"');
        $output = shell_exec($command); 
    
        ?>

        <object data="Uploads/<?php echo $fileID.$index ?>.html" width="1200" height="820">
        </object>  

        <br><br>


    </main><!-- End #main -->
<!-- ======= Footer ======= -->
<div class="container position-absolute bottom-1 start-50 translate-middle-x">
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
  </footer></div>  <!-- End Footer -->

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


    <!-- Vendor JS Files -->

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/echarts/echarts.min.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>


    <!-- Template Main JS File -->
    <script src="assets/js/UserHome.js"></script>

</body>

</html>