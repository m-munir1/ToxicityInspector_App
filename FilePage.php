<?php
session_start();
include("Db.php");

$username = $_SESSION["username"];

if (!(isset($_SESSION["Role"])) || $_SESSION["Role"] != "User")
  echo '<script>window.location="signup.php"; alert("Error! Please try again");</script>';

$collectionF = $db->files;
$collectionU = $db->users;
$collectionP = $db->projects;
$collectionR = $db->CommentsByAPI;

$name = $_GET['name'];
$ProjectName = $_GET['ProjectName'];
$FileName = $_GET['FileName'];
$files = $collectionF->findOne(['username' => $username, 'ProjectName' => $ProjectName, 'FileName' => $FileName]);
$fileID = $files['_id'];
$Model = $files['Model'];
$language = $files['Languages'];
$feedbackLevel = $files['FeedbackLevel'];
$UploadedFile = $files['UploadedFile'];
$F1_Scores_List = $files['F1_Scores'];
$project = $collectionP->findOne(['username' => $username, 'ProjectName' => $ProjectName]);
$efiles = $project['NumberOfEnglishFiles'];
$afiles = $project['NumberOfArabicFiles'];
$file = $collectionF->find(['username' => $username, 'ProjectName' => $ProjectName, 'Languages' => $language]);
$file = $file->toArray();
$count = 0;
foreach ($file as $f) {
  if ($f['FileName'] != $FileName) {
    $filesSameID[] = $f['_id'];
    $filesSameName[] = $f['FileName'];
    $count++;
  }
  if($count==0){
    $filesSameID=null;
    $filesSameName=null;
  }
}

