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

    if (isset($_GET['submit'])) {


        $email = $_GET['input1'];
        $password = $_GET['input2'];
        $sql = "select * from table_1 where column_1='$email' and column_2='$password' ";
        $result = mysqli_query($conn, $sql);
        $count = mysqli_num_rows($result);
        if ($count == 1) {
            // include("Welcome.php");
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
            <form method="GET" class="form">
                <h2 style="text-align: center;">Login Form</h2>
                <label for="input1">Input Email: </label>
                <input name="input1" type="email"><br><br>
                <label for="input2">Input Password: </label>
                <input name="input2" type="password"><br><br>
                <div style="display:flex;justify-content:center;"><input type="submit" name="submit"></div>

            </form>
        </div>
        <div class="sub-section">
            <form method="GET" class="form">
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