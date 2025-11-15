<?php
// 1. START SESSION at the very top
session_start();

include("../connection.php");

if (isset($_POST['submit'])) {
    $email = trim($_POST['input_email']);
    $password = trim($_POST['input_password']);
    $type = $_POST['input_type'];
    if ($type == "") {
        $type = "admin";
    }
    $sql = "select * from account where email='$email' and password = '$password' and type='$type' ";
    $result = mysqli_query($conn, $sql);
    $count = mysqli_num_rows($result);

    if ($count == 1) {
        // 2. FETCH USER DATA AND SET SESSION
        $row = mysqli_fetch_assoc($result);
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $row['username'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['user_id'] = $row['user_id'];
        if ($type == "") {
            $_SESSION['user_type'] = 'admin';
        } else {
            $_SESSION['user_type'] = $row['type'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Support Hero</title>
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

        textarea {
            resize: vertical;
            min-height: 100px;
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
        <?php if (isset($count) && $count == 1) { ?>
            <div class="form-message">
                <h3>Login Successful</h3>
                <p>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
                <!-- <p><a href="../Home_Page/index.php">Browse the Website</a></p> -->
                 <br><br>
                <small style="color:white">Redirecting to Homepage</small>
                <meta http-equiv="refresh" content="3;url=../Home_Page/index.php">
            </div>
        <?php } else { ?>
            <form method="POST" class="form">
                <h2>Login Form</h2>

                <?php if (isset($count) && $count == 0) { ?>
                    <div class="form-group"
                        style="background-color: #5a2a2a; color: #ffc0c0; padding: 1rem; border-radius: 6px; text-align: center;">
                        <strong>Login Failed.</strong><br>Please check your credentials and try again.
                    </div>
                <?php } ?>

                <div class="form-group">
                    <label for="input_type">User Type:</label>
                    <select id="input_type" name="input_type">
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
                    <input type="submit" name="submit" value="Login">
                </div>

                <div style="text-align: center; font-size: 0.9rem; color: #ccc;">
                    <p>Don&apos;t have an account? <a href="registration_form.php">Create one now</a></p>
                    <p><a href="forgot_password.php">Forgot Password?</a></p>
                </div>

            </form>
        <?php } ?>
    </div>
</body>

</html>