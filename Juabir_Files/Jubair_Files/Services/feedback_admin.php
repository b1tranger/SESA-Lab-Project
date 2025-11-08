<?php
session_start();
include("../connection.php"); // Make sure this path is correct

// Check if user is logged in (optional, but good practice)
$is_logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$user_type = $_SESSION['user_type'];

// Fetch all feedback, newest first
$sql = "SELECT * FROM feedback ORDER BY date_submitted DESC";
$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Feedback</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="list.css">
</head>

<body>

    <div class="list-container">
        <header class="list-header">
            <h1>All Feedback</h1>
            <a href="../Home_Page/index.php" class="btn btn-back">Back to Home</a>
            <?php if ($user_type == 'admin'): ?>
                <a href="../Home_Page/admin.php" class="btn btn-back">Back to Dashboard</a>
            <?php endif ?>

        </header>

        <main class="vertical-list">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Get data for each feedback entry
                    $feedback_user = htmlspecialchars($row['username']);
                    $subject = htmlspecialchars($row['subject']);
                    $message = htmlspecialchars($row['message']);
                    $date = htmlspecialchars(date("d M, Y", strtotime($row['date_submitted'])));
                    ?>

                    <div class="list-item">
                        <div class="item-header">
                            <h3><?php echo $subject; ?></h3>
                        </div>
                        <div class="item-body">
                            <p class="item-meta">
                                <strong>From:</strong> <?php echo $feedback_user; ?> |
                                <strong>Date:</strong> <?php echo $date; ?>
                            </p>
                            <p class="item-content"><?php echo $message; ?></p>
                        </div>
                    </div>

                    <?php
                } // End while loop
            } else {
                echo '<p class="no-entries">No feedback has been submitted.</p>';
            }
            ?>
        </main>
    </div>

</body>

</html>