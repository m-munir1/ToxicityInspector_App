<?php
session_start();
include("Db.php");

if (!(isset($_SESSION["Role"])) || $_SESSION["Role"] != "User")
  echo '<script>window.location="signIn.php"; alert("You don\'t have access to the requested page!, Please sign in first.");</script>';

$username = $_SESSION["username"];
$name = $_SESSION["FirstName"];
$FileName = $_GET["FileName"];
$ProjectName = $_GET["ProjectName"];
$locat = 'FilePage.php?ProjectName=' . $ProjectName . '&FileName=' . $FileName . '&name=' . $name;

$collectionU = $db->users;
$collectionP = $db->projects;
$collectionF = $db->files;

$toxicityForComments = 0;
$toxicComments1 = 0;
$allComments1 = 0;
$toxicityForComments2 = 0;
$toxicComments2 = 0;
$allComments2 = 0;

$file1 = $collectionF->findOne( //get the first file info
  [
    'username' => $username,
    'ProjectName' => $ProjectName,
    'FileName' => $FileName,
  ],
);


$selectedOption = $_POST['gridRadios']; //get the selected option
if ($selectedOption != 'option3') {
  $selectedFile = $_POST['file']; //get the id of the selected file

  $file2 = $collectionF->findOne( //get the second file info
    ['_id' => new MongoDB\BSON\ObjectId($selectedFile),],
  );
  // check if the first file has labels 

  $CSVfp1 = fopen('/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/' . $file1['_id'] . '.csv', "r");
  $trainFile = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/' . $file1['_id'] . 'train.csv';
    $data1 = fgetcsv($CSVfp1, 1000, ",");

    $labelFlag1 = false;
    $index1 = -1;
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
    $CSVfp2 = fopen('/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/' . $file2['_id'] . '.csv', "r");
    $trainFile2 = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/' . $file2['_id'] . 'train.csv';
    if (!file_exists($trainFile2)) {
      $labelFlag2 = false;
      $index2 = -1;
      $data2 = fgetcsv($CSVfp2, 1000, ",");
      if (!empty($data2)) {
        if (isset($data2[0]) && str_contains($data2[0], 'toxic')) {
          $labelFlag2 = true;
          $index2 = 0;
        } else if (isset($data2[1]) && str_contains($data2[1], 'toxic')) {
          $labelFlag2 = true;
          $index2 = 1;
        } else if (isset($data2[2]) && str_contains($data2[2], 'toxic')) {
          $labelFlag2 = true;
          $index2 = 2;
        } else if (isset($data2[3]) && str_contains($data2[3], 'toxic')) {
          $labelFlag2 = true;
          $index2 = 3;
        }
      }
    }
    $csv1 = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/' . $file1['_id'] . '.csv';
    $csv2 = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/' . $file2['_id'] . '.csv';
    if (!($labelFlag1)) {
      $csv1 = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/' . $file1['_id'] . 'API.csv';
      if (file_exists($csv1)) {
        $CSVfp1 = fopen($csv1, "r");
        $data1 = fgetcsv($CSVfp1, 1000, ",");
        if (!empty($data1)) {
          $labelFlag1 = true;
          $index1 = 1;
        }
      }
    }
    if (!file_exists($trainFile2)&&!($labelFlag2)) {
      $csv2 = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/' . $file2['_id'] . 'API.csv';
      if (file_exists($csv2)) {
        $CSVfp2 = fopen($csv2, "r");
        $data2 = fgetcsv($CSVfp2, 1000, ",");
        if (!empty($data2)) {
          $labelFlag2 = true;
          $index2 = 1;
        }
      }
    }
  

  if (!file_exists($trainFile) && !file_exists($csv1) && !($labelFlag1)) { //if the csv files is not created yet
    echo '<script>alert("In order to compare you have to check the overall toxicity for ' . $file1['FileName'] . ' file!");</script>';
    echo '<script>window.location="' . $locat . '";</script>';
  }

  if (!file_exists($trainFile2) && !file_exists($csv2) && !($labelFlag2)) { //if the csv files is not created yet
    echo '<script>alert("In order to compare you have to check the overall toxicity for ' . $file2['FileName'] . ' file!"");</script>';
    echo '<script>window.location="' . $locat . '";</script>';
  }

  $entriesC = array();
  $entriesT = array();
  $row = 1;

  if (($handle = fopen($csv1, "r")) !== FALSE) {
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
      $row++;
    }
    fclose($handle);
  }
}

