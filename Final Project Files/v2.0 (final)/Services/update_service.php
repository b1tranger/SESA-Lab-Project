<?php
// 1. START SESSION & CONNECT
session_start();
include("../connection.php");

// 2. SET UP JSON RESPONSE
header('Content-Type: application/json');

// 3. SECURITY: MUST BE LOGGED IN
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Error: You must be logged in to perform this action.']);
    exit;
}

// 4. CHECK REQUEST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'], $_POST['service_id'])) {

    $action = $_POST['action'];
    $service_id = (int) $_POST['service_id'];
    $username = $_SESSION['username'];
    $user_type = $_SESSION['user_type'];

    // --- ACTION: ACCEPT SERVICE ---
    if ($action == 'accept') {

        $conn->begin_transaction();

        try {
            $stmt_get = $conn->prepare("SELECT username, service_type, accept_count, worker_limit, status FROM service WHERE service_id = ? FOR UPDATE");
            $stmt_get->bind_param("i", $service_id);
            $stmt_get->execute();
            $result = $stmt_get->get_result();

            if ($result->num_rows == 0) {
                throw new Exception('Service not found.');
            }

            $service = $result->fetch_assoc();
            $stmt_get->close();

            $service_poster = $service['username'];
            $service_type_db = $service['service_type'];
            $status_db = $service['status'];

            // --- ALL NEW SECURITY CHECKS ---
            if ($user_type == 'admin') {
                throw new Exception('Admins cannot accept services.');
            }
            if ($service_poster == $username) {
                throw new Exception('You cannot accept your own service.');
            }
            if ($status_db != 'pending') {
                throw new Exception('This service is no longer pending.');
            }
            if ($user_type == 'provider' && $service_type_db != 'request') {
                throw new Exception('Providers can only accept requests.');
            }
            if ($user_type == 'consumer' && $service_type_db != 'offer') {
                throw new Exception('Consumers can only accept offers.');
            }
            // --- End of security checks ---

            // Logic for counter
            $new_accept_count = $service['accept_count'] + 1;
            $worker_limit = (int) $service['worker_limit'];
            if ($worker_limit <= 0)
                $worker_limit = 1;

            $new_status = 'pending';
            if ($new_accept_count >= $worker_limit) {
                $new_status = 'in_progress';
            }

            // Update the database
            $stmt_update = $conn->prepare("UPDATE service SET accept_count = ?, status = ? WHERE service_id = ?");
            $stmt_update->bind_param("isi", $new_accept_count, $new_status, $service_id);

            if (!$stmt_update->execute()) {
                throw new Exception('Database update failed.');
            }

            $stmt_update->close();
            $conn->commit();

            echo json_encode([
                'success' => true,
                'new_count' => $new_accept_count,
                'new_status' => $new_status
            ]);

        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // --- ACTION: COMPLETE SERVICE ---
    else if ($action == 'complete') {

        $stmt_get = $conn->prepare("SELECT username, service_type, status FROM service WHERE service_id = ?");
        $stmt_get->bind_param("i", $service_id);
        $stmt_get->execute();
        $result = $stmt_get->get_result();

        if ($result->num_rows == 0) {
            echo json_encode(['success' => false, 'message' => 'Service not found.']);
            exit;
        }

        $service = $result->fetch_assoc();
        $stmt_get->close();

        $service_poster = $service['username'];
        $service_type_db = $service['service_type'];
        $status_db = $service['status'];

        // --- NEW SECURITY CHECKS ---
        if ($status_db != 'in_progress') {
            echo json_encode(['success' => false, 'message' => 'This service is not "In Progress" and cannot be completed.']);
            exit;
        }

        $can_complete = false;
        if ($user_type == 'admin') {
            $can_complete = true;
        } else if ($user_type == 'provider' && $service_type_db == 'offer' && $service_poster == $username) {
            // Provider completing their OWN offer
            $can_complete = true;
        } else if ($user_type == 'consumer' && $service_type_db == 'request' && $service_poster == $username) {
            // Consumer completing their OWN request
            $can_complete = true;
        }

        if (!$can_complete) {
            echo json_encode(['success' => false, 'message' => 'You do not have permission to complete this service.']);
            exit;
        }
        // --- End of security checks ---

        // All checks passed, update the status
        $stmt_update = $conn->prepare("UPDATE service SET status = 'completed' WHERE service_id = ?");
        $stmt_update->bind_param("i", $service_id);

        if ($stmt_update->execute()) {
            echo json_encode(['success' => true, 'new_status' => 'completed']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database update failed.']);
        }
        $stmt_update->close();

    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

$conn->close();
?>