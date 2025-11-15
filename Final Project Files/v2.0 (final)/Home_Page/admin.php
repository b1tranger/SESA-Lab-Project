<?php
// This is the complete PHP logic from old.admin.php
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
} else {
    // Default values if not logged in, to prevent errors
    $username = "Guest";
    $user_type = "Guest";
    $user_email = "guest@example.com";
    $user_id = "N/A";
}

// Total Users
$sql = "SELECT COUNT(user_id) AS total_users FROM account";
$result = mysqli_query($conn, $sql);
$TotalUser = $result ? mysqli_fetch_assoc($result)['total_users'] : 0;

// Total Balance
$sql = "SELECT SUM(balance) AS total_balance FROM account";
$result = mysqli_query($conn, $sql);
$TotalBalance = $result ? mysqli_fetch_assoc($result)['total_balance'] : 0;

// Total Services
$sql = "SELECT COUNT(service_id) AS total_service FROM service";
$result = mysqli_query($conn, $sql);
$TotalServices = $result ? mysqli_fetch_assoc($result)['total_service'] : 0;

// Total Requests
$sql = "SELECT COUNT(service_id) AS total_service FROM service WHERE service_type='request' ";
$result = mysqli_query($conn, $sql);
$TotalRequests = $result ? mysqli_fetch_assoc($result)['total_service'] : 0;

// Total Offers
$sql = "SELECT COUNT(service_id) AS total_service FROM service WHERE service_type='offer' ";
$result = mysqli_query($conn, $sql);
$TotalOffers = $result ? mysqli_fetch_assoc($result)['total_service'] : 0;

// Total Tasks
$sql = "SELECT COUNT(task_id) AS total_tasks FROM tasks";
$result = mysqli_query($conn, $sql);
$TotalTasks = $result ? mysqli_fetch_assoc($result)['total_tasks'] : 0;

// Total Feedback
$sql = "SELECT COUNT(user_id) AS total_feedback FROM feedback ";
$result = mysqli_query($conn, $sql);
$TotalFeedback = $result ? mysqli_fetch_assoc($result)['total_feedback'] : 0;

// Total Comments
$sql = "SELECT COUNT(date_posted) AS total_comments FROM comments";
$result = mysqli_query($conn, $sql);
$TotalComments = $result ? mysqli_fetch_assoc($result)['total_comments'] : 0;


