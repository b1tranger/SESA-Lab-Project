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
    </style>
    <?php

    include("connection.php");



    if (isset($_POST['submit'])) {

        $username = trim($_POST['input_name']);
        $id = trim($_POST['input_id']);
        $email = trim($_POST['input_email']);
        $password = $_POST['input_password'];
        $confirm = $_POST['input_password2'];
    }
    $sql2 = "select email from table_1 where email='$email'";

    if (empty($username) || empty($id) || empty($email) || empty($password) || empty($confirm)) {
        echo '<script>alert("Please fill the form")</script>';
    } else if ($password != $confirm) {
        echo '<script>alert("Passwords do not match")</script>';
    } else if ($sql2) {
        echo '<script>alert("Email already exists")</script>';
    } else {

        // passing to the Database
    
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "insert into table_1(username, student_id, email, password) values('$username','$id','$email','$password')";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            echo '<script>alert("Signup successful")</script>';
            header("login.php");
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
                <h2 style="text-align: center;">Registation Form</h2>
                <label for="input_name">Username: </label>
                <input name="input_name" type="text"><br><br>
                <label for="input_id">Student ID: </label>
                <input name="input_id" type="number"><br><br>
                <label for="input_email">Email: </label>
                <input name="input_email" type="email"><br><br>
                <label for="input_password">Password: </label>
                <input name="input_password" type="password"><br><br>
                <label for="input_password2">Confirm Password: </label>
                <input name="input_password2" type="password"><br><br>
                <div style="display:flex;justify-content:center;"><input type="submit" name="submit"></div>

            </form>
        </div>
        <div class="sub-section">
            <form method="POST" class="form">
                <h2 style="text-align: center;">Input Data</h2>
                <label for="input1">Input Email: </label>
                <input name="input1" type="email" value="<?php echo $input1; ?>"><br><br>
                <label for="input2">Input Password: </label>
                <input name="input2" type="text" value="<?php echo $input2; ?>"><br><br>
                <!-- <div style="display:flex;justify-content:center;"><input type="submit" name="input2" value="enter"></div> -->

            </form>
        </div>
    </div>

</body>

</html>