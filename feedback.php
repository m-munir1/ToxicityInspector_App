<?php
session_start();
include("Db.php");

if (!(isset($_SESSION["Role"])) || $_SESSION["Role"] != "User")
  echo '<script>window.location="signIn.php"; alert("You don\'t have access to the requested page!, Please sign in first.");</script>';

$collectionU = $db->users;
$collectionP = $db->projects;
$collectionF = $db->files;

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

$csv = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/' . $file['_id'] . '.csv';
$feedbackLevel = $file['FeedbackLevel'];
if (($handle = fopen($csv, "r")) !== FALSE) { //to check if there is labels in the csv file
  $firstLine = fgetcsv($handle);

  if (!(in_array("toxic", $firstLine)) && !file_exists($_SERVER['DOCUMENT_ROOT'] . '/2_ToxicityInspector_App/Uploads/' . $file['_id'] . 'API.csv')) {
    fclose($handle);
    echo '<script>alert("In order to give your feedback you have to check the overall toxicity for ' . $file['FileName'] . ' file!");</script>';
    echo '<script>window.location="FilePage.php?ProjectName=' . $ProjectName . '&FileName=' . $FileName . '&name=' . $name . '";</script>';
  }

  $csv = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/' . $file['_id'] . 'train.csv';
  $csvTest = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/' . $file['_id'] . 'test.csv';
}
if ($feedbackLevel >= 6) {
  echo '<script>alert("You reach to the end of your File!");</script>';
  echo '<script>window.location="FilePage.php?ProjectName=' . $ProjectName . '&FileName=' . $FileName . '&name=' . $name . '";</script>';
}


$feedbackFileTrain = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/' . $file['_id'] . 'feedback' . strval($feedbackLevel) . 'train.csv'; //in which format should it be? fID + fbID or fbID
$feedbackFileTest = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/' . $file['_id'] . 'feedback' . strval($feedbackLevel) . 'test.csv'; //in which format should it be? fID + fbID or fbID

//read the train csv file and store it to array
$entriesC = array();
$entriesT = array();
$row = 1;
if (($handle = fopen($csv, "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    if ($row == 1) {
      $columns = $data;
      for ($i = 0; $i < count($columns); $i++) {
        $col = explode(' ', $columns[$i]);
        $columns[$i] = strtolower($col[0]);
      }
    } else {
      $entry = array();
      for ($c = 0; $c < count($data); $c++) {
        $entry[$columns[$c]] = $data[$c];
      }
      $entriesC[] = $entry['comment_text'];
      $entriesT[] = $entry['toxic'];
    }
    $row++; // number of comments in the orignal train file
  }
  fclose($handle);
}
//read the test csv file and store it to array
$entriesCtest = array();
$entriesTtest = array();
$rowTest = 1;
if (($handle = fopen($csvTest, "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    if ($rowTest == 1) {
      $columns = $data;
      for ($i = 0; $i < count($columns); $i++) {
        $col = explode(' ', $columns[$i]);
        $columns[$i] = strtolower($col[0]);
      }
    } else {
      $entry = array();
      for ($c = 0; $c < count($data); $c++) {
        $entry[$columns[$c]] = $data[$c];
      }
      $entriesCtest[] = $entry['comment_text'];
      $entriesTtest[] = $entry['toxic'];
    }
    $rowTest++; // number of comments in the orignal test file
  }
  fclose($handle);
}
// echo '<script>alert("test at open ' . $rowTest . '");</script>';
$p = 0;

