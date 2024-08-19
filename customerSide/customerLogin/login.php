<?php
// Include your database connection code here
require_once '../config.php';
session_start();

// Define variables for email and password
$email = $password = "";
$email_err = $password_err = "";

// Check if the form was submitted.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Check input errors before checking authentication
    if (empty($email_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT * FROM Accounts WHERE email = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);

            // Set parameters
            $param_email = $email;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Get the result
                $result = mysqli_stmt_get_result($stmt);

                // Check if a matching record was found.
                if (mysqli_num_rows($result) == 1) {
                    // Fetch the result row
                    $row = mysqli_fetch_assoc($result);

                    
                   // Verify the password
                    if ($password === $row["password"]) {
                        // Password is correct, start a new session and redirect the user to a dashboard or home page.
                        $_SESSION["loggedin"] = true;
                        $_SESSION["email"] = $email;

                        // Query to get membership details
                        $sql_member = "SELECT * FROM Memberships WHERE account_id = " . $row['account_id'];
                        $result_member = mysqli_query($link, $sql_member);

                        if ($result_member) {
                            $membership_row = mysqli_fetch_assoc($result_member);

                            if ($membership_row) {
                                $_SESSION["account_id"] = $membership_row["account_id"];
                                header("location: ../home/home.php"); // Redirect to the home page
                                exit;
                            } else {
                                // No membership details found
                                $password_err = "No membership details found for this account.";
                            }
                        } else {
                            // Error in membership query
                            $password_err = "Error fetching membership details: " . mysqli_error($link);
                        }
                    } else {
                        // Password is incorrect
                        $password_err = "Invalid password. Please try again.";
                    }


                } else {
                    // No matching records found
                    $email_err = "No account found with this email.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close the statement
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
    /* Style for the container within login.php */
    .login-container {
        animation: pop-up 0.5s ease forwards;
        opacity: 0;
        padding: 50px; /* Adjust the padding as needed */
        border-radius: 10px; /* Add rounded corners */
        margin: 100px auto; /* Center the container horizontally */
        max-width: 500px; /* Set a maximum width for the container */
        position: relative; /* Add position relative for video overlay */
        overflow: hidden; /* Hide overflowing video content */
    }

    /* Keyframe animation for popping up */
    @keyframes pop-up {
        from {
            transform: scale(0);
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    body {
        font-family: 'Montserrat', sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0; /* Remove default margin */
        color: white;
    }

    .video-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: -1;
    }

    .login_wrapper {
        animation: pop-up 1s ease forwards;
        opacity: 0;
        width: 400px; /* Adjust the container width as needed */
        padding: 20px;
    }

    h1 {
        animation: pop-up 1.5s ease forwards;
        opacity: 0;
        text-align: center;
        font-family: 'Copperplate', sans-serif;
        color: white;
        margin-bottom: 0;
    }

    p {
        animation: pop-up 2s ease forwards;
        opacity: 0;
        font-family: 'Montserrat', serif;
        text-align: center;
    }

    .form-group {
        animation: pop-up 2.5s ease forwards;
        opacity: 0;
        margin-bottom: 15px; /* Add space between form elements */
    }

    ::placeholder {
        font-size: 12px; /* Adjust the font size as needed */
    }

    .text-danger {
        animation: pop-up 3s ease forwards;
        opacity: 0;
        font-size: 13px;
        color: red;
    }

    .btn {
        animation: pop-up 3.5s ease forwards;
        opacity: 0;
        background-color: black; /* Set button background color */
        color: white; /* Set button text color */
        border: 1px solid white; /* Add white border to button */
        padding: 10px 20px; /* Adjust button padding */
        border-radius: 5px; /* Add button border-radius */
        cursor: pointer; /* Change cursor to pointer on hover */
        transition: transform 0.3s ease, border-color 0.3s ease; /* Add smooth transition */
    }

    .btn:hover {
        transform: scale(1.1); /* Scale up button on hover */
        border-color: transparent; /* Hide border on hover */
    }

    .form-control {
        background-color: black; /* Set form field background color */
        color: white; /* Set form field text color */
        border: 1px solid white; /* Add white border to form fields */
        border-radius: 5px; /* Add form field border-radius */
        padding: 10px; /* Adjust form field padding */
    }

    a {
        animation: pop-up 4s ease forwards;
        opacity: 0;
        color: white;
        text-decoration: none;
        font-weight: bold;
    }
</style>

</head>
<body>
<video class="video-background" autoplay muted loop>
            <source src="../image/food.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    <div class="login-container">
        

        <div class="login_wrapper">
            <a class="nav-link" href="../home/home.php#hero">
                <h1 class="text-center">DINEHUB</h1>
                <span class="sr-only"></span>
            </a>

            <div class="wrapper">
                <form action="login.php" method="post" class="form">
                    <div class="form-group">
                        <input type="email" name="email" class="form-control" placeholder="Enter User Email" required>
                        <span class="text-danger"></span>
                    </div>

                    <div class="form-group">
                        <input type="password" name="password" class="form-control" placeholder="Enter User Password" required>
                        <span class="text-danger"></span>
                    </div>

                    <button class="btn btn-dark" type="submit" name="submit" value="Login">Login</button>
                </form>

                <p>Don't have an account?</p>
                <a href="register.php">Proceed to Register</a>
            </div>
        </div>
    </div>
</body>
</html>



