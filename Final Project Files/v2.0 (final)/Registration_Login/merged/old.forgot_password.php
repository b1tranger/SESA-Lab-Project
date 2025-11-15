<?php
session_start();
include("../connection.php");

$step = 1; // We'll use this to control the form display
$form_error = '';
$form_success = false;
$email_to_update = ''; // To hold the email between steps

// VERIFY USER 
if (isset($_POST['submit_email'])) {
    $email = trim($_POST['input_email']);
    $username = trim($_POST['input_name']);

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
if (isset($_POST['submit_password'])) {
    $email_to_update = trim($_POST['email_to_update']);
    $password = trim($_POST['input_password']);
    $confirm = trim($_POST['input_password2']);

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Support Hero</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- STYLES (Same as login.php) -->
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: #202020;
            color: #f0f0f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-container {
            margin: auto;
            max-width: 450px;
            width: 90%;
            padding: 2.5rem;
            background-color: #333;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .form-container h2 {
            text-align: center;
            margin-top: 0;
            margin-bottom: 1.5rem;
            color: #ffffff;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #ccc;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 0.8rem;
            background-color: #444;
            border: 1px solid #555;
            border-radius: 6px;
            color: #f0f0f0;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            box-sizing: border-box;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.3);
        }

        .input-hint {
            font-size: 0.875rem;
            color: #999;
            margin-left: 0.5rem;
        }

        input[type="submit"] {
            width: 100%;
            padding: 0.8rem;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #1d4ed8;
        }

        a {
            color: #60a5fa;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #93c5fd;
            text-decoration: underline;
        }

        .form-message {
            text-align: center;
            padding-top: 1.5rem;
            margin-top: 1.5rem;
            border-top: 1px solid #444;
        }

        .form-message h3 {
            font-size: 1.5rem;
            margin: 0 0 1rem 0;
            color: #ffffff;
        }

        .form-message p {
            margin-bottom: 1rem;
        }

        .form-error {
            background-color: #5a2a2a;
            color: #ffc0c0;
            padding: 1rem;
            border-radius: 6px;
            text-align: center;
            margin-bottom: 1.25rem;
        }
    </style>
</head>

<body>
    <div class="form-container">

        <?php if ($form_success) { ?>
            <!-- Show Success Message -->
            <div class="form-message">
                <h3>Password Updated!</h3>
                <p>Your password has been successfully reset.</p>
                <p><a href="login.php">Click here to Login</a></p>
            </div>

        <?php } else if ($step == 1) { ?>
            <!-- STEP 1: Show Verify User Form -->
            <form method="POST" class="form">
                <h2>Forgot Password</h2>
                <p style="text-align: center; color: #ccc; margin-top: -1rem; margin-bottom: 1.5rem;">
                    Please enter your email and username to verify your account.
                </p>

                <?php if (!empty($form_error)) { ?>
                    <div class="form-error">
                        <?php echo $form_error; ?>
                    </div>
                <?php } ?>

                <div class="form-group">
                    <label for="input_email">Email:</label>
                    <input id="input_email" name="input_email" type="email" required>
                </div>
                <div class="form-group">
                    <label for="input_name">Username:</label>
                    <input id="input_name" name="input_name" type="text" required>
                </div>
                <div class="form-group">
                    <input type="submit" name="submit_email" value="Verify Account">
                </div>
            </form>

        <?php } else if ($step == 2) { ?>
            <!-- STEP 2: Show New Password Form -->
            <form method="POST" class="form">
                <h2>Reset Password</h2>
                <p style="text-align: center; color: #ccc; margin-top: -1rem; margin-bottom: 1.5rem;">
                    Enter your new password below.
                </p>

                <?php if (!empty($form_error)) { ?>
                    <div class="form-error">
                        <?php echo $form_error; ?>
                    </div>
                <?php } ?>

                <!-- Hidden field to pass the email to the next request -->
                <input type="hidden" name="email_to_update" value="<?php echo htmlspecialchars($email_to_update); ?>">

                <div class="form-group">
                    <label for="input_password">New Password:</label>
                    <input id="input_password" name="input_password" type="password" required>
                </div>
                <div class="form-group">
                    <label for="input_password2">Confirm New Password:</label>
                    <input id="input_password2" name="input_password2" type="password" required>
                </div>
                <div class="form-group">
                    <input type="submit" name="submit_password" value="Update Password">
                </div>
            </form>
        <?php } ?>

        <!-- General Links -->
        <div style="text-align: center; font-size: 0.9rem; color: #ccc; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #444;">
            <p>Remember your password? <a href="login.php">Login here</a></p>
        </div>

    </div>
</body>

</html>