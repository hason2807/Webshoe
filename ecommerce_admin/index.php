<?php
// index.php
// Redirect to admin dashboard or login page
session_start();

// Check if admin is logged in
if(isset($_SESSION["admin_logged_in"]) && $_SESSION["admin_logged_in"] === true){
    header("location: admin/dashboard.php");
    exit;
} else {
    header("location: admin/login.php");
    exit;
}
?>