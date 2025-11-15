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
$user_type = $_SESSION['user_type'];
$current_balance = 0.00;
$form_error = '';
$flag = 0; // 0=idle, 1=error, 2=success

// --- MODIFIED: Fetch current balance using prepared statement ---
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
// --- END OF MODIFICATION ---


// 3. HANDLE FORM SUBMISSION
if (isset($_POST['submit_donation'])) {
    $donation_amount = (float) $_POST['donation_amount'];

    if ($donation_amount <= 0) {
        $flag = 1; // Error
        $form_error = "Please enter a positive amount to add (e.g., 50.00).";
    } else {

        // --- MODIFIED: Use prepared statements and transaction ---
        $conn->begin_transaction();
        try {
            // 1. Update account balance
            $sql_update = "UPDATE account SET balance = balance + ? WHERE user_id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("di", $donation_amount, $user_id);
            $stmt_update->execute();

            if ($stmt_update->affected_rows == 0) {
                throw new Exception("Could not update balance.");
            }

            // 2. Create transaction record
            // We don't need to specify `timestamp` as the DB handles it by default
            $sqlTransaction = "INSERT INTO transactions(user_id, amount, report) VALUES (?, ?, ?)";
            $stmt_trans = $conn->prepare($sqlTransaction);
            $report = 'Added to Balance';
            $stmt_trans->bind_param("ids", $user_id, $donation_amount, $report);
            $stmt_trans->execute();

            if ($stmt_trans->affected_rows == 0) {
                throw new Exception("Could not create transaction record.");
            }

            // If both succeed, commit
            $conn->commit();
            $flag = 2; // Success
            $current_balance = $current_balance + $donation_amount; // Update display

        } catch (Exception $e) {
            // If anything fails, roll back
            $conn->rollback();
            $flag = 1;
            $form_error = "An error occurred. Please try again. " . $e->getMessage();
        }
        // --- END OF MODIFICATION ---
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add to Balance - Support Hero</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../style.css">
    <!-- STYLES (Same as login.php) -->
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
            max-width: 450px;
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

        .input-hint {
            font-size: 0.875rem;
            color: #999;
        }

        input[type="submit"] {
            width: 100%;
            padding: 0.8rem;
            background-color: #16a34a;
            /* Green for donation */
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #15803d;
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

        .form-error {
            background-color: #5a2a2a;
            color: #ffc0c0;
            padding: 1rem;
            border-radius: 6px;
            text-align: center;
            margin-bottom: 1.25rem;
        }


        /* === Current Balance Display === */
        .balance-display {
            text-align: center;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background-color: #2a2a2a;
            border-radius: 8px;
        }

        .balance-display .label {
            font-size: 1rem;
            color: #ccc;
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

    <!-- Go Back Button -->


    <div class="form-container">
        <!-- <div class="back-link-container">
            <a href="../Home_Page/index.php" class="btn-back">
                &larr; Go to Homepage
            </a>
            <br><br><br><br>
        </div> -->
        <a href="../Home_Page/index.php" class="btn btn-back"
            style="background-color: #444; color: #f0f0f0; padding: 0.6rem 1.2rem; text-decoration: none; border-radius: 6px; margin-bottom: 2rem; display: inline-block;">
            &larr; Go to Homepage
        </a>
        <form method="POST" class="form">
            <h2>Add to Balance</h2>

            <div class="balance-display">
                <p class="label">Your Current Balance:</p>
                <p class="amount">BDT <?php echo number_format($current_balance, 2); ?></p>
            </div>

            <?php if ($flag == 2) { // Success ?>
                <div class="form-message" style="border-top: none; margin-top: 0; padding-top: 0;">
                    <h3 style="color: #22c55e;">Success!</h3>
                    <p>Your balance has been updated.</p>
                </div>
            <?php } else if ($flag == 1) { // Error ?>
                    <div class="form-error">
                    <?php echo $form_error; ?>
                    </div>
            <?php } ?>

            <div class="form-group">
                <label for="donation_amount">Amount to Add (BDT):</label>
                <!-- Using step="0.01" allows decimals -->
                <input id="donation_amount" name="donation_amount" type="number" step="0.01" placeholder="e.g., 1000.50"
                    required>
            </div>

            <div class="form-group">
                <input type="submit" name="submit_donation" value="Add to Balance">
            </div>
        </form>

        <div
            style="text-align: center; font-size: 0.9rem; color: #ccc; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #444;">
            <p><a href="../Home_Page/index.php#donation">Learn about donations</a></p>
        </div>

    </div>
</body>

</html>