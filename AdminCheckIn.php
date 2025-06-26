<?php
session_start(); // Start the session

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION["username"])) {
    header("Location: AdminLogin.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if user is logged in as admin or has appropriate permissions

    // Get the student ID and balance from the form
    $studentID = $_POST["studentID"];
    $gateNumber = $_POST["gate_no"];
    $vehicleNo = $_POST["vno"];

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

    // Prepare and execute the SQL select statement to get the username and balance
    $sql1 = "SELECT username, balance, email FROM pcard WHERE student_id = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("s", $studentID);
    $stmt1->execute();
    $stmt1->store_result();

    if ($stmt1->num_rows > 0) {
        // Retrieve user data
        $stmt1->bind_result($username, $balance, $email);
        $stmt1->fetch();

        // Check if balance is zero
        if ($balance <= 0) {
            echo '<script>alert("Unable to proceed due to insufficient balance. Kindly recharge your account!");</script>';
            echo "<script>window.location.href = 'AdminCheckIn.php';</script>";
            exit();
        }
    } else {
        echo '<script>alert("Student ID not found!");</script>';
        echo "<script>window.location.href = 'AdminCheckIn.php';</script>";
        exit();
    }

    

    // Prepare and execute the SQL update statement
    $sql3 = "UPDATE pcard SET balance = balance - 5 WHERE student_id = ?";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("s", $studentID);

    

    
    if ($stmt3->execute()) {

        $sql4 = "SELECT balance FROM pcard WHERE student_id = ?";
        $stmt4 = $conn->prepare($sql4);
        $stmt4->bind_param("s", $studentID);
        $stmt4->execute();
        $stmt4->store_result();
        $stmt4->bind_result($balance);
        $stmt4->fetch();

        // Prepare and execute the SQL insert statement
        $sql2 = "INSERT INTO transaction (gate_number, student_id, username, vehicle_no,transaction_type, amount, email,balance) VALUES (?, ?, ?, ?,'Check In', 5, ?,?)";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("sssssi", $gateNumber, $studentID, $username, $vehicleNo, $email, $balance);
        $stmt2->execute();
        

        $sql2 = "INSERT INTO checkIn (gate_number, student_id, username, vehicle_no, email, amount,balance) VALUES (?, ?, ?, ?,?, 5,?)";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("sssssi", $gateNumber, $studentID, $username, $vehicleNo,$email,$balance);
        $stmt2->execute();


        header("Location: AdminCheckIn.php");
    } else {
        echo "Error updating balance: " . $conn->error;
    }

    // Close statements and connection
    $stmt2->close();
    $stmt3->close();
    $conn->close();
} else {
    // If the form is not submitted, deny access
    echo "";
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
    <styles>
        
    </styles>
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
                        <a href="AdminHome.php" class="nav-item nav-link">Home</a>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Ticket</a>
                                <div class="dropdown-menu border-0 rounded-0 m-0">
                                    <a href="AdminGetTicket.php" class="dropdown-item">Get Ticket</a>
                                    <a href="AdminCheckIn.php" class="dropdown-item active">Check In</a>
                                </div>
                        </div>
                        
                        <a href="AdminCardRecharge.php" class="nav-item nav-link">Recharge</a>
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
            <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 500px">
                <h3 class="display-4 text-white text-uppercase" style="letter-spacing: 5px;">Ticket</h3>
                <div class="d-inline-flex text-white">
                    <p class="m-0 text-uppercase"><a class="text-white" href="index.html">Home</a></p>
                    <i class="fa fa-angle-double-right pt-1 px-3"></i>
                    <p class="m-0 text-uppercase">Ticket</p>
                </div>
            </div>
        </div>
    </div>
<!-- Header End -->

<!-- Chrck In Starts -->

<div class="container-fluid booking mt-5 pb-5">
    <div class="container pb-5">
        <div class="bg-light shadow" style="padding: 30px;">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="row align-items-center" style="min-height: 60px;">
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3 mb-md-0">
                                    <select name="gate_no" class="custom-select px-4" id="gateSelect" style="height: 47px;">
                                        <option selected>Gate No.</option>
                                        <option value="Gate No- 1">Gate No- 1</option>
                                        <option value="Gate No- 2">Gate No- 2</option>
                                        <option value="Gate No- 3">Gate No- 3</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3 mb-md-0">
                                    <div class="studentID" id="studentIDInput" data-target-input="nearest">
                                        <input type="text" name="studentID" class="form-control p-4" id="studentIDInput" placeholder="Student ID" required="required" />
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
                            <div class="VehicleNo" id="vehicleNoInput" data-target-input="nearest">
                                <input type="text" name="vno" class="form-control p-4" id="vehicleNoInput" placeholder="Vehicle No" required="required"/>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Moved the button outside of the row and col-md-9 -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button class="btn btn-primary btn-block" type="submit" style="height: 50px;">Check In</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Check In Ends -->
    
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

    <!-- Template Javascript -->
    <script src="js/main.js"></script>

    
</body>
</html>
