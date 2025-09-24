<?php
session_start();

// Check login & role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'club') {
    header("Location: login.php");
    exit();
}

// DB connection
$conn = new mysqli("localhost", "aditya", "8767", "users");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle Event Creation
if (isset($_POST['create'])) {
    $title = trim($_POST['title']);
    $location = trim($_POST['location']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $created_by = $_SESSION['username'];

    $stmt = $conn->prepare("INSERT INTO events (title, location, event_date, event_time, created_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $location, $date, $time, $created_by);
    $stmt->execute();
    $stmt->close();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ? AND created_by = ?");
    $stmt->bind_param("is", $id, $_SESSION['username']);
    $stmt->execute();
    $stmt->close();
}

// Handle Edit
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $title = trim($_POST['title']);
    $location = trim($_POST['location']);
    $date = $_POST['date'];
    $time = $_POST['time'];

    $stmt = $conn->prepare("UPDATE events SET title=?, location=?, event_date=?, event_time=? WHERE id=? AND created_by=?");
    $stmt->bind_param("ssssds", $title, $location, $date, $time, $id, $_SESSION['username']);
    $stmt->execute();
    $stmt->close();
}

// Get Events
$events = $conn->query("SELECT * FROM events WHERE created_by = '" . $_SESSION['username'] . "' ORDER BY event_date, event_time");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Club Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h2>Welcome, <?php echo $_SESSION['username']; ?> (Club)</h2>
    <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>

    <h4 class="mt-4">Create Event</h4>
    <form method="POST" class="card card-body">
        <div class="mb-2">
            <label>Event Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-2">
            <label>Location</label>
            <input type="text" name="location" class="form-control" required>
        </div>
        <div class="mb-2">
            <label>Date</label>
            <input type="date" name="date" class="form-control" required>
        </div>
        <div class="mb-2">
            <label>Time</label>
            <input type="time" name="time" class="form-control" required>
        </div>
        <button type="submit" name="create" class="btn btn-primary">Create Event</button>
    </form>

    <h4 class="mt-4">My Events</h4>
    <table class="table table-bordered bg-white">
        <tr>
            <th>Title</th>
            <th>Location</th>
            <th>Date</th>
            <th>Time</th>
            <th>Actions</th>
        </tr>
        <?php while($row = $events->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['title']; ?></td>
            <td><?php echo $row['location']; ?></td>
            <td><?php echo $row['event_date']; ?></td>
            <td><?php echo $row['event_time']; ?></td>
            <td>
                <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this event?');">Delete</a>
                <!-- Simple inline edit form -->
                <form method="POST" style="display:inline-block;">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <input type="text" name="title" value="<?php echo $row['title']; ?>" required>
                    <input type="text" name="location" value="<?php echo $row['location']; ?>" required>
                    <input type="date" name="date" value="<?php echo $row['event_date']; ?>" required>
                    <input type="time" name="time" value="<?php echo $row['event_time']; ?>" required>
                    <button type="submit" name="update" class="btn btn-warning btn-sm">Update</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
