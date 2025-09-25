<?php
session_start();

// Check if the admin is logged in via the new session variable
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// DB connection
$conn = new mysqli("localhost", "lag", "1011", "users");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle user approval
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $stmt = $conn->prepare("UPDATE user SET is_approved = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php");
    exit();
}

// Fetch unapproved users
$unapproved_users = $conn->query("SELECT id, uname, role FROM user WHERE is_approved = 0");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Panel</a>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>Welcome, Admin!</h2>
        <h4 class="mt-4">Users Awaiting Approval</h4>
        <table class="table table-bordered bg-white">
            <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
            <?php while($row = $unapproved_users->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['uname']; ?></td>
                <td><?php echo $row['role']; ?></td>
                <td>
                    <a href="?approve=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">Approve</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
