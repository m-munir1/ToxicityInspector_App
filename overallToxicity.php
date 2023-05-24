<?php
session_start();
include("Db.php");

if (!(isset($_SESSION["Role"])) || $_SESSION["Role"] != "User")
    echo '<script>window.location="signup.php"; alert("Error on registerd record! Please try again");</script>';

$username = $_SESSION["username"];
$collectionF = $db->files;
$collectionU = $db->users;
$collectionP = $db->projects;
$collectionR = $db->CommentsByAPI;

$name = $_GET['name'];
$ProjectName = $_GET['ProjectName'];
$FileName = $_GET['FileName'];

$files = $collectionF->findOne(['username' => $username, 'ProjectName' => $ProjectName, 'FileName' => $FileName]);
$fileID = $files['_id'];

$UploadedFile = $files['UploadedFile'];

$results = $collectionR->find();
$flag = 0;
foreach ($results as $r) {
    $res =  $r['fileID']['fileID'];
    if ($res == $fileID)
        $flag = 1;
}

if ($files['Languages'] == 'English' && $flag == 0) {
    $command = escapeshellcmd('/Users/hourianalthunayangmail.com/opt/anaconda3/bin/python API.py "' . $fileID . '"');
    $output = shell_exec($command);
    $projectStatus = $collectionP->updateOne(
        ['ProjectName' => $ProjectName, 'username' => $username],
        ['$set' => ['ProjectStatus' => 'In Progress']]
    ); // start opreations in the project
    $filename = 'Uploads/' . $fileID . 'API.csv'; // store API results in csv file 
    $f = fopen($filename, 'w');
    if ($f == true) {
        $head = array('comment_text', 'toxic');
        fputcsv($f, $head);
        $results = $collectionR->find();
        $results = $results->toArray();

        foreach ($results as $commentInfo) {
            $idInfo =  $commentInfo['fileID']['fileID'];
            if ($idInfo == $fileID) {
                $commentText =  $commentInfo['comment']['comment'];
                $scoreTOXICITY = $commentInfo['response']['attributeScores']['TOXICITY']['summaryScore']['value'];
                if ($scoreTOXICITY >= 0.75) {
                    $row = array($commentText, '1');
                    fputcsv($f, $row);
                } else {
                    $row = array($commentText, '0');
                    fputcsv($f, $row);
                }
            }
        }
    }
    fclose($f);
    $csv = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/' . $fileID . 'train.csv';
    if (!file_exists($csv)) {
        $command = escapeshellcmd('/Users/hourianalthunayangmail.com/opt/anaconda3/bin/python splitAPI.py "' . $fileID . '"');
        $output = shell_exec($command);
    }
} else if ($files['Languages'] == 'Arabic' && $flag == 0) {
    $command = escapeshellcmd('/Users/hourianalthunayangmail.com/opt/anaconda3/bin/python APIar.py "' . $fileID . '"');
    $output = shell_exec($command);
    $projectStatus = $collectionP->updateOne(
        ['ProjectName' => $ProjectName, 'username' => $username],
        ['$set' => ['ProjectStatus' => 'In Progress']]
    );
    $filename = 'Uploads/' . $fileID . 'API.csv';
    $f = fopen($filename, 'w');
    if ($f == true) {
        $head = array('comment_text', 'toxic');
        fputcsv($f, $head);
        $results = $collectionR->find();
        $results = $results->toArray();

        foreach ($results as $commentInfo) {
            $idInfo =  $commentInfo['fileID']['fileID'];
            if ($idInfo == $fileID) {
                $commentText =  $commentInfo['comment']['comment'];
                $scoreTOXICITY = $commentInfo['response']['attributeScores']['TOXICITY']['summaryScore']['value'];
                if ($scoreTOXICITY >= 0.75) {
                    $row = array($commentText, '1');
                    fputcsv($f, $row);
                } else {
                    $row = array($commentText, '0');
                    fputcsv($f, $row);
                }
            }
        }
    }
    fclose($f);
    $csv = '/Applications/MAMP/htdocs/2_ToxicityInspector_App/Uploads/' . $fileID . 'train.csv';
    if (!file_exists($csv)) {
        $command = escapeshellcmd('/Users/hourianalthunayangmail.com/opt/anaconda3/bin/python splitAPI.py "' . $fileID . '"');
        $output = shell_exec($command);
    }
}


