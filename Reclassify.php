<?php
session_start();
include("Db.php");

$collectionU = $db->users;
$collectionP = $db->projects;
$collectionF = $db->files;
$collectionR = $db->CommentsByAPI;

if (!(isset($_SESSION["Role"])) || $_SESSION["Role"] != "User")
  echo '<script>window.location="index.php";</script>';

$username = $_SESSION["username"];
$name = $_SESSION["FirstName"];
$FileName = $_GET["FileName"];
$ProjectName = $_GET["ProjectName"];

$FileInfo = $collectionF->findOne([ //get the file info
  'FileName' => $FileName,
  'ProjectName' => $ProjectName,
  'username' => $username,
]);
$fileID = $FileInfo['_id'];
$curr_model = $FileInfo['Model'];
if ($curr_model == 'None') { // to change the model if there is no model selected
  $Model = $_GET["Model"];
  $fileEdit = $collectionF->updateOne(
    ['username' => $username, 'FileName' => $FileName, 'ProjectName' => $ProjectName], // conditions 
    ['$set' => ['Model' => $Model],], // update to set the selected model 
  );
}
$Model = $_GET["Model"];
$F1_Scores_List = $FileInfo['F1_Scores'];
$feedbackLevel = $FileInfo['FeedbackLevel'];

if ($F1_Scores_List[0] == 0) { // if the original file with no f1 
  if ($Model == 'BaseLine') {
    $command = escapeshellcmd('/Users/hourianalthunayangmail.com/opt/anaconda3/bin/python BaseLine.py "' . $fileID . '"');
    $output = shell_exec($command);
    $output = ((float)$output) * 100;
  } else if ($Model == 'Advanced') {
    $command = escapeshellcmd('/Users/hourianalthunayangmail.com/opt/anaconda3/bin/python Advanced.py "' . $fileID . '"');
    $output = shell_exec($command);
    $findme = 'PHP';
    $pos = strpos($output, $findme);
    $output = substr($output, $pos + 3,);
    $output = ((float)$output) * 100;
  }
  $fileEdit = $collectionF->updateOne(
    ['username' => $username, 'FileName' => $FileInfo['FileName'], 'ProjectName' => $ProjectName], // conditions 
    ['$set' => ['F1_Scores.0' => $output],], // update to set the selected model 
  );
}


for ($i = 1; $i < $feedbackLevel; $i++) {
  if ($F1_Scores_List[$i] == 0) {
    if ($Model == 'BaseLine') {
      $command = escapeshellcmd('/Users/hourianalthunayangmail.com/opt/anaconda3/bin/python BaseLine.py "' . $fileID . 'feedback' . $i . '"');
      $output = shell_exec($command);
      $output = ((float)$output) * 100;
      $f1 = 'F1_Scores.' . $i;
      $fileEdit = $collectionF->updateOne(
        ['username' => $username, 'FileName' => $FileName, 'ProjectName' => $ProjectName], // conditions 
        ['$set' => [$f1 => $output],], // update to set the reclassified feedback
      );
      break;
    } else if ($Model == 'Advanced') {
      $command = escapeshellcmd('/Users/hourianalthunayangmail.com/opt/anaconda3/bin/python Advanced.py "' . $fileID . 'feedback' . $i . '"');
      $output = shell_exec($command);
      $findme = 'PHP';
      $pos = strpos($output, $findme);
      $output = substr($output, $pos + 3,);
      $output = ((float)$output) * 100;
      $f1 = 'F1_Scores.' . $i;
      $fileEdit = $collectionF->updateOne(
        ['username' => $username, 'FileName' => $FileName, 'ProjectName' => $ProjectName], // conditions 
        ['$set' => [$f1 => $output],], // update to set the reclassified feedback
      );
      break;
    }
  }
}
$FileInfo = $collectionF->findOne([ //get the file info
  'FileName' => $FileName,
  'ProjectName' => $ProjectName,
  'username' => $username,
]);
$F1_Scores_List = $FileInfo['F1_Scores'];

