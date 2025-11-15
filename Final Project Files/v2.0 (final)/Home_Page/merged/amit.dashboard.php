<?php
// --- 1. DB CONNECTION (from db_connect.php) ---
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password
$dbname = "dashboard_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// --- 2. API LOGIC (from api.php) ---
// This block checks if the request is an API call (e.g., from JavaScript fetch)
// If it is, it processes the request, returns JSON, and stops.
if (isset($_POST['action'])) {
    
    header('Content-Type: application/json');
    $response = ['status' => 'error', 'message' => 'Invalid request'];
    $action = $_POST['action'] ?? '';
    $metric = $_POST['metric'] ?? '';
    $id = $_POST['id'] ?? 0;

    // Security Whitelist from api.php
    $table_map = [
        'total_users' => 'users',
        'unread_feedback' => 'feedback',
        'funds' => 'funds',
        'work_completions' => 'work_completions',
        'comments' => 'comments',
        'total_tasks' => 'tasks',
        'emails' => 'emails',
        'requests' => 'requests',
        'offers' => 'offers'
    ];

    $table_name = $table_map[$metric] ?? null;

    // Delete action logic from api.php
    if ($action === 'deleteEntry') {
        if ($table_name && $id > 0) {
            $sql = "DELETE FROM `$table_name` WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    $response = ['status' => 'success', 'message' => 'Entry deleted successfully.'];
                } else {
                    $response['message'] = 'Database execute failed: ' . $stmt->error;
                }
                $stmt->close();
            } else {
                $response['message'] = 'Database prepare failed: ' . $conn->error;
            }
        } else {
            $response['message'] = 'Invalid table or ID provided for deletion.';
        }
    }

    // Add/Update placeholder logic from api.php
    if ($action === 'addEntry' || $action === 'updateEntry') {
        if (!$table_name) {
            $response['message'] = 'Invalid metric specified.';
        } else {
            // This is a simplified example. A real implementation would be more robust.
            // You would build out the SQL INSERT/UPDATE logic here.
            $response = ['status' => 'success', 'message' => 'Action ' . $action . ' completed.'];
        }
    }

    echo json_encode($response);
    $conn->close();
    exit; // Stop execution to avoid rendering the HTML page
}

// --- 3. DATA LOGIC (from data.php) ---
// This runs if it's NOT an API call.
// (Requires $conn from section 1)

// User info
$user_info = [
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'position' => 'System Operator'
];

// Function to get a single count value from the database
function get_count($conn, $query) {
    $result = $conn->query($query);
    return $result->fetch_row()[0] ?? 0;
}

// Dashboard metrics data from data.php
$dashboard_metrics = [
    'total_users' => [
        'label' => 'Total Users', 'value' => get_count($conn, "SELECT COUNT(*) FROM users"),
        'icon' => 'fas fa-users',
        'graph' => [ 'type' => 'line', 'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], 'dataPoints' => [12, 15, 20, 18, 25, 23, 30] ]
    ],
    'unread_feedback' => [
        'label' => 'Unread Feedback', 'value' => get_count($conn, "SELECT COUNT(*) FROM feedback WHERE is_read = 0"),
        'icon' => 'fas fa-comment-dots',
        'graph' => [ 'type' => 'bar', 'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'], 'dataPoints' => [12, 19, 3, 5, 22] ]
    ],
    'funds' => [
        'label' => 'Funds', 'value' => '$' . number_format(get_count($conn, "SELECT SUM(amount) FROM funds WHERE type = 'Deposit' OR type = 'Revenue'")),
        'icon' => 'fas fa-dollar-sign',
        'graph' => [ 'type' => 'bar', 'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'], 'dataPoints' => [45000, 52000, 38000, 50900] ]
    ],
    'work_completions' => [
        'label' => 'Work Completions', 'value' => round(get_count($conn, "SELECT AVG(completion_percentage) FROM work_completions")) . '%',
        'icon' => 'fas fa-check-circle',
        'graph' => [ 'type' => 'line', 'labels' => ['Project A', 'Project B', 'Project C', 'Project D'], 'dataPoints' => [95, 80, 75, 90], 'tension' => 0.4 ]
    ],
    'comments' => [
        'label' => 'Comments', 'value' => get_count($conn, "SELECT COUNT(*) FROM comments"),
        'icon' => 'fas fa-comments',
        'graph' => [ 'type' => 'bar', 'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'], 'dataPoints' => [45, 60, 51, 78, 70] ]
    ],
    'total_tasks' => [
        'label' => 'Total Tasks', 'value' => get_count($conn, "SELECT COUNT(*) FROM tasks WHERE status != 'Completed'"),
        'icon' => 'fas fa-tasks',
        'graph' => [ 'type' => 'line', 'labels' => ['Pending', 'In Progress', 'Overdue'], 'dataPoints' => [40, 35, 5], 'fill' => true ]
    ],
    'emails' => [
        'label' => 'New Emails', 'value' => get_count($conn, "SELECT COUNT(*) FROM emails"),
        'icon' => 'fas fa-envelope',
        'graph' => [ 'type' => 'bar', 'labels' => ['Inbox', 'Spam', 'Drafts'], 'dataPoints' => [458, 90, 15] ]
    ],
    'requests' => [
        'label' => 'Pending Requests', 'value' => get_count($conn, "SELECT COUNT(*) FROM requests WHERE status = 'Pending'"),
        'icon' => 'fas fa-concierge-bell',
        'graph' => [ 'type' => 'line', 'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'], 'dataPoints' => [10, 15, 8, 12, 2] ]
    ],
    'offers' => [
        'label' => 'Active Offers', 'value' => get_count($conn, "SELECT COUNT(*) FROM offers WHERE expiry_date >= CURDATE()"),
        'icon' => 'fas fa-tags',
        'graph' => [ 'type' => 'bar', 'labels' => ['Discount', 'Promo', 'Bundle'], 'dataPoints' => [15, 8, 7] ]
    ]
];

