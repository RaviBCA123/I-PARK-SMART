<?php
session_start(); // Start the session

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>I Park Smart</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Favicon -->
    <link href="img/park.jpg" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"> 

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <!-- Topbar Start -->
    <div class="container-fluid bg-light pt-3 d-none d-lg-block">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 text-center text-lg-left mb-2 mb-lg-0">
                    <div class="d-inline-flex align-items-center">
                        <p><i class="fa fa-envelope mr-2"></i>iparksmart45@gmail.com</p>
                        <p class="text-body px-3">|</p>
                        <p><i class="fa fa-phone-alt mr-2"></i>+91 2041038200</p>
                    </div>
                </div>
                <div class="col-lg-6 text-center text-lg-right">
                    <div class="d-inline-flex align-items-center">
                        <a class="text-primary px-3" href="https://www.facebook.com/mesagcofficial">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a class="text-primary px-3" href="https://twitter.com/iparksmart45">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a class="text-primary px-3" href="https://www.linkedin.com/company/agc-pune/">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a class="text-primary px-3" href="https://www.instagram.com/mesagcofficial?utm_source=ig_web_button_share_sheet&igsh=zdnlzdc0mzixnw==">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a class="text-primary pl-3" href="https://youtube.com/@mesagcofficial4511?si=wtvlpotuf7vsffla">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->


    <!-- Navbar Start -->
    <div class="container-fluid position-relative nav-bar p-0">
        <div class="container-lg position-relative p-0 px-lg-3" style="z-index: 9;">
            <nav class="navbar navbar-expand-lg bg-light navbar-light shadow-lg py-3 py-lg-0 pl-3 pl-lg-5">
                <a href="" class="navbar-brand">
                    <h1 class="m-0 text-primary"><span class="text-dark">I Park</span>Smart</h1>
                </a>
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-between px-3" id="navbarCollapse">
                    <div class="navbar-nav ml-auto py-0">
                        <a href="userHome.php" class="nav-item nav-link">Home</a>
                        <a href="userAbout.php" class="nav-item nav-link">About</a>
                        <a href="userService.php" class="nav-item nav-link">Services</a>
                        <a href="userParkingCard.php" class="nav-item nav-link">Parking Card</a>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Pages</a>
                            <div class="dropdown-menu border-0 rounded-0 m-0">
                                <a href="userParkingPackages.php" class="dropdown-item ">Parking Packages</a>
                                <a href="userContact.php" class="dropdown-item ">Contact</a>
                                <a href="userComments.php" class="dropdown-item ">CommentUs</a>
                                <a href="userTestimonial.php" class="dropdown-item">Testimonial</a>
                            </div>
                        </div>
                        <!-- Other navigation links -->
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                 <!--<img src="img/profile.png" alt="Profile Picture" class="rounded-circle" style="width: 30px; height: 30px;">-->
                                 <i class="fa fa-2x fa-user-circle" style="font-size: 27px;"></i>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="profileDropdown">
                            <a class="dropdown-item" href="userProfile.php"><?php echo $_SESSION["username"]; ?></a>
                                <a class="dropdown-item" href="userPCardDetails.php">P-Card Details</a>
                                <a class="dropdown-item" href="userWallet.php">Wallet</a>
                                <a class="dropdown-item" href="userPCardApplication.php">P-Card Application</a>
                                <a class="dropdown-item" href="userTransactionHistory.php">Transaction History</a>
                                <a class="dropdown-item" href="userChangePassword.php">Change Password</a>
                                <a class="dropdown-item active" href="userPrivacyPolicy.php">Privacy Policy</a>
                                <a class="dropdown-item" href="#"><form action="submitLogout.php" method="post"><button type="submit">Logout</button></form></a>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->

    <!-- Header Start -->
    <div class="container-fluid page-header">
        <div class="container">
            <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 400px">
                <h3 class="display-4 text-white text-uppercase" style="letter-spacing: 5px;">Privacy Policy</h3>
                <div class="d-inline-flex text-white">
                    <p class="m-0 text-uppercase"><a class="text-white" href="userHome.php">Home</a></p>
                    <i class="fa fa-angle-double-right pt-1 px-3"></i>
                    <p class="m-0 text-uppercase">Privacy Policy</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Booking Start -->
    <div class="container-fluid booking mt-5 pb-5">
        <div class="container pb-5">
            <div class="bg-light shadow" style="padding: 30px;">
            <h6 class="text-primary text-uppercase" style="letter-spacing: 5px; font-size: 20px;">Privacy and Policy</h6><hr>
                1. Your privacy is important to us. I Park Smart solutions pvt. ltd.'s policy to respect your privacy regarding any information we may collect from you across our website and other sites we own and operate.

                <br><br>2. We only ask for personal information when we truly need it to provide a service to you. We collect it by fair and lawful means, with your knowledge and consent. We also let you know why we’re collecting it and how it will be used.

                <br><br>3. We only retain collected information for as long as necessary to provide you with your requested service. What data we store, we’ll protect within commercially acceptable means to prevent loss and theft, as well as unauthorised access, disclosure, copying, use or modification.

                <br><br>4. We don’t share any personally identifying information publicly or with third-parties, except when required to by law.

                <br><br>5. Our website may link to external sites that are not operated by us. Please be aware that we have no control over the content and practices of these sites, and cannot accept responsibility or liability for their respective privacy policies.

                <br><br>6. You are free to refuse our request for your personal information, with the understanding that we may be unable to provide you with some of your desired services.

                <br><br>7. Your continued use of our website will be regarded as acceptance of our practices around privacy and personal information. If you have any questions about how we handle user data and personal information, feel free to contact us.
            </div>   
        </div>
    </div>
    <!-- Booking End -->

     <!-- Footer Start -->
     <div class="container-fluid bg-dark text-white-50 py-5 px-sm-3 px-lg-5" style="margin-top: 90px;">
            <div class="row pt-5">
                <div class="col-lg-3 col-md-6 mb-5">
                    <a href="" class="navbar-brand">
                        <h1 class="text-primary"><span class="text-white">PARK</span>SMART</h1></a>
                    <p>Welcome to the one-stop destination for effortless parking solutions – where convenience meets innovation at I-PARK-SMART's digital platform.</p>
                    <h6 class="text-white text-uppercase mt-4 mb-3" style="letter-spacing: 5px;">Follow Us</h6>
                    <div class="d-flex justify-content-start">
                        <a class="btn btn-outline-primary btn-square mr-2" href="https://www.facebook.com/mesagcofficial"><i class="fab fa-facebook-f"></i></a>
                <a class="btn btn-outline-primary btn-square mr-2" href="https://twitter.com/iparksmart45"><i class="fab fa-twitter"></i></a>
                <a class="btn btn-outline-primary btn-square mr-2" href="https://www.linkedin.com/company/agc-pune/"><i class="fab fa-linkedin-in"></i></a>
                <a class="btn btn-outline-primary btn-square mr-2" href="https://www.instagram.com/mesagcofficial?utm_source=ig_web_button_share_sheet&igsh=zdnlzdc0mzixnw=="><i class="fab fa-instagram"></i></a>
                <a class="btn btn-outline-primary btn-square mr-2" href="https://youtube.com/@mesagcofficial4511?si=wtvlpotuf7vsffla"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-5">
                    <h5 class="text-white text-uppercase mb-4" style="letter-spacing: 5px;">Our Services</h5>
                    <div class="d-flex flex-column justify-content-start">
                        <a class="text-white-50 mb-2" ><i class="fa fa-angle-right mr-2"></i> About</a>
                        <a class="text-white-50 mb-2" ><i class="fa fa-angle-right mr-2"></i> Services</a>
                        <a class="text-white-50 mb-2" ><i class="fa fa-angle-right mr-2"></i> Packages</a>
                        <a class="text-white-50 mb-2" ><i class="fa fa-angle-right mr-2"></i> Testimonial</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-5">
                    <h5 class="text-white text-uppercase mb-4" style="letter-spacing: 5px;">Usefull Links</h5>
                    <div class="d-flex flex-column justify-content-start">
                        <a class="text-white-50 mb-2" href="userHome.php"><i class="fa fa-angle-right mr-2"></i> Home</a>
                        <a class="text-white-50 mb-2" href="userAbout.php"><i class="fa fa-angle-right mr-2"></i> About</a>
                        <a class="text-white-50 mb-2" href="userService.php"><i class="fa fa-angle-right mr-2"></i> Services</a>
                        <a class="text-white-50 mb-2" href="userParkingPackages.php"><i class="fa fa-angle-right mr-2"></i> Packages</a>
                        <a class="text-white-50 mb-2" href="userTestimonial.php"><i class="fa fa-angle-right mr-2"></i> Testimonial</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-5">
                    <h5 class="text-white text-uppercase mb-4" style="letter-spacing: 5px;">Contact Us</h5>
                    <p><b>MES Abasaheb Garware College</b></p>
                    <p><i class="fa fa-map-marker-alt mr-2"></i>Abasaheb Garware College,<br> Karve Road, Pune – 411004</p>
                    <p><i class="fa fa-phone-alt mr-2"></i> +91 2041038200</p>
                    <p><i class="fa fa-envelope mr-2"></i> iparksmart45@gmail.com</p>
                </div>
            </div>
        </div>
        <div class="container-fluid bg-dark text-white border-top py-4 px-sm-3 px-md-5" style="border-color: rgba(256, 256, 256, .1) !important;">
            <div class="row">
                <div class="col-lg-6 text-center text-md-left mb-3 mb-md-0">
                    <p class="m-0 text-white-50">Copyright &copy; <a href="#">I Park Smart</a>. All Rights Reserved.</a>
                    </p>
                </div>
            </div>
    
        </div>
    </div>
    <!-- Footer End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="fa fa-angle-double-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Contact Javascript File -->
    <script src="mail/jqBootstrapValidation.min.js"></script>
    <script src="mail/contact.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>