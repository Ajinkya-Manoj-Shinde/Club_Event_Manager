<?php
session_start();

// Check login & role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

// DB connection
$conn = new mysqli("localhost", "lag", "1011", "users");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get event ID and student username
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$student_uname = $_SESSION['username'];

// Check if student is already registered for this event
$check_stmt = $conn->prepare("SELECT * FROM event_registrations WHERE student_uname = ? AND event_id = ?");
$check_stmt->bind_param("si", $student_uname, $event_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    echo "<div style='padding: 20px; color: red;'>You have already registered for this event.</div>";
    header("refresh:3; url=studentdashboard.php");
} else {
    // Register the student for the event
    $insert_stmt = $conn->prepare("INSERT INTO event_registrations (student_uname, event_id) VALUES (?, ?)");
    $insert_stmt->bind_param("si", $student_uname, $event_id);
    if ($insert_stmt->execute()) {
        echo "<div style='padding: 20px; color: green;'>Registration successful!</div>";
        header("refresh:3; url=studentdashboard.php");
    } else {
        echo "<div style='padding: 20px; color: red;'>Registration failed. Please try again.</div>";
        header("refresh:3; url=studentdashboard.php");
    }
    $insert_stmt->close();
}

$check_stmt->close();
$conn->close();
?>
