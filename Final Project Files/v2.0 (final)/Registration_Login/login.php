<?php
// 1. START SESSION at the very top
// This is the complete PHP logic from old.login.php
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
    // Note: The $count variable is now set for the HTML part
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Support Hero</title> <!-- Title from old.login.php -->

    <!-- Font from tmplog.php -->
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet" />

    <!-- CSS from tmplog.php -->
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
            /* Height set to auto to accommodate error messages */
            margin: auto;
            height: 500px;
            max-height: 520px;
            overflow: hidden;
            background: #fff url("background.jpg") no-repeat center/cover;
            border-radius: 10px;
            box-shadow: 5px 20px 50px rgba(0, 0, 0, 0.3);
            position: relative;
            padding-bottom: 20px;
        }

        #chk {
            display: none;
        }

        .signup {
            position: relative;
            width: 100%;
            height: 100%;
            /* Added padding for spacing */
            padding-bottom: 20px;
        }

        label {
            color: #000;
            font-size: 2em;
            display: flex;
            justify-content: center;
            margin: 40px;
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

        select {
            width: 65%;
            background: #e0dede;
            justify-content: center;
            display: flex;
            margin: 10px auto;
            padding: 10px;
            /* Matched input padding */
            border: none;
            outline: none;
            border-radius: 5px;
            color: #333;
            font-size: 0.9em;
            font-family: 'Jost', sans-serif;
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
            transform: translateY(-135px);
            transition: 0.6s ease-in-out;
            /* padding-bottom: 20px; */
        }

        .home a {
            text-decoration: none;
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

        /* Added style for the error message from old.login.php */
        .error-message {
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

    <!-- This is the HTML structure from tmplog.php -->
    <div class="main">

        <?php
        // This is the PHP logic from old.login.php
        if (isset($count) && $count == 1) {
            ?>
            <!-- Success Message (from old.login.php) styled like tmplog.php -->
            <div class="signup">
                <label for="chk" aria-hidden="true" style="margin-bottom: 10px;">Login Successful</label>
                <p style="text-align: center; color: #333; font-size: 1.2em;">
                    Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!
                </p>
                <p style="text-align: center; color: #555;">Redirecting to Homepage...</p>
            </div>

            <!-- Redirect logic from old.login.php -->
            <meta http-equiv="refresh" content="3;url=../Home_Page/index.php">

        <?php
        } else {
            ?>
            <!-- Login Form (HTML from tmplog.php) -->
            <div class="signup">
                <!-- Form action is empty, method is POST -->
                <form method="POST">
                    <label for="chk" aria-hidden="true">Login</label>

                    <!-- Select box from tmplog.php, matches old.login.php inputs -->
                    <select id="input_type" name="input_type">
                        <option value="">-- Select your role --</option>
                        <option value="provider">Provider</option>
                        <option value="consumer">Consumer</option>
                        <!-- <option value="donor">Donor</option> -->
                    </select>

                    <!-- Inputs from tmplog.php, names match old.login.php -->
                    <input type="email" name="input_email" placeholder="Email" required />
                    <input type="password" name="input_password" placeholder="Password" required />

                    <?php
                    // This is the error message logic from old.login.php
                    if (isset($count) && $count == 0) {
                        ?>
                        <div class="error-message">
                            <strong>Login Failed.</strong><br>Please check your credentials and try again.
                        </div>
                    <?php } ?>

                    <!-- 
                        CRITICAL: The button name is "submit" to match the PHP logic 
                        from old.login.php (if (isset($_POST['submit'])))
                    -->
                    <button type="submit" name="submit">Login</button>

                    <!-- Links from old.login.php -->
                    <div style="text-align: center; font-size: 0.9rem;">
                        <p><a href="forgot_password.php">Forgot Password?</a></p>
                        <p>Don&apos;t have an account? <a href="registration_form.php">Create one now</a></p>

                    </div>
                </form>
            </div>

            <!-- Home Section from tmplog.php -->
            <div class="home">
                <form method="POST" action="">
                    <label for="chk" aria-hidden="true">
                        <a href="../Home_Page/index.php">Home</a>
                    </label>
                </form>
            </div>
        <?php } ?>
    </div>
</body>

</html>