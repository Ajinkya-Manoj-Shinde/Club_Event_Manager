<?php
session_start();

// Only allow clubs to access this page
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'club') {
    header("Location: login.php");
    exit();
}

// DB connection
$conn = new mysqli("localhost", "lag", "1011", "users");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get the event ID from the URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch event details (only if it belongs to the current user)
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ? AND created_by = ?");
$stmt->bind_param("is", $id, $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    echo "<div style='padding: 20px;'>Event not found or access denied.</div>";
    exit();
}

// Handle the form submission
if (isset($_POST['update'])) {
    $title = trim($_POST['title']);
    $location = trim($_POST['location']);
    $date = $_POST['date'];
    $time = $_POST['time'];

    $updateStmt = $conn->prepare("UPDATE events SET title = ?, location = ?, event_date = ?, event_time = ? WHERE id = ? AND created_by = ?");
    $updateStmt->bind_param("ssssis", $title, $location, $date, $time, $id, $_SESSION['username']);
    $updateStmt->execute();

    // Redirect back to the clubdashboard
    header("Location: clubdashboard.php");

}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Event Manager</a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h3>Edit Event</h3>
        <form method="POST" class="card card-body mt-3">
            <div class="mb-3">
                <label>Event Title</label>
                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($event['title']); ?>" required>
            </div>
            <div class="mb-3">
                <label>Location</label>
                <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($event['location']); ?>" required>
            </div>
            <div class="mb-3">
                <label>Date</label>
                <input type="date" name="date" class="form-control" value="<?php echo $event['event_date']; ?>" required>
            </div>
            <div class="mb-3">
                <label>Time</label>
                <input type="time" name="time" class="form-control" value="<?php echo $event['event_time']; ?>" required>
            </div>
            <button type="submit" name="update" class="btn btn-success">Update Event</button>
            <a href="clubdashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
