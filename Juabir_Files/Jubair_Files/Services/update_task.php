<?php
session_start();
include("../connection.php"); // Adjust path if needed

// --- SECURITY CHECK ---
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// --- MAIN API LOGIC ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    
    header('Content-Type: application/json');
    $action = $_POST['action'];

    switch ($action) {
        
        // --- ACTION: TOGGLE (Check/Uncheck) ---
        case 'toggle':
            if (isset($_POST['task_id'], $_POST['is_completed'])) {
                $task_id = (int) $_POST['task_id'];
                $is_completed = (int) $_POST['is_completed']; // 1 or 0
                
                $stmt = $conn->prepare("UPDATE admin_tasks SET is_completed = ? WHERE task_id = ?");
                $stmt->bind_param("ii", $is_completed, $task_id);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Database error']);
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            }
            break;

        // --- ACTION: EDIT (Update text) ---
        case 'edit':
            if (isset($_POST['task_id'], $_POST['task_text'])) {
                $task_id = (int) $_POST['task_id'];
                $task_text = trim($_POST['task_text']);

                if (!empty($task_text)) {
                    $stmt = $conn->prepare("UPDATE admin_tasks SET task_text = ? WHERE task_id = ?");
                    $stmt->bind_param("si", $task_text, $task_id);
                    
                    if ($stmt->execute()) {
                        echo json_encode(['success' => true]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Database error']);
                    }
                    $stmt->close();
                } else {
                     echo json_encode(['success' => false, 'message' => 'Task text cannot be empty']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            }
            break;

        // --- ACTION: DELETE (Remove task) ---
        case 'delete':
            if (isset($_POST['task_id'])) {
                $task_id = (int) $_POST['task_id'];

                $stmt = $conn->prepare("DELETE FROM admin_tasks WHERE task_id = ?");
                $stmt->bind_param("i", $task_id);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Database error']);
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            }
            break;

        // --- DEFAULT: Invalid Action ---
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} else {
    // Not a POST request
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>