// --- 4. PAGE ROUTING & DETAILS-PAGE LOGIC ---
// This decides whether to show the 'index' or 'details' content
$page = $_GET['page'] ?? 'index';
$page_title = 'Admin Dashboard'; // Default title

// If we're on the details page, load all the data (from details.php)
if ($page === 'details') {
    $metric_key = $_GET['metric'] ?? '';
    $headers = [];
    $rows = [];

    // This switch logic is from details.php
    switch ($metric_key) {
        case 'total_users':
            $page_title = 'All Users';
            $headers = ['ID', 'Name', 'Email', 'Join Date', 'Status'];
            $result = $conn->query("SELECT id, name, email, join_date, status FROM users ORDER BY id");
            break;
        case 'unread_feedback':
            $page_title = 'Unread Feedback';
            $headers = ['ID', 'User', 'Message', 'Received Date'];
            $result = $conn->query("SELECT id, user_name, message, received_date FROM feedback WHERE is_read = 0 ORDER BY received_date DESC");
            break;
        case 'funds':
            $page_title = 'Financial Transactions';
            $headers = ['ID', 'Amount', 'Type', 'Transaction Date', 'Status'];
            $result = $conn->query("SELECT id, amount, type, transaction_date, status FROM funds ORDER BY transaction_date DESC");
            break;
        case 'work_completions':
            $page_title = 'Work Completions';
            $headers = ['ID', 'Project Name', 'Team Lead', 'Completion %', 'Deadline'];
            $result = $conn->query("SELECT id, project_name, team_lead, completion_percentage, deadline FROM work_completions ORDER BY deadline");
            break;
        case 'comments':
            $page_title = 'All Comments';
            $headers = ['ID', 'User Name', 'Post Title', 'Comment', 'Date'];
            $result = $conn->query("SELECT id, user_name, post_title, comment_text, comment_date FROM comments ORDER BY comment_date DESC");
            break;
        case 'total_tasks':
            $page_title = 'All Tasks';
            $headers = ['ID', 'Assigned To', 'Task', 'Due Date', 'Status'];
            $result = $conn->query("SELECT id, assigned_to, task_description, due_date, status FROM tasks ORDER BY due_date");
            break;
        case 'emails':
            $page_title = 'New Emails';
            $headers = ['ID', 'From', 'Subject', 'Received', 'Priority'];
            $result = $conn->query("SELECT id, sender, subject, received_date, priority FROM emails ORDER BY received_date DESC");
            break;
        case 'requests':
            $page_title = 'All Requests';
            $headers = ['ID', 'Type', 'Submitted By', 'Date', 'Status'];
            $result = $conn->query("SELECT id, request_type, submitted_by, request_date, status FROM requests ORDER BY request_date DESC");
            break;
        case 'offers':
            $page_title = 'All Offers';
            $headers = ['ID', 'Code', 'Type', 'Discount', 'Expires'];
            $result = $conn->query("SELECT id, offer_code, offer_type, discount_value, expiry_date FROM offers ORDER BY expiry_date");
            break;
        default:
            $page_title = 'Error';
            $headers = ['Status'];
            $rows = [['Data not found for this metric.']];
            $result = null;
    }
    
    // Populate rows from the database query
    if ($result) {
        $rows = $result->fetch_all(MYSQLI_ASSOC);
    }
}
// End of PHP logic block
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    
    <!-- CDN Links (from index.php and details.php) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Embedded Styles (from style.css) -->
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
        }

        .dashboard-container { display: flex; min-height: 100vh; }

        /* --- Sidebar --- */
        .sidebar {
            width: 240px;
            background-color: var(--bg-sidebar);
            color: var(--text-light);
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
        }
        .sidebar-header { padding: 1.5rem; border-bottom: 1px solid #374151; }
        .sidebar-nav { flex-grow: 1; }
        .sidebar-nav ul { list-style: none; padding-top: 1rem; }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.9rem 1.5rem;
            color: var(--text-light);
            text-decoration: none;
            transition: background-color 0.2s;
        }
        .sidebar-nav a:hover { background-color: #374151; }
        .sidebar-nav a.active {
            background-color: var(--accent-primary);
            font-weight: 600;
        }
        .sidebar-nav a i { width: 20px; text-align: center; }

        /* --- Logout Button UI/UX Overhaul --- */
        .sidebar-footer {
            padding: 1rem;
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
            background-color: #D32F2F;
            border-color: #B71C1C;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(211, 47, 47, 0.3);
        }

        /* --- Main Content --- */
        .main-content { flex-grow: 1; padding: 2rem; }
        .main-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .main-header h1 { font-size: 1.75rem; font-weight: 600; }
        .user-profile { text-align: right; }
        .user-name { display: block; font-weight: 600; }
        .user-email { font-size: 0.8rem; color: var(--text-secondary); }

        /* --- Clickable Cards --- */
        .dashboard-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem; }
        .card {
            display: flex; align-items: center; gap: 1rem;
            background-color: var(--bg-card); padding: 1.5rem; border-radius: 0.75rem;
            border: 1px solid var(--border-color); box-shadow: var(--shadow);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card.clickable { cursor: pointer; }
        .card.clickable:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        .card-icon i { font-size: 1.75rem; color: var(--accent-primary); }
        .card-info .value { font-size: 2rem; font-weight: 700; }
        .card-info h3 { font-size: 1rem; font-weight: 500; color: var(--text-secondary); }

        /* --- Details Page & Table --- */
        .details-container { background-color: var(--bg-card); border-radius: 0.75rem; padding: 2rem; box-shadow: var(--shadow); }
        .details-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .back-link {
            display: inline-flex; align-items: center; gap: 0.5rem;
            text-decoration: none; color: var(--accent-primary); font-weight: 600;
        }
        .back-link:hover { text-decoration: underline; }
        .details-table-wrapper { overflow-x: auto; }
        .details-table { width: 100%; border-collapse: collapse; }
        .details-table th, .details-table td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid var(--border-color); }
        .details-table th { font-weight: 600; background-color: var(--bg-primary); }
        .details-table tbody tr:hover { background-color: #EFF6FF; }

        /* --- Action Buttons & Table Actions --- */
        .action-btn {
            padding: 0.6rem 1.2rem; border: none; border-radius: 0.5rem;
            font-size: 0.9rem; font-weight: 600; cursor: pointer;
            transition: background-color 0.2s, box-shadow 0.2s;
        }
        .action-btn.primary { background-color: var(--accent-primary); color: white; }
        .action-btn.primary:hover { background-color: var(--accent-hover); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }
        .actions-cell { display: flex; gap: 0.75rem; }
        .action-icon-btn {
            background: none; border: none; cursor: pointer;
            font-size: 1rem; color: var(--text-secondary); transition: color 0.2s;
        }
        .action-icon-btn.edit-btn:hover { color: var(--accent-primary); }
        .action-icon-btn.delete-btn:hover { color: #EF4444; }

        /* --- Modal Styles --- */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.6); display: flex;
            justify-content: center; align-items: center; z-index: 1000;
            opacity: 0; visibility: hidden; transition: opacity 0.3s, visibility 0.3s;
        }
        .modal-overlay:not(.hidden) { opacity: 1; visibility: visible; }
        .modal {
            background: white; padding: 2rem; border-radius: 0.75rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); width: 90%; max-width: 550px;
            position: relative; transform: translateY(-20px); transition: transform 0.3s;
        }
        .modal-overlay:not(.hidden) .modal { transform: translateY(0); }
        .modal-close-btn {
            position: absolute; top: 1rem; right: 1rem; background: none; border: none;
            font-size: 1.5rem; cursor: pointer; color: var(--text-secondary);
        }
        .modal h2 { margin-bottom: 1.5rem; }

        /* --- Graph & Form Styles --- */
        .chart-container { position: relative; height: 250px; width: 100%; margin: 1.5rem 0; }
        .modal-actions { margin-top: 1rem; text-align: right; }
        #data-form .form-group { margin-bottom: 1rem; }
        #data-form label { display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.875rem; color: var(--text-secondary); }
        #data-form input {
            width: 100%; padding: 0.75rem; border: 1px solid var(--border-color);
            border-radius: 0.5rem; font-size: 1rem;
        }
        #data-form input:focus { outline: none; border-color: var(--accent-primary); box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2); }
        .form-actions { margin-top: 1.5rem; text-align: right; }

        /* Helper class */
        .hidden { display: none !important; }

        /* --- Responsive Design --- */
        @media (max-width: 768px) {
            .dashboard-container { flex-direction: column; }
            .sidebar { width: 100%; height: auto; }
            .main-content { padding: 1rem; }
        }
    </style>
