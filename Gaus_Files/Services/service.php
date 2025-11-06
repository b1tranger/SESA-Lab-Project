<?php
session_start();
include("../connection.php");


$is_logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$user_type = $_SESSION['user_type'];
$username = $_SESSION['username'];

$sql = "SELECT * FROM service ORDER BY deadline ASC";
$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full Service List</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Link to the new CSS file -->
    <link rel="stylesheet" href="service.css">
</head>

<body>

    <div class="list-container">
        <header class="list-header">
            <h1>All Services</h1>
            <a href="../Home_Page/index.php" class="btn btn-back">Back to Home</a>
            <?php if ($user_type == 'admin'): ?>
                <a href="../Home_Page/admin.php" class="btn btn-back">Back to Dashboard</a>
            <?php endif ?>


        </header>

        <main class="service-grid">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Get data for each service
                    $service_id = $row['service_id'];
                    $service_name = htmlspecialchars($row['service_name']);
                    $service_desc = htmlspecialchars($row['details']);

                    $service_type = htmlspecialchars($row['service_type']); // 'request' or 'offer'
                    $service_poster = htmlspecialchars($row['username']); // The user who posted it
                    $deadline = htmlspecialchars(date("d M, Y", strtotime($row['deadline'])));
                    $compensation = htmlspecialchars($row['compensation']);
                    $status = htmlspecialchars($row['status']); // 'pending', 'in_progress', 'completed'
                    $accept_count = (int) $row['accept_count'];

                    // --- Button Visibility Logic (Simplified as requested) ---
            
                    // 1. "Accept" button
                    $can_accept = false;
                    if ($user_type == 'provider' || $user_type == 'admin') {
                        // Shows 'Accept' if user is a provider or admin, regardless of service status
                        $can_accept = true;
                    }

                    // 2. "Completed" button
                    $can_complete = false;
                    // ASSUMING the other user type is 'consumer'.
                    // You might need to change 'consumer' to whatever your user type is for regular users.
                    if ($user_type == 'consumer' || $user_type == 'admin') {
                        // Shows 'Completed' if user is a consumer or admin, regardless of service status
                        $can_complete = true;
                    }

                    // --- End of new logic ---
            
                    // Admin-only: Show all services.
                    // Non-admins: Hide completed services.
                    if ($status == 'completed' && $user_type != 'admin') {
                        continue; // Skip this item
                    }
                    ?>

                    <!-- Service Card HTML -->
                    <div class="service-card" id="service-<?php echo $service_id; ?>">
                        <div class="card-header">
                            <h3><?php echo $service_name; ?></h3>
                            <!-- Status Indicator -->
                            <span class="status-indicator status-<?php echo $status; ?>"
                                title="Status: <?php echo ucfirst($status); ?>">
                                <?php echo ucfirst($status); ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <p class="service-meta">
                                <strong>Posted by:</strong> <?php echo $service_poster; ?> |
                                <strong>Type:</strong> <?php echo ucfirst($service_type); ?>
                            </p>
                            <p class="service-meta">
                                <strong>Compensation:</strong> <?php echo $compensation; ?> |
                                <strong>Deadline:</strong> <?php echo $deadline; ?>
                            </p>
                            <p class="service-details"><?php echo $service_desc; ?></p>
                        </div>
                        <div class="card-actions">
                            <?php if ($can_accept): ?>
                                <!-- "Accept" button with click counter display -->
                                <button class="btn btn-accept" data-service-id="<?php echo $service_id; ?>">
                                    Accept (<span class="accept-count"><?php echo $accept_count; ?></span>)
                                </button>
                            <?php endif; ?>

                            <?php if ($can_complete): ?>
                                <!-- "Completed" button -->
                                <button class="btn btn-complete" data-service-id="<?php echo $service_id; ?>">
                                    Mark as Completed
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php
                } // End while loop
            } else {
                echo '<p class="no-services">No services are currently available.</p>';
            }
            ?>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const grid = document.querySelector('.service-grid');

            grid.addEventListener('click', async (e) => {
                // --- Handle "Accept" Button Click ---
                if (e.target.classList.contains('btn-accept')) {
                    const button = e.target;
                    const serviceId = button.dataset.serviceId;

                    // Send data to the backend
                    const formData = new FormData();
                    formData.append('action', 'accept');
                    formData.append('service_id', serviceId);

                    try {
                        const response = await fetch('update_service.php', {
                            method: 'POST',
                            body: formData
                        });
                        const result = await response.json();

                        if (result.success) {
                            // Update UI
                            const card = document.getElementById(`service-${serviceId}`);
                            const countSpan = button.querySelector('.accept-count');
                            const statusSpan = card.querySelector('.status-indicator');

                            // Update click counter
                            countSpan.textContent = result.new_count;

                            // Update status
                            statusSpan.textContent = 'In Progress';
                            statusSpan.classList.remove('status-pending');
                            statusSpan.classList.add('status-in_progress');

                            // Hide this button and show "Completed" button (if logic allows)
                            button.style.display = 'none';
                            <?php if ($user_type == 'provider' || $user_type == 'admin'): ?>
                                // Dynamically create and show "Completed" button
                                const completeBtn = card.querySelector('.btn-complete');
                                if (!completeBtn) {
                                    const newCompleteBtn = document.createElement('button');
                                    newCompleteBtn.className = 'btn btn-complete';
                                    newCompleteBtn.dataset.serviceId = serviceId;
                                    newCompleteBtn.textContent = 'Mark as Completed';
                                    card.querySelector('.card-actions').appendChild(newCompleteBtn);
                                }
                            <?php endif; ?>

                        } else {
                            console.error('Failed to accept:', result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                }

                // --- Handle "Completed" Button Click ---
                if (e.target.classList.contains('btn-complete')) {
                    const button = e.target;
                    const serviceId = button.dataset.serviceId;

                    // Send data to the backend
                    const formData = new FormData();
                    formData.append('action', 'complete');
                    formData.append('service_id', serviceId);

                    try {
                        const response = await fetch('update_service.php', {
                            method: 'POST',
                            body: formData
                        });
                        const result = await response.json();

                        if (result.success) {
                            // Update UI
                            const card = document.getElementById(`service-${serviceId}`);

                            <?php if ($user_type == 'admin'): ?>
                                // Admin sees status change
                                const statusSpan = card.querySelector('.status-indicator');
                                statusSpan.textContent = 'Completed';
                                statusSpan.classList.remove('status-in_progress');
                                statusSpan.classList.add('status-completed');
                                card.querySelector('.card-actions').innerHTML = ''; // Clear buttons
                            <?php else: ?>
                                // Regular users just see the card disappear
                                card.style.opacity = '0';
                                setTimeout(() => card.remove(), 300);
                            <?php endif; ?>

                        } else {
                            console.error('Failed to complete:', result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                }
            });
        });
    </script>
</body>

</html>