$results = $collectionR->find();
$numberOfResults = 0;
$sumScores = 0;
$scoreTOXICITY = 0; // toxic comments
$scorePROFANITY = 0;
$scoreSEVERE_TOXICITY = 0;
$scoreIDENTITY_ATTACK = 0;
$scoreINSULT = 0;
$scoreTHREAT = 0;
$toxicComments = 0;
$results = $results->toArray();
foreach ($results as $commentInfo) {
    $idInfo =  $commentInfo['fileID']['fileID'];
    if ($idInfo == $fileID) { // if the comment from the same file 
        $scoreTOXICITY = $commentInfo['response']['attributeScores']['TOXICITY']['summaryScore']['value'];
        if ($scoreTOXICITY >= 0.75)
            $toxicComments++; // increment toxic comments
        if ($files['Languages'] == 'English') {
            $scorePROFANITY += $commentInfo['response']['attributeScores']['PROFANITY']['summaryScore']['value'];
            $scoreSEVERE_TOXICITY += $commentInfo['response']['attributeScores']['SEVERE_TOXICITY']['summaryScore']['value'];
            $scoreIDENTITY_ATTACK += $commentInfo['response']['attributeScores']['IDENTITY_ATTACK']['summaryScore']['value'];
            $scoreINSULT += $commentInfo['response']['attributeScores']['INSULT']['summaryScore']['value'];
            $scoreTHREAT += $commentInfo['response']['attributeScores']['THREAT']['summaryScore']['value'];
        }
        $sumScores += $scoreTOXICITY; // summation of all toxicity 
        $numberOfResults++; //count number of results 
    }
}
$nonToxic = $numberOfResults - $toxicComments; // non toxic comments
if ($flag == 0) {
    $sumScores1 = $sumScores / $numberOfResults; // over all toxicty 
    $sumScores1 = $sumScores1 * 100; // over all toxicty percent 

    $project = $collectionP->findOne(['username' => $username, 'ProjectName' => $ProjectName]);
    $toxicityPro = $project['OverallToxicity'] + $sumScores1; // retrive project's toxicty level
    $project = $collectionP->updateOne(
        ['ProjectName' => $ProjectName, 'username' => $username],
        ['$set' => ['OverallToxicity' => $toxicityPro]]
    ); // add file's toxicity to project's toxicity

    $project = $collectionF->updateOne(
        ['ProjectName' => $ProjectName, 'username' => $username, 'FileName' => $FileName],
        ['$set' => ['ToxicityLevel' => $sumScores1]]
    ); // add file's toxicity to file's toxicity
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
            <h1>Overall Toxicity</h1> <!-- link it----------- -->
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="userHome.php?name=<?php echo $name; ?>">Home</a></li> <!-- link it----------- -->
                    <li class="breadcrumb-item"><a href="UserProjects.php?name=<?php echo $name; ?>">Projects</a></li>
                    <li class="breadcrumb-item"><a href="ProjectPage.php?name=<?php echo $name; ?>&ProjectName=<?php echo $ProjectName; ?>"><?php echo $ProjectName; ?></a></li>
                    <li class="breadcrumb-item"><a href="FilePage.php?ProjectName=<?php echo $ProjectName; ?>&FileName=<?php echo $FileName; ?>&name=<?php echo $name; ?>"><?php echo $FileName; ?></a></li>
                    <li class="breadcrumb-item active">Overall Toxicity</li> <!-- link it----------- -->
                </ol>
            </nav>

        </div><!-- End Page Title -->
        <a href="commentsToxicity.php?ProjectName=<?php echo $ProjectName; ?>&FileName=<?php echo $FileName; ?>&name=<?php echo $name; ?>">
            <button href="index.php" class="btn btn-primary float-end" role="button">
                <span><i class="bi bi-info-circle"></i></span> Check By Comment
            </button>
        </a>
        <a href="FilePage.php?ProjectName=<?php echo $ProjectName; ?>&FileName=<?php echo $FileName; ?>&name=<?php echo $name; ?>">
            <button class="btn btn-primary" role="button">
                <span><i class="bi bi-arrow-left-circle"></i></span> Back to your file
            </button>
        </a>
        <br><br>
        <section class="section">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Overall Toxicity Distribution</h5>
                            <!-- Pie Chart -->
                            <div id="pieChart"></div>

                            <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    new ApexCharts(document.querySelector("#pieChart"), {
                                        series: [<?php echo $sumScores; ?>, <?php echo $numberOfResults - $sumScores; ?>],
                                        chart: {
                                            height: 350,
                                            type: 'pie',
                                            toolbar: {
                                                show: true
                                            }
                                        },
                                        labels: ['TOXIC', 'NON-TOXIC']
                                    }).render();
                                });
                            </script>
                            <!-- End Pie Chart -->

                            <br><br>
                        </div>
                    </div>
                </div>
                <?php
                if ($files['Languages'] == 'English') { // fain grain 
                    echo '<div class="col-lg-6">';
                    echo '<div class="card">';
                    echo '<div class="card-body"><h5 class="card-title">Fine-grain Labels</h5>';
                    echo  '<div id="barChart2"></div><script>'; //<!-- Bar Chart -->
                    echo  'document.addEventListener("DOMContentLoaded", () => {';
                    echo  'new ApexCharts(document.querySelector("#barChart2"), {';
                    echo 'series: [{data: [' . $scoreINSULT . ',' . $scoreTHREAT . ',' . $scorePROFANITY . ',' . $scoreSEVERE_TOXICITY . ',' . $scoreIDENTITY_ATTACK . ']}],';
                    echo 'chart: {type: "bar",height: 350},';
                    echo 'plotOptions: {bar: {borderRadius: 4,horizontal: true,}},';
                    echo 'dataLabels: {enabled: false}, xaxis: {';
                    echo 'categories: ["INSULT", "THREAT", "PROFANITY", "SEVERE TOXICITY", "IDENTITY ATTACK"';
                    echo '],}}).render() });</script> </div></div></div>';
                }
                ?>

                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Number Of Comments</h5>
                            <!-- Bar Chart -->
                            <div id="barChart"></div>

                            <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    new ApexCharts(document.querySelector("#barChart"), {
                                        series: [{
                                            data: ['<?php echo $toxicComments; ?>', '<?php echo $nonToxic; ?>']
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
                                                text: 'number of comments'
                                            }

                                        },
                                        xaxis: {
                                            categories: ['Toxic Comments', 'Non-Toxic Comments'],
                                        }
                                    }).render();
                                });
                            </script>
                            <!-- End Bar Chart -->

                        </div>
                    </div>
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