for ($i = 1; $i < $feedbackLevel; $i++)
  $p = $p + 20;

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

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.2/css/dataTables.bootstrap5.min.css">
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

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
      <h1>Enhancement of the toxicity result</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="userHome.php?name=<?php echo $name; ?>">Home</a></li>
          <li class="breadcrumb-item"><a href="UserProjects.php?name=<?php echo $name; ?>">Projects</a></li>
          <li class="breadcrumb-item"><a href="ProjectPage.php?name=<?php echo $name; ?>&ProjectName=<?php echo $ProjectName; ?>"><?php echo $ProjectName; ?></a></li>
          <li class="breadcrumb-item"><a href="FilePage.php?ProjectName=<?php echo $ProjectName; ?>&FileName=<?php echo $FileName; ?>&name=<?php echo $name; ?>"><?php echo $FileName; ?></a></li>
          <li class="breadcrumb-item active">Enhancement of the toxicity result</li>
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
          Your feedback progress:
          <br>
          <div class="progress" style="height: 30px;">
            <div class="progress-bar" role="progressbar" style='width: <?php echo $p ?>%' aria-valuenow="<?php echo $p; ?>" aria-valuemin="16" aria-valuemax="100"><?php echo $p; ?>%</div>
          </div>
          <br>
          <table class="table table-hover" id="example">
            <thead>
              <tr>
                <th>#</th>
                <th>Comment</th>
                <th>Toxicity</th>
                <th>Your feedback</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // count the shown and hidden comments for train
              $countNewTrain = 0;
              $countNewTest = 0;
              $TrainAllcomments = 0;
              $TrainskippedComments = 0;
              $TestAllcomments = 0;
              $TestskippedComments = 0;
              $rowTest = $rowTest - 1; // to skip the head 
              $row = $row - 1; // to skip the head 
              if ($feedbackLevel == 1) {
                $TrainAllcomments = $row / 5;
                $TrainskippedComments = 0;

                $TestAllcomments = $rowTest / 5;
                $TestskippedComments = 0;
              } else if ($feedbackLevel == 2) {
                $TrainAllcomments = $row / 5;
                $TrainAllcomments = $TrainAllcomments * 2;
                $TrainskippedComments =  $row / 5;

                $TestAllcomments = $rowTest / 5;
                $TestAllcomments = $TestAllcomments * 2;
                $TestskippedComments = $rowTest / 5;
              } else if ($feedbackLevel == 3) {
                $TrainAllcomments = $row / 5;
                $TrainAllcomments = $TrainAllcomments * 3;
                $TrainskippedComments =  $row / 5;
                $TrainskippedComments =  $TrainskippedComments * 2;

                $TestAllcomments = $rowTest / 5;
                $TestAllcomments = $TestAllcomments * 3;
                $TestskippedComments = $rowTest / 5;
                $TestskippedComments = $TestskippedComments * 2;
              } else if ($feedbackLevel == 4) {
                $TrainAllcomments = $row / 5;
                $TrainAllcomments = $TrainAllcomments * 4;
                $TrainskippedComments =  $row / 5;
                $TrainskippedComments =  $TrainskippedComments * 3;

                $TestAllcomments = $rowTest / 5;
                $TestAllcomments = $TestAllcomments * 4;
                $TestskippedComments = $rowTest / 5;
                $TestskippedComments = $TestskippedComments * 3;
              } else if ($feedbackLevel == 5) {
                $TrainAllcomments = $row;
                $TrainskippedComments =  $row / 5;
                $TrainskippedComments =  $TrainskippedComments * 4;

                $TestAllcomments = $rowTest;
                $TestskippedComments = $rowTest / 5;
                $TestskippedComments = $TestskippedComments * 4;
              }

              if ($feedbackLevel == 5) {
                $TrainAllcomments--;
                $TestAllcomments--;
              }

              for ($i = 0; $i < $TrainAllcomments; $i++) { // new array with shown comments only (train)
                if ($i >= $TrainskippedComments) {
                  $TrainLabels[] = $entriesT[$i];
                  $TrainCommments[] = $entriesC[$i];
                  $countNewTrain++;
                }
              }
              for ($i = 0; $i < $TestAllcomments; $i++) { // new array with shown comments only (test)
                if ($i >= $TestskippedComments) {
                  $TestLabels[] = $entriesTtest[$i];
                  $TestComments[] = $entriesCtest[$i];
                  $countNewTest++;
                }
              }
              for ($i = 0; $i < $countNewTrain; $i++) {
                $j = $i + 1;

                echo '<tr>';
                echo '<td>' . $j . '</td>';
                echo '<td>' . $TrainCommments[$i] . '</td>';
                if ($TrainLabels[$i] == "1") //toxic
                  echo '<td><span class="badge bg-danger">Toxic</span></td>';
                elseif ($TrainLabels[$i] == "0") //non-toxic
                  echo '<td><span class="badge bg-success">Non-Toxic</span></td>';
                echo '<td>
                      
                        <div class="col-sm-10">
  
                        <div class="form-check">
                        
                          <input class="form-check-input" type="radio" name="' . $i . '" id="' . $i . '" value="1">
                          <label class="form-check-label" for="' . $i . '">
                            Toxic
                          </label>
                        </div>
  
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="' . $i . '" id="' . $i . '" value="0">
                          <label class="form-check-label" for="' . $i . '">
                            Non-Toxic
                          </label>
                        </div>
                        <input style="visibility: hidden;" type="radio" id="' . $i . '" name="' . $i . '" value="2" checked>
  
                        </div></td>';
                echo '</tr>';
              }
              $allComments = $countNewTrain;
              for ($i = 0; $i < $countNewTest; $i++, $allComments++) {
                $j = $allComments + 1;
                echo '<tr>';
                echo '<td>' . $j . '</td>';
                echo '<td>' . $TestComments[$i] . '</td>';

                if ($TestLabels[$i] == "1") //toxic
                  echo '<td><span class="badge bg-danger">Toxic</span></td>';
                elseif ($TestLabels[$i] == "0") //non-toxic
                  echo '<td><span class="badge bg-success">Non-Toxic</span></td>';
                echo '<td>
                      
                        <div class="col-sm-10">
  
                        <div class="form-check">
                        
                          <input class="form-check-input" type="radio" name="' . $allComments . '" id="' . $allComments . '" value="1">
                          <label class="form-check-label" for="' . $allComments . '">
                            Toxic
                          </label>
                        </div>
  
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="' . $allComments . '" id="' . $allComments . '" value="0">
                          <label class="form-check-label" for="' . $allComments . '">
                            Non-Toxic
                          </label>
                        </div>
                        <input style="visibility: hidden;" type="radio" id="' . $allComments . '" name="' . $allComments . '" value="2" checked>
  
                        </div></td>';
                echo '</tr>';
              }
              ?>
            </tbody>
          </table>
          <div class="text-center">
            <div hidden>kkk</div>
            <button type="submit" name="submit" value="submit" class="btn btn-primary" id="submit">Submit</button>

          </div>
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
  <script src="https://cdn.jsdelivr.net/npm/json2csv"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
  <appSettings>
    <add key="aspnet:MaxJsonDeserializerMembers" value="15000000000" />
  </appSettings>
  <script>
    $(document).ready(function() {
      var table = $('#example').DataTable();
      document.getElementById('submit').onclick = function() {
        var data = table.$('input, select').serialize();

        const myArray = data.split("&");
        const feedback = new Array();
        const feedbackTrain = new Array();
        const feedbackTest = new Array();
        var TrainCount = <?php echo $countNewTrain; ?>;
        var TestCount = <?php echo $countNewTest; ?>;
        var all = TrainCount + TestCount;

        for (let i = 0; i < all; i++) {
          var num = myArray[i].indexOf('=');
          var index = myArray[i].substring(0, num);
          var value = myArray[i].substring(num + 1);
          feedback[index] = value;
        }

        for (let j = 0; j < TrainCount; j++) {
          feedbackTrain[j] = feedback[j];
        }
        // TrainCount++;
        for (let j = 0; j <= all; j++) {
          feedbackTest[j] = feedback[TrainCount];
          TrainCount++;
        }

        var commentsTrain = <?php echo json_encode($TrainCommments); ?>;
        var labelsTrain = <?php echo json_encode($TrainLabels); ?>; // old feedback
        var feedbackFileTrain = "<?php echo $feedbackFileTrain; ?>";

        var commentsTest = <?php echo json_encode($TestComments); ?>;
        var labelsTest = <?php echo json_encode($TestLabels); ?>; // old feedback
        var feedbackFileTest = "<?php echo $feedbackFileTest; ?>";
        $.ajax({
          url: "feedbackFiles.php?ProjectName=<?php echo $ProjectName; ?>&FileName=<?php echo $FileName ?>",
          type: "POST",
          data: {
            'TrainFeedback': feedbackTrain,
            'commentsTrain': commentsTrain,
            'labelsTrain': labelsTrain,
            'feedbackFileTrain': feedbackFileTrain,
          }
        });
        $.ajax({
          url: "feedbackFiles.php?ProjectName=<?php echo $ProjectName; ?>&FileName=<?php echo $FileName ?>",
          type: "POST",
          data: {
            'TestFeedback': feedbackTest,
            'Testcomments': commentsTest,
            'Testlabels': labelsTest,
            'feedbackFileTest': feedbackFileTest,
          },
          success: function() {
            alert("Thank you for your feedback, Reclassify your file based the given feedback!");
            window.location = "FilePage.php?ProjectName=<?php echo $ProjectName; ?>&FileName=<?php echo $FileName; ?>&name=<?php echo $name; ?>";
          },
          error: function() {
            alert("Error in giving your feedback, please try again!");
          },
        });
      };
    });
  </script>

  <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
  <script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.2/js/dataTables.bootstrap5.min.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/UserHome.js"></script>
  <script src="assets/js/main.js"></script>

</body>

</html>