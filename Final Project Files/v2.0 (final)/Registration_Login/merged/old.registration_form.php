<?php
include("../connection.php");

$form_error = '';
$form_success = false;

if (isset($_POST['submit'])) {
    $username = trim($_POST['input_name']);
    $type = $_POST['input_type'];
    $email = trim($_POST['input_email']);
    $password = trim($_POST['input_password']);
    $confirm = trim($_POST['input_password2']);

    if (empty($username) || empty($type) || empty($email) || empty($password) || empty($confirm)) {
        $form_error = "Please fill out all fields.";
    } else if ($password != $confirm) {
        $form_error = "Passwords do not match. Please try again.";
    } else {
        // Check if email already exists
        $sql_check = "select email from account where email='$email'";
        $res_check = mysqli_query($conn, $sql_check);
        if (mysqli_num_rows($res_check) > 0) {
            $form_error = "An account with this email already exists. <a href='login.php'>Login instead?</a>";
        } else {
            // passing to the Database
            $sql = "insert into account(email, password, username, type) values('$email','$password','$username','$type')";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                $form_success = true;
                // header("location:login.php"); // Redirect on success
            } else {
                $form_error = "An error occurred. Please try again later.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Support Hero</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

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
        <div class="back-link-container">
            <a href="../Home_Page/index.php" class="btn-back">
                &larr; Go to Homepage
            </a>
            <br><br><br><br>
        </div>
        <?php if ($form_success) { ?>
            <div class="form-message">
                <h3>Registration Successful!</h3>
                <p>Your account has been created.</p>
                <p><a href="login.php">Click here to Login</a></p>
                <!-- <br><br> -->
                <!-- <small style="color:white">Redirecting to Login</small> -->
                <!-- <meta http-equiv="refresh" content="3;url=login.php"> -->
            </div>
        <?php } else { ?>
            <form method="POST" class="form">
                <h2 style="text-align: center;">Registration Form</h2>

                <?php if (!empty($form_error)) { ?>
                    <div class="form-error">
                        <?php echo $form_error; ?>
                    </div>
                <?php } ?>

                <div class="form-group">
                    <label for="input_name">Username:</label>
                    <input id="input_name" name="input_name" type="text" required>
                </div>
                <div class="form-group">
                    <label for="input_type">User Type:</label>
                    <select id="input_type" name="input_type" required>
                        <option value="">-- Select your role --</option>
                        <option value="provider">Provider</option>
                        <option value="consumer">Consumer</option>
                        <!-- <option value="donor">Donor</option> -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="input_email">Email:</label>
                    <input id="input_email" name="input_email" type="email" required>
                </div>
                <div class="form-group">
                    <label for="input_password">Password:</label>
                    <input id="input_password" name="input_password" type="password" required>
                </div>
                <div class="form-group">
                    <label for="input_password2">Confirm Password:</label>
                    <input id="input_password2" name="input_password2" type="password" required>
                </div>
                <div class="form-group">
                    <input type="submit" name="submit" value="Create Account">
                </div>

                <div style="text-align: center; font-size: 0.9rem; color: #ccc;">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </form>
        <?php } ?>
    </div>
</body>

</html>