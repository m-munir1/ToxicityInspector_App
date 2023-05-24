<?php
session_start();
include("Db.php");

$collectionU = $db->users;
$collectionP = $db->projects;
$collectionF = $db->files;

if (!(isset($_SESSION["Role"])) || $_SESSION["Role"] != "User")
    echo '<script>window.location="index.php";</script>';

$username = $_SESSION["username"];
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$projects = $collectionP->find(['username' => $username]);
$name = $_GET['name'];
$numberOfProjcts = 0;
$projects = $projects->toArray();
foreach ($projects as $proInfo)
    $numberOfProjcts++;

$files = $collectionF->find(['username' => $username]);
$files = $files->toArray();

$numberofEfiles = 0;
$numberofAfiles = 0;

foreach ($files as $fileInfo)
    if ($fileInfo['Languages'] == 'Arabic')
        $numberofAfiles++;
    else
        $numberofEfiles++;

$projects = $collectionP->find(['username' => $username]);
$projects = $projects->toArray();
foreach ($projects as $project) {
    $project = $collectionP->findOne(['username' => $username, 'ProjectName' => $project['ProjectName']]);
    if ($project['NumberOfEnglishFiles'] == 0 && $project['NumberOfArabicFiles'] == 0) {
        $zero = 0;
        $projectStatus = $collectionP->updateOne(
            ['ProjectName' => $project['ProjectName'], 'username' => $username],
            ['$set' => ['ProjectStatus' => 'not started', 'OverallToxicity' => $zero]]
        );
    }
}

