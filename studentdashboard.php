<?php
session_start();

// Check login & role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

// DB connection
$conn = new mysqli("localhost", "aditya", "8767", "users");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch all events
$events = $conn->query("SELECT * FROM events ORDER BY event_date, event_time");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h2>Welcome, <?php echo $_SESSION['username']; ?> (Student)</h2>
    <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>

    <h4 class="mt-4">Upcoming Events</h4>
    <table class="table table-bordered bg-white">
        <tr>
            <th>Title</th>
            <th>Location</th>
            <th>Date</th>
            <th>Time</th>
            <th>Organized By</th>
        </tr>
        <?php while($row = $events->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['title']; ?></td>
            <td><?php echo $row['location']; ?></td>
            <td><?php echo $row['event_date']; ?></td>
            <td><?php echo $row['event_time']; ?></td>
            <td><?php echo $row['created_by']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
