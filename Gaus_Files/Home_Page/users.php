<?php
session_start();
include("../connection.php"); // Path from Admin/ to root

// --- ADMIN SECURITY CHECK ---
// If user is not logged in or is not an admin, redirect to homepage
if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../Home_Page/index.php"); // Adjust path as needed
    exit;
}

$user_type = $_SESSION["user_type"];

// Fetch all user data from the 'account' table
$sql = "SELECT user_id, username, email, type, balance FROM account ORDER BY username ASC";
$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Users - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Link to the list.css file from the Services folder -->
    <link rel="stylesheet" href="../Services/list.css">

    <!-- Inline styles for this page only (e.g., user type badges) -->
    <style>
        .user-type-badge {
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            text-transform: capitalize;
        }

        /* Color coding for user types */
        .type-admin {
            background-color: #fecaca;
            /* Red */
            color: #991b1b;
        }

        .type-provider {
            background-color: #dbeafe;
            /* Blue */
            color: #1d4ed8;
        }

        .type-consumer {
            background-color: #d1fae5;
            /* Green */
            color: #065f46;
        }

        .type-visitor {
            /* Just in case */
            background-color: #e5e7eb;
            /* Gray */
            color: #4b5563;
        }

        .item-body {
            /* Add a bit more spacing for readability */
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .item-meta {
            /* Make meta text slightly larger */
            font-size: 1rem;
            line-height: 1.5;
        }
    </style>
</head>

<body>

    <div class="list-container">
        <header class="list-header">
            <h1>All Users (Admin)</h1>
            <a href="../Home_Page/index.php" class="btn btn-back">Back to Home</a>
            <?php if ($user_type == 'admin'): ?>
                <a href="../Home_Page/admin.php" class="btn btn-back">Back to Dashboard</a>
            <?php endif ?>

        </header>

        <main class="vertical-list">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Get data for each user
                    $user_id = $row['user_id'];
                    $username = htmlspecialchars($row['username']);
                    $email = htmlspecialchars($row['email']);
                    $type = htmlspecialchars($row['type']);
                    $balance = htmlspecialchars($row['balance']);
                    ?>

                    <!-- User List Item -->
                    <div class="list-item">
                        <div class="item-header">
                            <h3><?php echo $username; ?></h3>
                            <span class="user-type-badge type-<?php echo strtolower($type); ?>">
                                <?php echo ucfirst($type); ?>
                            </span>
                        </div>
                        <div class="item-body">
                            <p class="item-meta">
                                <strong>User ID:</strong> <?php echo $user_id; ?>
                            </p>
                            <p class="item-meta">
                                <strong>Email:</strong> <?php echo $email; ?>
                            </p>
                            <p class="item-meta">
                                <strong>Balance:</strong> <?php echo $balance; ?> BDT
                            </p>
                        </div>
                        <!-- No actions needed per request -->
                    </div>

                    <?php
                } // End while loop
            } else {
                echo '<p class="no-entries">No users found in the database.</p>';
            }
            ?>
        </main>
    </div>

</body>

</html>