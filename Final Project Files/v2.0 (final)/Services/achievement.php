<?php
// 1. REQUIRE LOGIN
session_start();
include("../connection.php");
// Check if the user is logged in. If not, redirect to login page.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../Registration_Login/login.php");
    exit;
}

// 2. GET ACHIEVEMENT DATA

// $username = $_SESSION['username']; // No longer needed for query
$user_type = $_SESSION['user_type'];

// MODIFIED: Fetch *all* completed services and include username/service_type
$sql_get_achievements = "SELECT service_name, compensation, details, deadline, username, service_type 
                         FROM service 
                         WHERE status = 'completed' 
                         ORDER BY deadline DESC";

$stmt = $conn->prepare($sql_get_achievements);
// $stmt->bind_param("s", $username); // No longer needed
$stmt->execute();
$result_achievements = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Achievements - Support Hero</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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
        }

        .form-container {
            margin: auto;
            max-width: 600px;
            width: 90%;
            padding: 2.5rem;
            background-color: #333;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            box-sizing: border-box;
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

        .btn-back {
            display: inline-block;
            margin-bottom: 1.5rem;
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

        .achievement-list-wrapper {
            background-color: #2a2a2a;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #444;
            max-height: 60vh;
            overflow-y: auto;
        }

        .achievement-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .achievement-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #444;
            gap: 1rem;
        }

        .achievement-item:last-child {
            border-bottom: none;
        }

        .achievement-icon {
            font-size: 1.5rem;
            color: #22c55e;
            flex-shrink: 0;
        }

        .achievement-details {
            flex-grow: 1;
            min-width: 200px;
        }

        .achievement-details p {
            margin: 0;
            color: #ccc;
            font-size: 0.9rem;
        }

        /* Service Name */
        .achievement-details h3 {
            margin: 0;
            /* MODIFIED */
            font-size: 1.1rem;
            color: #f0f0f0;
            font-weight: 600;
        }

        /* --- NEW STYLES --- */
        .achievement-details .user-details {
            font-size: 0.85rem;
            color: #aaa;
            margin: 0.35rem 0 0.35rem 0;
            /* Add spacing above and below */
        }

        .achievement-details .user-details strong {
            color: #f0f0f0;
            font-weight: 500;
        }

        .achievement-details .user-details .service-type {
            text-transform: capitalize;
            background-color: #444;
            padding: 0.1rem 0.4rem;
            border-radius: 4px;
            font-size: 0.8rem;
            color: #ddd;
        }

        /* --- END NEW STYLES --- */

        .achievement-details .date {
            font-size: 0.8rem;
            color: #999;
            font-style: italic;
            margin-top: 0;
            /* MODIFIED */
        }

        .achievement-compensation {
            font-size: 1.25rem;
            font-weight: 700;
            color: #22c55e;
            flex-shrink: 0;
        }

        .no-achievements {
            justify-content: center;
            color: #999;
            padding: 1.5rem;
            text-align: center;
        }

        @media (max-width: 480px) {
            .form-container {
                width: 95%;
                padding: 1.5rem;
            }

            .achievement-item {
                flex-wrap: wrap;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .achievement-icon {
                font-size: 1.2rem;
                order: 1;
            }

            .achievement-details {
                order: 2;
                flex-basis: 70%;
            }

            .achievement-compensation {
                font-size: 1.1rem;
                order: 3;
                flex-basis: 100%;
                text-align: left;
                margin-top: 0.5rem;
            }
        }
    </style>
</head>

<body>

    <div class="form-container">

        <a href="../Home_Page/index.php" class="btn-back">
            &larr; Go to Homepage
        </a>

        <h2>Our Achievements</h2>

        <div class="achievement-list-wrapper">
            <ul class="achievement-list">

                <?php
                if ($result_achievements && mysqli_num_rows($result_achievements) > 0) {
                    while ($row = mysqli_fetch_assoc($result_achievements)) {

                        // Sanitize output
                        $service_name = htmlspecialchars($row['service_name']);
                        $compensation = (float) $row['compensation'];
                        $deadline = htmlspecialchars(date("d M, Y", strtotime($row['deadline'])));
                        $service_username = htmlspecialchars($row['username']); // NEW
                        $service_type = htmlspecialchars($row['service_type']); // NEW
                
                        // Display the item
                        echo '<li class="achievement-item">';
                        echo '  <div class="achievement-icon"><i class="fas fa-check-circle"></i></div>';

                        echo '  <div class="achievement-details">';
                        echo "      <h3>$service_name</h3>";

                        // --- NEW HTML BLOCK ---
                        echo "      <p class='user-details'>";
                        echo "          By: <strong>$service_username</strong> | Type: <span class='service-type'>$service_type</span>";
                        echo "      </p>";
                        // --- END NEW HTML BLOCK ---
                
                        echo "      <p class='date'>Completed on (or by): $deadline</p>";
                        echo '  </div>';

                        // Note: There was a typo here, "classS". I fixed it to "class".
                        echo "  <div class='achievement-compensation'>"; // MODIFIED
                        echo "      +" . number_format($compensation, 2) . " BDT";
                        echo '  </div>';

                        echo '</li>';
                    }
                } else {
                    // Show a message if no completed services are found
                    echo '<li class="achievement-item no-achievements">No completed services found. Keep up the good work!</li>';
                }
                $stmt->close();
                ?>

            </ul>
        </div>

    </div>
</body>

</html>