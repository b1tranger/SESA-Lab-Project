<?php
session_start();

$conn = new mysqli("localhost", "root", "", "support_hero");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$signup_msg = "";
$login_msg = "";
$show_login = false;

// SIGNUP 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $user_type = $_POST['user_type'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $location = $_POST['location'];
    $blood_group = $_POST['blood_group'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO User (User_type, User_Name, User_Phone, User_Email, User_address, User_Blood_Group, User_Pass) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $user_type, $name, $phone, $email, $location, $blood_group, $password);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;

        if ($user_type == "Provider") {
            $stmt2 = $conn->prepare("INSERT INTO Provider (P_ID) VALUES (?)");
            $stmt2->bind_param("i", $user_id);
            $stmt2->execute();
        } elseif ($user_type == "Consumer") {
            $stmt2 = $conn->prepare("INSERT INTO Consumer (C_ID) VALUES (?)");
            $stmt2->bind_param("i", $user_id);
            $stmt2->execute();
        } elseif ($user_type == "Donor") {
            $stmt2 = $conn->prepare("INSERT INTO Donor (D_ID) VALUES (?)");
            $stmt2->bind_param("i", $user_id);
            $stmt2->execute();
        }

        $signup_msg = $signup_msg = "<p style='color: green; text-align:center;'> Registration successful! You can now log in.</p>";
    } else {
        $signup_msg = "<p style='color: white; text-align:center;'> Error: " . $conn->error . "</p>";
    }
}

// LOGIN 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['pswd'];

    $stmt = $conn->prepare("SELECT User_ID, User_Name, User_Pass, User_Type FROM User WHERE User_Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['User_Pass'])) {
            $_SESSION['user_id'] = $user['User_ID'];
            $_SESSION['user_name'] = $user['User_Name'];
            $_SESSION['user_type'] = $user['User_Type'];

            switch ($user['User_Type']) {
                case 'Consumer':
                    header("Location: consumer_dashboard.php");
                    break;
                case 'Provider':
                    header("Location: provider_dashboard.php");
                    break;
                case 'Donor':
                    header("Location: donor_dashboard.php");
                    break;
                case 'Admin':
                    header("Location: admin_dashboard.php");
                    break;
                default:
                    header("Location: index.php");
                    break;
            }
            exit();
        } else {
            $login_msg = "<p style='color: red; text-align:center;'> Invalid password!</p>";
            $show_login = true;
        }
    } else {
        $login_msg = "<p style='color: red; text-align:center;'> User not found!</p>";
        $show_login = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Slide LogRegister</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet" />
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Jost', sans-serif;
            background: linear-gradient(to bottom, #472400, #5d3301, #6b3900);
        }

        .main {
            width: 370px;
            height: 570px;
            overflow: hidden;
            background: url("support.jpeg") no-repeat center/cover;
            border-radius: 10px;
            box-shadow: 5px 20px 50px #3c2d1e;
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
            color: #fff;
            font-size: 2.3em;
            justify-content: center;
            display: flex;
            margin: 40px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.5s ease-in-out;
        }

        input {
            width: 60%;
            height: 10px;
            background: #e0dede;
            justify-content: center;
            display: flex;
            margin: 10px auto;
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
            margin: 8px auto;
            justify-content: center;
            display: block;
            color: #fff;
            background: #573b8a;
            font-size: 1em;
            font-weight: bold;
            margin-top: 20px;
            outline: none;
            border: none;
            border-radius: 5px;
            transition: 0.3s ease-in;
            cursor: pointer;
        }

        button:hover {
            background: #6d44b8;
        }

        .login {
            height: 500px;
            background: #eee;
            border-radius: 60% / 10%;
            transform: translateY(-135px);
            transition: 0.6s ease-in-out;

        }

        .login label {
            color: #573b8a;
            transform: scale(0.6);
            transition: 0.6s ease-in-out;
            margin-top: 30px;
        }

        #chk:checked~.login {
            transform: translateY(-540px);
        }

        #chk:checked~.login label {
            transform: scale(1);
        }

        #chk:checked~.signup label {
            transform: scale(0.6);
        }
    </style>
</head>

<body>
    <div class="main">

        <input type="checkbox" id="chk" aria-hidden="true" <?php echo ($show_login) ? 'checked' : ''; ?> />


        <div class="signup">
            <form action="" method="POST">
                <label for="chk" aria-hidden="true">Sign up</label>

                <input type="text" name="name" placeholder="Full Name" required />
                <input type="text" name="phone" placeholder="Phone" required />
                <input type="email" name="email" placeholder="Email" required />
                <input type="text" name="location" placeholder="Location" required />

                <select name="blood_group" required>
                    <option value="">Select Blood Group</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>

                <input type="password" name="password" placeholder="Password" required />

                <select name="user_type" id="user_type" required>
                    <option value="">Select Role</option>
                    <option value="Consumer">Consumer</option>
                    <option value="Provider">Provider</option>
                    <option value="Donor">Donor</option>
                    <option value="Admin">Admin</option>
                </select>

                <button type="submit" name="signup">Sign up</button>

                <?php echo $signup_msg; ?>
            </form>
        </div>


        <div class="login">
            <form action="" method="POST">
                <label for="chk" aria-hidden="true">Login</label>

                <input type="email" name="email" placeholder="Email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required />
                <input type="password" name="pswd" placeholder="Password" required />
                <button type="submit" name="login">Login</button>

                <?php echo $login_msg; ?>
            </form>
        </div>
    </div>

</body>

</html>