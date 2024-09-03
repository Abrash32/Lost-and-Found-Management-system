<?php
session_start();
include("conn.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admins WHERE username = :username LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':username' => $username]);
    $admin = $stmt->fetch();

    if ($admin && $password == $admin['password']) {
        $_SESSION['admin_id'] = $admin['id'];
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "<script>alert('Invalid username or password.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: #ffffff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-container img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 1rem;
        }
        .login-container h2 {
            margin-bottom: 1.5rem;
            font-weight: 700;
            color: #333333;
        }
        .form-label {
            color: #555555;
            font-weight: 600;
        }
        .form-control {
            border-radius: 4px;
        }
        .btn-primary {
            width: 100%;
            padding: 0.75rem;
            border-radius: 4px;
            font-size: 1rem;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        @media (max-width: 576px) {
            .login-container {
                padding: 1.5rem;
            }
            .login-container img {
                width: 80px;
                height: 80px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="./img/admin.png" alt="Admin Picture">
        <h2>Admin Login</h2>
        <form method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</body>
</html>
