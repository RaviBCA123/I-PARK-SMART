<?php
session_start();

include('dbcon.php'); // Ensure this file is correctly including and establishing a database connection

require 'vendor/autoload.php'; // Make sure the autoload.php file path is correct
require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendemail_verify($name, $email, $username, $student_id, $password)
{
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'iparksmart45@gmail.com'; // Update with your email credentials
        $mail->Password   = 'mhgcpjxmqqbssnhf'; // Update with your email password
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        $mail->setFrom('iparksmart45@gmail.com', 'I Park Smart');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Welcome to I Park Smart';
        $mail->Body    = "<h3>Hii $username,</h3>
        
        <h4>Congratulations on successfully registration with us! Start exploring parking spots and managing your account.</h4><br>
        Should you have any questions or need assistance, our support team is here to help. Just drop us a message at iparksmart45@gmail.com, and we'll be happy to assist you.<br><br>
        
        Best regards,<br>
        I Park Smart";

        $mail->send();
    } catch (Exception $e) {
        echo "Mailer Error: {$mail->ErrorInfo}";
    }
}

function hasSpecialCharacter($str) {
    return preg_match('/[^a-zA-Z0-9]/', $str);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_btn'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $username = $_POST['UserName'];
    $student_id = $_POST['student_id'];
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($username) || empty($student_id) || empty($password)) {
        echo "<p class='text-danger'>All fields are required</p>";
    } elseif (strlen($password) < 8 || !hasSpecialCharacter($password)) {
        echo "<script>alert('Password must be at least 8 characters long and contain at least one special character.');</script>";
        echo "<script>window.location.href = 'register.php';</script>";
    } else {
        $check_email_query = "SELECT email from users WHERE email='$email' LIMIT 1";
        $check_email_query_run = mysqli_query($con, $check_email_query);
        if (mysqli_num_rows($check_email_query_run) > 0) {
            echo "<script>alert('Email Already exists.');</script>";
            echo "<script>window.location.href = 'register.php';</script>";
        } else {
            $query = "INSERT INTO users(name,email,username,student_id,password) VALUES ('$name','$email','$username','$student_id','$password')";
            $query_run = mysqli_query($con, $query);

            if ($query_run) {
                sendemail_verify($name, $email, $username, $student_id, $password);
                echo "<script>alert('Registered successfully, You can now login.');</script>";
                echo "<script>window.location.href = 'login.php';</script>";
                exit; // Added to prevent further execution
            } else {
                echo "<p class='text-danger'>Error: " . mysqli_error($con) . "</p>";
                // Redirect the user back to the registration page
                echo "<script>window.location.href = 'register.php';</script>";
            }
        }
    }
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

<body >
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
                        <a class="text-primary px-3" href="https://www.facebook.com/MESAGCOfficial">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a class="text-primary px-3" href="https://twitter.com/iparksmart45">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a class="text-primary px-3" href="https://www.linkedin.com/company/agc-pune/">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a class="text-primary px-3" href="https://www.instagram.com/mesagcofficial?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a class="text-primary pl-3" href="https://youtube.com/@mesagcofficial4511?si=WTVlpoTUF7vsfflA">
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
                        <a href="index.html" class="nav-item nav-link active">Home</a>
                        <a href="about.html" class="nav-item nav-link">About</a>
                        <a href="service.html" class="nav-item nav-link">Services</a>
                        <a href="Contact.php" class="nav-item nav-link">Contact</a>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Pages</a>
                            <div class="dropdown-menu border-0 rounded-0 m-0">
                                <a href="package.html" class="dropdown-item">Packages</a>
                                <a href="Comments.php" class="dropdown-item ">CommentUs</a>
                                <a href="testimonial.html" class="dropdown-item">Testimonial</a>   
                            </div>
                        </div>
                        <!-- Other navigation links -->
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <!--<img src="img/profile.png" alt="Profile Picture" class="rounded-circle" style="width: 30px; height: 30px;">-->
                                <i class="fa fa-2x fa-user-circle" style="font-size: 27px;"></i>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="profileDropdown">
                                <a class="dropdown-item" href="login.php">Student Login</a>
                                <a class="dropdown-item" href="AdminLogin.php">Admin Login</a>
                            </div>
                        </div>
                    </div>

                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->
           

                

    <!--Sign Up Starts-->
    <div class="container d-flex justify-content-center align-items-center vh-100" 
    style="background-image: url('./img/login.jpg'); background-size:cover; 
    margin-bottom: 50px; 
    max-width: 1700px; ">
        <div class="card border-0" >
            <div class="card-header bg-primary text-center p-4" >
                <h1 class="text-white m-0">Register Now</h1>
            </div>
            <div class="card-body rounded-bottom bg-white p-5"  style="width: 600px;" >
                <form action="#" method="post">
                    <div class="form-group">
                        <input type="text" name="name" id="name" class="form-control p-4" placeholder="Your Name" required="required" />
                    </div>
                    <div class="form-group">
                        <input type="text" name="email" id="email" class="form-control p-4" placeholder="Email" required="required" />
                    </div>
                    <div class="form-group">
                        <input type="text" name="UserName" id="UserName" class="form-control p-4" placeholder="User Name" required="required" />
                    </div>
                    <div class="form-group">
                        <input type="text" name="student_id" id="student_id" class="form-control p-4" placeholder="Student ID" required="required" />
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" id="password" class="form-control p-4" placeholder="Enter password" required="required"/>
                    </div>
                    <div>
                        <button class="btn btn-primary btn-block py-3" name="register_btn" type="submit">Register</button>
                    </div>
                    <p style="text-align: center; margin-top: 10px;">Already have an account? <a href="login.php">Login Here.</a></p>
                </form>
                
            </div>
        </div>
    </div>
    <!--Sign Up Ends-->

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
                    <a class="text-white-50 mb-2" href="./about.html"><i class="fa fa-angle-right mr-2"></i> About</a>
                    <a class="text-white-50 mb-2" href="./service.html"><i class="fa fa-angle-right mr-2"></i> Services</a>
                    <a class="text-white-50 mb-2" href="./package.html"><i class="fa fa-angle-right mr-2"></i> Packages</a>
                    <a class="text-white-50 mb-2" href="./testimonial.html"><i class="fa fa-angle-right mr-2"></i> Testimonial</a>
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