if ($selectedOption == 'option3' && $file1['FeedbackLevel'] == 1) { //if it is comparison between files and they have different languages
  echo '<script>alert("Give Your feedback first!");</script>';
  echo '<script>window.location="' . $locat . '";</script>';
}
//read the csv colums and store it to array

if ($selectedOption == 'option3') {
  $entriesC = array();
  $entriesT = array();
  $row = 1;
  $csv1 = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/' . $file1['_id'] . '.csv';

  if (($handle = fopen($csv1, "r")) !== FALSE) {
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
      $row++;
    }
    fclose($handle);
  }

  $feedbackComments = 0;
  $entriesCf = array();
  $entriesTf = array();
  $feedbackLevel = $file1['FeedbackLevel'];
  for ($i = 1; $i < $feedbackLevel; $i++) {
    $entriesC2 = array();
    $entriesT2 = array();
    $train = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/' . $file1['_id'] . 'feedback' . $i . 'train.csv';
    $test = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/' . $file1['_id'] . 'feedback' . $i . 'test.csv';
    $row2 = 1;
    if (($handle = fopen($train, "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if ($row2 == 1) {
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
          $entriesC2[] = $entry['comment_text'];
          $entriesT2[] = $entry['toxic'];
        }
        $row2++;
      }
      for ($i = 0; $i < count($entriesC2); $i++) {
        $entriesCf[] = $entriesC2[$i];
      }
      for ($i = 0; $i < count($entriesT2); $i++) {
        $entriesTf[] = $entriesT2[$i];
      }
      $row2= $row2-2;
      $feedbackComments += $row2;

      fclose($handle);
    }
    $row2 = 1;
    if (($handle = fopen($test, "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if ($row2 == 1) {
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
          $entriesC2[] = $entry['comment_text'];
          $entriesT2[] = $entry['toxic'];
        }
        $row2++;
      }
      for ($i = 0; $i < count($entriesC2); $i++) {
        $entriesCf[] = $entriesC2[$i];
      }
      for ($i = 0; $i < count($entriesT2); $i++) {
        $entriesTf[] = $entriesT2[$i];
      }
      $row2= $row2-2;
      $feedbackComments += $row2;

      fclose($handle);
    }
  }
  echo '<script>alert("count ' . count($entriesC2) . '");</script>';
  echo '<script>alert("feedbackComments' . $feedbackComments . '");</script>';
  $entriesCf = array_filter($entriesCf);
  $entriesTf = array_filter($entriesTf);
} else {
  $row2 = 1;
  if (($handle = fopen($csv2, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      if ($row2 == 1) {
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
        $entriesC2[] = $entry['comment_text'];
        $entriesT2[] = $entry['toxic'];
      }
      $row2++;
    }
    fclose($handle);
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

  <title>Toxicity Inspector - <?php echo '@' . $username; ?></title>
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
  <link href="https://cdn.datatables.net/1.13.2/css/dataTables.bootstrap5.min.css" rel="stylesheet">


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

    <section>

      <div class="pagetitle">
        <h1>Comparison of the Toxicity Level</h1> <!-- link it----------- -->
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="userHome.php?name=<?php echo $name; ?>">Home</a></li> <!-- link it----------- -->
            <li class="breadcrumb-item"><a href="UserProjects.php?name=<?php echo $name; ?>">Projects</a></li>
            <li class="breadcrumb-item"><a href="ProjectPage.php?name=<?php echo $name; ?>&ProjectName=<?php echo $ProjectName; ?>"><?php echo $ProjectName; ?></a></li>
            <li class="breadcrumb-item"><a href="FilePage.php?ProjectName=<?php echo $ProjectName; ?>&FileName=<?php echo $FileName; ?>&name=<?php echo $name; ?>"><?php echo $FileName; ?></a></li>
            <li class="breadcrumb-item active">Comparison of the Toxicity Level</li> <!-- link it----------- -->
          </ol>
        </nav>
      </div><!-- End Page Title -->
      <a href="FilePage.php?ProjectName=<?php echo $ProjectName; ?>&FileName=<?php echo $FileName; ?>&name=<?php echo $name; ?>">
        <button class="btn btn-primary" role="button">
          <span><i class="bi bi-arrow-left-circle"></i></span> Back to your file
        </button>
      </a>
      <br><br>

      <div class="card">

        <div class="data_table" style="overflow: auto;">
          <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i>
            Warning! the following content may contain sensitive language
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          <?php
          if ($selectedOption != 'option3') {
            $command1 = escapeshellcmd('/Users/hourianalthunayangmail.com/opt/anaconda3/bin/python scores.py "' . $file1['_id'] . '"');
            $output1 = shell_exec($command1);
            $performance = substr($output1, 0, 4);
            $performance = $performance * 100;
            $words = substr($output1, 5,);
            $words = explode(",", $words);
            $fileEdit = $collectionF->updateOne(
              ['username' => $username, 'FileName' => $FileName, 'ProjectName' => $ProjectName], // conditions 
              ['$set' => ['F1_Scores.0' => $performance],], // update to set the selected model 
            );
            // Note : toxicWords array for toxic words for file 1, nonToxicWords array for non-toxic words for file 1 
            // Note : toxicWords2 array for toxic words for file 2, nonToxicWords2 array for non-toxic words for file 2 

            $toxicWords[] = substr($words[0], 2, -1); // to remove bracets and quotes
            $toxicWords[] = substr($words[1], 2, -1); // to remove bracets and quotes
            $toxicWords[] = substr($words[2], 2, -1); // to remove bracets and quotes
            $toxicWords[] = substr($words[3], 2, -1); // to remove bracets and quotes
            $toxicWords[] = substr($words[4], 2, -3); // to remove bracets and quotes
            $nonToxicWords[] = substr($words[5], 3, -1); // to remove bracets and quotes
            $nonToxicWords[] = substr($words[6], 2, -1); // to remove bracets and quotes
            $nonToxicWords[] = substr($words[7], 2, -1); // to remove bracets and quotes
            $nonToxicWords[] = substr($words[8], 2, -1); // to remove bracets and quotes
            $nonToxicWords[] = substr($words[9], 2, -3); // to remove bracets and quotes
            $command2 = escapeshellcmd('/Users/hourianalthunayangmail.com/opt/anaconda3/bin/python scores.py "' . $file2['_id'] . '"');
            $output2 = shell_exec($command2);
            $performance2 = substr($output2, 0, 4);
            $performance2 = $performance2 * 100;
            $words2 = substr($output2, 5,);
            $words2 = explode(",", $words2);
            $toxicWords2[] = substr($words2[0], 2, -1); // to remove bracets and quotes
            $toxicWords2[] = substr($words2[1], 2, -1); // to remove bracets and quotes
            $toxicWords2[] = substr($words2[2], 2, -1); // to remove bracets and quotes
            $toxicWords2[] = substr($words2[3], 2, -1); // to remove bracets and quotes
            $toxicWords2[] = substr($words2[4], 2, -3); // to remove bracets and quotes
            $nonToxicWords2[] = substr($words2[5], 3, -1); // to remove bracets and quotes
            $nonToxicWords2[] = substr($words2[6], 2, -1); // to remove bracets and quotes
            $nonToxicWords2[] = substr($words2[7], 2, -1); // to remove bracets and quotes
            $nonToxicWords2[] = substr($words2[8], 2, -1); // to remove bracets and quotes
            $nonToxicWords2[] = substr($words2[9], 2, -3); // to remove bracets and quotes
            $fileEdit = $collectionF->updateOne(
              ['username' => $username, 'FileName' => $file2['FileName'], 'ProjectName' => $ProjectName], // conditions 
              ['$set' => ['F1_Scores.0' => $performance2],], // update to set the selected model 
            );
          }
          ?>

          <div class="float-end">

            <h5 style="color: #012970;"> <i class="bi bi-speedometer"></i> Performance = <span class="green-background"> <?php echo $performance2 . '%'; ?> </span></h5>
            <br>
            <h5 style="color: #33835e;"> <i class="bi bi-emoji-smile"></i> Most Contributed Non-toxic Words <br> <span class="green-background"> <?php echo $nonToxicWords2[0] . ', ' . $nonToxicWords2[1] . ', ' . $nonToxicWords2[2] . ', ' . $nonToxicWords2[3] . ', ' . $nonToxicWords2[4]; ?>. </span></h5>
            <br>
            <h5 style="color: #d71313e8;"> <i class="bi bi-emoji-frown"></i> Most Contributed Toxic Words <br> <span class="green-background"> <?php echo $toxicWords2[0] . ', ' . $toxicWords2[1] . ', ' . $toxicWords2[2] . ', ' . $toxicWords2[3] . ', ' . $toxicWords2[4]; ?>. </span></h5>
          </div>

          <div>
            <h5 style="color: #012970;"> <i class="bi bi-speedometer"></i> Performance = <span class="green-background"> <?php echo $performance . '%'; ?> </span></h5>
            <br>
            <h5 style="color: #33835e;"> <i class="bi bi-emoji-smile"></i> Most Contributed Non-toxic Words <br><span class="green-background"> <?php echo $nonToxicWords[0] . ', ' . $nonToxicWords[1] . ', ' . $nonToxicWords[2] . ', ' . $nonToxicWords[3] . ', ' . $nonToxicWords[4]; ?>.</span></h5>
            <br>
            <h5 style="color: #d71313e8;"> <i class="bi bi-emoji-frown"></i> Most Contributed Toxic Words <br> <span class="green-background"> <?php echo $toxicWords[0] . ', ' . $toxicWords[1] . ', ' . $toxicWords[2] . ', ' . $toxicWords[3] . ', ' . $toxicWords[4]; ?>.</span></h5>
          </div>

          <br>
          <br>

          <table class="table table-hover" id="example">

            <thead>
              <tr>
                <th><?php echo "\"" . $FileName . "\" File <br>"; ?> <br>Comments</th>
                <th>Toxicity</th>
                <?php
                if ($selectedOption == 'option3') {
                  echo '<th>Feedback File <br><br>Comments</th>
                <th>Toxicity</th></tr></thead><tbody>';
                  if (count($entriesC) > count($entriesCf))
                    $count = count($entriesC);
                  else
                    $count = count($entriesCf);
                  for ($i = 0; $i < $count; $i++) {
                    if ($i < count($entriesC)) {
                      if ($entriesT[$i] == "1") //toxic
                        $entriesT[$i] = '<span class="badge bg-danger">Toxic</span>';
                      elseif ($entriesT[$i] == "0") //non-toxic
                        $entriesT[$i] = '<span class="badge bg-success">Non-Toxic</span>';
                    } else {
                      $entriesT[$i] = ' ';
                      $entriesC[$i] = ' ';
                    }

                    if ($i < count($entriesCf)) {
                      if ($entriesTf[$i] == "1") //toxic
                        $entriesTf[$i] = '<span class="badge bg-danger">Toxic</span>';
                      elseif ($entriesTf[$i] == "0") //non-toxic
                        $entriesTf[$i] = '<span class="badge bg-success">Non-Toxic</span>';
                    } else {
                      $entriesTf[$i] = ' ';
                      $entriesCf[$i] = ' ';
                    }

                    echo '<tr>';
                    echo '<td>' . $entriesC[$i] . '</td>';
                    echo '<td>' . $entriesT[$i] . '</td>';
                    echo '<td>' . $entriesCf[$i] . '</td>';
                    echo '<td>' . $entriesTf[$i] . '</td>';
                    echo '</tr>';
                  }
                } else {
                  echo '<th>"' . $file2['FileName'] . '" File <br><br>Comments</th>
                <th>Toxicity</th></tr></thead><tbody>';
                  if (count($entriesC) > count($entriesC2))
                    $count = count($entriesC);
                  else
                    $count = count($entriesC2);
                  for ($i = 0; $i < $count; $i++) {
                    if ($i < count($entriesC)) {
                      if ($entriesT[$i] == "1") //toxic
                        $entriesT[$i] = '<span class="badge bg-danger">Toxic</span>';
                      elseif ($entriesT[$i] == "0") //non-toxic
                        $entriesT[$i] = '<span class="badge bg-success">Non-Toxic</span>';
                    } else {
                      $entriesT[$i] = ' ';
                      $entriesC[$i] = ' ';
                    }
                    if ($i < count($entriesC2)) {
                      if ($entriesT2[$i] == "1") //toxic
                        $entriesT2[$i] = '<span class="badge bg-danger">Toxic</span>';
                      elseif ($entriesT2[$i] == "0") //non-toxic
                        $entriesT2[$i] = '<span class="badge bg-success">Non-Toxic</span>';
                    } else {
                      $entriesT2[$i] = ' ';
                      $entriesC2[$i] = ' ';
                    }
                    echo '<tr>';
                    echo '<td>' . $entriesC[$i] . '</td>';
                    echo '<td>' . $entriesT[$i] . '</td>';
                    echo '<td>' . $entriesC2[$i] . '</td>';
                    echo '<td>' . $entriesT2[$i] . '</td>';
                    echo '</tr>';
                  }
                }
                ?>
                </tbody>
          </table>
        </div>
      </div>
    </section>


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
    </footer>
  </div> <!-- End Footer -->


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

    /*--------------------------------------------------------------
# Profie Page
--------------------------------------------------------------*/
    .profile .profile-card h2 {
      font-size: 24px;
      font-weight: 700;
      color: #2c384e;
      margin: 10px 0 0 0;
    }

    .profile .profile-overview .row {
      margin-bottom: 20px;
      font-size: 15px;
    }

    .profile .profile-overview .card-title {
      color: #012970;
    }

    .profile .profile-overview .label {
      font-weight: 600;
      color: rgba(1, 41, 112, 0.6);
    }

    .profile .profile-edit label {
      font-weight: 600;
      color: rgba(1, 41, 112, 0.6);
    }

    .data_table {
      background: #fff;
      padding: 15px;
      box-shadow: 1px 3px 5px #aaa;
      border-radius: 5px;
    }

    .data_table .btn {
      padding: 5px 10px;
      margin: 10px 3px 10px 0;
    }

    .btn-secondary {
      --bs-btn-color: #fff;
      --bs-btn-bg: #0d6efd;
      --bs-btn-border-color: #0d6efd;
      --bs-btn-hover-color: #fff;
      --bs-btn-hover-bg: #0d6efd;
      --bs-btn-hover-border-color: #0d6efd;
      --bs-btn-focus-shadow-rgb: 130, 138, 145;
      --bs-btn-active-color: #fff;
      --bs-btn-active-bg: #0d6efd;
      --bs-btn-active-border-color: #0d6efd;
      --bs-btn-active-shadow: inset 0 3px 5pxrgba(0, 0, 0, 0.125);
      --bs-btn-disabled-color: #fff;
      --bs-btn-disabled-bg: #0d6efd;
      --bs-btn-disabled-border-color: #0d6efd;
    }

    td {
      word-break: break-word;
      /*or you can use word-break:break-all*/
      min-width: 15px;
      /*set min-width as needed*/
    }

    .green-background {
      color: #444444;
    }
  </style>

  <!-- Template Main JS File -->
  <script src="assets/js/UserHome.js"></script>
  <script src="assets/js/main.js"></script>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/chart.js/chart.min.js"></script>

  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/jquery-3.6.0.min.js"></script>
  <script src="assets/js/datatables.min.js"></script>
  <script src="assets/js/pdfmake.min.js"></script>
  <script src="assets/js/vfs_fonts.js"></script>
  <script src="assets/js/custom.js"></script>

  <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
  <script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.2/js/dataTables.bootstrap5.min.js"></script>
</body>

</html>