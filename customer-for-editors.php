<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';
include('customer-nav.php');

// Check if the user is logged in
if (!isset($_SESSION['user_name'])) {
    // Redirect to the login page or handle accordingly
    header("Location: http://localhost/journal/login.php");
    exit;
}

$userName = $_SESSION['user_name'];

// If you want to log out, you can add a condition to check for a logout action
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    // Clear all session variables
    session_unset();
    // Destroy the session
    session_destroy();
    // Redirect to the login page or handle accordingly
    header("Location: http://localhost/journal/login.php");
    exit;
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user profile picture
$sqlGetUser = "SELECT image_path FROM users WHERE name = ?";
$stmt = $conn->prepare($sqlGetUser);
$stmt->bind_param("s", $userName);
$stmt->execute();
$resultUser = $stmt->get_result();

if ($resultUser->num_rows > 0) {
    $user = $resultUser->fetch_assoc();
    $profilePic = $user['image_path'];
} else {
    // Default profile picture if none is found
    $profilePic = 'default-profile.png';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editors Information</title>
</head>
<body>
    <div class="content">
        <h1 class="title">INFORMATION FOR EDITORS</h1>
        <p class="info">
            Interested in becoming an editor for our journal? Review the <a href="customer-about-us.php">about</a> on journal page to understand our editorial policies and responsibilities.
            Editors ensure quality by overseeing peer reviews, making decisions based on feedback, communicating with authors, and upholding ethical standards.
            To join our team, <a href="register.php">register</a> and submit an applicatopn outlining your qualifications.
            Existing editors can <a href="login.php">login</a> to manage their manuscripts.
            Thank you for your commitment to maintaining our journal&apos;s high standards.
        </p>
    </div>
    <div class="info-role">
        <h4 class="info-content">INFORMATION</h4>
        <a href="customer-for-readers.php">For Readers</a> <br>
        <a href="customer-for-authors.php">For Authors</a> <br>
        <a href="customer-for-reviewers.php">For Reviewers</a> <br>
        <a href="customer-for-editors.php">For Editors</a>
    </div>
</body>
</html>