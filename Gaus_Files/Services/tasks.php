<?php
session_start();
include("../connection.php"); // Adjust path if needed

// --- SECURITY CHECK ---
// If user is not an admin, redirect them to the homepage
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header('Location: ../Home_Page/index.php'); // Adjust path if needed
    exit;
}

$user_type = $_SESSION['user_type'];

// --- HANDLE NEW TASK (Form submission on page load) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_task'])) {
    $new_task_text = trim($_POST['task_text']);
    if (!empty($new_task_text)) {
        $stmt = $conn->prepare("INSERT INTO tasks (task_text) VALUES (?)");
        $stmt->bind_param("s", $new_task_text);
        $stmt->execute();
        $stmt->close();
        // Redirect to self to prevent form resubmission
        header("Location: tasks.php");
        exit;
    }
}

// --- FETCH ALL TASKS ---
// Order by completion status (incomplete first), then by date (newest first)
$sql = "SELECT * FROM tasks ORDER BY is_completed ASC, date_created DESC";
$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Task List</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Link to the new CSS file -->
    <link rel="stylesheet" href="tasks.css">
    <!-- Font Awesome for icons (used in your index.php) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <div class="task-list-container">
        <header class="task-list-header">
            <h1>Admin Task List</h1>
            <a href="../Home_Page/index.php" class="btn btn-back">Back to Home</a>
            <?php if ($user_type == 'admin'): ?>
                <a href="../Home_Page/admin.php" class="btn btn-back">Back to Dashboard</a>
            <?php endif ?>

        </header>

        <!-- "Add New Task" Form -->
        <form method="POST" action="tasks.php" class="add-task-form">
            <div class="form-group">
                <input type="text" name="task_text" placeholder="Add a new task..." required>
                <button type="submit" name="add_task" class="btn btn-add">Add Task</button>
            </div>
        </form>

        <!-- The Task List -->
        <main class="task-list-main">
            <ul class="task-list" id="task-list-container">
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $task_id = $row['task_id'];
                        $task_text = htmlspecialchars($row['task_text']);
                        $is_completed = (bool) $row['is_completed'];
                        ?>

                        <!-- Single Task Item -->
                        <li class="task-item <?php echo $is_completed ? 'completed' : ''; ?>"
                            data-task-id="<?php echo $task_id; ?>">

                            <!-- Checkbox -->
                            <input type="checkbox" class="task-checkbox" <?php echo $is_completed ? 'checked' : ''; ?>>

                            <!-- Task Content (Text or Edit Form) -->
                            <div class="task-content">
                                <span class="task-text"><?php echo $task_text; ?></span>

                                <!-- Hidden Edit Form -->
                                <form class="edit-form" style="display: none;">
                                    <input type="text" class="edit-input" value="<?php echo $task_text; ?>">
                                    <button type="submit" class="btn btn-save">Save</button>
                                    <button type="button" class="btn btn-cancel">Cancel</button>
                                </form>
                            </div>

                            <!-- Action Buttons -->
                            <div class="task-actions">
                                <button class="btn btn-edit" title="Edit"><i class="fa-solid fa-pen"></i></button>
                                <button class="btn btn-delete" title="Delete"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </li>

                        <?php
                    } // End while loop
                } else {
                    echo '<p class="no-tasks">No tasks found. Add one above!</p>';
                }
                ?>
            </ul>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const listContainer = document.getElementById('task-list-container');

            // --- Use a single event listener for the entire list ---
            listContainer.addEventListener('click', async (e) => {
                const taskItem = e.target.closest('.task-item');
                if (!taskItem) return; // Clicked outside a task item

                const taskId = taskItem.dataset.taskId;

                // --- 1. Handle Checkbox Click ---
                if (e.target.classList.contains('task-checkbox')) {
                    const isChecked = e.target.checked;

                    // Update UI immediately
                    taskItem.classList.toggle('completed', isChecked);

                    // Move item in the list
                    if (isChecked) {
                        listContainer.appendChild(taskItem); // Move to bottom
                    } else {
                        // Move to top (before the first completed item)
                        const firstCompleted = listContainer.querySelector('.task-item.completed');
                        if (firstCompleted) {
                            listContainer.insertBefore(taskItem, firstCompleted);
                        } else {
                            listContainer.appendChild(taskItem); // No completed items, just append (should be at top anyway)
                        }
                    }

                    // Update Database
                    const formData = new FormData();
                    formData.append('action', 'toggle');
                    formData.append('task_id', taskId);
                    formData.append('is_completed', isChecked ? 1 : 0);

                    await fetch('update_task.php', { method: 'POST', body: formData });
                }

                // --- 2. Handle "Edit" Button Click ---
                if (e.target.closest('.btn-edit')) {
                    e.preventDefault();
                    taskItem.classList.add('editing');
                    taskItem.querySelector('.edit-input').focus();
                }

                // --- 3. Handle "Cancel" Button Click ---
                if (e.target.closest('.btn-cancel')) {
                    e.preventDefault();
                    taskItem.classList.remove('editing');
                    // Reset input value to original text
                    const originalText = taskItem.querySelector('.task-text').textContent;
                    taskItem.querySelector('.edit-input').value = originalText;
                }

                // --- 4. Handle "Delete" Button Click ---
                if (e.target.closest('.btn-delete')) {
                    e.preventDefault();

                    // Custom confirmation modal (since alert/confirm is disallowed)
                    // For this example, we'll just delete immediately. 
                    // Add a custom modal here if confirmation is critical.

                    // Remove from UI
                    taskItem.style.opacity = '0';
                    setTimeout(() => taskItem.remove(), 300);

                    // Update Database
                    const formData = new FormData();
                    formData.append('action', 'delete');
                    formData.append('task_id', taskId);

                    await fetch('update_task.php', { method: 'POST', body: formData });
                }
            });

            // --- Handle "Save" (Form Submission) ---
            listContainer.addEventListener('submit', async (e) => {
                if (e.target.classList.contains('edit-form')) {
                    e.preventDefault(); // Prevent form from reloading page

                    const taskItem = e.target.closest('.task-item');
                    const taskId = taskItem.dataset.taskId;
                    const newText = taskItem.querySelector('.edit-input').value.trim();

                    if (newText) {
                        // Update UI
                        taskItem.querySelector('.task-text').textContent = newText;
                        taskItem.classList.remove('editing');

                        // Update Database
                        const formData = new FormData();
                        formData.append('action', 'edit');
                        formData.append('task_id', taskId);
                        formData.append('task_text', newText);

                        await fetch('update_task.php', { method: 'POST', body: formData });
                    }
                }
            });
        });
    </script>
</body>

</html>