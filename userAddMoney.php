<?php
session_start(); // Start the session

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION["username"])) {
    header("Location: login.html");
    exit();
}
?>

