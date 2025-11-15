<?php
include("../connection.php");

$show_password_form = false;
$email = "";

// Step 1: Verify user
if (isset($_POST['verify'])) {
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);

    if ($email == "" || $username == "") {
        echo "<script>alert('Please enter email and username!');</script>";
    } else {
        $check = mysqli_query($conn, "SELECT * FROM account WHERE email='$email' AND username='$username'");
        if (mysqli_num_rows($check) == 1) {
            $show_password_form = true;
        } else {
            echo "<script>alert('No account found with that email and username.');</script>";
        }
    }
}

// Step 2: Update password
if (isset($_POST['update'])) {
    $email = $_POST['email'];
    $newpass = $_POST['newpass'];
    $confirm = $_POST['confirm'];
    $show_password_form = true;

    if ($newpass == "" || $confirm == "") {
        echo "<script>alert('Please fill all password fields!');</script>";
    } elseif ($newpass != $confirm) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        $sql = "UPDATE account SET password='$newpass' WHERE email='$email'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_affected_rows($conn) == 1) {
            echo "<script>alert('Password updated successfully! Now login...');window.location='tmplog.php';</script>";
        } else {
            echo "<script>alert('An error occurred while updating your password. Please try again.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Student Login/Register</title>
    <link rel="icon" href="Images/techimage.jpg" type="image">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet" />

    <!-- CSS -->
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
            height: 520px;
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
        }

        label {
            color: #000;
            font-size: 1.5em;
            display: flex;
            justify-content: center;
            margin: 55px;
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
            padding: 3px;
            border: none;
            outline: none;
            border-radius: 5px;
            color: #333;
            font-size: 13px;
            height: auto;
            min-height: 30px;

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
    </style>
</head>

<!-- HTML -->

<body>
    <div class="main">


        <!-- Login Section -->
        <div class="signup">
            <form method="POST" action="">
                <label for="chk" aria-hidden="true">Forgot Password</label>

                <!-- Step 1: Email and Username -->
                <?php if (!$show_password_form && !isset($_POST['update'])) { ?>
                    <input type="email" name="email" placeholder="Enter Email">
                    <input type="text" name="username" placeholder="Enter Username">
                    <button type="submit" name="verify">Verify Account</button>
                <?php } ?>

                <!-- Step 2: Password Fields -->
                <?php if ($show_password_form) { ?>
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    <input type="password" name="newpass" placeholder="New Password">
                    <input type="password" name="confirm" placeholder="Confirm Password">
                    <button type="submit" name="update">Update</button>
                <?php } ?>
            </form>
        </div>


        <!-- Home Section -->
        <div class="home">
            <form method="POST" action="">
                <label for="chk" aria-hidden="true"><a href="tmplog.php">Login</a></label>
            </form>
        </div>
    </div>
</body>

</html>