$notStarted = 0;
$inProgress = 0;
$completed = 0;
foreach ($projects as $proInfo) {
    if ($proInfo['ProjectStatus'] == 'not started')
        $notStarted++;
    else if ($proInfo['ProjectStatus'] == 'In Progress')
        $inProgress++;
    else if ($proInfo['ProjectStatus'] == 'Completed')
        $completed++;
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
                <a class="nav-link " href="userHome.php?name=<?php echo $name; ?>">
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
            <h1>Dashboard</h1>

            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="userHome.php?name=<?php echo $name; ?>">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </nav>

            
            <div class="container">
  
           <h1 style=" text-align: center;"> Welcome <?php echo $name; ?>!</h1> 
           <p style=" text-align: center; font-weight: 600; opacity: 0.9;"> In order to start,follow these basic steps </p>
           <br>
            <div class='progress_inner'>
          <div class='progress_inner__step'>
            <label for='step-1'>Creat Project</label>
          </div>
          <div class='progress_inner__step'>
            <label for='step-2'>Upload File</label>
          </div>
          <div class='progress_inner__step'>
            <label for='step-3'>Inspect your file</label>
          </div>
          <div class='progress_inner__step'>
            <label for='step-4'>Overall Toxicity</label>
          </div>
          <div class='progress_inner__step'>
            <label for='step-5'>Other services</label>
          </div>
          
          <input checked='checked' id='step-1' name='step' type='radio'>
          <input id='step-2' name='step' type='radio'>
          <input id='step-3' name='step' type='radio'>
          <input id='step-4' name='step' type='radio'>
          <input id='step-5' name='step' type='radio'>
          <div class='progress_inner__bar'></div>
          <div class='progress_inner__bar--set'></div>
          <div class='progress_inner__tabs'>
         <div class='tab tab-0'>
        <h1>Creat Project</h1>
        <p>Creat your first project by choosing "projects" then "creat new projrct".</p>
      </div>
      <div class='tab tab-1'>
        <h1>Upload File</h1>
        <p>Upload the file you want to inspect by choosing "upload new file"</p>
      </div>
      <div class='tab tab-2'>
        <h1>Inspect your file</h1>
        <p>See the topic molding for your comments file along with the frequency of the words.</p>
      </div>
      <div class='tab tab-3'>
        <h1>Overall Toxicity</h1>
        <p>Check the overall comments toxicity of your file</p>
      </div>
      <div class='tab tab-4'>
        <h1>Other services</h1>
        <p>Try any of the services provided on your comments file!</p>
      </div>
            </div>
          </div>
          </div>

        </div><!-- End Page Title -->
        

        <section class="section dashboard" style="margin: top 230px;">
            <div class="row">
     
        
        

                   
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Overall Toxicity</h5>

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
                                            name: 'Project Toxicity',
                                            data: [<?php
                                                    $projects = $collectionP->find(['username' => $username]);
                                                    foreach ($projects as $proInfo)
                                                        if (is_nan($proInfo['OverallToxicity']))
                                                            echo 0 . ",";
                                                        else
                                                            echo $proInfo['OverallToxicity'] . ","; ?>]
                                        }],
                                        xaxis: {

                                            categories: [<?php
                                                            $projects = $collectionP->find(['username' => $username]);
                                                            foreach ($projects as $proInfo)
                                                                echo "'" . $proInfo['ProjectName'] . "',"; ?>]
                                        },

                                        yaxis: {
                                            title: {
                                                text: 'Toxicity Level'
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



                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Projects</h5>

                            <!-- Donut Chart -->
                            <div id="donutChart"></div>

                            <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    new ApexCharts(document.querySelector("#donutChart"), {
                                        series: [<?php echo $notStarted; ?>, <?php echo $inProgress; ?>, <?php echo $completed; ?>],
                                        chart: {
                                            height: 350,
                                            type: 'donut',
                                            toolbar: {
                                                show: true
                                            }
                                        },
                                        labels: ['Not Started', 'In Progress', 'Completed'],
                                    }).render();
                                });
                            </script>
                            <!-- End Donut Chart -->
                            <br>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Number Of Files</h5>

                            <!-- Bar Chart -->
                            <div id="barChart"></div>

                            <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    new ApexCharts(document.querySelector("#barChart"), {
                                        series: [{
                                            data: ['<?php echo $numberofEfiles; ?>', '<?php echo $numberofAfiles; ?>']
                                        }],
                                        chart: {
                                            type: 'bar',
                                            height: 150
                                        },
                                        plotOptions: {
                                            bar: {
                                                borderRadius: 4,
                                                horizontal: true,
                                            }
                                        },
                                        dataLabels: {
                                            enabled: true,

                                            title: {
                                                text: 'number of files'
                                            }

                                        },
                                        xaxis: {
                                            categories: ['English Files', 'Arabic Files'],
                                        }
                                    }).render();
                                });
                            </script>
                            <!-- End Bar Chart -->

                        </div>
                    </div>
                </div>

                <!-- Sales Card -->

                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Number Of Projects</span></h5>

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                </div>
                                <div class="ps-3">
                                    <h2><?php echo $numberOfProjcts; ?></h2>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- End Sales Card -->

                </div>
        </section>

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
@import url("https://fonts.googleapis.com/css?family=Nunito:400,900");
body .progress_inner #step-1:checked + input + input + input + input + div + div + div + div > .box_base, body .progress_inner #step-5:checked + div + div + div + div > .box_base, body .progress_inner #step-4:checked + input + div + div + div + div > .box_base, body .progress_inner #step-3:checked + input + input + div + div + div + div > .box_base, body .progress_inner #step-2:checked + input + input + input + div + div + div + div > .box_base {
  top: 80%;
  left: 0px;
  opacity: 1;
}
body .progress_inner #step-1:checked + input + input + input + input + div + div + div + div > .box_item, body .progress_inner #step-5:checked + div + div + div + div > .box_item, body .progress_inner #step-4:checked + input + div + div + div + div > .box_item, body .progress_inner #step-3:checked + input + input + div + div + div + div > .box_item, body .progress_inner #step-2:checked + input + input + input + div + div + div + div > .box_item {
  top: -10px;
  left: 0px;
  opacity: 0;
}

body .progress_inner #step-2:checked + input + input + input + div + div + div + div > .box_lid, body .progress_inner #step-5:checked + div + div + div + div > .box_lid, body .progress_inner #step-4:checked + input + div + div + div + div > .box_lid {
  top: -10px;
  left: 0px;
  opacity: 0;
}
body .progress_inner #step-2:checked + input + input + input + div + div + div + div > .box_item, body .progress_inner #step-5:checked + div + div + div + div > .box_item, body .progress_inner #step-4:checked + input + div + div + div + div > .box_item {
  top: -10px;
  left: 0px;
  opacity: 1;
}

