<?php
include("../connection.php");


if (isset($_POST['register'])) {
    $username = trim($_POST['input_name']);
    $type = $_POST['input_type'];
    $email = trim($_POST['input_email']);
    $password = trim($_POST['input_password']);
    $confirm = trim($_POST['input_password2']);

    // if (empty($username) || empty($type) || empty($email) || empty($password) || empty($confirm)) {
    //     echo "<script>alert('Please fill all password fields!');</script>";
    //}  
    if ($password != $confirm) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        // Check if email already exists
        $sql_check = "select email from account where email='$email'";
        $res_check = mysqli_query($conn, $sql_check);
        
        if (mysqli_num_rows($res_check) > 0) {
            echo "<script>alert('An account with this email already exists.'); window.location.href = 'tmplog.php'</script>";
        } else {
            // passing to the Database
            $sql = "insert into account(email, password, username, type) values('$email','$password','$username','$type')";
            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('Registration successful! Your account has been created.Click okk to Login');window.location.href = 'tmplog.php'</script>";
            } else {
                echo '<script>alert("An error occurred. Please try again later.")</script>';
            }
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
            font-size: 2em;
            display: flex;
            justify-content: center;
            margin: 30px;
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
            margin: 20px auto;
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
            transform: translateY(-115px);
            transition: 0.6s ease-in-out;
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
    </style>
</head>

<!-- HTML -->

<body>
    <div class="main">


        <!-- Login Section -->
        <div class="signup">
            <form method="POST" action="">
                <label for="chk" aria-hidden="true">Registration</label>
                <input type="name" name="input_name" placeholder="Name" required />
                <select id="input_type" name="input_type">
                    <option value="">-- Select your role --</option>
                    <option value="provider">Provider</option>
                    <option value="consumer">Consumer</option>
                </select>
                <input type="email" name="input_email" placeholder="Email" required />
                <input type="password" name="input_password" placeholder="Password" required />
                <input type="password" name="input_password2" placeholder="Confirm Password" required />

                <button type="submit" name="register">Create Account</button>

                <div style="text-align: center; font-size: 0.9rem;">
                    <p>Already have an account? <a href="tmplog.php">Login here</a></p>
                </div>
            </form>
        </div>


        <!-- Home Section -->
        <div class="home">
            <form method="POST" action="">
                <label for="chk" aria-hidden="true"><a href="../Home_Page/index.php">Home</a></label>
            </form>
        </div>
    </div>
</body>

</html>