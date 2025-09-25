<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "lag", "1011", "users");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all events
$events = $conn->query("SELECT * FROM events ORDER BY event_date, event_time");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .jumbotron {
            padding: 4rem 2rem;
            margin-bottom: 2rem;
            background-color: #e9ecef;
            border-radius: .3rem;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Event Manager</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                         <a class="btn btn-outline-secondary btn-sm ms-2" href="admin_login.php">Admin</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-secondary btn-sm ms-2" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-secondary btn-sm ms-2" href="signup.php">Signup</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="p-5 mb-4 bg-light rounded-3">
            <div class="container-fluid py-5">
                <h1 class="display-5 fw-bold">Welcome to Event Manager!</h1>
                <p class="col-md-8 fs-4">Your one-stop solution for managing and attending events.</p>
                <a href="signup.php" class="btn btn-primary btn-lg">Sign Up Now</a>
            </div>
        </div>

        <h2 class="mt-4">Upcoming Events</h2>
        <table class="table table-bordered bg-white">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Location</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Organized By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($events->num_rows > 0): ?>
                    <?php while($row = $events->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['location']); ?></td>
                        <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['event_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_by']); ?></td>
                        <td>
                            <?php if (isset($_SESSION['username'])): ?>
                                <a href="register_event.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Register</a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-primary btn-sm">Register for Event</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No upcoming events at the moment.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