body .progress_inner #step-3:checked + input + input + div + div + div + div > .box_item, body .progress_inner #step-5:checked + div + div + div + div > .box_item, body .progress_inner #step-4:checked + input + div + div + div + div > .box_item {
  top: 10px;
  left: 0px;
  opacity: 1;
}
body .progress_inner #step-3:checked + input + input + div + div + div + div > .box_lid, body .progress_inner #step-5:checked + div + div + div + div > .box_lid, body .progress_inner #step-4:checked + input + div + div + div + div > .box_lid {
  top: -1px;
  left: 0px;
  opacity: 1;
}
body .progress_inner #step-3:checked + input + input + div + div + div + div > .box_ribbon, body .progress_inner #step-5:checked + div + div + div + div > .box_ribbon, body .progress_inner #step-4:checked + input + div + div + div + div > .box_ribbon {
  top: 70%;
  left: 0px;
  opacity: 0;
}
body .progress_inner #step-3:checked + input + input + div + div + div + div > .box_bow, body .progress_inner #step-5:checked + div + div + div + div > .box_bow, body .progress_inner #step-4:checked + input + div + div + div + div > .box_bow {
  top: 0px;
  left: 0px;
  opacity: 0;
}

body .progress_inner #step-4:checked + input + div + div + div + div > .box_ribbon, body .progress_inner #step-5:checked + div + div + div + div > .box_ribbon {
  top: 50%;
  left: 0px;
  opacity: 1;
}
body .progress_inner #step-4:checked + input + div + div + div + div > .box_bow, body .progress_inner #step-5:checked + div + div + div + div > .box_bow {
  top: -10px;
  left: 0px;
  opacity: 1;
}

body .progress_inner #step-5:checked + div + div + div + div > .box_tag {
  top: 10px;
  left: 20px;
  opacity: 1;
}
body .progress_inner #step-5:checked + div + div + div + div > .box_string {
  top: 10px;
  left: 20px;
  opacity: 1;
}

* {
  box-sizing: border-box;
}

body .progress_inner__status .box_string, body .progress_inner__status .box_tag, body .progress_inner__status .box_bow__right, body .progress_inner__status .box_bow__left, body .progress_inner__status .box_bow, body .progress_inner__status .box_ribbon, body .progress_inner__status .box_item, body .progress_inner__status .box_base, body .progress_inner, body .progress_inner__step:before {
  position: absolute;
  left: 0;
  right: 0;
  top: 30%;
  transform: translateY(-50%);
  margin: auto;
}

