<?php

include("../connection.php");

session_start();

// Check if the user is logged in and store the state in a variable
$is_logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

if (!$is_logged_in) {
    // If not logged in, redirect to login
    header("location: ../Registration_Login/login.php");
    exit;
}

// Grab user info
$username = $_SESSION['username'];
$user_type = $_SESSION['user_type'];
$user_email = $_SESSION['email'];
$user_id = $_SESSION['user_id'];


// --- UPDATED & FIXED DATA FETCHING ---

$Balance = 0;
$TotalOffers = 0;
$TotalRequests = 0;
$service_records = []; // Array to hold the list of services

// 1. Get Balance (using prepared statement)
$sql_balance = "SELECT balance FROM account WHERE user_id = ?";
$stmt_balance = $conn->prepare($sql_balance);
$stmt_balance->bind_param("i", $user_id);
$stmt_balance->execute();
$result_balance = $stmt_balance->get_result();

if ($result_balance && $result_balance->num_rows == 1) {
    $row_balance = $result_balance->fetch_assoc();
    $Balance = $row_balance['balance'];
}

// 2. Get Counts for Metric Boxes (Offers or Requests)
if ($user_type == 'provider') {
    $sql_count = "SELECT COUNT(service_id) AS total_service FROM service WHERE user_id = ? AND service_type='offer'";
    $stmt_count = $conn->prepare($sql_count);
    $stmt_count->bind_param("i", $user_id);
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    if ($result_count) {
        $row_count = $result_count->fetch_assoc();
        $TotalOffers = $row_count['total_service'];
    }
} elseif ($user_type == 'consumer') {
    $sql_count = "SELECT COUNT(service_id) AS total_service FROM service WHERE user_id = ? AND service_type='request'";
    $stmt_count = $conn->prepare($sql_count);
    $stmt_count->bind_param("i", $user_id);
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    if ($result_count) {
        $row_count = $result_count->fetch_assoc();
        $TotalRequests = $row_count['total_service'];
    }
}
// Admins will see counts if they match 'provider' or 'consumer' logic based on their posts, or we can add a specific admin query.
// For now, this logic is fine.

// 3. Get Full Service Records for the new list
$sql_records = "";

if ($user_type == 'provider') {
    $sql_records = "SELECT service_name, status, compensation, deadline FROM service WHERE user_id = ? AND service_type = 'offer' ORDER BY deadline DESC";
} elseif ($user_type == 'consumer') {
    $sql_records = "SELECT service_name, status, compensation, deadline FROM service WHERE user_id = ? AND service_type = 'request' ORDER BY deadline DESC";
} elseif ($user_type == 'admin') {
    // Admins see all services they posted
    $sql_records = "SELECT service_name, status, compensation, deadline, service_type FROM service WHERE user_id = ? ORDER BY deadline DESC";
}

if (!empty($sql_records)) {
    $stmt_records = $conn->prepare($sql_records);
    $stmt_records->bind_param("i", $user_id);
    $stmt_records->execute();
    $result_records = $stmt_records->get_result();
    if ($result_records) {
        while ($row = $result_records->fetch_assoc()) {
            $service_records[] = $row; // Add each row to the array
        }
    }
}
// --- END OF UPDATED DATA FETCHING ---

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="profile.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body>

    <div class="dashboard-layout">

        <nav class="sidebar">
            <div class="sidebar-header">
                <h2>User Menu</h2>
            </div>
            <ul>
                <li><a href="../Home_Page/index.php">Go to Home Page</a></li>

                <?php if ($user_type == 'admin'): ?>
                    <li><a href="admin.php">Dashboard</a></li>
                    <li><a href="../Services/service.php">All Services</a></li>
                    <li><a href="../Services/tasks.php">Admin Tasks</a></li>
                    <li><a href="../Services/feedback_admin.php">Feedbacks</a></li>
                    <li><a href="../Services/comments.php">Comments</a></li>

                <?php else: ?>
                    <li><a href="../Services/service.php">All Services</a></li>
                    <li><a href="../Services/feedback_user.php">Leave Feedback</a></li>
                <?php endif ?>
                <li><a href="profile.php"><?php echo ucfirst($user_type); ?> Profile</a></li>

                <li><a href="../Registration_Login/logout.php">Logout</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <div class="dashboard-container">
                <h1>User Profile</h1>

                <div class="dashboard-content">
                    <div class="info-card">
                        <h2><?php echo ucfirst($user_type); ?> Info:</h2>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($username); ?></p>
                        <p><strong>ID:</strong> <?php echo htmlspecialchars($user_id); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user_email); ?></p>
                    </div>

                    <div class="overview-card">
                        <h2>My Activity:</h2>
                        <p class="overview-note">A summary of your services and tasks.</p>

                        <div class="metrics-grid">
                            <a href="add_balance.php" class="metric-item">
                                <p><?php echo number_format($Balance, 2); ?><br>Balance (BDT)</p>
                            </a>
                            <?php if ($user_type == 'provider'): ?>
                                <a href="#service-records" class="metric-item">
                                    <p><?php echo $TotalOffers; ?><br> Offers Made</p>
                                </a>
                            <?php elseif ($user_type == 'consumer'): ?>
                                <a href="#service-records" class="metric-item">
                                    <p><?php echo $TotalRequests; ?><br>Requests Made</p>
                                </a>
                            <?php endif ?>
                        </div>
                    </div>

                    <div class="records-card" id="service-records">
                        <h2>My Service Postings</h2>

                        <?php if (!empty($service_records)): ?>
                            <div class="service-record-list">
                                <?php foreach ($service_records as $record): ?>
                                    <div class="service-record-item">
                                        <div class="record-info">
                                            <span
                                                class="record-name"><?php echo htmlspecialchars($record['service_name']); ?></span>
                                            <span class="record-meta">
                                                BDT <?php echo htmlspecialchars($record['compensation']); ?> |
                                                Due:
                                                <?php echo htmlspecialchars(date("d M, Y", strtotime($record['deadline']))); ?>
                                                <?php if ($user_type == 'admin' && isset($record['service_type'])): // Show type for admin ?>
                                                    | Type: <?php echo htmlspecialchars(ucfirst($record['service_type'])); ?>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                        <div class="record-status">
                                            <span
                                                class="status-badge status-<?php echo str_replace('_', '-', htmlspecialchars($record['status'])); ?>">
                                                <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $record['status']))); ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="overview-note">You have not posted any services yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>