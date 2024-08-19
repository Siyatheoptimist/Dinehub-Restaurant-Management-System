<?php
// Include your database connection code here (not shown in this example).
require_once '../config.php';
session_start();

// Define variables and initialize them to empty values
$email = $member_name = $password = $phone_number = "";
$email_err = $member_name_err = $password_err = $phone_number_err = "";

// Check if the form was submitted.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate member name
    if (empty(trim($_POST["member_name"]))) {
        $member_name_err = "Please enter your member name.";
    } else {
        $member_name = trim($_POST["member_name"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate phone number
    if (empty(trim($_POST["phone_number"]))) {
        $phone_number_err = "Please enter your phone number.";
    } else {
        $phone_number = trim($_POST["phone_number"]);
    }

    // Check input errors before inserting into the database
    if (empty($email_err) && empty($member_name_err) && empty($password_err) && empty($phone_number_err)) {
        // Start a transaction
        mysqli_begin_transaction($link);

        // Prepare an insert statement for Accounts table
        $sql_accounts = "INSERT INTO Accounts (email, password, phone_number, register_date) VALUES (?, ?, ?, NOW())";
        if ($stmt_accounts = mysqli_prepare($link, $sql_accounts)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt_accounts, "sss", $param_email, $param_password, $param_phone_number);

            // Set parameters
            $param_email = $email;
            // Store the password as plain text (not recommended for production)
            $param_password = $password;
            $param_phone_number = $phone_number;

            // Attempt to execute the prepared statement for Accounts table
            if (mysqli_stmt_execute($stmt_accounts)) {
                // Get the last inserted account_id
                $last_account_id = mysqli_insert_id($link);

                // Prepare an insert statement for Memberships table
                // (Code for Memberships table insertion goes here...)
            } else {
                // If execution fails, display an error message
                echo "Oops! Something went wrong while inserting data into Accounts table. Please try again later.";
            }

            // Close the statement for Accounts table
            mysqli_stmt_close($stmt_accounts);
        }

        // Prepare an insert statement for Memberships table
        $sql_memberships = "INSERT INTO Memberships (member_name, points, account_id) VALUES (?, ?, ?)";
        if ($stmt_memberships = mysqli_prepare($link, $sql_memberships)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt_memberships, "sii", $param_member_name, $param_points, $last_account_id);

            // Set parameters for Memberships table
            $param_member_name = $member_name;
            $param_points = 0; // You can set an initial value for points

            // Attempt to execute the prepared statement for Memberships table
            if (mysqli_stmt_execute($stmt_memberships)) {
                // Commit the transaction
                mysqli_commit($link);

                // Registration successful, redirect to the login page
                header("location: register_process.php");
                exit;
            } else {
                // Rollback the transaction if there was an error
                mysqli_rollback($link);
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close the statement for Memberships table
            mysqli_stmt_close($stmt_memberships);
        } else {
            // Rollback the transaction if there was an error
            mysqli_rollback($link);
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    // Close the statement for Accounts table
    if (isset($stmt_accounts)) {
        mysqli_stmt_close($stmt_accounts);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0; /* Remove default margin */
            color: white;
            overflow: hidden; /* Hide overflow to prevent scroll bars */
        }

        /* Style for the container within login.php */
        .register-container {
            animation: pop-up 1s forwards;
            opacity: 0;
            padding: 50px; /* Adjust the padding as needed */
            border-radius: 10px; /* Add rounded corners */
            margin: 100px auto; /* Center the container horizontally */
            max-width: 500px; /* Set a maximum width for the container */
            position: relative; /* Position the container */
            overflow: hidden; /* Hide overflow to prevent scroll bars */
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

        .register_wrapper {
            animation: pop-up 1s forwards;
            opacity: 0;
            width: 400px; /* Increase the container width */
            padding: 20px;
        }

        h1 {
            animation: pop-up 1s forwards;
            opacity: 0;
            text-align: center;
            font-family: 'Copperplate', sans-serif;
            color: white;
            margin-bottom: 0;
        }

        p {
            animation: pop-up 1s forwards;
            opacity: 0;
            font-family: 'Montserrat', serif;
            text-align: center;
            color: white;
            margin-top: 1em;
        }

        .form-group {
            animation: pop-up 1s forwards;
            opacity: 0;
            margin-bottom: 15px; /* Add space between form elements */
        }

        ::placeholder {
            font-size: 12px; /* Adjust the font size as needed */
        }

        .text-danger {
            animation: pop-up 1s forwards;
            opacity: 0;
            font-size: 13px;
            color: red;
        }

        .btn {
            animation: pop-up 1s forwards;
            opacity: 0;
            background-color: black;
            color: white;
            border: 1px solid white; /* Add thin white border */
        }

        a {
            animation: pop-up 1s forwards;
            opacity: 0;
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        @keyframes pop-up {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .pop-up-delay-1 {
            animation-delay: 0.5s;
        }

        .pop-up-delay-2 {
            animation-delay: 1s;
        }

        .pop-up-delay-3 {
            animation-delay: 1.5s;
        }

        .pop-up-delay-4 {
            animation-delay: 2s;
        }

        .pop-up-delay-5 {
            animation-delay: 2.5s;
        }

        .pop-up-delay-6 {
            animation-delay: 3s;
        }

        .pop-up-delay-7 {
            animation-delay: 3.5s;
        }

        /* Style for video */
        video {
            position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: -1;
        }
    </style>
</head>
<body>
<video autoplay muted loop>
            <source src="../image/food2.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    <div class="register-container">
        
        <div class="register_wrapper">
            <a class="nav-link" href="../home/home.php">
                <h1 class="text-center">DINEHUB</h1>
                <span class="sr-only"></span>
            </a>
           
            <form action="register.php" method="post">
                <div class="form-group pop-up-delay-1">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter Email" required>
                    <span class="text-danger"><?php echo $email_err; ?></span>
                </div>

                <div class="form-group pop-up-delay-2">
                    <label>Member Name</label>
                    <input type="text" name="member_name" class="form-control" placeholder="Enter Member Name" required>
                    <span class="text-danger"><?php echo $member_name_err; ?></span>
                </div>

                <div class="form-group pop-up-delay-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter Password" required>
                    <span class="text-danger"><?php echo $password_err; ?></span>
                </div>

                <div class="form-group pop-up-delay-4">
                    <label>Phone Number</label>
                    <input type="text" name="phone_number" class="form-control" placeholder="Enter Phone Number" required>
                    <span class="text-danger"><?php echo $phone_number_err; ?></span>
                </div>

                <button class="btn btn-dark pop-up-delay-5" type="submit" name="register" value="Register">Register</button>
            </form>

            <p>Already have an account?</p>
            <a href="../customerLogin/login.php">Proceed to Login</a>
        </div>
    </div>
</body>
</html>