// --- NEW DATA ARRAY ---
// This combines old.admin.php's data with amit.dashboard.php's graph/linking data
// This array will be used by the JavaScript to power the modals.
// I've added icons from Font Awesome to match amit.dashboard.php
$dashboard_metrics = [
    'total_users' => [
        'label' => 'Total Users',
        'value' => $TotalUser,
        'icon' => 'fas fa-users',
        'details_url' => 'users.php', // From old.admin.php
        'graph' => [ 'type' => 'line', 'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], 'dataPoints' => [12, 15, 20, 18, 25, 23, 30] ]
    ],
    'total_balance' => [
        'label' => 'Total Balance',
        'value' => $TotalBalance,
        'icon' => 'fas fa-dollar-sign',
        'details_url' => '../Services/transactions.php?ref=admin', // From old.admin.php
        'graph' => [ 'type' => 'bar', 'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'], 'dataPoints' => [45000, 52000, 38000, 50900] ]
    ],
    'total_services' => [
        'label' => 'Total Services',
        'value' => $TotalServices,
        'icon' => 'fas fa-concierge-bell',
        'details_url' => '../Services/service.php', // From old.admin.php
        'graph' => [ 'type' => 'bar', 'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'], 'dataPoints' => [12, 19, 3, 5, 22] ]
    ],
    'total_requests' => [
        'label' => 'Total Requests',
        'value' => $TotalRequests,
        'icon' => 'fas fa-clipboard-list',
        'details_url' => '../Services/service.php?type=request', // From old.admin.php
        'graph' => [ 'type' => 'line', 'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'], 'dataPoints' => [10, 15, 8, 12, 2] ]
    ],
    'total_offers' => [
        'label' => 'Total Offers',
        'value' => $TotalOffers,
        'icon' => 'fas fa-tags',
        'details_url' => '../Services/service.php?type=offer', // From old.admin.php
        'graph' => [ 'type' => 'bar', 'labels' => ['Discount', 'Promo', 'Bundle'], 'dataPoints' => [15, 8, 7] ]
    ],
    'total_tasks' => [
        'label' => 'Tasks',
        'value' => $TotalTasks,
        'icon' => 'fas fa-tasks',
        'details_url' => '../Services/tasks.php', // From old.admin.php
        'graph' => [ 'type' => 'line', 'labels' => ['Pending', 'In Progress', 'Overdue'], 'dataPoints' => [40, 35, 5], 'fill' => true ]
    ],
    'total_feedback' => [
        'label' => 'Feedbacks',
        'value' => $TotalFeedback,
        'icon' => 'fas fa-comment-dots',
        'details_url' => '../Services/feedback_admin.php', // From old.admin.php
        'graph' => [ 'type' => 'bar', 'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'], 'dataPoints' => [12, 19, 3, 5, 22] ]
    ],
    'total_comments' => [
        'label' => 'Comments',
        'value' => $TotalComments,
        'icon' => 'fas fa-comments',
        'details_url' => '../Services/comments.php', // From old.admin.php
        'graph' => [ 'type' => 'bar', 'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'], 'dataPoints' => [45, 60, 51, 78, 70] ]
    ]
];
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <!-- Added from amit.dashboard.php for graphs and icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- NEW CSS from amit.dashboard.php -->
    <style>
        /* --- Global Styles & Variables --- */
        :root {
            --bg-primary: #F8F9FA;
            --bg-sidebar: #1F2937;
            --bg-card: #FFFFFF;
            --text-primary: #111827;
            --text-secondary: #6B7280;
            --text-light: #F9FAFB;
            --accent-primary: #3B82F6;
            --accent-hover: #2563EB;
            --border-color: #E5E7EB;
            --shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
        }

        /* NEW layout container from amit.dashboard.php */
        .dashboard-container { 
            display: flex; 
            min-height: 100vh; 
        }

        /* --- Sidebar (New CSS) --- */
        .sidebar {
            width: 240px;
            background-color: var(--bg-sidebar);
            color: var(--text-light);
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
        }
        .sidebar-header { 
            padding: 1.5rem; 
            border-bottom: 1px solid #374151;
            /* Added text-align from old.admin.php */
            text-align: center;
        }
        /* Styling the h2 from old.admin.php */
        .sidebar-header h2 {
            margin: 0;
            font-size: 1.4em;
            color: var(--text-light); /* Changed from purple */
        }
        
        .sidebar-nav { 
            flex-grow: 1; 
            overflow-y: auto;
        }
        .sidebar-nav ul { 
            list-style: none; 
            padding: 1rem 0; /* Use padding for spacing */
            margin: 0;
        }
        .sidebar-nav li {
            /* Remove default margin */
            margin: 0;
        }
        .sidebar-nav a {
            display: flex; /* Use flex for icon alignment */
            align-items: center;
            gap: 0.75rem; /* Space between icon and text */
            padding: 0.9rem 1.5rem;
            color: var(--text-light);
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.2s;
            font-size: 0.9em; /* Match old admin style */
        }
        .sidebar-nav a:hover { 
            background-color: #374151; 
        }
        .sidebar-nav a.active {
            background-color: var(--accent-primary);
            font-weight: 600;
        }
        /* Simple icon styling */
        .sidebar-nav a i {
             width: 20px;
             text-align: center;
             font-size: 1.1em;
             color: #9CA3AF; /* Muted icon color */
        }
        .sidebar-nav a:hover i {
            color: var(--text-light);
        }

        /* --- Logout Button (Adapted from amit.dashboard.php) --- */
        .sidebar-footer {
            padding: 1.5rem; /* Increased padding */
            margin-top: auto;
            border-top: 1px solid #374151;
        }
        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            width: 100%;
            padding: 0.75rem;
            border-radius: 0.5rem;
            border: 1px solid #4B5563;
            background-color: #374151;
            color: var(--text-light);
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }
        .logout-btn:hover {
            background-color: #D32F2F; /* Red hover for logout */
            border-color: #B71C1C;
            color: white;
        }

        /* --- Main Content (New CSS) --- */
        .main-content { 
            flex-grow: 1; 
            padding: 2rem; 
            overflow-y: auto;
            height: 100vh;
        }
        
        /* NEW Header from amit.dashboard.php */
        .main-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 2rem; 
        }
        .main-header h1 { 
            font-size: 1.75rem; 
            font-weight: 600; 
            margin: 0; /* Removed old margin */
        }
        /* Using user-profile to style the admin info */
        .user-profile { 
            text-align: right; 
            background-color: var(--bg-card);
            padding: 0.75rem 1.25rem;
            border-radius: 0.75rem;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow);
        }
        .user-name { 
            display: block; 
            font-weight: 600; 
            color: var(--text-primary);
        }
        .user-email { 
            font-size: 0.8rem; 
            color: var(--text-secondary); 
        }
        .user-id {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-top: 2px;
        }


        /* --- NEW Card Styles --- */
        .dashboard-cards { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); /* Adjusted min-width */
            gap: 1.5rem; 
        }
        .card {
            display: flex; 
            align-items: center; 
            gap: 1rem;
            background-color: var(--bg-card); 
            padding: 1.5rem; 
            border-radius: 0.75rem;
            border: 1px solid var(--border-color); 
            box-shadow: var(--shadow);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card.clickable { cursor: pointer; }
        .card.clickable:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); 
        }
        .card-icon {
            flex-shrink: 0;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #EFF6FF; /* Light blue bg */
        }
        .card-icon i { 
            font-size: 1.5rem; /* Larger icon */
            color: var(--accent-primary); 
        }
        .card-info .value { 
            font-size: 2rem; 
            font-weight: 700;
            line-height: 1.1;
        }
        .card-info h3 { 
            font-size: 0.9rem; /* Slightly smaller */
            font-weight: 500; 
            color: var(--text-secondary);
            margin: 0;
        }

        /* --- Modal Styles (from amit.dashboard.php) --- */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.6); display: flex;
            justify-content: center; align-items: center; z-index: 1000;
            opacity: 0; visibility: hidden; transition: opacity 0.3s, visibility 0.3s;
        }
        .modal-overlay:not(.hidden) { opacity: 1; visibility: visible; }
        .modal {
            background: white; 
            color: var(--text-primary);
            padding: 2rem; border-radius: 0.75rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); width: 90%; max-width: 550px;
            position: relative; transform: translateY(-20px); transition: transform 0.3s;
        }
        .modal-overlay:not(.hidden) .modal { transform: translateY(0); }
        .modal-close-btn {
            position: absolute; top: 1rem; right: 1rem; background: none; border: none;
            font-size: 1.5rem; cursor: pointer; color: var(--text-secondary);
        }
        .modal h2 { margin-bottom: 1.5rem; }
        .chart-container { position: relative; height: 250px; width: 100%; margin: 1.5rem 0; }
        .modal-actions { margin-top: 1rem; text-align: right; }
        .action-btn {
            padding: 0.6rem 1.2rem; border: none; border-radius: 0.5rem;
            font-size: 0.9rem; font-weight: 600; cursor: pointer;
            text-decoration: none; 
            display: inline-block; 
            transition: background-color 0.2s, box-shadow 0.2s;
        }
        .action-btn.primary { background-color: var(--accent-primary); color: white; }
        .action-btn.primary:hover { background-color: var(--accent-hover); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }
        .hidden { display: none !important; }

        /* --- Responsive Design --- */
        @media (max-width: 768px) {
            .dashboard-container { flex-direction: column; }
            .sidebar { 
                width: 100%; 
                height: auto; 
                position: static; /* No longer sticky */
            }
            .main-content { 
                padding: 1rem; 
                height: auto; /* Allow content to flow */
            }
            .main-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            .user-profile {
                width: 100%;
                text-align: left;
            }
            .dashboard-cards {
                grid-template-columns: 1fr; /* Stack cards on smallest screens */
            }
        }
    </style>

