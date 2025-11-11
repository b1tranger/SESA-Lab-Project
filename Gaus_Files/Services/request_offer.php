<?php

include("../connection.php");
session_start();

// 1. REQUIRE LOGIN
// Check if the user is logged in. If not, redirect to login page.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../Registration_Login/login.php");
    exit;
}

// 2. GET SESSION DATA
$username = $_SESSION['username'];
$email = $_SESSION['email'];
$user_type = $_SESSION['user_type']; // 'consumer' or 'provider'
$user_id = $_SESSION['user_id'];



$flag = 0; // 0=idle, 1=error, 2=success
$error_message = ''; // Variable to hold specific error messages

// 3. SET SERVICE TYPE BASED ON USER ROLE
$service_type_value = '';
$service_type_display = '';
$compensation_label = 'Compensation (BDT):';

if ($user_type == 'consumer') {
    $service_type_value = 'request';
    $service_type_display = 'I am Requesting this';
    $compensation_label = 'Compensation/Bounty (Will be deducted from your balance):';
} else if ($user_type == 'provider') {
    $service_type_value = 'offer';
    $service_type_display = 'I am Offering this';
    $compensation_label = 'Service Price (What you will be paid):';
} else if ($user_type == 'admin') {
    // Admin case (defaulting to 'request', but you can change this)
    $service_type_value = 'request';
    $service_type_display = 'Posting as Admin (Request)';
    $compensation_label = 'Compensation/Bounty (Admin Post):';
}


// 4. HANDLE FORM SUBMISSION
if (isset($_POST['submit'])) {

    // Get all form data
    $service_name = trim($_POST['service_name']);
    $service_type = $_POST['service_type']; // from hidden input
    $service_category = trim($_POST['service_category']); // --- NEW ---
    $deadline = trim($_POST['deadline']);
    $details = $_POST['details'];
    $compensation = (int) trim($_POST['compensation']);
    $worker_limit = (int) trim($_POST['worker_limit']);
    $status_default = 'pending'; // As requested in your SQL

    // Validate inputs
    // --- MODIFIED --- Added check for service_category
    if (empty($service_name) || empty($service_type) || empty($deadline) || empty($details) || $compensation < 0 || $worker_limit <= 0 || empty($service_category)) {
        $flag = 1; // Error
        $error_message = "Please fill out all fields correctly. Ensure compensation and worker limit are valid numbers.";
    } else {

        // --- BALANCE CHECK LOGIC ---
        $balance_sufficient = true; // Assume true unless check fails

        // Only check balance for consumers posting a request with a cost
        if ($user_type == 'consumer' && $service_type == 'request' && $compensation > 0) {

            // Query for the user's current balance
            $sql_balance = "SELECT balance FROM account WHERE user_id = ?";
            $stmt_check = $conn->prepare($sql_balance);
            $stmt_check->bind_param("i", $user_id);
            $stmt_check->execute();
            $result = $stmt_check->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $current_balance = $row['balance'];

                if ($current_balance < $compensation) {
                    $balance_sufficient = false;
                    $flag = 1;
                    $error_message = "Insufficient funds. Your current balance is " . $current_balance . " BDT, but this request requires " . $compensation . " BDT.";
                }
            } else {
                $balance_sufficient = false;
                $flag = 1;
                $error_message = "Error: Could not retrieve your account balance.";
            }
            $stmt_check->close();
        }
        // --- END OF BALANCE CHECK ---


        // Proceed only if all checks passed
        if ($balance_sufficient && $flag == 0) {

            // 5. USE PREPARED STATEMENT (Prevents SQL Injection)
            // --- MODIFIED --- Added 'service_category' to the query
            $sql = "INSERT INTO service (user_id, service_name, service_type, service_category, username, email, deadline, details, compensation, status, worker_limit) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            // --- MODIFIED --- Added 's' for service_category (issssssisi -> isssssssisi)
            $stmt->bind_param(
                "isssssssisi",
                $user_id,
                $service_name,
                $service_type,
                $service_category, // --- NEW ---
                $username,
                $email,
                $deadline,
                $details,
                $compensation,
                $status_default,
                $worker_limit
            );

            // 6. EXECUTE AND UPDATE BALANCE (IF NEEDED)
            if ($stmt->execute()) {
                $flag = 2; // Success

                // Only deduct balance if the user is a 'consumer' posting a 'request'
                if ($service_type == 'request' && $user_type == 'consumer' && $compensation > 0) {

                    // Deduct from balance
                    $sqlUpdateBalance = "UPDATE account SET balance = balance - ? WHERE user_id = ?";
                    $stmt_balance = $conn->prepare($sqlUpdateBalance);
                    $stmt_balance->bind_param("ii", $compensation, $user_id);
                    $stmt_balance->execute();
                    $stmt_balance->close();

                    // Create transaction record
                    $sqlTransaction = "INSERT INTO transactions(user_id, amount, report) VALUES (?, ?, ?)";
                    $stmt_trans = $conn->prepare($sqlTransaction);
                    $report = "Funded service: " . substr($service_name, 0, 50);
                    $neg_compensation = -$compensation; // Store as negative for a withdrawal

                    $stmt_trans->bind_param("ids", $user_id, $neg_compensation, $report);
                    $stmt_trans->execute();
                    $stmt_trans->close();
                }

            } else {
                $flag = 1; // Error
                $error_message = "A database error occurred. Please try again.";
            }
            $stmt->close();
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
    <link rel="stylesheet" href="../style.css">

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

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.3);
        }

        /* --- NEW --- Style for placeholder option */
        select option[disabled] {
            color: #888;
        }

        /* Style for readonly inputs */
        input[readonly] {
            background-color: #555;
            cursor: not-allowed;
            color: #ccc;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .input-hint {
            font-size: 0.8rem;
            color: #999;
            margin-top: 0.25rem;
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
        }

        a:hover {
            text-decoration: underline;
        }

        .form-message {
            text-align: center;
            padding-top: 1.5rem;
            margin-top: 1.5rem;
            border-top: 1px solid #444;
        }
    </style>