</head>
<body>

    <?php
    /**
     * Helper function to render the Sidebar (from sidebar.php)
     * We define this PHP function to avoid pasting the sidebar code twice.
     */
    function render_sidebar($dashboard_metrics) {
    ?>
        <aside class="sidebar">
            <div class="sidebar-header"><h2>Dashboard</h2></div>
            <nav class="sidebar-nav">
                <ul>
                    <!-- MODIFIED LINK: Points to this file with page=index -->
                    <li><a href="full_dashboard.php?page=index" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    
                    <?php foreach ($dashboard_metrics as $key => $metric): ?>
                        <li>
                            <!-- MODIFIED LINK: Points to this file with page=details&metric=... -->
                            <a href="full_dashboard.php?page=details&metric=<?php echo $key; ?>" class="nav-link">
                                <i class="<?php echo $metric['icon']; ?>"></i> <?php echo $metric['label']; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
            <div class="sidebar-footer">
               <a href="#" id="logout-btn" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </aside>
    <?php
    } // End of render_sidebar function

    /**
     * Helper function to render the Header (from header.php)
     */
    function render_header($page_title, $user_info) {
    ?>
        <header class="main-header">
            <h1><?php echo htmlspecialchars($page_title ?? 'Admin Dashboard'); ?></h1>
            <div class="user-profile">
                <div class="user-info">
                    <span class="user-name"><?php echo htmlspecialchars($user_info['name']); ?></span>
                    <span class="user-email"><?php echo htmlspecialchars($user_info['email']); ?></span>
                </div>
            </div>
        </header>
    <?php
    } // End of render_header function
    ?>

    <!-- Main Page Content -->
    <div class="dashboard-container">
        
        <!-- Render Sidebar -->
        <?php render_sidebar($dashboard_metrics); ?>

        <main class="main-content">
            
            <?php if ($page === 'index'): ?>
                <!-- 
                =================================
                INDEX PAGE CONTENT (from index.php)
                =================================
                -->
                
                <!-- Render Header -->
                <?php 
                $index_page_title = 'Dashboard Overview'; // Title from index.php
                render_header($index_page_title, $user_info); 
                ?>
            
                <section class="dashboard-cards">
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
                
                <!-- Graph Modal (from index.php) -->
                <div id="graph-modal-overlay" class="modal-overlay hidden">
                    <div id="graph-modal" class="modal">
                        <button id="close-graph-modal-btn" class="modal-close-btn">&times;</button>
                        <h2 id="graph-modal-title">Metric Summary</h2>
                        <div class="chart-container"><canvas id="summary-chart"></canvas></div>
                        <div class="modal-actions">
                            <!-- MODIFIED LINK: Will be set by JavaScript -->
                            <a href="#" id="view-details-btn" class="action-btn primary">View Full Details</a>
                        </div>
                    </div>
                </div>
                
                <!-- Dashboard Data for JS (from index.php) -->
                <script id="dashboard-data" type="application/json">
                    <?php echo json_encode($dashboard_metrics); ?>
                </script>

            <?php elseif ($page === 'details'): ?>
                <!-- 
                ===================================
                DETAILS PAGE CONTENT (from details.php)
                ===================================
                -->

                <!-- Render Header (uses $page_title defined in routing logic) -->
                <?php render_header($page_title, $user_info); ?>

                <div class="details-container">
                    <div class="details-header">
                        <!-- MODIFIED LINK -->
                        <a href="full_dashboard.php?page=index" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                        <button id="add-new-btn" class="action-btn primary" data-metric="<?php echo htmlspecialchars($metric_key); ?>"><i class="fas fa-plus"></i> Add New Entry</button>
                    </div>
                    
                    <div class="details-table-wrapper">
                        <table class="details-table" id="details-table">
                            <thead>
                                <tr>
                                    <?php foreach ($headers as $header): ?>
                                        <th><?php echo htmlspecialchars($header); ?></th>
                                    <?php endforeach; ?>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($rows)): ?>
                                    <?php foreach ($rows as $row): ?>
                                        <!-- Make sure 'id' is selected in your SQL queries -->
                                        <tr data-id="<?php echo htmlspecialchars($row['id'] ?? ''); ?>">
                                            <?php foreach ($row as $cell): ?>
                                                <td><?php echo htmlspecialchars($cell); ?></td>
                                            <?php endforeach; ?>
                                            <td class="actions-cell">
                                                <button class="action-icon-btn edit-btn" title="Edit"><i class="fas fa-pencil-alt"></i></button>
                                                <button class="action-icon-btn delete-btn" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="<?php echo count($headers) + 1; ?>" style="text-align: center;">No data available.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- CRUD Modal (from details.php) -->
                <div id="modal-overlay" class="modal-overlay hidden">
                    <div id="data-modal" class="modal">
                        <button id="close-modal-btn" class="modal-close-btn">&times;</button>
                        <h2 id="modal-title">Modal Title</h2>
                        <form id="data-form">
                            <div id="modal-form-content"></div>
                            <div class="form-actions">
                                <button type="submit" class="action-btn primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>

            <?php endif; // End of page routing ?>

        </main>
    </div>

    <!-- Embedded JavaScript (from script.js) -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- 1. SIDEBAR ACTIVE LINK LOGIC (MODIFIED) ---
        // This code highlights the correct link in the sidebar
        try {
            const urlParams = new URLSearchParams(window.location.search);
            // Default to 'index' if no page parameter is found
            const currentPage = urlParams.get('page') || 'index'; 
            const currentMetric = urlParams.get('metric');
            const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');

            navLinks.forEach(link => {
                link.classList.remove('active'); // First, remove active from all
                const linkHref = link.getAttribute('href');

                // Case 1: We are on the main dashboard page
                if (currentPage === 'index' && linkHref.includes('page=index')) {
                    link.classList.add('active');
                }
                // Case 2: We are on a details page
                else if (currentPage === 'details' && currentMetric && linkHref.includes(`metric=${currentMetric}`)) {
                    link.classList.add('active');
                }
            });
        } catch (e) { console.error("Error setting active sidebar link:", e); }

        // --- 2. LOGOUT SIMULATION ---
        const logoutBtn = document.getElementById('logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (confirm('Are you sure you want to log out?')) {
                    alert('You have been logged out.');
                    // In a real app, you would redirect: window.location.href = '/login.php';
                }
            });
        }

        // --- 3. GRAPH MODAL LOGIC (for index page) ---
        const dashboardCards = document.querySelector('.dashboard-cards');
        if (dashboardCards) { // This code only runs on the index page
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
                
                // MODIFIED LINK: Create the correct URL for this single-file app
                const detailsUrl = `full_dashboard.php?page=details&metric=${metricKey}`;

                if (!metricData.graph) { // If a card has no graph, just go to details
                    window.location.href = detailsUrl;
                    return;
                }

                // Populate and show the modal
                graphModalTitle.textContent = `${metricData.label} Summary`;
                viewDetailsBtn.href = detailsUrl; // Set modified link on the button

                if (summaryChart) summaryChart.destroy(); // Clear old chart
                
                // Create new chart
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
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
                });

                graphModalOverlay.classList.remove('hidden');
            });
            
            // Modal close events
            const closeGraphModal = () => graphModalOverlay.classList.add('hidden');
            closeGraphModalBtn.addEventListener('click', closeGraphModal);
            graphModalOverlay.addEventListener('click', (e) => { if (e.target === graphModalOverlay) closeGraphModal(); });
        }

        // --- 4. CRUD MODAL LOGIC (for details page) ---
        const table = document.getElementById('details-table');
        if (table) { // This code only runs on the details page
            const modalOverlay = document.getElementById('modal-overlay');
            const modalTitle = document.getElementById('modal-title');
            const dataForm = document.getElementById('data-form');
            const formContent = document.getElementById('modal-form-content');
            const addNewBtn = document.getElementById('add-new-btn');
            const closeModalBtn = document.getElementById('close-modal-btn');
            
            let editingRow = null; // To track if we are adding or editing
            const metric = addNewBtn.dataset.metric;

            const openModal = () => modalOverlay.classList.remove('hidden');
            const closeModal = () => modalOverlay.classList.add('hidden');

            // Dynamically creates form fields based on table headers
            const createFormFields = (headers, rowData = {}) => {
                formContent.innerHTML = '';
                headers.forEach((header) => {
                    const key = header.toLowerCase().replace(/[^a-z0-9]/gi, '_');
                    const value = rowData[header] || (header.toLowerCase().includes('date') ? new Date().toISOString().slice(0, 10) : '');
                    if (header.toLowerCase() === 'id') return; // Don't create a field for ID

                    const formGroup = document.createElement('div');
                    formGroup.className = 'form-group';
                    formGroup.innerHTML = `<label for="field-${key}">${header}</label><input type="text" id="field-${key}" name="${header}" value="${value}" required>`;
                    formContent.appendChild(formGroup);
                });
            };

            // "Add New" button click
            addNewBtn.addEventListener('click', () => {
                editingRow = null; // We are adding, not editing
                modalTitle.textContent = 'Add New Entry';
                const headers = Array.from(table.querySelectorAll('thead th:not(:last-child)')).map(th => th.textContent);
                createFormFields(headers); // Create empty fields
                openModal();
            });
            
            // Modal close events
            closeModalBtn.addEventListener('click', closeModal);
            modalOverlay.addEventListener('click', e => { if (e.target === modalOverlay) closeModal(); });

            // Clicks on "Edit" or "Delete" buttons in the table
            table.addEventListener('click', async (e) => {
                const target = e.target.closest('button');
                if (!target) return; // Didn't click a button

                const row = target.closest('tr');
                const id = row.dataset.id;
                
                // MODIFIED URL: All API calls go to this file itself
                const apiUrl = 'full_dashboard.php';

                if (target.classList.contains('edit-btn')) {
                    editingRow = row; // We are editing
                    modalTitle.textContent = 'Edit Entry';
                    const headers = Array.from(table.querySelectorAll('thead th:not(:last-child)')).map(th => th.textContent);
                    const cells = Array.from(row.querySelectorAll('td:not(:last-child)')).map(td => td.textContent);
                    
                    const rowData = {};
                    headers.forEach((header, i) => { rowData[header] = cells[i]; });
                    
                    createFormFields(headers, rowData); // Create fields pre-filled with data
                    openModal();
                }

                if (target.classList.contains('delete-btn')) {
                    if (!confirm('Are you sure you want to delete this entry?')) return;
                    
                    const formData = new FormData();
                    formData.append('action', 'deleteEntry'); 
                    formData.append('id', id);
                    formData.append('metric', metric);

                    // Send data to the API logic at the top of this file
                    const response = await fetch(apiUrl, { method: 'POST', body: formData });
                    const result = await response.json();

                    if (result.status === 'success') {
                        row.remove(); // Remove row from table on success
                    } else {
                        alert('Error: ' (result.message || 'Could not delete entry.'));
                    }
                }
            });

            // Form submission (for both Add and Edit)
            dataForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(dataForm);
                formData.append('metric', metric);
                
                // MODIFIED URL: All API calls go to this file itself
                const apiUrl = 'full_dashboard.php';
                
                if (editingRow) {
                    formData.append('action', 'updateEntry');
                    formData.append('id', editingRow.dataset.id);
                } else {
                    formData.append('action', 'addEntry');
                }

                // Send data to the API logic at the top of this file
                const response = await fetch(apiUrl, { method: 'POST', body: formData });
                const result = await response.json();

                if (result.status === 'success') {
                    location.reload(); // Reload page to see changes
                } else {
                    alert('Error: ' + (result.message || 'Could not save entry.'));
                }
            });
        }
    });
    </script>
</body>
</html>