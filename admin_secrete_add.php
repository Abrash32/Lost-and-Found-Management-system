<?php
session_start();
include("conn.php");

// Check if the primary admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_id'] != 1) { // Assuming the primary admin has id 1
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Insert new admin
    $sql = "INSERT INTO admins (username, password) VALUES (:username, :password)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':username' => $username, ':password' => $password]);

    echo "<script>alert('New admin added successfully.');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .navbar {
            z-index: 1050;
            background-color: #f8f9fa;
            border-bottom: 1px solid #ddd;
        }

        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
            padding-top: 56px;
            border-right: 1px solid #ddd;
            background-color: #343a40;
        }

        .sidebar-sticky {
            position: sticky;
            top: 0;
            height: calc(100vh - 56px);
            padding-top: 0.5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .sidebar .nav-link {
            font-weight: 500;
            color: #ffffff;
        }

        .sidebar .nav-link.active {
            color: #0d6efd;
        }

        .content-area {
            margin-left: 250px;
            padding: 40px;
        }

        .navbar-toggler {
            z-index: 1051;
        }

        @media (max-width: 767.98px) {
            .sidebar {
                width: 100%;
                height: auto;
                border-right: none;
                position: relative;
            }
            .content-area {
                margin-left: 0;
            }
        }

        .form-container {
            max-width: 600px;
            margin: 0 auto;
        }

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="sidebar">
        <div class="sidebar-sticky">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="admin_dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="adminreport.php">Report Found Items</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="adminreview.php">Review Reported Found Items</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_found.php">Approve Claims</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="adminview.php">Reported Missing Items</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_secrete_add.php">Add Admin</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="analytics.php">Report & Analytics</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="content-area">
        <h2 class="mb-4">Add New Admin</h2>
        <div class="form-container">
            <div class="card p-4">
                <form method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add Admin</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
