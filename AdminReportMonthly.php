<?php
session_start();

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION["username"])) {
    header("Location: AdminLogin.php");
    exit();
}

// Database configuration
$dbHost = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "park";

// Establishing connection to the database
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$checkin_count = 0;
$total_total_count = 0;
$ticket_count = 0;
$checkin_amount = 0;
$ticket_amount = 0;
$total_revenue = 0;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['from']) && isset($_POST['to']) && isset($_POST['gate_number'])) {
    $from_date = date('Y-m-d', strtotime($_POST['from']));
    $to_date = date('Y-m-d', strtotime($_POST['to']));
    $gate_number = htmlspecialchars($_POST['gate_number']);

    // Validate gate number
    if (in_array($gate_number, ['Gate No- 1', 'Gate No- 2', 'Gate No- 3', 'ALL'])) {
        // Prepare SQL statements
        $sql_checkin = "SELECT COUNT(*) AS checkin_count, SUM(amount) AS checkin_amount FROM transaction WHERE created_at BETWEEN ? AND ?  AND transaction_type = 'Check In' ";
        $sql_total_count = "SELECT COUNT(*) AS total_count FROM transaction WHERE created_at BETWEEN ? AND ? AND transaction_type IN ('Check In', 'Ticket')";
        $sql_ticket_count = "SELECT COUNT(*) AS ticket_count, SUM(amount) AS ticket_amount FROM transaction WHERE created_at BETWEEN ? AND ?  AND transaction_type = 'Ticket'";

        // Append gate condition if not 'ALL'
        if ($gate_number != 'ALL') {
            $sql_checkin .= " AND gate_number = ?";
            $sql_total_count .= " AND gate_number = ?";
            $sql_ticket_count .= " AND gate_number = ?";
        }

        // Prepare and bind parameters
        $stmt_checkin = $conn->prepare($sql_checkin);
        $stmt_checkin->bind_param("ss", $from_date, $to_date);
        if ($gate_number != 'ALL') {
            $stmt_checkin->bind_param("s", $gate_number);
        }

        $stmt_total_count = $conn->prepare($sql_total_count);
        $stmt_total_count->bind_param("ss", $from_date, $to_date);

        $stmt_ticket_count = $conn->prepare($sql_ticket_count);
        $stmt_ticket_count->bind_param("ss", $from_date, $to_date);
        if ($gate_number != 'ALL') {
            $stmt_ticket_count->bind_param("s", $gate_number);
        }

        // Execute statements
        $stmt_checkin->execute();
        $result_checkin = $stmt_checkin->get_result();
        $stmt_total_count->execute();
        $result_total_count = $stmt_total_count->get_result();
        $stmt_ticket_count->execute();
        $result_ticket_count = $stmt_ticket_count->get_result();

        // Fetch data
        if ($result_checkin && $result_total_count && $result_ticket_count) {
            $row = $result_checkin->fetch_assoc();
            $checkin_count = $row['checkin_count'];
            $checkin_amount = $row['checkin_amount'];

            $row = $result_total_count->fetch_assoc();
            $total_total_count = $row['total_count'];

            $row = $result_ticket_count->fetch_assoc();
            $ticket_count = $row['ticket_count'];
            $ticket_amount = $row['ticket_amount'];

            // Calculate total revenue
            $total_revenue = $checkin_amount + $ticket_amount;
        } else {
            echo "Error fetching data: " . $conn->error;
        }

        // Close statements
        $stmt_checkin->close();
        $stmt_total_count->close();
        $stmt_ticket_count->close();
    } else {
        echo "Invalid gate number selected.";
    }
}

