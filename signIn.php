<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Favicons -->
    <link href="assets/img/logo.png" rel="icon">
    <link href="assets/img/logo.png" rel="apple-touch-icon">
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Toxicity Inspector - Sign In</title>
    <meta content="" name="description">
    <a href="index.php" accesskey="h"></a>
    <meta content="" name="keywords">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/form.css" rel="stylesheet">
    <script type="text/javascript" src="https://cdn.weglot.com/weglot.min.js"></script>
    <script>
        Weglot.initialize({
            api_key: 'wg_7971b0c8d0752818fdd77c7810fb22808'
        });
    </script>

</head>

<body>

    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top">
        <div class="container-fluid container-xl d-flex align-items-center justify-content-between">

            <a href="index.php" class="logo d-flex align-items-center">
                <img src="assets/img/logo.png" alt="Logo">
                <span>Toxicity Inspector</span>
            </a>

            <nav id="navbar" class="navbar">
                <ul>
                    <li><a class="nav-link scrollto" href="index.php">Home</a></li>
                    <li><a class="nav-link scrollto" href="index.php#about">About Us</a></li>
                    <li><a class="nav-link scrollto" href="index.php#mission">Our Mission</a></li>
                    <li><a class="nav-link scrollto" href="index.php#vision">Our Vision</a></li>
                    <li><a class="nav-link scrollto" href="index.php#contact">Contact Us</a></li>
                    <li><a class="nav-link scrollto active" href="signIn.php">Sign In</a></li>
                    <li><a class="getstarted scrollto" href="signup.php">Sign Up</a></li>
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav>

        </div>
    </header><!-- End Header -->

    <!-- ======= Breadcrumbs ======= -->
    <section class="breadcrumbs">
        <div class="container">

            <ol>
                <li><a href="index.php">Home</a></li>
                <li>SignIn</li>
            </ol>
            <h2>SignIn</h2>

        </div>
    </section><!-- End Breadcrumbs -->

    <!-- ======= Hero Section ======= -->
    <section id="hero" class="hero d-flex align-items-center">
        <div style="width: 90%;" class="wrapper">
            <div class="title-text">
                <div class="title user">
                    Welcome Back!
                </div>
                <label class="btn-success"></label>
            </div>
            <div class="form-container">

                <div class="form-inner">
                    <form action="signInForm.php" method="POST" class="user">


                        <div class="form-group">
                           <label for="username">Username</label>
                          <input type="text" class="form-control" name="username" placeholder="Username" required>
                        </div>

                        <div class="form-group">
                           <label for="password">Password</label>
                          <input type="password" class="form-control" name="password" placeholder="password" required>
                        </div>
         

                       
                 
                        <div class="fplink">
                            <a data-bs-toggle="modal" data-bs-target="#inlineForm">
                                Forget Password?
                            </a>
                        </div>

                        <div class="field fb">
                            <div class="fb-layer"></div>
                            <input type="submit" name="submit" value="Sign In">
                        </div>

                        <div class="user-link">
                            Not a member? <a href="signup.php">Sign up now</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade text-left" id="inlineForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel33">Forget Password</h4>

                        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
                    </div>
                    <form method="POST" action="forgotPassword.php">

                        <div class="modal-body">
                            Enter your email and we will send you a new password.
                            <br><br>
                            <div class="form-group">
                                <input type="email" placeholder="Email" class="form-control" name="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required>
                            </div>

                            <div class="modal-footer">

                                <button type="submit" name="submit" class="btn btn-primary ml-1">
                                    <i class="bx bx-check d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Send</span>
                                </button>

                            </div>
                            </form>
                        </div>

                </div>
            </div>
    </section>

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">

        <div class="footer-top">
            <div class="container">
                <div class="row gy-4">
                    <div class="col-lg-5 col-md-12 footer-info">
                        <a href="index.php" class="logo d-flex align-items-center">
                            <img src="assets/img/logo.png" alt="Logo">
                            <span>Toxicity Inspector</span>
                        </a>
                        <p>Toxicity Inspector is a website that enable the users to inspect the
                            overall toxicity score as well as enable the users to provide their feedback on the obtained results.</p>
                        <div class="social-links mt-3">
                            <a href="https://twitter.com/" target="_blank" class="twitter"><i class="bi bi-twitter"></i></a>
                            <a href="https://facebook.com/" target="_blank" class="facebook"><i class="bi bi-facebook"></i></a>
                            <a href="https://instagram.com/" target="_blank" class="instagram"><i class="bi bi-instagram"></i></a>
                            <a href="https://linkedin.com/" target="_blank" class="linkedin"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-2 col-6 footer-links">
                        <h4>Useful Links</h4>
                        <ul>
                            <li><i class="bi bi-chevron-right"></i> <a href="index.php">Home</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="index.php#about">About us</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="index.php#mission">Our Mission</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="index.php#vision">Our Vision</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="index.php#contact">Contact Us</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="signIn.php">Sign In</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="signup.php">Sign Up</a></li>
                        </ul>
                    </div>



                    <div class="col-lg-3 col-md-12 footer-contact text-center text-md-start">
                        <h4>Contact Us</h4>
                        <p>
                            King Saud University, <br>
                            Riyadh
                            <br><br>
                            <strong>Phone:</strong> +966 555555555<br>
                            <strong>Email:</strong> <a href="mailto: toxicityinspector@gmail.com">toxicityinspector@gmail.com</a><br>
                        </p>
                    </div>



                </div>
            </div>
        </div>

        <div class="container">
            <div class="copyright">
                &copy; Copyright <strong><span>Toxicity Inspector</span></strong>. All Rights Reserved
            </div>
            <div class="credits">

            </div>
        </div>
    </footer><!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
    <style>
        .admin .fplink a {
            font-size: 20px;
            display: inline-block;
            color: #013289;
            line-height: 0;
            margin-right: 10px;
            transition: 0.3s;
        }

        .admin .fplink a:hover {
            color: #4154f1;
        }
    </style>
    <!-- Vendor JS Files -->
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>

</body>

</html>