<?php
// 1. REQUIRE LOGIN
session_start();

// Check if the user is logged in. If not, redirect to login page.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../Registration_Login/login.php");
    exit;
}

// 2. GET USER DATA AND CURRENT BALANCE
include("../connection.php");
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$user_type = $_SESSION['user_type']; // Not strictly needed here, but good to have
$current_balance = 0.00;
$form_error = '';
$flag = 0; // 0=idle, 1=error, 2=success

// Fetch current balance using prepared statement
$sql_get_balance = "SELECT balance FROM account WHERE user_id = ?";
$stmt_get = $conn->prepare($sql_get_balance);
$stmt_get->bind_param("i", $user_id);
$stmt_get->execute();
$result_get = $stmt_get->get_result();

if ($result_get && $result_get->num_rows == 1) {
    $row = $result_get->fetch_assoc();
    $current_balance = (float) $row['balance'];
}
$stmt_get->close();

// 3. HANDLE FORM SUBMISSION
if (isset($_POST['submit_donation'])) {
    $donation_amount = (float) $_POST['donation_amount'];

    // --- VALIDATION ---
    if ($donation_amount <= 0) {
        $flag = 1; // Error
        $form_error = "Please enter a positive amount to donate.";
    } else if ($donation_amount > $current_balance) {
        $flag = 1; // Error
        $form_error = "Insufficient funds. You only have BDT " . number_format($current_balance, 2) . ".";
    } else {

        // --- PROCESS DONATION (using prepared statements) ---
        $conn->begin_transaction(); // Start transaction for safety

        try {
            // 1. Deduct from user's balance
            $sql_update = "UPDATE account SET balance = balance - ? WHERE user_id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("di", $donation_amount, $user_id);
            $stmt_update->execute();

            if ($stmt_update->affected_rows == 0) {
                throw new Exception("Could not update balance.");
            }

            // 2. Create transaction record
            $sql_transaction = "INSERT INTO transactions(user_id, amount, report) VALUES (?, ?, ?)";
            $stmt_trans = $conn->prepare($sql_transaction);

            $neg_amount = -$donation_amount; // Store as a negative value (withdrawal)
            $report = "Donation to Support Hero";

            $stmt_trans->bind_param("ids", $user_id, $neg_amount, $report);
            $stmt_trans->execute();

            if ($stmt_trans->affected_rows == 0) {
                throw new Exception("Could not create transaction record.");
            }

            // If both queries succeed, commit the changes
            $conn->commit();
            $flag = 2; // Success
            $current_balance = $current_balance - $donation_amount; // Update display

        } catch (Exception $e) {
            // If anything fails, roll back
            $conn->rollback();
            $flag = 1;
            $form_error = "An error occurred. Please try again. " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donate - Support Hero</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../style.css">

    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
            /* Light grey background */
            color: #333;
            /* Dark text */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }

        .form-container {
            margin: auto;
            max-width: 450px;
            width: 90%;
            padding: 2.5rem;
            background-color: #ffffff;
            /* White card */
            border-radius: 12px;
            border: 1px solid #dee2e6;
            /* Light border */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            /* Softer shadow */
        }

        .form-container h2 {
            text-align: center;
            margin-top: 0;
            margin-bottom: 0.5rem;
            color: #212529;
            /* Dark heading */
            font-weight: 700;
        }

        .form-container .subtitle {
            text-align: center;
            font-size: 1rem;
            color: #6c757d;
            /* Medium grey text */
            margin-top: 0;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #495057;
            /* Dark label text */
        }

        input[type="number"] {
            width: 100%;
            padding: 0.8rem;
            background-color: #ffffff;
            /* White input background */
            border: 1px solid #ced4da;
            /* Standard border */
            border-radius: 6px;
            color: #495057;
            /* Dark input text */
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            box-sizing: border-box;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input[type="number"]:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.3);
        }

        input[type="submit"] {
            width: 100%;
            padding: 0.8rem;
            background-color: #2563eb;
            /* Blue for donation */
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
            color: #2563eb;
            /* Blue link */
            text-decoration: none;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #1d4ed8;
            /* Darker blue hover */
            text-decoration: underline;
        }

        .form-message {
            text-align: center;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.25rem;
        }

        .form-message.success {
            background-color: #d1e7dd;
            /* Light green */
            color: #0f5132;
            /* Dark green text */
            border: 1px solid #badbcc;
        }

        .form-message h3 {
            margin: 0;
        }

        .form-error {
            background-color: #f8d7da;
            /* Light red */
            color: #721c24;
            /* Dark red text */
            padding: 1rem;
            border: 1px solid #f5c6cb;
            border-radius: 6px;
            text-align: center;
            margin-bottom: 1.25rem;
        }


        .balance-display {
            text-align: center;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background-color: #f8f9fa;
            /* Very light grey */
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .balance-display .label {
            font-size: 1rem;
            color: #6c757d;
            /* Medium grey text */
            margin: 0;
        }

        .balance-display .amount {
            font-size: 2.25rem;
            font-weight: 700;
            color: #22c55e;
            /* Green */
            margin: 0.25rem 0 0 0;
        }
    </style>
</head>

<body>

    <div class="form-container">

        <a href="../Home_Page/index.php" class="btn btn-back"
            style="background-color: #e9ecef; color: #343a40; padding: 0.6rem 1.2rem; text-decoration: none; border-radius: 6px; margin-bottom: 2rem; display: inline-block; border: 1px solid #ced4da;">
            &larr; Go to Homepage
        </a>

        <form method="POST" class="form">
            <h2>Donate to Support Hero</h2>
            <p class="subtitle">Your support helps keep our community running!</p>

            <div class="balance-display">
                <p class="label">Your Current Balance:</p>
                <p class="amount">BDT <?php echo number_format($current_balance, 2); ?></p>
            </div>

            <?php if ($flag == 2) { // Success ?>
                <div class="form-message success">
                    <h3>Thank you for your donation!</h3>
                </div>
            <?php } else if ($flag == 1) { // Error ?>
                    <div class="form-error">
                    <?php echo htmlspecialchars($form_error);
                    echo '<br><br><a href="add_balance.php">Add to Balance</a>'; ?>

                    </div>
            <?php } ?>

            <div class="form-group">
                <label for="donation_amount">Donation Amount (BDT):</label>
                <input id="donation_amount" name="donation_amount" type="number" step="0.01" placeholder="e.g., 500.00"
                    required>
            </div>

            <div class="form-group">
                <input type="submit" name="submit_donation" value="Donate Now">
            </div>
        </form>

    </div>
</body>

</html>