body .progress_inner__bar--set, body .progress_inner__bar {
  height: 6px;
  left: 10%;
  background: repeating-linear-gradient(45deg, #1ea4ec, #1ea4ec 4px, #1f8bc5 4px, #1f8bc5 10px);
  transition: width 800ms cubic-bezier(0.915, 0.015, 0.3, 1.005);
  border-radius: 6px;
  width: 0;
  position: relative;
  z-index: -1;
}

body .progress_inner__step:before {
  width: 30px;
  height: 30px;
  color: #012970;
  background: white;
  line-height: 30px;
  border: 3px solid #a6cde2;
  font-size: 12px;
  top: 3px;
  border-radius: 100%;
  transition: all 0.4s;
  cursor: pointer;
  pointer-events: none;
}

body .progress_inner__step {
  width: 20%;
  font-size: 20px;
  padding: 0 10px;
  transition: all 0.4s;
  float: left;
  text-align: center;
  position: relative;
}
body .progress_inner__step label {
  padding-top: 50px;
  top: -30px;;
  display: block;
  position: relative;
  cursor: pointer;
}
body .progress_inner__step:hover {
  color:rgb(27, 124, 222);
}
body .progress_inner__step:hover:before {
  color: white;
  background: #1ea4ec;
}

body {
  font-family: "Open Sans", sans-serif;
  color: #012970;
  font-weight: 900;
}
body .progress_inner {
  height: auto;
  width: 1000px;
  position: sticky;
  margin-bottom: 230px;

}


body .progress_inner #step-5:checked + div {
  width: 80%;
}
body .progress_inner #step-5:checked + div + div + div > .tab:nth-of-type(5) {
  opacity: 1;
  top: 0;
}
body .progress_inner #step-5:checked + div + div + div + div {
  right: 10%;
}
body .progress_inner #step-4:checked + input + div {
  width: 60%;
}
body .progress_inner #step-4:checked + input + div + div + div > .tab:nth-of-type(4) {
  opacity: 1;
  top: 0;
}
body .progress_inner #step-4:checked + input + div + div + div + div {
  right: 30%;
}
body .progress_inner #step-3:checked + input + input + div {
  width: 40%;
}
body .progress_inner #step-3:checked + input + input + div + div + div > .tab:nth-of-type(3) {
  opacity: 1;
  top: 0;
}
body .progress_inner #step-3:checked + input + input + div + div + div + div {
  right: 50%;
}
body .progress_inner #step-2:checked + input + input + input + div {
  width: 20%;
}
body .progress_inner #step-2:checked + input + input + input + div + div + div > .tab:nth-of-type(2) {
  opacity: 1;
  top: 0;
}
body .progress_inner #step-2:checked + input + input + input + div + div + div + div {
  right: 70%;
}
body .progress_inner #step-1:checked + input + input + input + input + div {
  width: 0%;
}
body .progress_inner #step-1:checked + input + input + input + input + div + div + div > .tab:nth-of-type(1) {
  opacity: 1;
  top: 0;
}
body .progress_inner #step-1:checked + input + input + input + input + div + div + div + div {
  right: 90%;
}
body .progress_inner__step:nth-of-type(1):before {
  content: "1";
}
body .progress_inner__step:nth-of-type(2):before {
  content: "2";
}
body .progress_inner__step:nth-of-type(3):before {
  content: "3";
}
body .progress_inner__step:nth-of-type(4):before {
  content: "4";
}
body .progress_inner__step:nth-of-type(5):before {
  content: "5";
}
body .progress_inner__bar--set {
  width: 80%;
  top: -6px;
  background: #70afd0;
  position: relative;
  z-index: -2;
}
body .progress_inner__tabs .tab {
  opacity: 0;
  position: absolute;
  top: 40px;
  text-align: center;
  margin-top: 80px;
  padding: 30px;
  background: white;
  border-radius: 10px;
  transition: all 0.2s;

}
body .progress_inner__tabs .tab h1 {
  width: 900px;
  margin: 0;
}
body .progress_inner__tabs .tab p {
    
  font-weight: 600;
  opacity: 0.9;
}
body .progress_inner__status {
  width: 40px;
  height: 40px;
  top: -80px;
  transition: right 800ms cubic-bezier(0.915, 0.015, 0.3, 1.005);
  transform: translateX(50%);
  position: absolute;
}
body .progress_inner__status div {
  opacity: 0;
  transition: all 600ms cubic-bezier(0.915, 0.015, 0.3, 1.005);
  transition-delay: 300ms;
}
body .progress_inner__status div {
  position: absolute;
}
body .progress_inner__status .box_base {
  background: repeating-linear-gradient(45deg, #986c5d, #986c5d 2px, #775144 2px, #775144 4px);
  width: 36px;
  height: 40px;
  z-index: 1;
  border-radius: 1px;
}
body .progress_inner__status .box_lid {
  width: 40px;
  height: 13.3333333333px;
  background: #775144;
  z-index: 2;
  border-radius: 1px;
  top: 0;
}
body .progress_inner__status .box_item {
  width: 20px;
  height: 20px;
  background: #be69d2;
  z-index: 0;
  border-radius: 4px;
  transform: rotate(45deg);
}
body .progress_inner__status .box_ribbon {
  width: 10px;
  height: 42px;
  background: #ee0f29;
  z-index: 4;
  border-radius: 1px;
}
body .progress_inner__status .box_bow__right, body .progress_inner__status .box_bow__left {
  width: 6px;
  height: 10px;
  background: #be0c21;
  position: absolute;
  z-index: 3;
  opacity: 1;
  border-radius: 1px;
}
body .progress_inner__status .box_bow {
  top: -6px;
  z-index: 1;
  transition-delay: 500ms;
}
body .progress_inner__status .box_bow__left {
  left: 6px;
  transform: rotate(45deg);
}
body .progress_inner__status .box_bow__right {
  left: -4px;
  transform: rotate(-45deg);
}
.pagetitle {
  margin-bottom: 100px;
}
body .progress_inner__status .box_tag {
  width: 20px;
  height: 10px;
  background: #487ac7;
  z-index: 4;
  transform: rotate(-10deg) translateX(-40px) translateY(0px);
  border-radius: 2px;
  transition-delay: 500ms;
}
body .progress_inner__status .box_string {
  width: 17px;
  height: 2px;
  background: #343434;
  z-index: 4;
  transform: rotate(-39deg) translateX(-22px) translateY(-12px);
}
body .progress_inner input[type=radio] {
  display: none;
}



  
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
    <!-- <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <span data-purecounter-start="0" data-purecounter-end="232" data-purecounter-duration="" class="purecounter"></span> -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/echarts/echarts.min.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="assets/vendor/chart.js/chart.min.js"></script>


    <!-- Template Main JS File -->
    <script src="assets/js/UserHome.js"></script>
    <script src="assets/js/main.js"></script>

</body>

</html>