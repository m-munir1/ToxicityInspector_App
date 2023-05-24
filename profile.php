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

$user = $collectionU->findOne(
  ['username' => $username],
);

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
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $name . ' ' . $user['LastName']; ?></span> <!-- php -->
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
      <h1>Profile</h1>

      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="userHome.php?name=<?php echo $name; ?>">Home</a></li>
          <li class="breadcrumb-item active">Profile</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section profile">

      <div class="card">
        <div class="card-body pt-3">
          <!-- Bordered Tabs -->
          <ul class="nav nav-tabs nav-tabs-bordered">

            <li class="nav-item">
              <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
            </li>

            <li class="nav-item">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button>
            </li>

            <li class="nav-item">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
            </li>

          </ul>
          <div class="tab-content pt-2">

            <div class="tab-pane fade show active profile-overview" id="profile-overview">

              <h5 class="card-title">Profile Details</h5>

              <div class="row">
                <div class="col-lg-3 col-md-4 label ">First Name</div>
                <div class="col-lg-9 col-md-8"><?php echo $name; ?></div>
              </div>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Last Name</div>
                <div class="col-lg-9 col-md-8"><?php echo $user['LastName']; ?></div>
              </div>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Username</div>
                <div class="col-lg-9 col-md-8"><?php echo $username; ?></div>
              </div>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Email</div>
                <div class="col-lg-9 col-md-8"><?php echo $user['Email']; ?> </div>
              </div>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Date of birth</div>
                <div class="col-lg-9 col-md-8"><?php echo $user['DateOfBirth']; ?> </div>
              </div>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Education level</div>
                <div class="col-lg-9 col-md-8"><?php echo $_SESSION['educationLevel']; ?> </div>
              </div>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Gender</div>
                <div class="col-lg-9 col-md-8"><?php echo $_SESSION['Gender']; ?> </div>
              </div>

            </div>

            <div class="tab-pane fade profile-edit pt-3" id="profile-edit">

              <!-- Profile Edit Form -->
              <form method="POST" action="editProfile.php">

                <div class="row mb-3">
                  <label for="newFirstName" class="col-md-4 col-lg-3 col-form-label">First Name</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="newFirstName" type="text" class="form-control" id="newFirstName" value=<?php echo $name; ?> pattern="[a-zA-Z]*" required>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="newLastName" class="col-md-4 col-lg-3 col-form-label">Last Name</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="newLastName" type="text" class="form-control" id="newLastName" value=<?php echo $_SESSION['LastName']; ?> pattern="[a-zA-Z]*" required>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="newUsername" class="col-md-4 col-lg-3 col-form-label">Username</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="newUsername" type="text" class="form-control" id="newUsername" value=<?php echo $username; ?> pattern="[a-z\d]{4,}" required>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="newEmail" class="col-md-4 col-lg-3 col-form-label">Email</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="newEmail" type="email" class="form-control" id="newEmail" value=<?php echo $user['Email']; ?> pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required>
                  </div>
                </div>

              

            

                <div class="text-center">
                  <button type="submit" name="submit" class="btn btn-primary">Save Changes</button>
                </div>

              </form><!-- End Profile Edit Form -->
            </div>

            <div class="tab-pane fade pt-3" id="profile-change-password">
              <!-- Change Password Form -->
              <form method="POST" action="changePassword.php">

                <div class="row mb-3">
                  <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="oldPassword" type="password" class="form-control" id="oldPassword" required>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New Password</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="newPassword" type="password" class="form-control" id="newPassword" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Please enter at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">Confirm New Password</label>
                <div class="col-md-8 col-lg-9">
                    <input name="newPassword2" type="password" class="form-control" id="newPassword2" onChange="PassVal();" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Please enter at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
                  </div>
                  </div>
                <div class="text-center"> 
                  <button type="submit" name='submit' class="btn btn-primary">Change Password</button>
                </div>
              </form><!-- End Change Password Form -->
              <script>
                function PassVal() {
                  if (document.getElementById('newPassword').value != document.getElementById('newPassword2').value) {
                    alert("passwords are not match");
                    document.getElementById('newPassword').value = null;
                    document.getElementById('newPassword2').value = null;
                  }
                }
              </script>
            </div>

          </div><!-- End Bordered Tabs -->

        </div>
      </div>

      </div>

    </section>


  </main><!-- End #main -->
  <!-- ======= Footer ======= -->
  <div class="container position-absolute bottom-0 start-50 translate-middle-x">
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
  </style>
  <!-- Vendor JS Files -->
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