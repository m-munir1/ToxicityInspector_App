<?php
session_start();
include("Db.php");

$collectionU = $db->users;
$collectionP = $db->projects;
$collectionF = $db->files;

if (!(isset($_SESSION["Role"])) || $_SESSION["Role"] != "User")
  echo '<script>window.location="index.php";</script>';

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
// check if the file has labels 
$locat = 'FilePage.php?ProjectName=' . $ProjectName . '&FileName=' . $FileName . '&name=' . $name;
$CSVfp1 = fopen('/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/' . $file['_id'] . '.csv', "r");
$labelFlag1 = false;
$index1 = -1;
$data1 = fgetcsv($CSVfp1, 1000, ",");
if (!empty($data1)) {
  if (isset($data1[0]) && str_contains($data1[0], 'toxic')) {
    $labelFlag1 = true;
    $index1 = 0;
  } else if (isset($data1[1]) && str_contains($data1[1], 'toxic')) {
    $labelFlag1 = true;
    $index1 = 1;
  } else if (isset($data1[2]) && str_contains($data1[2], 'toxic')) {
    $labelFlag1 = true;
    $index1 = 2;
  } elseif (isset($data1[3]) && str_contains($data1[3], 'toxic')) {
    $labelFlag1 = true;
    $index1 = 3;
  }
}

$csv = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/' . $file['_id'] . '.csv';
if (!($labelFlag1)) {
  $csv = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/' . $file['_id'] . 'API.csv';
  if (file_exists($csv)) {
    $CSVfp1 = fopen($csv, "r");
    $data1 = fgetcsv($CSVfp1, 1000, ",");
    if (!empty($data1)) {
      if (isset($data1[0]) && str_contains($data1[0], 'toxic')) {
        $labelFlag1 = true;
        $index1 = 0;
      } else if (isset($data1[1]) && str_contains($data1[1], 'toxic')) {
        $labelFlag1 = true;
        $index1 = 1;
      } else if (isset($data1[2]) && str_contains($data1[2], 'toxic')) {
        $labelFlag1 = true;
        $index1 = 2;
      } elseif (isset($data1[3]) && str_contains($data1[3], 'toxic')) {
        $labelFlag1 = true;
        $index1 = 3;
      }
    }
  }
}
if (!($labelFlag1)) { //if the csv files is not created yet
  echo '<script>alert("In order to interpret you have to check the overall toxicity for ' . $file['FileName'] . ' file!");</script>';
  echo '<script>window.location="' . $locat . '";</script>';
}

$csv = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/' . $file['_id'] . '.csv';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Favicons -->
  <link href="assets/img/logo.png" rel="icon">
  <link href="assets/img/logo.png" rel="apple-touch-icon">
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Toxicity Inspector - <?php echo '@' . $_SESSION['username']; ?></title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <a href="userHome.php?name=<?php echo $name; ?>" accesskey="h"></a>

  <!-- Favicons -->
  <link href="assets/img/logo.png" rel="icon">
  <link href="assets/img/logo.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

  <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">


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
      <a href="userHome.php?name=<?php echo $name; ?>" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
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
        <a class="nav-link" href="profile.php">
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
        <a class="nav-link collapsed" href="UserProjects.php?name=<?php echo $name; ?>">
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
      <h1>Interpretation of Toxicity Level</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="userHome.php?name=<?php echo $name; ?>">Home</a></li>
          <li class="breadcrumb-item"><a href="UserProjects.php?name=<?php echo $name; ?>">Projects</a></li>
          <li class="breadcrumb-item"><a href="ProjectPage.php?name=<?php echo $name; ?>&ProjectName=<?php echo $ProjectName; ?>"><?php echo $ProjectName; ?></a></li>
          <li class="breadcrumb-item"><a href="FilePage.php?ProjectName=<?php echo $ProjectName; ?>&FileName=<?php echo $FileName; ?>&name=<?php echo $name; ?>"><?php echo $FileName; ?></a></li>
          <li class="breadcrumb-item active">Interpretation of Toxicity Level</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <a href="FilePage.php?ProjectName=<?php echo $ProjectName; ?>&FileName=<?php echo $FileName; ?>&name=<?php echo $name; ?>">
      <button class="btn btn-primary" role="button">
        <span><i class="bi bi-arrow-left-circle"></i></span> Back to your file
      </button>
    </a>
    <br><br>
    <!-- Basic Tables start -->
    <section class="section">
      <div class="card">
        <div class="card-body" style="overflow: auto;">
          <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i>
            Warning! the following content may contain sensitive language
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          <table class="table table-hover" id="example">
            <thead>
              <tr>
                <th>#</th>
                <th>Comment</th>
                <th>Toxicity Level Interpretation</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $start_row = 1; //define start row
              $i = 1; //define row count flag
              $file = fopen($csv, "r");
              $flag = true;
              $in = 888;
              while (($row = fgetcsv($file)) !== FALSE) {
                if ($flag) {
                  if (str_contains($row[0], 'comment_text'))
                    $in = 0;
                  else if (str_contains($row[1], 'comment_text'))
                    $in = 1;
                  else if (str_contains($row[2], 'comment_text'))
                    $in = 2;
                  else if (str_contains($row[3], 'comment_text'))
                    $in = 3;
                  $flag = false;
                  continue;
                }
                if ($i >= $start_row) {
                  echo '<tr>';
                  echo "<td>" . $i . "</td>";
                  echo "<td>" . $row[$in] . "</td>";
                  echo '<td><a class="btn btn-sm btn-outline-primary rounded-pill" href="interpretationOfaComment.php?ProjectName=' . $ProjectName . '&FileName=' . $FileName . '&name=' . $name . '&comment=' . $row[$in] . '&index=' . $i . '">Check</a></td>';
                  echo '</tr>';
                }
                $i++;
              }
              fclose($file);
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
    <!-- Basic Tables end -->

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
    </footer><!-- End Footer -->
  </div>

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

    .card-body {
      background: #fff;
      padding: 15px;
      box-shadow: 1px 3px 5px #aaa;
      border-radius: 5px;
    }

    .card-body .btn {
      padding: 5px 10px;
      margin: 10px 3px 10px 0;
    }

    td {
      word-break: break-word;
      /*or you can use word-break:break-all*/
      min-width: 15px;
      /*set min-width as needed*/
    }
  </style>
  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
  <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#example').DataTable();
    });
  </script>


  <!-- Template Main JS File -->
  <script src="assets/js/UserHome.js"></script>
  <script src="assets/js/main.js"></script>

</body>

</html>