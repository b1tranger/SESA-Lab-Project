<?php
// 1. START SESSION at the very top
session_start();

include("../connection.php");

// 2. CHECK IF USER IS LOGGED IN
// If not logged in, redirect them to the login page
$is_logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
if (!$is_logged_in) {
    header("Location: login.php");
    exit;
}

// Get user info from session
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

// Initialize variables for form state
$submit_success = false;
$submit_error = "";

// 3. HANDLE FORM SUBMISSION
if (isset($_POST['submit_feedback'])) {
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    // Validate input
    if (empty($subject) || empty($message)) {
        $submit_error = "Please fill out both the subject and message fields.";
    } else {
        // Use prepared statements to prevent SQL injection
        $sql = "INSERT INTO feedback (user_id, username, subject, message, date_submitted) VALUES (?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        
        // Bind parameters: i = integer, s = string
        $stmt->bind_param("isss", $user_id, $username, $subject, $message);

        if ($stmt->execute()) {
            $submit_success = true;
        } else {
            $submit_error = "Error: Could not submit feedback. Please try again later.";
            // For debugging, you might log: $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Feedback - Support Hero</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- This style is copied directly from your login.php for a consistent look -->
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

        /* Style for readonly inputs */
        input:read-only {
            background-color: #555;
            cursor: not-allowed;
            color: #aaa;
        }

        textarea {
            resize: vertical;
            min-height: 120px; /* Made it a bit taller for feedback */
        }

        .input-hint {
            font-size: 0.875rem;
            color: #999;
            margin-left: 0.5rem;
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

        /* Added this for the back link, as it's not in the inline style */
        .back-link-container {
            margin-bottom: 2rem;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="form-container">
        
        <?php if ($submit_success) { ?>
            <!-- SHOW SUCCESS MESSAGE -->
            <div class="form-message">
                <h3>Feedback Submitted!</h3>
                <p>Thank you, <?php echo htmlspecialchars($username); ?>. We appreciate your input.</p>
                <p><a href="../Home_Page/index.php">Return to Homepage</a></p>
            </div>
        <?php } else { ?>
            <!-- SHOW FEEDBACK FORM -->
            <form method="POST" action="feedback_user.php" class="form">
                <h2>Submit Feedback</h2>
                
                <div style="text-align: center; margin-bottom: 2rem; font-size: 0.9rem;">
                     <a href="../Home_Page/index.php">&larr; Back to Homepage</a>
                </div>

                <?php if (!empty($submit_error)) { ?>
                    <div class="form-group"
                        style="background-color: #5a2a2a; color: #ffc0c0; padding: 1rem; border-radius: 6px; text-align: center;">
                        <strong>Submission Failed.</strong><br><?php echo htmlspecialchars($submit_error); ?>
                    </div>
                <?php } ?>

                <div class="form-group">
                    <label for="username">Your Username:</label>
                    <input id="username" name="username" type="text" value="<?php echo htmlspecialchars($username); ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="subject">Subject:</label>
                    <input id="subject" name="subject" type="text" placeholder="What is this feedback about?" required>
                </div>

                <div class="form-group">
                    <label for="message">Your Feedback:</label>
                    <textarea id="message" name="message" placeholder="Please provide your detailed feedback..." required></textarea>
                </div>
                
                <div class="form-group">
                    <input type="submit" name="submit_feedback" value="Submit Feedback">
                </div>
            </form>
        <?php } ?>
    </div>
</body>

</html>
