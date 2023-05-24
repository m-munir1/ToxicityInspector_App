<?php
session_start();
include("Db.php");

$username = $_SESSION["username"];

if (!(isset($_SESSION["Role"])) || $_SESSION["Role"] != "User")
    echo '<script>window.location="signup.php"; alert("Error on registerd record! Please try again");</script>';

$collectionF = $db->files;
$collectionU = $db->users;
$collectionP = $db->projects;

$name = $_GET['name'];
$ProjectName = $_GET['ProjectName'];
$userID = $_SESSION['userID'];
$files = $collectionF->find(['userID' => $userID, 'username' => $username, 'ProjectName' => $ProjectName]); // find all user's files that belongs to this project based on project name and username and userID
$name = $_GET['name'];
$numberOfFiles = 0;
$files = $files->toArray();
foreach ($files as $fileInfo)
    $numberOfFiles++;

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Favicons -->
    <link href="assets/img/logo.png" rel="icon">
    <link href="assets/img/logo.png" rel="apple-touch-icon">

    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Toxicity Inspector - <?php echo $ProjectName . ' project'; ?></title>
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
            <h1>Project Name: <a href="ProjectPage.php?ProjectName=<?php echo $ProjectName; ?>&name=<?php echo $name; ?>"><?php echo $ProjectName; ?></a></h1> <!-- link it----------- -->
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="userHome.php?name=<?php echo $name; ?>">Home</a></li> <!-- link it----------- -->
                    <li class="breadcrumb-item"><a href="UserProjects.php?name=<?php echo $name; ?>">Projects</a></li>
                    <li class="breadcrumb-item active"><?php echo $ProjectName; ?></li> <!-- link it----------- -->
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <div class="card">
            <div class="card-body">

                <br>

                <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#inlineForm">
                    <span><i class="bi bi-upload"></i></span> Upload New Files
                </button>
                <h5 class="card-title">All Files</h5>

                <!--creat form Modal -->
                <div class="modal fade text-left" id="inlineForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel33">Upload New File</h4>

                                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>


                            </div>
                            <form method="POST" action="UploadedFileForm.php?name=<?php echo $name; ?>" enctype="multipart/form-data">

                                <div class="modal-body">
                                    <label>File Name: </label>
                                    <div class="form-group">
                                        <input type="text" id="FileName" placeholder="File Name" class="form-control" name="FileName" required>
                                    </div>
                                    <br>
                                    <label>Select File's Language: </label>

                                    <fieldset class="form-group">
                                        <select class="form-select" id="basicSelect" name="Languages">
                                            <option value="Arabic">Arabic</option>
                                            <option value="English">English</option>
                                        </select>
                                    </fieldset>

                                    <br>

                                    <label>Upload File: </label>
                                    

                                    <input class="form-control" type="file" name="UploadedFile" id="formFile" onchange="FileVal();" accept=".csv" required>
                                    <span style="width: 100%; margin-top: 0.25rem; font-size: .875em; color: #dc3545;">
                                    -Comments must be under a column with "comment_text" name, and if there are labels, they should be under a column with "toxic"(0 for non-toxic, 1 for toxic)<br>-File must be in .csv Format!</span>
                                    <input type="hidden" name="ProjectName" value="<?php echo $ProjectName; ?>">

                                </div>

                                <div class="modal-footer">

                                    <button type="submit" name="submit" class="btn btn-primary ml-1">
                                        <i class="bx bx-check d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">Upload</span>
                                    </button>

                                </div>
                            </form>
                            <script>
                                function FileVal() { // validation for the number of files and size of the file 
                                    var file = document.getElementById("formFile");
                                    x = file.files.length; // file number
                                    y = file.files.item(0).size; // file size in bytes
                                    fileKB = y / 1000; // file size in kb
                                    fileMb = fileKB / 1000; // file size in mb
                                    if (x > 1) {
                                        alert("Please choose at most 1 file");
                                        document.getElementById("formFile").value = null;
                                        window.location="ProjectPage.php?ProjectName=<?php echo $ProjectName; ?>&name=<?php echo $name;?>";
                                    } else if (y < 20) {
                                        alert("This file is empty");
                                        document.getElementById("formFile").value = null;
                                        window.location="ProjectPage.php?ProjectName=<?php echo $ProjectName; ?>&name=<?php echo $name;?>";
                                    } else if (fileMb > 1000) {
                                        alert("Maximum size for a file is 1GB, Please try again!");
                                        document.getElementById("formFile").value = null;
                                        window.location="ProjectPage.php?ProjectName=<?php echo $ProjectName; ?>&name=<?php echo $name;?>";
                                    }
                                }
                            </script>
                        </div>
                    </div>
                </div>


                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Language</th>
                            <th scope="col">Toxicity Score</th>
                            <th scope="col">View File</th>
                            <th scope="col"> Delete</th>
                        </tr>
                    </thead>


                    <tbody>
                        <?php
                        foreach ($files as $fileInfo) {
                            $fileName = $fileInfo['FileName'];
                            $filePath = $fileInfo['UploadedFile'];
                            echo '<td><i class="bi bi-file-earmark-fill" style="color: #006affcf; font-size: 20px;"></i><a href="FilePage.php?ProjectName=' . $fileInfo['ProjectName'] . '&FileName=' . $fileName . '&name=' . $name . '">' . ' ' . $fileName . '</a></td>';
                            echo '<td>' . $fileInfo['Languages'] . '</td>';
                            echo '<td>  ' . (int)$fileInfo['ToxicityLevel'] . '%</td>';
                            echo '<td><a href="' . $filePath . '" target="_blank"><button type="button" class="btn btn-info"><i class="bi bi-box-arrow-up-right"></i></button></a></td>';
                        ?>

                            <td><a onclick="deleteFile('<?php echo $fileName; ?>', '<?php echo $name; ?>', '<?php echo $ProjectName; ?>')"> <button type="button" class="btn btn-danger"><i class="bi bi-x-lg"></i></button></a></td>
                            <tr>
                            <?php
                        }
                            ?>
                    </tbody>
                </table>
                <script>
                    function deleteFile(FileName, name, ProjectName) {
                        var con = confirm("All the comments and results belong to this file will be deleted too, are you sure you want to delete " + FileName + " file?") // check if the user sure about deleting the project 
                        if (con) // if user clicks ok
                            var loc = "deleteFile.php?ProjectName=" + ProjectName + "&name=" + name + "&FileName=" + FileName;
                        else
                            return; // if user clicks cancle
                        window.location = loc;
                    }
                    document.addEventListener('DOMContentLoaded', function() {
                        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl)
                        })
                    }, false);
                </script>
                <!-- End Table with stripped rows -->

            </div>
        </div>

        <!-- End #main -->
    </main>
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