</head>

<body>
    <div class="form-container">

        <a href="../Home_Page/index.php" class="btn btn-back"
            style="background-color: #444; color: #f0f0f0; padding: 0.6rem 1.2rem; text-decoration: none; border-radius: 6px; margin-bottom: 2rem; display: inline-block;">
            &larr; Go to Homepage
        </a>

        <?php if ($flag == 0) { // Show form if idle ?>
            <form method="POST" class="form">

                <h2><?php echo ($service_type_value == 'request') ? 'Request a Service' : 'Offer a Service'; ?></h2>

                <div class="form-group">
                    <label for="service_name">Service Name:</label>
                    <input id="service_name" name="service_name" type="text" placeholder="e.g., 'Website Design'" required>
                </div>

                <div class="form-group">
                    <label for="service_type_display">Service Type:</label>
                    <input type="hidden" name="service_type" value="<?php echo $service_type_value; ?>">
                    <input id="service_type_display" name="service_type_display" type="text"
                        value="<?php echo $service_type_display; ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="deadline">Deadline/Uptime:</label>
                    <input id="deadline" name="deadline" type="date" required>
                </div>

                <div class="form-group">
                    <label for="service_category">Service Category:</label>
                    <select id="service_category" name="service_category" required>
                        <option value="" disabled selected>Select a category</option>
                        <option value="cleaning">Cleaning</option>
                        <option value="waste management">Waste Management</option>
                        <option value="transport & delivery">Transport & Delivery</option>
                        <option value="security">Security</option>
                        <option value="others">Others</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="details">Details:</label>
                    <textarea id="details" name="details" rows="5" placeholder="Enter service details here..."
                        required></textarea>
                </div>

                <div class="form-group">
                    <label for="compensation"><?php echo $compensation_label; ?></label>
                    <input id="compensation" name="compensation" type="number" placeholder="e.g., 5000" min="0" required>
                </div>

                <div class="form-group">
                    <label for="worker_limit">Worker Limit:</label>
                    <input id="worker_limit" name="worker_limit" type="number" value="1" min="1" required>
                    <p class="input-hint">Set to 1 for a single-person job. More than 1 allows multiple accepts.</p>
                </div>

                <div class="form-group">
                    <input type="submit" name="submit" value="Submit <?php echo ucfirst($service_type_value); ?>">
                </div>
            </form>

        <?php } else { // Show success or error message ?>

            <div class="form-message">
                <?php if ($flag == 2) { ?>
                    <h3>Entry Successful!</h3>
                    <p>Your service has been posted.</p>
                    <br>
                    <p><a href="request_offer.php">Post another service</a></p>
                <?php } else if ($flag == 1) { ?>
                        <h3>Error</h3>
                        <p><?php echo $error_message; ?></p>
                        <br>
                    <?php if (strpos($error_message, 'Insufficient funds') !== false) { ?>
                            <p><a href="../Services/add_balance.php">Add Balance</a></p><br>
                    <?php } ?>
                        <p><a href="request_offer.php">Try again</a></p>
                <?php } ?>

            </div>

        <?php } ?>

    </div>
</body>

</html>