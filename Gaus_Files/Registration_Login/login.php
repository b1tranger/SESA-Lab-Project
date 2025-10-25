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

    include("../connection.php");

    if (isset($_POST['submit'])) {


        $email = trim($_POST['input1']);
        $password = trim($_POST['input2']);
        $type = $_POST['input_type'];
        $sql = "select * from account where email='$email' ";
        $result = mysqli_query($conn, $sql);
        $count = mysqli_num_rows($result);
        if ($count == 1) {
            // include("Welcome.php");
            // echo '<script>alert("Login Successful")</script>';
            header("location:Welcome.php");
        } else {
            echo '<script>alert("Login Failed")</script>';
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
                <h2 style="text-align: center;">Login Form</h2>
                <label for="input_type">User Type: </label>
                <select name="input_type">
                    <option value=""></option>
                    <option value="provider">Provider</option>
                    <option value="consumer">Consumer</option>
                    <option value="donor">Donor</option>
                </select>
                <br><br>
                <label for="input1">Input Email: </label>
                <input name="input1" type="email"><br><br>
                <label for="input2">Input Password: </label>
                <input name="input2" type="password"> &nbsp;(numbers only)<br><br>
                <div style="display:flex;justify-content:center;"><input type="submit"></div>

            </form>
        </div>

    </div>

</body>

</html>