<?php
session_start();
include("Db.php");

$username = $_SESSION["username"];

if (!(isset($_SESSION["Role"])) || $_SESSION["Role"] != "User")
  echo '<script>window.location="signup.php"; alert("Error on registerd record! Please try again");</script>';

$collectionF = $db->files;
$collectionU = $db->users;
$collectionP = $db->projects;
$collectionR = $db->CommentsByAPI;

$name = $_GET['name'];
$ProjectName = $_GET['ProjectName'];
$FileName = $_GET['FileName'];


$files = $collectionF->findOne(['username' => $username, 'ProjectName' => $ProjectName, 'FileName' => $FileName]);
$fileID = $files['_id'];

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

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" charset="utf-8"></script>
  <!--jquery for pagination -->

  <!-- Template Main CSS File -->
  <link href="assets/css/userHome.css" rel="stylesheet">
  <script type="text/javascript" src="https://cdn.weglot.com/weglot.min.js"></script>
  <script>
    Weglot.initialize({
      api_key: 'wg_7971b0c8d0752818fdd77c7810fb22808'
    });
  </script>ß
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
      <h1>Each Comment's Toxicity</h1> <!-- link it----------- -->
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="userHome.php?name=<?php echo $name; ?>">Home</a></li> <!-- link it----------- -->
          <li class="breadcrumb-item"><a href="UserProjects.php?name=<?php echo $name; ?>">Projects</a></li>
          <li class="breadcrumb-item"><a href="ProjectPage.php?name=<?php echo $name; ?>&ProjectName=<?php echo $ProjectName; ?>"><?php echo $ProjectName; ?></a></li>
          <li class="breadcrumb-item"><a href="FilePage.php?ProjectName=<?php echo $ProjectName; ?>&FileName=<?php echo $FileName; ?>&name=<?php echo $name; ?>"><?php echo $FileName; ?></a></li>
          <li class="breadcrumb-item"><a href="overallToxicity.php?ProjectName=<?php echo $ProjectName; ?>&FileName=<?php echo $FileName; ?>&name=<?php echo $name; ?>">Overall Toxicity</a></li>
          <li class="breadcrumb-item active">Each Comment's Toxicity</li> <!-- link it----------- -->
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <div class="section">
      <a href="FilePage.php?ProjectName=<?php echo $ProjectName; ?>&FileName=<?php echo $FileName; ?>&name=<?php echo $name; ?>">
        <button class="btn btn-primary" role="button">
          <span><i class="bi bi-arrow-left-circle"></i></span> Back to your file
        </button>
      </a>
      <br>
      <br>
      <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <i class="bi bi-exclamation-triangle me-1"></i>
          Warning! the following content may contain sensitive language
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <br>
      <?php
      $results = $collectionR->find();
      $scoreTOXICITY = 0;
      $scorePROFANITY = 0;
      $scoreSEVERE_TOXICITY = 0;
      $scoreIDENTITY_ATTACK = 0;
      $scoreINSULT = 0;
      $scoreTHREAT = 0;
      $counter = 10; // counter for charts 
      $results = $results->toArray();
      if ($files['Languages'] == 'Arabic')
        $lan = 1;
      else if ($files['Languages'] == 'English')
        $lan = 2;
      foreach ($results as $commentInfo) {
        $idInfo =  $commentInfo['fileID']['fileID'];
        if ($idInfo == $fileID) { // if the comment from the same file 
          echo '<div class="card">';
          $counter++;
          $scoreTOXICITY = $commentInfo['response']['attributeScores']['TOXICITY']['summaryScore']['value'];
          $nonToxic = 1 - $scoreTOXICITY;
          $comment = $commentInfo['comment']['comment']; // comment printed 
          echo '<h5 class="card-title" style="padding-left: 15px;">Comment:</h5> <div class="card-body"><p>';
          echo '<i class="bx bxs-quote-alt-left quote-icon-left" style="color: #d0e8ff;"></i>' . $comment . ' <i class="bx bxs-quote-alt-right quote-icon-right" style="color: #d0e8ff;"></i>';
          echo '</p></div></div><div class="row"><div class="col-lg-6"><div class="card"><div class="card-body"><h5 class="card-title">Toxicity</h5>';
          echo '<div id="pieChart' . $counter . '"></div><script>document.addEventListener("DOMContentLoaded", () => { new ApexCharts(document.querySelector("#pieChart' . $counter . '"), {';
          echo 'series: [' . $scoreTOXICITY . ',' . $nonToxic . '],';

          echo 'chart: {height: 350, type: "pie",toolbar: {show: true}},labels: ["TOXIC", "NON-TOXIC"]}).render();});</script>';
          echo '<br><br></div></div></div>';
          if ($files['Languages'] == 'English') {
            $scorePROFANITY = $commentInfo['response']['attributeScores']['PROFANITY']['summaryScore']['value'];
            $scoreSEVERE_TOXICITY = $commentInfo['response']['attributeScores']['SEVERE_TOXICITY']['summaryScore']['value'];
            $scoreIDENTITY_ATTACK = $commentInfo['response']['attributeScores']['IDENTITY_ATTACK']['summaryScore']['value'];
            $scoreINSULT = $commentInfo['response']['attributeScores']['INSULT']['summaryScore']['value'];
            $scoreTHREAT = $commentInfo['response']['attributeScores']['THREAT']['summaryScore']['value'];
            echo '<div class="col-lg-6"><div class="card"><div class="card-body">';
            echo '<h5 class="card-title">Fain-grain Labels</h5><div id="barChart' . $counter . '"></div><script>document.addEventListener("DOMContentLoaded", () => {';
            echo 'new ApexCharts(document.querySelector("#barChart' . $counter . '"), {series: [{data: [' . $scoreINSULT . ', ' . $scoreTHREAT . ', ' . $scorePROFANITY . ',' . $scoreSEVERE_TOXICITY . ',' . $scoreIDENTITY_ATTACK . ']';
            echo '}],chart: {type: "bar", height: 350}, plotOptions: {bar: {borderRadius: 4, horizontal: true,}}, dataLabels: { enabled: false},xaxis: {';
            echo 'categories: ["INSULT", "THREAT", "PROFANITY", "SEVERE TOXICITY", "IDENTITY ATTACK"],}}).render();});</script></div></div></div></div>';
          } else
            echo '</div>';
        }
      }
      ?>
    </div>
    <!-- end card one -->
    <!-- cards must be above this section -->

    <div class="pagination d-flex justify-content-center">

    </div>

    </div> <!-- section end -->

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
  <script type="text/javascript">
    function getPageList(totalPages, page, maxLength) {
      function range(start, end) {
        return Array.from(Array(end - start + 1), (_, i) => i + start);
      }

      var sideWidth = maxLength < 9 ? 1 : 2;
      var leftWidth = (maxLength - sideWidth * 2 - 3) >> 1;
      var rightWidth = (maxLength - sideWidth * 2 - 3) >> 1;

      if (totalPages <= maxLength) {
        return range(1, totalPages);
      }

      if (page <= maxLength - sideWidth - 1 - rightWidth) {
        return range(1, maxLength - sideWidth - 1).concat(0, range(totalPages - sideWidth + 1, totalPages));
      }

      if (page >= totalPages - sideWidth - 1 - rightWidth) {
        return range(1, sideWidth).concat(0, range(totalPages - sideWidth - 1 - rightWidth - leftWidth, totalPages));
      }

      return range(1, sideWidth).concat(0, range(page - leftWidth, page + rightWidth), 0, range(totalPages - sideWidth + 1, totalPages));
    }

    $(function() {
      var limit = <?php echo $lan; ?>;
      var numberOfItems = $(".section .card").length;
      if (limit == 1)
        var limitPerPage = 2; //How many card items visible per a page
      else
        var limitPerPage = 3; //How many card items visible per a page
      var totalPages = Math.ceil(numberOfItems / limitPerPage);
      var paginationSize = 7; //How many page elements visible in the pagination
      var currentPage;

      function showPage(whichPage) {
        if (whichPage < 1 || whichPage > totalPages) return false;

        currentPage = whichPage;

        $(".section .card").hide().slice((currentPage - 1) * limitPerPage, currentPage * limitPerPage).show();

        $(".pagination li").slice(1, -1).remove();

        getPageList(totalPages, currentPage, paginationSize).forEach(item => {
          $("<li>").addClass("page-item").addClass(item ? "current-page" : "dots")
            .toggleClass("active", item === currentPage).append($("<a>").addClass("page-link")
              .attr({
                href: "javascript:void(0)"
              }).text(item || "...")).insertBefore(".next-page");
        });

        $(".previous-page").toggleClass("disable", currentPage === 1);
        $(".next-page").toggleClass("disable", currentPage === totalPages);
        return true;
      }

      $(".pagination").append(
        $("<li>").addClass("page-item").addClass("previous-page").append($("<a>").addClass("page-link").attr({
          href: "javascript:void(0)"
        }).text("Prev")),
        $("<li>").addClass("page-item").addClass("next-page").append($("<a>").addClass("page-link").attr({
          href: "javascript:void(0)"
        }).text("Next"))
      );

      $(".section").show();
      showPage(1);

      $(document).on("click", ".pagination li.current-page:not(.active)", function() {
        return showPage(+$(this).text());
      });

      $(".next-page").on("click", function() {
        return showPage(currentPage + 1);
      });

      $(".previous-page").on("click", function() {
        return showPage(currentPage - 1);
      });
    });
  </script>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/UserHome.js"></script>

</body>

</html>