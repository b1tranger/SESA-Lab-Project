<?php
session_start();
include("../connection.php");

// 1. Get User Session Info
$is_logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$user_type = $_SESSION['user_type'] ?? 'visitor'; // Use null coalescing operator for safety
$username = $_SESSION['username'] ?? 'Visitor';

// 2. Get the Service ID from the URL
$service_to_show = null;
$error_message = '';

if (isset($_GET['id'])) {
    $service_id = (int) $_GET['id']; // Cast to integer for security

    // 3. Fetch the single service using a prepared statement
    // SELECT * will already include the new user_id column
    $sql = "SELECT * FROM service WHERE service_id = ?"; //
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $service_to_show = $result->fetch_assoc();
    } else {
        $error_message = "Service not found. It may have been deleted or the ID is incorrect.";
    }
    $stmt->close();
} else {
    $error_message = "No service ID was provided.";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $service_to_show ? htmlspecialchars($service_to_show['service_name']) : 'Service Not Found'; ?>
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="service.css">
</head>

<body>

    <div class="list-container">
        <header class="list-header">
            <h1><?php echo $service_to_show ? 'Service Details' : 'Error'; ?></h1>
            <div>
                <a href="service.php" class="btn btn-back">Back to Full List</a>
                <a href="../Home_Page/index.php" class="btn btn-back">Back to Home</a>
                <?php if ($user_type == 'admin'): ?>
                    <a href="../Home_Page/admin.php" class="btn btn-back">Back to Dashboard</a>
                <?php endif ?>
            </div>
        </header>

        <main class="service-grid detail-page">
            <?php
            // 4. Check if we found a service
            if ($service_to_show):
                // Get data for the service
                $service_id = $service_to_show['service_id'];
                $user_id = $service_to_show['user_id']; // --- ADDED THIS LINE ---
                $service_name = htmlspecialchars($service_to_show['service_name']);

                // Show the FULL details, not a snippet
                $service_desc = htmlspecialchars($service_to_show['details']);

                $service_type = htmlspecialchars($service_to_show['service_type']);
                $service_poster = htmlspecialchars($service_to_show['username']);
                $deadline = htmlspecialchars(date("l, d F, Y", strtotime($service_to_show['deadline']))); // More detailed date
                $compensation = htmlspecialchars($service_to_show['compensation']);
                $status = htmlspecialchars($service_to_show['status']);
                $accept_count = (int) $service_to_show['accept_count'];

                // --- Re-using Button Visibility Logic from service.php ---
                // This logic is from service_details.php and is different from service.php
                // We should use the more robust logic from service.php
            
                $show_accept_button = false;
                $show_complete_button = false;

                if ($status == 'pending') {
                    if ($user_type == 'provider' && $service_type == 'request' && $username != $service_poster) {
                        $show_accept_button = true;
                    } else if ($user_type == 'consumer' && $service_type == 'offer' && $username != $service_poster) {
                        $show_accept_button = true;
                    }
                } else if ($status == 'in_progress') {
                    if ($user_type == 'admin') {
                        $show_complete_button = true;
                    } else if ($user_type == 'provider' && $service_type == 'offer' && $username == $service_poster) {
                        $show_complete_button = true;
                    } else if ($user_type == 'consumer' && $service_type == 'request' && $username == $service_poster) {
                        $show_complete_button = true;
                    }
                }
                // --- End of button logic ---
            
                ?>

                <div class="service-card" id="service-<?php echo $service_id; ?>">
                    <div class="card-header">
                        <h3><?php echo $service_name; ?></h3>
                        <span class="status-indicator status-<?php echo $status; ?>"
                            title="Status: <?php echo ucfirst($status); ?>">
                            <?php echo ucfirst($status); ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <p class="service-meta" data-poster="<?php echo $service_poster; ?>"
                            data-service-type="<?php echo $service_type; ?>">
                            <strong>Posted by:</strong> <?php echo $service_poster; ?> (ID: <?php echo $user_id; ?>)
                        </p>
                        <p class="service-meta">
                            <strong>Service Type:</strong> <?php echo ucfirst($service_type); ?>
                        </p>
                        <p class="service-meta">
                            <strong>Compensation:</strong> <?php echo $compensation; ?>
                        </p>
                        <p class="service-meta">
                            <strong>Deadline:</strong> <?php echo $deadline; ?>
                        </p>

                        <h4 class="detail-heading">Service Details:</h4>
                        <p class="service-details full-details">
                            <?php echo nl2br($service_desc); // nl2br respects line breaks ?>
                        </p>
                    </div>
                    <div class="card-actions">
                        <?php if ($show_accept_button): // Use updated logic ?>
                            <button class="btn btn-accept" data-service-id="<?php echo $service_id; ?>">
                                Accept (<span
                                    class="accept-count"><?php echo $accept_count; ?></span>/<?php echo $service_to_show['worker_limit'] > 0 ? $service_to_show['worker_limit'] : 1; ?>)
                            </button>
                        <?php endif; ?>

                        <?php if ($show_complete_button): // Use updated logic ?>
                            <button class="btn btn-complete" data-service-id="<?php echo $service_id; ?>">
                                Mark as Completed
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <?php
                // 6. Else, show the error message
            else:
                echo '<p class="no-services">' . $error_message . '</p>';
            endif;
            ?>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const grid = document.querySelector('.service-grid');

            // --- Pass the PHP session variables to JS ---
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

                            // Check if we need to show the complete button
                            const posterMeta = card.querySelector('.service-meta[data-poster]');
                            const posterName = posterMeta ? posterMeta.dataset.poster : '';
                            const serviceType = posterMeta ? posterMeta.dataset.serviceType : '';

                            let showDynamicComplete = false;
                            if (userType === 'admin') {
                                showDynamicComplete = true;
                            } else if (userType === 'consumer' && serviceType === 'request' && currentUser === posterName) {
                                showDynamicComplete = true;
                            } else if (userType === 'provider' && serviceType === 'offer' && currentUser === posterName) {
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

                // Handle "Completed" Button Click
                if (e.target.classList.contains('btn-complete')) {
                    const button = e.target;
                    const serviceId = button.dataset.serviceId;
                    button.disabled = true;

                    const result = await sendServiceUpdate('complete', serviceId);

                    if (result.success) {
                        const card = document.getElementById(`service-${serviceId}`);

                        // On the details page, it's better to show the status change
                        // for everyone, not just the admin.
                        const statusSpan = card.querySelector('.status-indicator');
                        statusSpan.textContent = 'Completed';
                        statusSpan.classList.remove('status-in_progress');
                        statusSpan.classList.add('status-completed');
                        card.querySelector('.card-actions').innerHTML = ''; // Clear buttons

                        // Optional: Redirect back to the list after a delay
                        // setTimeout(() => {
                        //     window.location.href = 'service.php';
                        // }, 1500);

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