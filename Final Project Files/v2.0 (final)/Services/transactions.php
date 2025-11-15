<?php
// 1. REQUIRE LOGIN
session_start();

// Check if the user is logged in. If not, redirect to login page.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../Registration_Login/login.php");
    exit;
}

// 2. GET TRANSACTION DATA
include("../connection.php");
$username = $_SESSION['username'];
$user_type = $_SESSION['user_type'];

// --- NEW: Check for referrer flag ---
$referrer = $_GET['ref'] ?? null;

// --- MODIFIED: Added `timestamp` to the query ---
$sql_get_transactions = "SELECT user_id, amount, transaction_id, report, timestamp 
                         FROM transactions 
                         ORDER BY transaction_id DESC";
$result_transactions = mysqli_query($conn, $sql_get_transactions);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="10">
    <title>All Transactions - Support Hero</title>
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
            box-sizing: border-box;
            /* Added for better layout */
        }

        .form-container {
            margin: auto;
            max-width: 600px;
            /* Made wider for transaction details */
            width: 90%;
            padding: 2.5rem;
            background-color: #333;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            box-sizing: border-box;
            /* Added for better layout */
        }

        .form-container h2 {
            text-align: center;
            margin-top: 0;
            margin-bottom: 1.5rem;
            color: #ffffff;
            font-weight: 700;
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

        /* --- NEW: Header for navigation buttons --- */
        .nav-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            /* This was on btn-back, moved here */
        }

        .btn-back {
            /* display: inline-block; */
            /* No longer needed */
            /* margin-bottom: 1.5rem; */
            /* MOVED to .nav-header */
            padding: 0.6rem 1.2rem;
            background-color: #444;
            color: #f0f0f0;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: background-color 0.3s, color 0.3s;
        }

        .btn-back:hover {
            background-color: #555;
            color: #fff;
        }

        /* --- NEW: Style for the dashboard button --- */
        .btn-dashboard {
            padding: 0.6rem 1.2rem;
            background-color: #60a5fa;
            /* Blue to stand out */
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }

        .btn-dashboard:hover {
            background-color: #3b82f6;
        }


        /* --- New Transaction List Styles --- */
        .transaction-list-wrapper {
            background-color: #2a2a2a;
            /* Darker background for the list */
            border-radius: 8px;
            overflow: hidden;
            /* To clip the corners of the list */
            border: 1px solid #444;
            max-height: 60vh;
            /* Add a max-height for very long lists */
            overflow-y: auto;
            /* Make the list scrollable if it's too long */
        }

        .transaction-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .transaction-item {
            display: flex;
            flex-wrap: wrap;
            /* Allow wrapping */
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #444;
        }

        /* Remove border from the last item */
        .transaction-item:last-child {
            border-bottom: none;
        }

        .transaction-details {
            flex-grow: 1;
            /* Allow details to take up space, but also shrink */
            min-width: 200px;
            margin-right: 1rem;
            /* Add space between details and amount */
        }

        .transaction-details p {
            margin: 0.1rem 0;
            color: #ccc;
            font-size: 0.9rem;
        }

        .transaction-details p strong {
            color: #f0f0f0;
            min-width: 80px;
            /* Aligns the colons nicely */
            display: inline-block;
        }

        .transaction-details .report {
            font-size: 0.8rem;
            color: #999;
            font-style: italic;
            word-break: break-all;
            /* Ensure long reports wrap */
        }

        .transaction-amount {
            font-size: 1.25rem;
            font-weight: 700;
            flex-shrink: 0;
            /* Prevent amount from shrinking */
        }

        /* Style amount based on transaction type */
        .amount-deposit {
            color: #22c55e;
            /* Green */
        }

        .amount-withdrawal {
            color: #ef4444;
            /* Red */
        }

        .amount-other {
            color: #f0f0f0;
            /* White */
        }

        /* --- ADDED: Responsive Media Query --- */
        @media (max-width: 480px) {
            .form-container {
                width: 95%;
                padding: 1.5rem;
            }

            /* --- NEW: Responsive nav buttons --- */
            .nav-header {
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .btn-back,
            .btn-dashboard {
                text-align: center;
                flex-grow: 1;
                /* Allow buttons to grow */
            }

            .transaction-item {
                flex-direction: column;
                /* Stack items vertically */
                align-items: flex-start;
                /* Align to the left */
                gap: 0.5rem;
                /* Add space between details and amount */
            }

            .transaction-details {
                margin-right: 0;
                /* No right margin when stacked */
                width: 100%;
                /* Take full width */
            }

            .transaction-amount {
                font-size: 1.1rem;
                width: 100%;
                /* Take full width */
                text-align: left;
                /* Align to the left */
            }
        }
    </style>
</head>

<body>

    <div class="form-container">

        <div class="nav-header">
            <a href="../Home_Page/index.php" class="btn-back">
                &larr; Go to Homepage
            </a>

            <?php if ($referrer === 'admin' && $user_type === 'admin'): ?>
                <a href="../Home_Page/admin.php" class="btn-dashboard">
                    Back to Dashboard &rarr;
                </a>
            <?php endif; ?>
        </div>
        <h2>Transaction History</h2>
        <div class="transaction-list-wrapper">
            <ul class="transaction-list">

                <?php
                if ($result_transactions && mysqli_num_rows($result_transactions) > 0) {
                    while ($row = mysqli_fetch_assoc($result_transactions)) {

                        // Sanitize output
                        $user_id = htmlspecialchars($row['user_id']);
                        $amount = (float) $row['amount'];
                        $report = htmlspecialchars($row['report']);
                        $transaction_id = htmlspecialchars($row['transaction_id']); // Get ID
                
                        // --- ADDED: Fetch and format the timestamp ---
                        $timestamp = htmlspecialchars(date("d M, Y, g:i a", strtotime($row['timestamp'])));

                        // Determine amount color and prefix based on value
                        if ($amount > 0) {
                            $amount_class = 'amount-deposit';
                            $amount_prefix = '+';
                        } else if ($amount < 0) {
                            $amount_class = 'amount-withdrawal';
                            $amount_prefix = ''; // Negative sign is already there
                        } else {
                            $amount_class = 'amount-other';
                            $amount_prefix = '';
                        }

                        // Display the item
                        echo '<li class="transaction-item">';
                        echo '  <div class="transaction-details">';
                        echo "      <p><strong>User ID:</strong> $user_id</p>";
                        echo "      <p><strong>TXN ID:</strong> $transaction_id</p>";
                        // --- ADDED: Display the timestamp ---
                        echo "      <p><strong>Date:</strong> $timestamp</p>";
                        echo "      <p class='report'><strong>Report:</strong> $report</p>";
                        echo '  </div>';

                        echo "  <div class='transaction-amount $amount_class'>";
                        echo "      " . $amount_prefix . number_format($amount, 2) . " BDT";
                        echo '  </div>';

                        echo '</li>';
                    }
                } else {
                    // Show a message if no transactions are found
                    echo '<li class="transaction-item" style="justify-content: center; color: #999;">No transactions found.</li>';
                }
                ?>

            </ul>
        </div>

    </div>
</body>

</html>