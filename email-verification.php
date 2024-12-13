<?php
require 'connection.php';

function verifyAccount($conn, $table, $email, $verification_code) {
    // Prepare the SQL query for each role's table
    $sql = "SELECT * FROM $table WHERE email = '$email' AND verification_code = '$verification_code'";
    $result = mysqli_query($conn, $sql);
    
    // If a row is found, proceed with the update
    if ($result && mysqli_num_rows($result) > 0) {
        $sql_update = '';
        
        // If the 'type' GET parameter is set, update the reset token
        if (isset($_GET['type'])) {
            $sql_update = "UPDATE $table SET reset_token = '', reset_token_expiration = '' WHERE email = '$email' AND verification_code = '$verification_code'";
        } else {
            // Otherwise, mark the email as verified
            $sql_update = "UPDATE $table SET email_verified_at = NOW() WHERE email = '$email' AND verification_code = '$verification_code'";
        }
        
        // Execute the update query
        $result_update = mysqli_query($conn, $sql_update);
        
        if ($result_update && mysqli_affected_rows($conn) > 0) {
            // Redirect to the correct page depending on whether it's a password reset or verification
            if (isset($_GET['type'])) {
                header("Location: http://localhost/journal/updatepassword.php?email=$email");
                exit();
            } else {
                echo "<script>alert('Successfully Registered!'); document.location.href = 'login.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('Database error. Please try again later.'); window.history.back();</script>";
            exit();
        }
    }
    return false;
}

if (isset($_POST["verify_email"])) {
    $email = $_POST["email"];
    $verification_code = $_POST["verification_code"];
    
    // Check for the verification code in all relevant tables
    $tables = ['users', 'authors', 'editors', 'reviewers'];
    $isVerified = false;
    
    foreach ($tables as $table) {
        if (verifyAccount($conn, $table, $email, $verification_code)) {
            $isVerified = true;
            break;
        }
    }
    
    if (!$isVerified) {
        echo "<script>alert('Incorrect verification code. Please try again.'); window.history.back();</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VERIFY CODE</title>
    <link rel="icon" type="image/png" href="bee.png"/>
</head>
<body>
    <div class="content">
        <p class="verify-label"> Verify it's you.</p>
        <p class="sentences"> We sent a verification code to your email account.
                Please check your inbox and enter the 6-digit code.
        </p>

        <form method="POST">
            <input type="hidden" name="email" value="<?php echo $_GET['email']; ?>" required>
            <input type="text" name="verification_code" placeholder="Enter verification code" required /> <br> <br>
            <input type="submit" name="verify_email" value="Verify Email">
        </form>
        <p class="try-again"> Didn't receive an email? <a href=""> Try Again</a></p>
    </div>
</body>
</html>