<?php
include("../connection.php");

session_start();

// Check if the user is logged in and store the state in a variable
$is_logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

if ($is_logged_in) {
    // Grab user info if they are logged in
    $username = $_SESSION['username'];
    $user_type = $_SESSION['user_type'];
    $user_email = $_SESSION['email'];
    $user_id = $_SESSION['user_id'];
}


// if (!isset($_SESSION['loggedin'])) {
//     header("Location: ../Home_Page/index.php");
//     exit();
// }

// $loggedin = $_SESSION['loggedin'];
// $query = "SELECT * FROM account WHERE loggedin = '$loggedin'";
// $result = $conn->query($query);
// $data = $result->fetch_assoc();

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- <link rel="stylesheet" href="admin.css"> -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <?php
    include("../connection.php");

    // Total Users
    $sql = "SELECT COUNT(user_id) AS total_users FROM account";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row_1 = mysqli_fetch_assoc($result);
        $TotalUser = $row_1['total_users'];
    } else {
        $TotalUser = 0;
    }
    // Total Balance
    $sql = "SELECT SUM(balance) AS total_balance FROM account";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row_2 = mysqli_fetch_assoc($result);
        $TotalBalance = $row_2['total_balance'];
    } else {
        $TotalBalance = 0;
    }
    // Total Services
    $sql = "SELECT COUNT(service_id) AS total_service FROM service";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row_2 = mysqli_fetch_assoc($result);
        $TotalServices = $row_2['total_service'];
    } else {
        $TotalServices = 0;
    }
    // Total Requests
    $sql = "SELECT COUNT(service_id) AS total_service FROM service WHERE service_type='request' ";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row_2 = mysqli_fetch_assoc($result);
        $TotalRequests = $row_2['total_service'];
    } else {
        $TotalRequests = 0;
    }
    // Total Offers
    $sql = "SELECT COUNT(service_id) AS total_service FROM service WHERE service_type='offer' ";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row_2 = mysqli_fetch_assoc($result);
        $TotalOffers = $row_2['total_service'];
    } else {
        $TotalOffers = 0;
    }
    // Total Tasks
    $sql = "SELECT COUNT(task_id) AS total_tasks FROM tasks";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row_2 = mysqli_fetch_assoc($result);
        $TotalTasks = $row_2['total_tasks'];
    } else {
        $TotalTasks = 0;
    }
    // Total Feedback
    $sql = "SELECT COUNT(user_id) AS total_feedback FROM feedback ";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row_2 = mysqli_fetch_assoc($result);
        $TotalFeedback = $row_2['total_feedback'];
    } else {
        $TotalFeedback = 0;
    }
    // Total Comments
    $sql = "SELECT COUNT(date_posted) AS total_comments FROM comments";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row_2 = mysqli_fetch_assoc($result);
        $TotalComments = $row_2['total_comments'];
    } else {
        $TotalComments = 0;
    }


    ?>

    <style>
        /* Import font */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap');

        /* --- Root Variables for Dark Theme --- */
        :root {
            --bg-dark-primary: #1a1d24;
            /* Main background */
            --bg-dark-secondary: #2c303a;
            /* Card/Sidebar background */
            --border-dark: #44475a;
            /* Borders */
            --text-primary: #f8f8f2;
            /* Main text */
            --text-accent: #bd93f9;
            /* Purple accent for headings */
            --text-muted: #9a9a9a;
            /* Muted text for notes */
            --hover-dark: #44475a;
            /* Hover for metric boxes */
            --hover-link: #deabff;
            /* Hover for links */
            --shadow-color: rgba(0, 0, 0, 0.2);
        }

        /* --- Basic Setup --- */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            /* Padding moved to layout container */
            background-color: var(--bg-dark-primary);
            color: var(--text-primary);
            min-height: 100vh;
        }

        /* --- Dashboard Layout (Sidebar + Main) --- */
        .dashboard-layout {
            display: flex;
            padding: 20px;
            gap: 20px;
            align-items: flex-start;
            /* Align items to the top */
        }

        /* --- Detached Sidebar --- */
        .sidebar {
            width: 240px;
            flex-shrink: 0;
            /* Prevent sidebar from shrinking */
            background-color: var(--bg-dark-secondary);
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px var(--shadow-color);
            border: 1px solid var(--border-dark);

            /* Sticky position to stay in view */
            position: sticky;
            top: 20px;
            height: calc(100vh - 40px);
            /* Full height minus top/bottom padding */
            overflow-y: auto;
            /* Add scroll if content is too long */
        }

        .sidebar-header {
            border-bottom: 1px solid var(--border-dark);
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .sidebar-header h2 {
            margin: 0;
            font-size: 1.4em;
            color: var(--text-accent);
            text-align: center;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar li {
            margin-bottom: 10px;
        }

        .sidebar a {
            display: block;
            padding: 12px 15px;
            text-decoration: none;
            color: var(--text-primary);
            font-weight: 500;
            border-radius: 5px;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .sidebar a:hover {
            background-color: var(--hover-dark);
            color: var(--hover-link);
        }

        /* --- Main Content Area --- */
        .main-content {
            flex: 1;
            /* Take remaining space */
            min-width: 0;
            /* Prevent flex overflow */
        }

        .dashboard-container {
            width: 100%;
            text-align: center;
        }

        .dashboard-container h1 {
            font-size: 2.5em;
            margin-bottom: 40px;
            position: relative;
            display: inline-block;
            color: var(--text-accent);
            /* Make title stand out */
        }

        .dashboard-container h1::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: -10px;
            height: 2px;
            background-color: var(--text-accent);
            /* Match title color */
        }

        .dashboard-content {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            justify-content: center;
            align-items: flex-start;
        }

        /* --- Card Styles (Dark Theme) --- */
        .admin-info-card,
        .overview-card {
            background-color: var(--bg-dark-secondary);
            border: 1px solid var(--border-dark);
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px var(--shadow-color);
            text-align: left;
        }

        .admin-info-card {
            flex: 1;
            min-width: 280px;
            max-width: 350px;
        }

        .admin-info-card h2,
        .overview-card h2 {
            margin-top: 0;
            font-size: 1.5em;
            margin-bottom: 20px;
            color: var(--text-primary);
        }

        .admin-info-card p {
            margin: 10px 0;
            font-size: 1em;
        }

        .overview-card {
            flex: 2;
            min-width: 320px;
            max-width: 100%;
            /* Allow it to fill space */
        }

        .overview-note {
            font-size: 0.9em;
            color: var(--text-muted);
            margin-bottom: 25px;
        }

        /* --- Metrics Grid (Dark Theme) --- */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        /* This now styles both <div> and <a> tags */
        .metric-item {
            background-color: var(--bg-dark-primary);
            /* Darker than card */
            border: 1px solid var(--border-dark);
            border-radius: 5px;
            padding: 20px 10px;
            text-align: center;
            font-weight: 500;
            transition: background-color 0.2s ease, transform 0.2s ease;
            min-height: 60px;

            /* Flex settings for vertical centering */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;

            /* Styles for <a> tag */
            text-decoration: none;
            color: var(--text-primary);
            cursor: pointer;
        }

        .metric-item p {
            margin: 0;
            /* Remove default p margin */
            line-height: 1.4;
        }

        .metric-item:hover {
            background-color: var(--hover-dark);
            transform: translateY(-2px);
            color: var(--hover-link);
            /* Change text color on hover */
        }

        /* Make non-link metric items not change color on hover */
        .metric-item:not(a):hover {
            color: var(--text-primary);
            cursor: default;
        }


        .analysis-note {
            font-size: 0.9em;
            color: var(--text-muted);
            text-align: right;
            margin-top: 20px;
        }

        /* --- Mobile-specific adjustments --- */
        @media (max-width: 900px) {
            .dashboard-layout {
                flex-direction: column;
                padding: 15px;
            }

            .sidebar {
                position: static;
                /* No longer sticky */
                width: 100%;
                height: auto;
                /* Fit content */
            }

            .dashboard-container h1 {
                font-size: 2em;
                margin-bottom: 30px;
            }

            .dashboard-content {
                flex-direction: column;
                gap: 20px;
            }

            .admin-info-card,
            .overview-card {
                max-width: 100%;
            }

            /* Order on mobile: Overview first, then Admin Info */
            .admin-info-card {
                order: 2;
            }

            .overview-card {
                order: 1;
            }

            .metrics-grid {
                grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
                gap: 10px;
            }

            .metric-item {
                padding: 15px 5px;
                min-height: 50px;
            }

            .overview-note,
            .analysis-note {
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .dashboard-container h1 {
                font-size: 1.8em;
            }

            .admin-info-card h2,
            .overview-card h2 {
                font-size: 1.3em;
            }
        }
    </style>

</head>

<body>

    <div class="dashboard-layout">

        <nav class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Menu</h2>
            </div>
            <ul>
                <li><a href="index.php">Go to Home_Page</a></li>
                <li><a href="#">Dashboard</a></li>
                <li><a href="profile.php"><?php echo $user_type; ?> Profile</a></li>
                <li><a href="users.php">User List</a></li>
                <li><a href="../Services/service.php">Service List</a></li>
                <li><a href="../Services/transactions.php">Transactions</a></li>
                <li><a href="../Services/tasks.php">Tasks</a></li>
                <li><a href="../Services/feedback_admin.php">Feedbacks</a></li>
                <li><a href="../Services/comments.php">Comments</a></li>
                <li><a href="../Registration_Login/logout.php">Logout</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <div class="dashboard-container">
                <h1>Admin Dashboard</h1>

                <div class="dashboard-content">
                    <div class="admin-info-card">
                        <h2><?php echo $user_type; ?> Info:</h2>
                        <p><strong>Name:</strong> <?php echo $username; ?></p>
                        <p><strong>ID:</strong> <?php echo $user_id; ?></p>
                        <p><strong>Email:</strong> <?php echo $user_email; ?></p>
                    </div>

                    <div class="overview-card">
                        <h2>Overview:</h2>
                        <p class="overview-note">These will show counters (some are clickable for detail)</p>

                        <div class="metrics-grid">
                            <a href="users.php" class="metric-item">
                                <p><?php echo $TotalUser; ?><br>Total Users</p>
                            </a>
                            <a href="../Services/transactions.php" class="metric-item">
                                <p><?php echo $TotalBalance; ?><br>Total Balance</p>
                            </a>
                            <a href="../Services/service.php" class="metric-item">
                                <p><?php echo $TotalServices; ?><br>Total Services</p>
                            </a>
                            <a href="#" class="metric-item">
                                <p><?php echo $TotalRequests; ?><br>Total Requests</p>
                            </a>
                            <a href="#" class="metric-item">
                                <p><?php echo $TotalOffers; ?><br>Total Offers</p>
                            </a>
                            <a href="../Services/tasks.php" class="metric-item">
                                <p><?php echo $TotalTasks; ?><br>Tasks</p>
                            </a>
                            <a href="../Services/feedback_admin.php" class="metric-item">
                                <p><?php echo $TotalFeedback; ?><br>Feedbacks</p>
                            </a>
                            <a href="../Services/comments.php" class="metric-item">
                                <p><?php echo $TotalComments; ?><br>Comments</p>
                            </a>
                            <!-- <div class="metric-item">?</div> -->
                        </div>

                        <p class="analysis-note">Based on Feedback and Analysis</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>