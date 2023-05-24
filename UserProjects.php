<?php
session_start();
include("Db.php");

$collectionU = $db->users;
$collectionP = $db->projects;

if (!(isset($_SESSION["Role"])) || $_SESSION["Role"] != "User")
    echo '<script>window.location="signup.php"; alert("Error on registerd record! Please try again");</script>';

$username = $_SESSION["username"];
$userID = $_SESSION['userID'];
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$projects = $collectionP->find(['userID' => $userID]); // retrive all user's projects by userID
$name = $_GET['name'];
$numberOfProjcts = 0; // start counting user projects 
$projects = $projects->toArray();

foreach ($projects as $proInfo)
    $numberOfProjcts++;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Favicons -->
    <link href="assets/img/logo.png" rel="icon">
    <link href="assets/img/logo.png" rel="apple-touch-icon">

    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Toxicity Inspector - Projects</title>
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
            <h1>Projects</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="UserHome.php?name=<?php echo $name; ?>">Home</a></li>
                    <li class="breadcrumb-item active">Projects</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <div class="card">
            <div class="card-body">
                <br>
                <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#inlineForm">
                    <span><i class="bi bi-upload"></i></span> Create New Project
                </button>
                <h5 class="card-title">All Projects</h5>

                <div class="modal fade text-left" id="inlineForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel33">Create New Project </h4>

                                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                            </div>
                            <form method="POST" action="CreateProjectForm.php?name=<?php echo $name; ?>">

                                <div class="modal-body">
                                    <label>Project Name: </label>
                                    <div class="form-group">
                                        <input type="text" placeholder="Project Name" class="form-control" name="ProjectName" required>
                                    </div>
                                    <br>
                                    <label>Project Description: </label>
                                    <div class="form-group">
                                        <input type="text" placeholder="Project Description" class="form-control" name="ProjectDesc" required>
                                    </div>
                                    <input type="hidden" name="first_name" value="<?php echo $name; ?>" required>
                                </div>
                                <div class="modal-footer">

                                    <button type="submit" name="submit" class="btn btn-primary ml-1">
                                        <i class="bx bx-check d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">Create</span>
                                    </button>

                                </div>
                            </form>
                        </div>

                    </div>
                </div>

                <!-- Table with stripped rows -->

                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Description</th>
                            <th scope="col">Edit</th>
                            <th scope="col"> Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($projects as $proInfo) {
                            $ProjectName = $proInfo['ProjectName'];
                            echo '<tr><td><i class="bi bi-folder-fill" style="color: #006affcf; font-size: 20px;"></i><a href="ProjectPage.php?ProjectName=' . $ProjectName . '&name=' . $name . '">' . ' ' . $ProjectName . '</a></td>';
                            echo '<td>' . $proInfo['ProjectDesc'] . '</td>';
                            echo '<td><button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editinlineForm'. $proInfo['_id'] .'"><i class="bi bi-pencil-square"></i></button></td>';
                        ?>
                    <div class="modal fade text-left" id="editinlineForm<?php echo $proInfo['_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel33">Edit Project </h4>

                                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                            </div>
                            <form method="POST" action="editProject.php?ProjectName=<?php echo $ProjectName; ?>&name=<?php echo $name; ?>">

                                <div class="modal-body">
                                    <label>Project Name: </label>
                                    <div class="form-group">
                                        <input type="text" placeholder="Project Name" class="form-control" value="<?php echo $ProjectName; ?>" name="eName" required>
                                    </div>
                                    <br>
                                    <label>Project Description: </label>
                                    <div class="form-group">
                                        <input type="text" placeholder="Project Description" class="form-control" value="<?php echo $proInfo['ProjectDesc']; ?>" name="eDes" required>
                                    </div>
                                </div>
                                <div class="modal-footer">

                                    <button type="submit" name="submit" class="btn btn-primary ml-1">
                                        <i class="bx bx-check d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">Edit</span>
                                    </button>

                                </div>
                            </form>
                        </div>

                    </div>
                </div>
                            <td><a onclick="deleteProject('<?php echo $ProjectName; ?>', '<?php echo $name; ?>')"> <button type="button" class="btn btn-danger"><i class="bi bi-x-lg"></i></button></a></td>
                            <tr>
                            <?php
                        }
                            ?>
                    </tbody>

                </table>
                <!-- End Table with stripped rows -->
                <script>
                    function deleteProject(ProjectName, name) {
                        var con = confirm("All the files belong to this project will be deleted too, are you sure you want to delete " + ProjectName + " project?") // check if the user sure about deleting the project 
                        if (con) // if user clicks ok
                            var loc = "delete.php?ProjectName=" + ProjectName + "&name=" + name;
                        else
                            return; // if user clicks cancle
                        window.location = loc;
                    }
                </script>
            </div>
        </div>
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
    <!-- End #main -->

    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/UserHome.js"></script>

</body>

</html>