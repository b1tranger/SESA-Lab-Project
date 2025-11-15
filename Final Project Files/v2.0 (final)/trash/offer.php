<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        body {
            margin: 0;
        }

        .section {
            min-height: 100vh;
            background-color: #202020ff;
            color: white;
            /* margin: 0; */
        }

        .sub-section {
            margin: auto;

            max-width: 500px;
            padding: 20px;
            background-color: #333;
            border-radius: 20px;
            margin-top: 20px;
        }

        .form {}

        input{
            width: 100%;
            padding: 5px;
        }
    </style>
    <?php

    include("../connection.php");



    if (isset($_POST['submit'])) {

        $username = trim($_POST['input_name']);
        // $id = trim($_POST['input_id']);
        $type = $_POST['input_type'];
        $email = trim($_POST['input_email']);
        $password = trim($_POST['input_password']);
        $confirm = trim($_POST['input_password2']);
    }
    // $sql2 = "select email from table_1 where email='$email'";

    if (empty($username) || empty($type) || empty($email) || empty($password) || empty($confirm)) {
        echo '<script>alert("Please fill the form")</script>';
    } else if ($password != $confirm) {
        echo '<script>alert("Passwords do not match")</script>';
    // } else if ($sql2) {
        // echo '<script>alert("Email already exists")</script>';
    } else {

        // passing to the Database
    
        // $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "insert into account(email, password, username, type) values('$email','$password','$username','$type')";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            // echo '<script>alert("Signup successful")</script>';
            header("location:login.php");
        }
    }
    ?>


</head>

<body>

    <div class="section">
        <br>
        <br>
        <div class="sub-section">
            <form method="POST" class="form">
                <h2 style="text-align: center;">Request a Service</h2>
                <label for="service_name">Service Name: </label>
                <input name="service_name" type="text" placeholder="give a short name for the request"><br><br>
                <label for="input_type">Service Type: </label>
                <!-- <input name="input_id" type="number"> -->
                <select name="input_type">
                    <option value="">--Select--</option>
                    <option value="request">Request</option>
                    <option value="offer">Offer</option>
                    <!-- <option value="donor">Donor</option> -->
                </select>
                <br><br>
                <label for="input_email">Email: </label>
                <input name="input_email" type="email"><br><br>
                <label for="input_password">Password: </label>
                <input name="input_password" type="password"> &nbsp;(numbers only)<br><br>
                <label for="input_password2">Confirm Password: </label>
                <input name="input_password2" type="password"> &nbsp;(numbers only)<br><br>
                <div style="display:flex;justify-content:center;max-width:80px;"><input type="submit" name="submit"></div>

            </form>
        </div>

    </div>

</body>

</html>