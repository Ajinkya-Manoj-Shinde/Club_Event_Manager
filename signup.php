<?php
$host = "localhost";
$db_user = "lag";
$db_pass = "1011";
$db_name = "users";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    $check = $conn->prepare("SELECT * FROM user WHERE uname = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows > 0) {
        $error = "Username already taken!";
    } else {
        $stmt = $conn->prepare("INSERT INTO user (uname, pass, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $role);
        if ($stmt->execute()) {
            $success = "Signup successful! Redirecting to login...";
            header("refresh:2; url=login.php");
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    $check->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="text-center mb-4">Sign Up</h3>
                    <?php if (!empty($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
                    <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="">-- Select Role --</option>
                                <option value="student">Student</option>
                                <option value="club">Club</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Sign Up</button>
                    </form>
                    <p class="mt-3 text-center">Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