if ($language == 'Arabic') {
  $filesN = $efiles;
  $file2 = $collectionF->find(['username' => $username, 'ProjectName' => $ProjectName, 'Languages' => 'English']);
  $file2 = $file2->toArray();
  foreach ($file2 as $f) {
      $filesDiffID[] = $f['_id'];
      $filesDiffName[] = $f['FileName'];
  }
  if($efiles==0){
  $filesDiffID=null;
  $filesDiffName=null;
  }
} else {
  $filesN = $afiles;
  $file2 = $collectionF->find(['username' => $username, 'ProjectName' => $ProjectName, 'Languages' => 'Arabic']);
  $file2 = $file2->toArray();
  foreach ($file2 as $f) {
      $filesDiffID[] = $f['_id'];
      $filesDiffName[] = $f['FileName'];
  }
  if($afiles==0){
    $filesDiffID=null;
    $filesDiffName=null;
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

  <title>Toxicity Inspector - <?php echo $FileName . ' file'; ?></title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <a href="userHome.php?name=<?php echo $name; ?>" accesskey="h"></a>


  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/userHome.css" rel="stylesheet">

  <script type="text/javascript" src="https://cdn.weglot.com/weglot.min.js"></script>
  <script>
    Weglot.initialize({
      api_key: 'wg_7971b0c8d0752818fdd77c7810fb22808'
    });
  </script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
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
      <h1>File Name: <?php echo $FileName; ?></h1> <!-- link it----------- -->
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="userHome.php?name=<?php echo $name; ?>">Home</a></li> <!-- link it----------- -->
          <li class="breadcrumb-item"><a href="UserProjects.php?name=<?php echo $name; ?>">Projects</a></li>
          <li class="breadcrumb-item"><a href="ProjectPage.php?name=<?php echo $name; ?>&ProjectName=<?php echo $ProjectName; ?>"><?php echo $ProjectName; ?></a></li>
          <li class="breadcrumb-item active"><?php echo $FileName; ?></li> <!-- link it----------- -->
        </ol>
      </nav>
    </div><!-- End Page Title -->



    <!-- ======= Services Section ======= -->
    <section id="services" class="services">
      <div class="container">

        <div class="section-title" data-aos="fade-up">
          <h2>Services</h2>
          <p>We Provide the Following Services for your Comments File</p>
        </div>

        <div class="row">

          <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="100">
              <div class="icon"><i class='bx bx-scatter-chart'></i></div>
              <h4 class="title">Inspection of the data distribution</h4>
              <p class="description">See the topic molding for your comments file along with the frequency of the words.</p>
              <br>
              <a href="InspectData.php?name=<?php echo $name; ?>&ProjectName=<?php echo $ProjectName; ?>&FileName=<?php echo $FileName; ?>" class="d-flex justify-content-center">
                <button type="button" class="btn btn-light btn-sm">Inspect</button>
              </a>
            </div>
          </div>

          <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="100">
              <div class="icon"><i class="bx bx-comment-error"></i></div>
              <h4 class="title">Overall Toxicity of comments</h4>
              <p class="description">Check the overall comments toxicity.</p>
              <br><br><br>
              <a href="overallToxicity.php?name=<?php echo $name; ?>&ProjectName=<?php echo $ProjectName; ?>&FileName=<?php echo $FileName; ?>" class="d-flex justify-content-center">
                <button type="button" class="btn btn-light btn-sm">Check</button>
              </a>
            </div>
          </div>

          <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="100">
              <div class="icon"><i class="bi bi-ui-checks-grid"></i></div>
              <h4 class="title">Interpretation of Toxicity Level</h4>
              <p class="description">See the contributed features that affect the health of your comments.</p>
              <br>
              <a href="interpretation.php?name=<?php echo $name; ?>&ProjectName=<?php echo $ProjectName; ?>&FileName=<?php echo $FileName; ?>" class="d-flex justify-content-center">
                <button type="button" class="btn btn-light btn-sm">Interpret</button>
              </a>
            </div>
          </div>

          <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="100">
              <div class="icon"><i class="bx bx-git-compare"></i></div>
              <h4 class="title">Comparison of the Toxicity Level</h4>
              <p class="description">Compare between any two files.</p>
              <br><br>
              <a href="" class="d-flex justify-content-center" data-bs-toggle="modal" data-bs-target="#inlineForm">
                <button type="button" class="btn btn-light btn-sm">Compare</button>
              </a>
            </div>
          </div>
        </div>
        <br>
        <div class="row d-flex justify-content-center">

          <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="100">
              <div class="icon"><i class="bi bi-pencil"></i></div>
              <h4 class="title">Enhancement of the toxicity result</h4>
              <p class="description">Give your feedback on the toxicity result.</p>
              <br><br><br><br><br>
              <a href="feedback.php?name=<?php echo $name; ?>&ProjectName=<?php echo $ProjectName; ?>&FileName=<?php echo $FileName; ?>" class="d-flex justify-content-center">
                <button type="button" class="btn btn-light btn-sm">Give my feedback</button>
              </a>
              <br>
            </div>
          </div>

          <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="100">
              <div class="icon"><i class="bi bi-tag"></i></div>
              <h4 class="title">Customize your model</h4>
              <p class="description">Enable the users to get instance enhancement based on the feedback provided.</p>
              <br><br><br>
              <a href="" class="d-flex justify-content-center" data-bs-toggle="modal" data-bs-target="#inlineForm3">
                <button type="button" class="btn btn-light btn-sm">Customize</button>
              </a>
            </div>
          </div>


        </div>
        <div class="modal fade text-left" id="inlineForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">Comparison</h4>

                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
              </div>
              <form method="POST" action="comparison.php?name=<?php echo $name; ?>&ProjectName=<?php echo $ProjectName; ?>&FileName=<?php echo $FileName; ?>">

                <div class="modal-body">

                  <fieldset class="row mb-3">
                    <legend class="col-form-label col-sm-2 pt-0">Compare:</legend>
                    <div class="col-sm-10">
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios1" value="option1" checked>
                        <label class="form-check-label" for="gridRadios1">
                          By Files
                        </label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios2" value="option2">
                        <label class="form-check-label" for="gridRadios2">
                          By Languages
                        </label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios3" value="option3">
                        <label class="form-check-label" for="gridRadios3">
                          With Given Feedback
                        </label>
                      </div>
                    </div>
                  </fieldset>


                  <div class="row mb-3" id="toHide">
                    <label class="col-sm-2 col-form-label">Select Files:</label>
                    <div class="col-sm-10" >
                      <select class="form-select" aria-label="Default select example" id="wlimit" name="file" title="Please select file from the list." required>
                        <?php

                        foreach ($file as $fileINFO) {
                          if ($FileName != $fileINFO['FileName']) {
                            echo "<option value='" . $fileINFO['_id'] . "'>" . $fileINFO['FileName'] . "</option>";
                          }
                        }

                        ?>
                      </select>
                      </div>
                      </div>
                      <script>
                        $("#gridRadios1").click(function() {
                          $('#toHide').show();
                          $('#wlimit').empty();
                          var i = 0;
                          var fileNumber = <?php echo $count; ?>;
                          if (fileNumber != 0) {
                            var filesID = <?php echo json_encode($filesSameID); ?>;
                            var filesName = <?php echo json_encode($filesSameName); ?>;
                            for (let key in filesID) {
                              let newOption = new Option(filesName[i], filesID[key]['$oid']);
                              const select = document.querySelector('#wlimit');
                              select.add(newOption, undefined);
                              i++;
                            }
                          }
                        });

                        $("#gridRadios2").click(function() {
                          $('#toHide').show();
                          $('#wlimit').empty();
                          var i = 0;
                          var fileNumber = <?php echo $filesN ?>;
                          if (fileNumber != 0) {
                            var filesID = <?php echo json_encode($filesDiffID); ?>;
                            var filesName = <?php echo json_encode($filesDiffName); ?>;
                            for (let key in filesID) {
                              let newOption = new Option(filesName[i], filesID[key]['$oid']);
                              const select = document.querySelector('#wlimit');
                              select.add(newOption, undefined);
                              i++;
                            }
                          }

                        });
                        $("#gridRadios3").click(function() {
                          $('#toHide').hide();
                        });
                      </script>
                    
                </div>

                <div class="modal-footer">
                  <div class="text-center">
                    <button type="submit" name='submit' class="btn btn-primary">Compare</button>
                  </div>

                </div>
              </form>
            </div>

          </div>
        </div>
      </div>

      <!--new reclassify-->
      <div class="modal fade text-left" id="inlineForm3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="myModalLabel33">Cutomize your model</h4>

              <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
            </div>
            <form onsubmit="return ChooseModel('<?php echo $FileName; ?>', '<?php echo $name; ?>', '<?php echo $ProjectName; ?>')">
              <?php
              if ($Model == 'None' && $feedbackLevel != 1) {
                echo '<div class="modal-body">

                <fieldset class="row mb-3">
                  <legend>Which model do you prefer?</legend>
                  <div class="col-sm-10">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="model" id="gridRadios1" value="BaseLine" checked>
                      <label class="form-check-label" for="gridRadios1">
                        Model1 (BaseLine)
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="model" id="gridRadios2" value="Advanced">
                      <label class="form-check-label" for="gridRadios2">
                        Model2 (Advanced)
                      </label>
                    </div>

                  </div>';
              } else if ($Model != 'None' && $feedbackLevel != 1) {
                echo '<div class="modal-body">

                <fieldset class="row mb-3">
                  <legend>You chose the ' . $Model . ' Model!</legend>
<label>
Start Reclassifying Your file Based on Your Feedback! <br>
List of checked levels:<br>';
                for ($i = 1; $i < 6; $i++) {
                  if ($F1_Scores_List[$i] != 0) {
                    echo 'Level ' . $i . '<br>';
                  }
                }
                echo '</label>';
              } else if ($feedbackLevel == 1) {
                echo '<div class="modal-body">
                <fieldset class="row mb-3">
                  <div class="col-sm-10">
                    <div class="form-check">
                    <legend>Give Your feedback first!</legend>
                    </div>
                  </div>';
              }
              ?>
              </fieldset>
          </div>
          <div class="modal-footer">

            <div class="text-center">
              <button type="submit" class="btn btn-primary">Customize</button>

            </div>
          </div>
          </form>
        </div>
      </div>
      </div>
    </section><!-- End Services Section -->
    <script>
      function ChooseModel(FileName, name, ProjectName) {
        const model = '<?php echo $Model; ?>';
        var loc = "Reclassify.php?ProjectName=" + ProjectName + "&name=" + name + "&FileName=" + FileName + "&Model=" + model;
        if (model == 'None') {
          const curr = document.querySelector('input[name="model"]:checked').value;
          var con = confirm("Once you choose the model you can't change it!") // check if the user sure about the selected model 
          if (con) // if user clicks ok
            loc = "Reclassify.php?ProjectName=" + ProjectName + "&name=" + name + "&FileName=" + FileName + "&Model=" + curr;
          else // if user clicks cancle
            loc = "FilePage.php?ProjectName=" + ProjectName + "&name=" + name + "&FileName=" + FileName;
        }
        window.location = loc;
        return false;
      }
    </script>
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
    .section-title {
      text-align: center;
      padding-bottom: 30px;
    }

    .section-title h2 {
      font-size: 32px;
      font-weight: bold;
      text-transform: uppercase;
      position: relative;
      color: #012970;
    }

    .section-title h2::before,
    .section-title h2::after {
      content: "";
      width: 50px;
      height: 2px;
      background: #54b5f7a1;
      display: inline-block;
    }

    .section-title h2::before {
      margin: 0 15px 10px 0;
    }

    .section-title h2::after {
      margin: 0 0 10px 15px;
    }

    .section-title p {
      margin: 15px 0 0 0;
    }

    .services .icon-box {
      padding: 30px;
      position: relative;
      overflow: hidden;
      background: #fff;
      box-shadow: 0 0 29px 0 rgba(68, 88, 144, 0.12);
      transition: all 0.3s ease-in-out;
      border-radius: 8px;
      z-index: 1;
    }

    .services .icon-box::before {
      content: "";
      position: absolute;
      background: #e1f0fa;
      right: -60px;
      top: -40px;
      width: 100px;
      height: 100px;
      border-radius: 50px;
      transition: all 0.3s;
      z-index: -1;
    }

    .services .icon-box:hover::before {
      background: #54b5f7a1;
      right: 0;
      top: 0;
      width: 100%;
      height: 100%;
      border-radius: 0px;
    }

    .services .icon {
      margin: 0 auto 20px auto;
      padding-top: 10px;
      display: inline-block;
      text-align: center;
      border-radius: 50%;
      width: 60px;
      height: 60px;
      background: #54b5f7a1;
      transition: all 0.3s ease-in-out;
    }

    .services .icon i {
      font-size: 36px;
      line-height: 1;
      color: #fff;
    }

    .services .title {
      font-weight: 700;
      margin-bottom: 15px;
      font-size: 18px;
    }

    .services .title a {
      color: #111;
    }

    .services .description {
      font-size: 15px;
      line-height: 28px;
      margin-bottom: 0;
    }

    .services .icon-box:hover .title,
    .services .icon-box:hover .description {
      color: #fff;
    }

    .services .icon-box:hover .icon {
      background: #fff;
    }

    .services .icon-box:hover .icon i {
      color: #54b5f7a1;
    }

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