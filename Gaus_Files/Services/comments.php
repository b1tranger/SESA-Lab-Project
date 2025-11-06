<?php
session_start();
include("../connection.php"); // Make sure this path is correct

// Check if user is logged in (optional, but good practice)
$is_logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

if ($is_logged_in) {
    // Grab user info if they are logged in
    $username = $_SESSION['username'];
    $user_type = $_SESSION['user_type'];
} else {
    $user_type = 'visitor';
    $username = 'visitor';
}

// Fetch all comments, newest first
$sql = "SELECT * FROM comments ORDER BY date_posted DESC";
$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Comments</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="list.css">
</head>

<body>

    <div class="list-container">
        <header class="list-header">
            <h1>All Comments</h1>
            <a href="../Home_Page/index.php" class="btn btn-back">Back to Home</a>
            <?php if ($user_type == 'admin'): ?>
                <a href="../Home_Page/admin.php" class="btn btn-back">Back to Dashboard</a>
            <?php endif ?>

        </header>

        <main class="vertical-list">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Get data for each comment entry
                    // Adjust column names as needed (e.g., 'comment_text', 'date_posted')
                    $comment_user = htmlspecialchars($row['username']);
                    $subject = htmlspecialchars($row['subject']); // Or 'context', etc.
                    $comment_text = htmlspecialchars($row['comment_text']);
                    $date = htmlspecialchars(date("d M, Y", strtotime($row['date_posted'])));
                    ?>

                    <div class="list-item">
                        <div class="item-header">
                            <h3><?php echo $subject; ?></h3>
                        </div>
                        <div class="item-body">
                            <p class="item-meta">
                                <strong>By:</strong> <?php echo $comment_user; ?> |
                                <strong>Date:</strong> <?php echo $date; ?>
                            </p>
                            <p class="item-content"><?php echo $comment_text; ?></p>
                        </div>
                    </div>

                    <?php
                } // End while loop
            } else {
                echo '<p class="no-entries">No comments have been posted.</p>';
            }
            ?>
        </main>
    </div>

</body>

</html>