// Close database connection
$conn->close();
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

    <style>
        .custom-date-input {
    height: 47px;
    width: 80%;
    border-color: lightgrey;
            text-align: center;
    }

    </style>

    <script>
        function formatDate(date) {
            var year = date.getFullYear();
            var month = ('0' + (date.getMonth() + 1)).slice(-2); // Add leading zero if needed
            var day = ('0' + date.getDate()).slice(-2); // Add leading zero if needed
            return year + '-' + month + '-' + day;
        }
    </script>
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

    <script>
        document.getElementById('datetime').addEventListener('change', function () {
            var date = new Date(this.value); // Convert input value to Date object
            this.value = formatDate(date);
        });

        document.getElementById('to_datetime').addEventListener('change', function () {
            var date = new Date(this.value); // Convert input value to Date object
            this.value = formatDate(date);
        });
    </script>
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
                                    <a href="AdminGetTicket.php" class="dropdown-item active">Get Ticket</a>
                                    <a href="AdminCheckIn.php" class="dropdown-item ">Check In</a>
                                </div>
                        </div>
                        
                        <a href="AdminCardRecharge.php" class="nav-item nav-link">Recharge</a>
                        <a href="AdminContact.php" class="nav-item nav-link">Contact</a>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Pages</a>
                            <div class="dropdown-menu border-0 rounded-0 m-0">
                                <a href="AdminComments.php" class="dropdown-item">CommentUs</a>
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
                <h3 class="display-4 text-white text-uppercase" style="letter-spacing: 5px;">Monthly Report</h3>
                <div class="d-inline-flex text-white">
                    <p class="m-0 text-uppercase"><a class="text-white" href="">Home</a></p>
                    <i class="fa fa-angle-double-right pt-1 px-3"></i>
                    <p class="m-0 text-uppercase">Monthly Report</p>
                </div>
            </div>
        </div>
    </div>
<!-- Header End -->


 <!-- Monthly Report Start -->
 <div class="container-fluid booking mt-5 pb-5">
    <div class="container pb-5">
        <div class="bg-light shadow" style="padding: 30px;">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="row align-items-center" style="min-height: 60px;">
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3 mb-md-0">
                                    <label for="from">From: </label>
                                    <input type="date" id="from" name="from" required class="px-4" style="height: 47px;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3 mb-md-0">
                                    <label for="to">To: </label>
                                    <input type="date" id="to" name="to" required class="px-4" style="height: 47px;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3 mb-md-0">
                                    <select class="px-4" name="gate_number" id="gate_number" style="height: 47px; width: 250px;" required>
                                        <option selected>Gate no.</option>
                                        <option value="Gate No- 1">Gate No- 1</option>
                                        <option value="Gate No- 2">Gate No- 2</option>
                                        <option value="Gate No- 3">Gate No- 3</option>
                                        <option value="ALL">ALL</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary btn-block d-flex align-items-center justify-content-center" type="submit" style="height: 52px; margin-top: -0px;">
                            Generate Report
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row justify-content-center">
        <?php if (!empty($total_amount)) { ?>
            <div class="col-md-6">
                <div class="alert alert-success" role="alert">
                    Total Amount: <?php echo $total_amount; ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Check-Ins</h5>
                    <p class="card-text"><?php echo $checkin_count; ?></p>
                    <p class="card-text">Total Amount: <?php echo $checkin_amount; ?></p>
                </div>
            </div>
        </div>
       
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tickets Issued</h5>
                    <p class="card-text"><?php echo $ticket_count; ?></p>
                    <p class="card-text">Total Amount: <?php echo $ticket_amount; ?></p>
                </div>
            </div>
        </div>


        <div class="col-md-4">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Total Revenue</h5>
            <p class="card-text"><?php echo $total_total_count; ?></p>
            <p class="card-text"><?php echo $total_revenue; ?></p>
        </div>
    </div>
</div>

    </div>
</div>

<!-- Monthly Report End -->

<!-- Footer Start -->
   <div class="container-fluid bg-dark text-white-50 py-5 px-sm-3 px-lg-5" style="margin-top: 40px;">
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

<!-- Inside the <script> tag -->

</body>
</html>