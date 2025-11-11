<?php
session_start();
include("../connection.php");


$is_logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Set user variables, with defaults for visitors
$user_type = $_SESSION['user_type'] ?? 'visitor';
$username = $_SESSION['username'] ?? 'visitor';

// MODIFIED SQL: Added 'service_category'
$sql = "SELECT service_id, user_id, service_name, details, service_type, service_category, username, deadline, compensation, 
               IF(status = '' OR status IS NULL, 'pending', status) as status, 
               accept_count, worker_limit 
        FROM service 
        ORDER BY deadline ASC"; //

$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full Service List</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
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
                    $user_id = $row['user_id'];
                    $service_name = htmlspecialchars($row['service_name']);
                    $service_desc = htmlspecialchars($row['details']);
                    $service_category = htmlspecialchars($row['service_category']); // --- MODIFIED: Added this line ---
                    $service_type = htmlspecialchars($row['service_type']); // 'request' or 'offer'
                    $service_poster = htmlspecialchars($row['username']); // The user who posted it
                    $deadline = htmlspecialchars(date("d M, Y", strtotime($row['deadline'])));
                    $compensation = htmlspecialchars($row['compensation']);

                    $status = htmlspecialchars($row['status']);
                    $accept_count = (int) $row['accept_count'];
                    $worker_limit = (int) $row['worker_limit'];
                    if ($worker_limit <= 0)
                        $worker_limit = 1;

                    // --- NEW ROLE-BASED BUTTON LOGIC ---
            
                    $show_accept_button = false;
                    $show_complete_button = false;

                    if ($status == 'pending') {
                        // Logic for "Accept" button (only shows on 'pending' services)
                        if ($user_type == 'provider' && $service_type == 'request' && $username != $service_poster) {
                            // Provider can accept a REQUEST they didn't post
                            $show_accept_button = true;
                        } else if ($user_type == 'consumer' && $service_type == 'offer' && $username != $service_poster) {
                            // Consumer can accept an OFFER they didn't post
                            $show_accept_button = true;
                        }
                        // Admin cannot accept anything
            
                    } else if ($status == 'in_progress') {
                        // Logic for "Complete" button (only shows on 'in_progress' services)
                        if ($user_type == 'admin') {
                            // Admin can complete ANY service
                            $show_complete_button = true;
                        } else if ($user_type == 'provider' && $service_type == 'offer' && $username == $service_poster) {
                            // Provider can complete their OWN OFFER
                            $show_complete_button = true;
                        } else if ($user_type == 'consumer' && $service_type == 'request' && $username == $service_poster) {
                            // Consumer can complete their OWN REQUEST
                            $show_complete_button = true;
                        }
                    }
                    // --- End of new logic ---
            
                    // Non-admins: Hide completed services.
                    if ($status == 'completed' && $user_type != 'admin') {
                        continue; // Skip this item
                    }
                    ?>

                    <div class="service-card" id="service-<?php echo $service_id; ?>">
                        <div class="card-header">
                            <h3><?php echo $service_name; ?></h3>
                            <span class="status-indicator status-<?php echo $status; ?>"
                                title="Status: <?php echo ucfirst(str_replace('_', ' ', $status)); ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $status)); ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <p class="service-meta" data-poster="<?php echo $service_poster; ?>"
                                data-service-type="<?php echo $service_type; ?>"
                                data-service-category="<?php echo $service_category; ?>">
                                <strong>Posted by:</strong> <?php echo $service_poster; ?> (ID: <?php echo $user_id; ?>) |
                                <strong>Type:</strong> <?php echo ucfirst($service_type); ?> |
                                <strong>Category:</strong> <?php echo ucfirst($service_category); ?>
                            </p>
                            <p class="service-meta">
                                <strong>Compensation:</strong> <?php echo $compensation; ?> |
                                <strong>Deadline:</strong> <?php echo $deadline; ?>
                            </p>
                            <p class="service-details"
                                style="overflow-wrap: break-word; word-break: break-word; white-space: normal;">
                                <?php echo $service_desc; ?>
                            </p>
                        </div>
                        <div class="card-actions">
                            <?php if ($show_accept_button): ?>
                                <button class="btn btn-accept" data-service-id="<?php echo $service_id; ?>">
                                    Accept (<span
                                        class="accept-count"><?php echo $accept_count; ?></span>/<?php echo $worker_limit; ?>)
                                </button>
                            <?php endif; ?>

                            <?php if ($show_complete_button): ?>
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

            const currentUser = '<?php echo $username; ?>';
            const userType = '<?php echo $user_type; ?>';

            async function sendServiceUpdate(action, serviceId) {
                const formData = new FormData();
                formData.append('action', action);
                formData.append('service_id', serviceId);

                try {
                    const response = await fetch('update_service.php', {
                        method: 'POST',
                        body: formData
                    });
                    return await response.json();
                } catch (error) {
                    console.error('Network Error:', error);
                    return { success: false, message: 'Network error. Please try again.' };
                }
            }

            grid.addEventListener('click', async (e) => {

                // Handle "Accept" Button Click
                if (e.target.classList.contains('btn-accept')) {
                    const button = e.target;
                    const serviceId = button.dataset.serviceId;
                    button.disabled = true;

                    const result = await sendServiceUpdate('accept', serviceId);

                    if (result.success) {
                        const card = document.getElementById(`service-${serviceId}`);
                        const countSpan = button.querySelector('.accept-count');
                        const statusSpan = card.querySelector('.status-indicator');

                        if (countSpan) {
                            countSpan.textContent = result.new_count;
                        }

                        if (result.new_status === 'in_progress') {
                            statusSpan.textContent = 'In Progress';
                            statusSpan.classList.remove('status-pending');
                            statusSpan.classList.add('status-in_progress');

                            button.style.display = 'none'; // Hide "Accept" button

                            // --- Dynamically add "Complete" button if needed ---
                            const posterMeta = card.querySelector('.service-meta[data-poster]');
                            const posterName = posterMeta ? posterMeta.dataset.poster : '';
                            const serviceType = posterMeta ? posterMeta.dataset.serviceType : '';

                            let showDynamicComplete = false;
                            if (userType === 'admin') {
                                showDynamicComplete = true;
                            } else if (userType === 'consumer' && serviceType === 'request' && currentUser === posterName) {
                                // Consumer who posted the request can complete it
                                showDynamicComplete = true;
                            } else if (userType === 'provider' && serviceType === 'offer' && currentUser === posterName) {
                                // Provider who posted the offer can complete it
                                showDynamicComplete = true;
                            }

                            if (showDynamicComplete) {
                                const completeBtn = card.querySelector('.btn-complete');
                                if (!completeBtn) {
                                    const newCompleteBtn = document.createElement('button');
                                    newCompleteBtn.className = 'btn btn-complete';
                                    newCompleteBtn.dataset.serviceId = serviceId;
                                    newCompleteBtn.textContent = 'Mark as Completed';
                                    card.querySelector('.card-actions').appendChild(newCompleteBtn);
                                }
                            }
                        } else {
                            button.disabled = false;
                        }

                    } else {
                        alert('Error: ' + result.message);
                        button.disabled = false;
                    }
                }

                // --- Handle "Completed" Button Click ---
                if (e.target.classList.contains('btn-complete')) {
                    const button = e.target;
                    const serviceId = button.dataset.serviceId;
                    button.disabled = true;

                    const result = await sendServiceUpdate('complete', serviceId);

                    if (result.success) {
                        const card = document.getElementById(`service-${serviceId}`);

                        if (userType === 'admin') {
                            const statusSpan = card.querySelector('.status-indicator');
                            statusSpan.textContent = 'Completed';
                            statusSpan.classList.remove('status-in_progress');
                            statusSpan.classList.add('status-completed');
                            card.querySelector('.card-actions').innerHTML = '';
                        } else {
                            // Regular users (provider/consumer) see the card disappear
                            card.style.opacity = '0';
                            setTimeout(() => card.remove(), 350);
                        }

                    } else {
                        alert('Error: ' + result.message);
                        button.disabled = false;
                    }
                }
            });
        });
    </script>
</body>

</html>