for ($i = 1; $i < 6; $i++) {
  if ($F1_Scores_List[$i] == 0) {
    $levelForGain = $i - 1;
    break;
  }
  if ($F1_Scores_List[5] != 0) {
    $levelForGain = 5;
    break;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Favicons -->
  <link href="assets/img/logo.png" rel="icon">
  <link href="assets/img/logo.png" rel="apple-touch-icon">
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta name="viewport" content="width=device-width, initial-scale=1">

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
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>


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
    <section>
      <div class="pagetitle">
        <h1>Reclassification of your file</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="userHome.php?name=<?php echo $name; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="UserProjects.php?name=<?php echo $name; ?>">Projects</a></li>
            <li class="breadcrumb-item"><a href="ProjectPage.php?name=<?php echo $name; ?>&ProjectName=<?php echo $ProjectName; ?>"><?php echo $ProjectName; ?></a></li>
            <li class="breadcrumb-item"><a href="FilePage.php?ProjectName=<?php echo $ProjectName; ?>&FileName=<?php echo $FileName; ?>&name=<?php echo $name; ?>"><?php echo $FileName; ?></a></li>
            <li class="breadcrumb-item active">Reclassification</li>
          </ol>
        </nav>
      </div><!-- End Page Title -->
      <a href="FilePage.php?ProjectName=<?php echo $ProjectName; ?>&FileName=<?php echo $FileName; ?>&name=<?php echo $name; ?>">
        <button class="btn btn-primary" role="button">
          <span><i class="bi bi-arrow-left-circle"></i></span> Back to your file
        </button>
      </a>
      <br><br>

      <body onload="myFunction()" style="margin:0;">
        <div id="loader"></div>
        <div style="display:none;" id="myDiv" class="animate-bottom">
          <div class="title">
            <h2><?php echo 'Model:' . $Model ?></h2>
            Congratulations! You Have Completed Customizing Your Model Until Level <?php echo $levelForGain; ?>!
          </div>
          <br>
        </div>
        </div>
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Gain</h5>

              <!-- Column Chart -->
              <div id="columnChart"></div>

              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  new ApexCharts(document.querySelector("#columnChart"), {

                    chart: {
                      type: 'bar',
                      height: 337
                    },
                    plotOptions: {
                      bar: {
                        horizontal: false,
                        columnWidth: '25%',
                        endingShape: 'rounded'
                      },
                    },
                    dataLabels: {
                      enabled: false
                    },
                    stroke: {
                      show: true,
                      width: 2,
                      colors: ['transparent']
                    },
                    series: [{
                      name: 'Version Accuracy',
                      data: [<?php for ($i = 0; $i < 6; $i++)
                                echo $F1_Scores_List[$i] . ',';
                              ?>]
                    }],
                    xaxis: {
                      categories: ['Original File ', ' Level 1 ', ' Level 2 ', ' Level 3 ', ' Level 4 ', ' Level 5']
                    },


                    yaxis: {
                      title: {
                        text: 'Accuracy Level'
                      }
                    },
                    fill: {
                      opacity: 1
                    },
                    tooltip: {
                      y: {
                        formatter: function(val) {
                          return val
                        }
                      }
                    }
                  }).render();
                });
              </script>
              <!-- End Column Chart -->
            </div>
          </div>
        </div>
    </section>

    <div class="modal-footer">

      <div class="text-center">
        <button type="submit" name='submit' class="btn btn-primary">Compare</button>
      </div>

    </div>

    </div>
    </form>
    </div>
    </div>
    </div>



  </main>
  <!-- End #main -->



  <style>
    /*--------------------------------------------------------------
# Footer&loader
--------------------------------------------------------------*/


    /* Center the loader */
    #loader {
      position: absolute;
      left: 60%;
      top: 50%;
      z-index: 1;
      width: 120px;
      height: 120px;
      margin: -76px 0 0 -76px;
      border: 16px solid #f3f3f3;
      border-radius: 50%;
      border-top: 16px solid #3498db;
      -webkit-animation: spin 5s linear infinite;
      animation: spin 1.5s linear infinite;
    }

    .title {}

    @-webkit-keyframes spin {
      0% {
        -webkit-transform: rotate(0deg);
      }

      100% {
        -webkit-transform: rotate(360deg);
      }
    }

    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }

    /* Add animation to "page content" */
    .animate-bottom {
      position: relative;
      -webkit-animation-name: animatebottom;
      -webkit-animation-duration: 1s;
      animation-name: animatebottom;
      animation-duration: 1s
    }

    @-webkit-keyframes animatebottom {
      from {
        bottom: -100px;
        opacity: 0
      }

      to {
        bottom: 0px;
        opacity: 1
      }
    }

    @keyframes animatebottom {
      from {
        bottom: -100px;
        opacity: 0
      }

      to {
        bottom: 0;
        opacity: 1
      }
    }

    /*    #myDiv {
      display: none;
      text-align: center;
    } */

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

    /*     .card-body {
      background: #fff;
      padding: 15px;
      box-shadow: 1px 3px 5px #aaa;
      border-radius: 5px;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
      display: grid;
      height: 100%;
      text-align: center;
      place-items: center;
      background: #dde6f0;
    }

    .card-body .btn {
      padding: 5px 10px;
      margin: 10px 3px 10px 0;
    } */

    svg {
      position: absolute;
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


    var myVar;

    function myFunction() {
      myVar = setTimeout(showPage, 3000);
    }

    function showPage() {
      document.getElementById("loader").style.display = "none";
      document.getElementById("myDiv").style.display = "block";
    }

    const numb = document.querySelector(".numb");
    let counter = 0;
    setInterval(() => {
      if (counter == 100) {
        clearInterval();
      } else {
        counter += 1;
        numb.textContent = counter + "%";
      }
    }, 80);
  </script>

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!--   <script src="assets/vendor/echarts/echarts.min.js"></script>
 -->
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <!--   <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/chart.js/chart.min.js"></script> -->
  <!-- Template Main JS File -->
  <script src="assets/js/UserHome.js"></script>
  <script src="assets/js/main.js"></script>

</body>

</html>