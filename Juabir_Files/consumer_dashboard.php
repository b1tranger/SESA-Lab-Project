



<!-- using some AI it make just for the check of database and linking trst of different page -->


<?php
session_start();

// Redirect if not logged in or not a Consumer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Consumer') {
    header("Location: index.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "support_hero");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch users
$result = $conn->query("SELECT * FROM User");

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Consumer Dashboard</title>
</head>
<body>

    <h1>Welcome, <?php echo $_SESSION['user_name']; ?></h1>

    <h2>All Users:</h2>
    <table border="1">
        <tr>
            <th>User_ID</th><th>User_Type</th><th>User_Name</th><th>User_Phone</th><th>User_Email</th><th>User_Address</th><th>User_Blood_Group</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['User_ID']; ?></td>
                <td><?php echo $row['User_type']; ?></td>
                <td><?php echo $row['User_Name']; ?></td>
                <td><?php echo $row['User_Phone']; ?></td>
                <td><?php echo $row['User_Email']; ?></td>
                <td><?php echo $row['User_address']; ?></td>
                <td><?php echo $row['User_Blood_Group']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>
