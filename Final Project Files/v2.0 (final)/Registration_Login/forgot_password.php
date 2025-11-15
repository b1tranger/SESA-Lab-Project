<?php
// This is the complete PHP logic from old.forgot_password.php
session_start();
include("../connection.php");

$step = 1; // We'll use this to control the form display
$form_error = '';
$form_success = false;
$email_to_update = ''; // To hold the email between steps

// VERIFY USER 
if (isset($_POST['submit_email'])) { // Logic from old.forgot_password.php
    $email = trim($_POST['input_email']); // Name from old.forgot_password.php
    $username = trim($_POST['input_name']); // Name from old.forgot_password.php

    if (empty($email) || empty($username)) {
        $form_error = "Please enter both your email and username.";
    } else {
        $sql = "SELECT * FROM account WHERE email='$email' AND username='$username'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            // User found! Proceed to step 2
            $step = 2;
            $email_to_update = $email; // Store email for the next step
        } else {
            $form_error = "No account found with that email and username combination.";
        }
    }
}

//  UPDATE PASSWORD
if (isset($_POST['submit_password'])) { // Logic from old.forgot_password.php
    $email_to_update = trim($_POST['email_to_update']); // Name from old.forgot_password.php
    $password = trim($_POST['input_password']); // Name from old.forgot_password.php
    $confirm = trim($_POST['input_password2']); // Name from old.forgot_password.php

    if (empty($password) || empty($confirm)) {
        $form_error = "Please enter and confirm your new password.";
        $step = 2; // Keep user on step 2
    } else if ($password != $confirm) {
        $form_error = "Your new passwords do not match. Please try again.";
        $step = 2; // Keep user on step 2
    } else {
        // Passwords match, update the database
        $sql = "UPDATE account SET password='$password' WHERE email='$email_to_update'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_affected_rows($conn) == 1) {
            // Success!
            $form_success = true;
        } else {
            $form_error = "An error occurred while updating your password. Please try again.";
            $step = 2; // Keep user on step 2
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Forgot Password - Support Hero</title> <!-- Title from old.forgot_password.php -->
    <link rel="icon" href="Images/techimage.jpg" type="image">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet" />

    <!-- CSS from forgetpass.php -->
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Jost', sans-serif;
            background: linear-gradient(to bottom, #00416A, #E4E5E6);
        }

        .main {
            width: 370px;
            /* Height set to auto to accommodate messages */
            height: 500px;
            max-height: 520px;
            overflow: hidden;
            background: #fff url("background.jpg") no-repeat center/cover;
            border-radius: 10px;
            box-shadow: 5px 20px 50px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        #chk {
            display: none;
        }

        .signup {
            position: relative;
            width: 100%;
            height: 100%;
            padding-bottom: 20px;
        }

        label {
            color: #000;
            font-size: 1.5em;
            /* From forgetpass.php */
            display: flex;
            justify-content: center;
            margin: 40px;
            /* Adjusted margin */
            font-weight: bold;
            cursor: pointer;
            transition: 0.5s ease-in-out;
        }

        input {
            width: 60%;
            background: #e0dede;
            display: flex;
            justify-content: center;
            margin: 15px auto;
            padding: 10px;
            border: none;
            outline: none;
            border-radius: 5px;
        }

        button {
            width: 50%;
            height: 40px;
            margin: 30px auto;
            display: block;
            color: #fff;
            background: #00416A;
            font-size: 1em;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s ease-in;
        }

        button:hover {
            background: #026AA7;
        }

        .home {
            height: 500px;
            background: #eee;
            border-radius: 60% / 10%;
            /* Adjusted transform from forgetpass.php */
            transform: translateY(-165px);
            transition: 0.6s ease-in-out;
        }

        .home a {
            text-decoration: none;
            font-size: 1.3em;
            color: #00416A;
            transform: scale(0.6);
        }

        .home a:hover {
            color: red;
            text-decoration: overline;
            cursor: pointer;
        }

        p {
            text-align: center;
        }

        /* Style for the error message (from old.forgot_password.php) */
        .form-error {
            background-color: #5a2a2a;
            color: #ffc0c0;
            padding: 1rem;
            border-radius: 5px;
            text-align: center;
            margin: 10px auto;
            width: 65%;
            font-size: 0.9em;
        }
    </style>
</head>

<body>
    <div class="main">

        <!-- HTML Structure from forgetpass.php, but logic from old.forgot_password.php -->
        <div class="signup">

            <?php if ($form_success) { ?>
                <!-- Success Message -->
                <label for="chk" aria-hidden="true" style="margin-bottom: 10px;">Password Updated!</label>
                <p style="text-align: center; color: #333; font-size: 1.1em; padding: 0 20px;">
                    Your password has been successfully reset.
                </p>
                <p style="text-align: center; font-size: 1em; margin-top: 20px;">
                    <a href="login.php" style="color: #00416A; text-decoration: underline;">Click here to Login</a>
                </p>

            <?php } else if ($step == 1) { ?>
                    <!-- STEP 1: Verify User Form -->
                    <form method="POST">
                        <label for="chk" aria-hidden="true">Forgot Password</label>
                        <p style="text-align: center; color: #555; margin-top: -30px; margin-bottom: 20px; font-size: 0.9em;">
                            Verify your account
                        </p>

                    <?php if (!empty($form_error)) { ?>
                            <div class="form-error">
                            <?php echo $form_error; ?>
                            </div>
                    <?php } ?>

                        <!-- Input names MUST match old.forgot_password.php logic -->
                        <input type="email" name="input_email" placeholder="Enter Email" required>
                        <input type="text" name="input_name" placeholder="Enter Username" required>
                        <!-- Button name MUST match old.forgot_password.php logic -->
                        <button type="submit" name="submit_email">Verify Account</button>
                    </form>

            <?php } else if ($step == 2) { ?>
                        <!-- STEP 2: New Password Form -->
                        <form method="POST">
                            <label for="chk" aria-hidden="true">Reset Password</label>
                            <p style="text-align: center; color: #555; margin-top: -30px; margin-bottom: 20px; font-size: 0.9em;">
                                Enter your new password
                            </p>

                    <?php if (!empty($form_error)) { ?>
                                <div class="form-error">
                            <?php echo $form_error; ?>
                                </div>
                    <?php } ?>

                            <!-- Hidden field name MUST match old.forgot_password.php logic -->
                            <input type="hidden" name="email_to_update" value="<?php echo htmlspecialchars($email_to_update); ?>">

                            <!-- Input names MUST match old.forgot_password.php logic -->
                            <input type="password" name="input_password" placeholder="New Password" required>
                            <input type="password" name="input_password2" placeholder="Confirm Password" required>
                            <!-- Button name MUST match old.forgot_password.php logic -->
                            <button type="submit" name="submit_password">Update Password</button>
                        </form>

            <?php } ?>

            <!-- General Link to Login -->
            <div style="text-align: center; font-size: 0.9rem; margin-top: 15px;">
                <p>Remember your password? <a href="login.php" style="color: #00416A;">Login here</a></p>
            </div>

        </div>

        <!-- Home/Login Section from forgetpass.php (modified to point to login.php) -->
        <div class="home">
            <form>
                <label for="chk" aria-hidden="true">
                    <!-- This now links to the new login.php -->
                    <a href="login.php">Login</a>
                </label>
            </form>
        </div>
    </div>
</body>

</html>