<?php
session_start(); // Start the session

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION["username"])) {
    header("Location: AdminLogin.php");
    exit();
}

include('dbcon.php'); // Ensure this file correctly includes and establishes a database connection

require 'vendor/autoload.php'; // Make sure the autoload.php file path is correct
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize form inputs
    $studentID = $_POST["studentID"];
    $credit = filter_var($_POST["balance"], FILTER_VALIDATE_FLOAT);
    $email = $_POST["email"];
    $transaction_type = $_POST["transaction_type"];

    // Check if balance is a valid number
    if ($credit === false) {
        echo "Invalid balance amount";
        exit();
    }

    // Database connection variables
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "park";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute the SQL select statement to get the username
    $sql1 = "SELECT username, email FROM users WHERE student_id = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("s", $studentID);
    $stmt1->execute();
    $stmt1->store_result();

    if ($stmt1->num_rows == 0) {
        echo '<script>alert("Student ID not found!");</script>';
        echo "<script>window.location.href = 'AdminCardRecharge.php';</script>";
        exit();
    }

    $stmt1->bind_result($username, $email);
    $stmt1->fetch();
    $stmt1->close();

    // Prepare and execute the SQL update statement for the balance
    $sql3 = "UPDATE pcard SET balance = balance + ? WHERE student_id = ?";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("is", $credit, $studentID);

    if ($stmt3->execute()) {
        // Get the updated balance
        $sql4 = "SELECT balance FROM pcard WHERE student_id = ?";
        $stmt4 = $conn->prepare($sql4);
        $stmt4->bind_param("s", $studentID);
        $stmt4->execute();
        $stmt4->bind_result($updated_balance);
        $stmt4->fetch();
        $stmt4->close();

        // Prepare and execute the SQL insert statement for the transaction
        $sql2 = "INSERT INTO transaction (gate_number, student_id, username, transaction_type, credit, email, balance) VALUES ('Office', ?, ?, ?, ?, ?, ?)";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("issssd", $studentID, $username, $transaction_type, $credit, $email, $updated_balance);
        $stmt2->execute();
        $stmt2->close();

        // Send email notification
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'iparksmart45@gmail.com';
            $mail->Password   = 'mhgcpjxmqqbssnhf';
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;

            $mail->setFrom('iparksmart45@gmail.com', 'I Park Smart');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Your Parking Wallet Balance Update';
            $mail->Body    = "<h3>Dear $username,</h3>
            
            We're pleased to inform you that your parking wallet balance has been successfully updated.<br>
            <h2>Balance added: $credit<br>
            Current Balance: $updated_balance<br></h2>
            If you have any questions or need further assistance, please feel free to reach out to our support team at iparksmart45@gmail.com or +91 2041038200.<br><br><br>

            Best regards,<br>
            I Park Smart";

            $mail->send();
            echo '<script>alert("Balance Updated Successfully!");</script>';
            echo "<script>window.location.href = 'AdminCardRecharge.php';</script>";
        } catch (Exception $e) {
            echo "Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo '<script>alert("Error in Updating Balance!");</script>' . $conn->error;
    }

    // Close connection
    $stmt3->close();
    $conn->close();
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <!-- Include Bootstrap JavaScript -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <!-- Include Moment.js for datetimepicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <!-- Include Tempus Dominus Bootstrap 4 datetime picker -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.1.2/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.1.2/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Include qrcode-generator -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator/qrcode.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@zxing/library@0.0.13/dist/zxing.min.js"></script>
    

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
                        <a href="AdminHome.php" class="nav-item nav-link ">Home</a>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Ticket</a>
                                <div class="dropdown-menu border-0 rounded-0 m-0">
                                    <a href="AdminGetTicket.php" class="dropdown-item">Get Ticket</a>
                                    <a href="AdminCheckIn.php" class="dropdown-item ">Check In</a>
                                </div>
                        </div>
                        
                        <a href="AdminCardRecharge.php" class="nav-item nav-link active">Recharge</a>
                        <a href="AdminReport.php" class="nav-item nav-link">Reports</a>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Pages</a>
                            <div class="dropdown-menu border-0 rounded-0 m-0">
                                <a href="AdminContact.php" class="dropdown-item">Contact</a>
                                <a href="AdminComments.php" class="dropdown-item">CommentUs</a>
                            </div>
                        </div>
                        <!-- Other navigation links -->
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <!--<img src="img/profile.png" alt="Profile Picture" class="rounded-circle" style="width: 30px; height: 30px;">-->
                                <i class="fa fa-2x fa-user-circle" style="font-size: 27px;"></i>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="profileDropdown">
                                <a class="dropdown-item" href=""><?php echo $_SESSION["username"]; ?></a>
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
                <h3 class="display-4 text-white text-uppercase" style="letter-spacing: 5px;">Card Recharge</h3>
                <div class="d-inline-flex text-white">
                    <p class="m-0 text-uppercase"><a class="text-white" href="">Home</a></p>
                    <i class="fa fa-angle-double-right pt-1 px-3"></i>
                    <p class="m-0 text-uppercase">Card Recharge</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->
    
<!-- Update Balance Starts -->

<div class="container-fluid booking mt-5 pb-5">
    <div class="container pb-5">
        <div class="bg-light shadow" style="padding: 30px;">
            <form action="#" method="post">
                <div class="row align-items-center" style="min-height: 60px;">
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3 mb-md-0">
                                    <div class="studentID" id="studentID" data-target-input="nearest">
                                        <input type="text" name="studentID" class="form-control p-4" id="studentID" placeholder="Student ID" required="required" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3 mb-md-0">
                                    <div class="balance" id="balance" data-target-input="nearest">
                                        <input type="text" name="balance" class="form-control p-4" id="balance" placeholder="Add Balance" required="required" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 mb-md-0">
                                    <input type="email" name="email" class="form-control p-4" id="email" placeholder="Email" required="required" style="width: 100%; max-width: 400px;"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3 mb-md-0">
                            <select name="transaction_type" class="custom-select px-4" id="transaction_type" style="height: 47px;">
                                <option selected>Transaction Type</option>
                                <option value="Online">Online</option>
                                <option value="Offline">Offline</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- Moved the button outside of the row and col-md-9 -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button class="btn btn-primary btn-block" type="submit" style="height: 50px;">Update Balance</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Balance Ends -->

<!-- Footer Start -->
   <div class="container-fluid bg-dark text-white-50 py-5 px-sm-3 px-lg-5" style="margin-top: 20px;">
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

    <!-- Template Javascript -->
    <script src="js/main.js"></script>

    <!--ticket-->
    <script src="ticketGenerator.js"></script>
</body>
</html>