</head>

<body>
    <!-- NEW HTML STRUCTURE (based on amit.dashboard.php) -->
    <div class="dashboard-container">

        <!-- Sidebar (New CSS, old.admin.php links) -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <!-- Title from old.admin.php -->
                <h2>Admin Menu</h2>
            </div>
            <!-- Nav links from old.admin.php -->
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="index.php"><i class="fas fa-home"></i> Go to Home_Page</a></li>
                    <li><a href="#" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="profile.php"><i class="fas fa-user"></i> <?php echo $user_type; ?> Profile</a></li>
                    <li><a href="users.php"><i class="fas fa-users"></i> User List</a></li>
                    <li><a href="../Services/service.php"><i class="fas fa-cogs"></i> Service List</a></li>
                    <li><a href="../Services/transactions.php"><i class="fas fa-exchange-alt"></i> Transactions</a></li>
                    <li><a href="../Services/tasks.php"><i class="fas fa-tasks"></i> Tasks</a></li>
                    <li><a href="../Services/feedback_admin.php"><i class="fas fa-comment-dots"></i> Feedbacks</a></li>
                    <li><a href="../Services/comments.php"><i class="fas fa-comments"></i> Comments</a></li>
                </ul>
            </nav>
            <!-- Logout button from amit.dashboard.php -->
            <div class="sidebar-footer">
               <a href="../Registration_Login/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </aside>

        <!-- Main content (New CSS, old.admin.php data) -->
        <main class="main-content">
            
            <!-- NEW Header (combines h1 and admin info) -->
            <header class="main-header">
                <h1>Admin Dashboard</h1>
                <!-- Admin info from old.admin.php, styled as user-profile -->
                <div class="user-profile">
                    <span class="user-name"><?php echo htmlspecialchars($username); ?> (<?php echo htmlspecialchars($user_type); ?>)</span>
                    <span class="user-email"><?php echo htmlspecialchars($user_email); ?></span>
                    <span class="user-id">ID: <?php echo htmlspecialchars($user_id); ?></span>
                </div>
            </header>

            <!-- NEW Card Grid (replaces metrics-grid) -->
            <section class="dashboard-cards">
                
                <!-- Loop through metrics array -->
                <?php foreach ($dashboard_metrics as $key => $metric): ?>
                    <div class="card clickable" data-metric="<?php echo $key; ?>">
                        <div class="card-icon"><i class="<?php echo $metric['icon']; ?>"></i></div>
                        <div class="card-info">
                            <p class="value"><?php echo htmlspecialchars($metric['value']); ?></p>
                            <h3><?php echo htmlspecialchars($metric['label']); ?></h3>
                        </div>
                    </div>
                <?php endforeach; ?>

            </section>
        </main>
    </div>

    <!-- Graph Modal HTML (from amit.dashboard.php) -->
    <div id="graph-modal-overlay" class="modal-overlay hidden">
        <div id="graph-modal" class="modal">
            <button id="close-graph-modal-btn" class="modal-close-btn">&times;</button>
            <h2 id="graph-modal-title">Metric Summary</h2>
            <div class="chart-container"><canvas id="summary-chart"></canvas></div>
            <div class="modal-actions">
                <!-- This "View Full Details" button's link will be set by JavaScript -->
                <a href="#" id="view-details-btn" class="action-btn primary">View Full Details</a>
            </div>
        </div>
    </div>
    
    <!-- Dashboard Data for JS -->
    <script id="dashboard-data" type="application/json">
        <?php echo json_encode($dashboard_metrics); ?>
    </script>
    
    <!-- JavaScript for Modal (from amit.dashboard.php) -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- GRAPH MODAL LOGIC ---
        
        const dashboardCards = document.querySelector('.dashboard-cards'); 
        
        if (dashboardCards) {
            const graphModalOverlay = document.getElementById('graph-modal-overlay');
            const graphModalTitle = document.getElementById('graph-modal-title');
            const viewDetailsBtn = document.getElementById('view-details-btn');
            const closeGraphModalBtn = document.getElementById('close-graph-modal-btn');
            const chartCanvas = document.getElementById('summary-chart');
            const dashboardData = JSON.parse(document.getElementById('dashboard-data').textContent);
            let summaryChart = null;

            dashboardCards.addEventListener('click', (e) => {
                const card = e.target.closest('.card.clickable'); 
                if (!card) return;

                const metricKey = card.dataset.metric;
                const metricData = dashboardData[metricKey];

                // Get the correct URL from our PHP array
                const detailsUrl = metricData.details_url; 

                if (!metricData.graph) {
                    window.location.href = detailsUrl;
                    return;
                }

                // Populate and show the modal
                graphModalTitle.textContent = `${metricData.label} Summary`;
                viewDetailsBtn.href = detailsUrl; // Set the correct URL!

                if (summaryChart) summaryChart.destroy();
                
                summaryChart = new Chart(chartCanvas, {
                    type: metricData.graph.type,
                    data: {
                        labels: metricData.graph.labels,
                        datasets: [{
                            label: metricData.label,
                            data: metricData.graph.dataPoints,
                            backgroundColor: 'rgba(59, 130, 246, 0.2)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 2,
                            tension: metricData.graph.type === 'line' ? 0.3 : 0,
                            fill: true,
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false, 
                        scales: { y: { beginAtZero: true } }, 
                        plugins: { legend: { display: false } } 
                    }
                });

                graphModalOverlay.classList.remove('hidden');
            });
            
            const closeGraphModal = () => graphModalOverlay.classList.add('hidden');
            closeGraphModalBtn.addEventListener('click', closeGraphModal);
            graphModalOverlay.addEventListener('click', (e) => { if (e.target === graphModalOverlay) closeGraphModal(); });
        }
        
        // --- Sidebar Active Link Logic (Adapted for new structure) ---
        try {
            // Get the current page filename (e.g., "admin.php")
            const currentPage = window.location.pathname.split('/').pop();
            
            // Get all links in the sidebar
            const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');
            let foundActive = false;

            navLinks.forEach(link => {
                const linkHref = link.getAttribute('href').split('/').pop();
                
                // Check if the link's href matches the current page
                if (linkHref === currentPage) {
                    link.classList.add('active');
                    foundActive = true;
                } else {
                    link.classList.remove('active');
                }
            });

            // Fallback: If no link matches, highlight the "Dashboard" link
            if (!foundActive) {
                const dashboardLink = document.querySelector('.sidebar-nav a[href="#"]');
                if(dashboardLink) {
                    dashboardLink.classList.add('active');
                }
            }

        } catch (e) {
            console.error("Error setting active sidebar link:", e);
        }
    });
    </script>
</body>
</html>