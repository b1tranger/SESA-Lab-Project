<?php
session_start();

// Check if the user is logged in. If not, redirect to login page.
// Make sure the path to login.php is correct from this file's location.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../Registration_Login/login.php");
    exit;
}

// If the script continues, the user is logged in.
// You can now safely use their session data.
$username = $_SESSION['username'];
$email = $_SESSION['email'];
$user_type = $_SESSION['user_type'];
$user_id = $_SESSION['user_id'];

include("../connection.php");

$flag = 0; // 0=idle, 1=error, 2=success

if (isset($_POST['submit'])) {
    $service_name = trim($_POST['service_name']);
    $service_type = $_POST['service_type'];
    // Use session username/email if available, otherwise use the form's input
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : trim($_POST['input_name']);
    $email = isset($_SESSION['email']) ? $_SESSION['email'] : trim($_POST['input_email']);
    $deadline = trim($_POST['deadline']);
    $details = $_POST['details'];

    $compensation = trim($_POST['compensation']);

    if (empty($service_name) || empty($service_type) || empty($username) || empty($email) || empty($deadline) || empty($details)) {
        $flag = 1; // Error
    } else {
        // passing to the Database
        $sql = "insert into service(service_name,service_type, username, email, deadline, details,compensation,status,worker_limit) values('$service_name','$service_type','$username','$email','$deadline','$details', '$compensation','incomplete','$worker_limit')";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $flag = 2; // Success
            $sqlUpdateBalance = "UPDATE account SET balance = balance - $compensation WHERE username = '$username' AND type = '$user_type'";
            $UpdateBalance = mysqli_query($conn, $sqlUpdateBalance);
            $sqlTransaction = "Insert into transactions(user_id, amount, report) values('$user_id','$compensation','Deducted from Balance')";
            $transactionSQL = mysqli_query($conn, $sqlTransaction);
        } else {
            $flag = 1; // Error
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Service - Support Hero</title>
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
            padding: 2rem 0;
        }

        .form-container {
            margin: auto;
            max-width: 500px;
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
        <?php if ($flag == 0) { // Show form if idle ?>
            <form method="POST" class="form">
                <h2 style="text-align: center;">Request a Service</h2>

                <div class="form-group">
                    <label for="service_name">Service Name:</label>
                    <input id="service_name" name="service_name" type="text" placeholder="e.g., 'Website Design'" required>
                </div>

                <div class="form-group">
                    <label for="service_type">Service Type:</label>
                    <select id="service_type" name="service_type" required>
                        <option value="">--Select Type--</option>
                        <option value="request">I am Requesting this</option>
                        <option value="offer">I am Offering this</option>
                    </select>
                </div>

                <?php if (!isset($_SESSION['loggedin'])) { ?>
                    <div class="form-group">
                        <label for="input_name">Your Username:</label>
                        <input id="input_name" name="input_name" type="text" required>
                    </div>
                    <div class="form-group">
                        <label for="input_email">Your Email:</label>
                        <input id="input_email" name="input_email" type="email" required>
                    </div>
                <?php } ?>

                <div class="form-group">
                    <label for="deadline">Deadline/Uptime:</label>
                    <input id="deadline" name="deadline" type="date" required>
                </div>

                <div class="form-group">
                    <label for="details">Details:</label>
                    <textarea id="details" name="details" rows="5" placeholder="Enter service details here..."
                        required></textarea>
                </div>

                <div class="form-group">
                    <label for="compensation">Compensation/Bounty (Will be deducted from balance):</label>
                    <input id="compensation" name="compensation" type="number" placeholder="e.g., 5000">
                </div>

                <div class="form-group">
                    <input type="submit" name="submit" value="Submit Request/Offer">
                </div>
            </form>

        <?php } else { // Show success or error message ?>

            <div class="form-message">
                <?php if ($flag == 2) { ?>
                    <h3>Entry Successful!</h3>
                    <p>Your service has been posted.</p>
                <?php } else if ($flag == 1) { ?>
                        <h3>Error</h3>
                        <p>Please fill out all fields correctly.</p>
                <?php } ?>
                <p><a href="../Home_Page/index.php#services">Go back to Services</a></p>
                <p><a href="request_offer.php">Post another service</a></p>
            </div>

        <?php } ?>

    </div>
</body>

</html>