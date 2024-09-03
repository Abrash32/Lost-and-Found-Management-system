<?php
session_start();
include("conn.php");

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch admin username
$adminId = $_SESSION['admin_id'];
$sqlAdmin = "SELECT username FROM admins WHERE id = :adminId";
$stmtAdmin = $pdo->prepare($sqlAdmin);
$stmtAdmin->execute([':adminId' => $adminId]);
$admin = $stmtAdmin->fetch();

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $userId = $_POST['user_id'];

    // Delete the user
    $sqlDelete = "DELETE FROM user_reg WHERE id = :userId";
    $stmtDelete = $pdo->prepare($sqlDelete);
    $stmtDelete->execute([':userId' => $userId]);

    echo "<script>alert('User deleted.');</script>";
}

// Handle user addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $department = $_POST['department'];
    $matric = $_POST['matric'];

    // Insert the new user
    $sqlAdd = "INSERT INTO user_reg (name, username, password, email, phone, department, matric) VALUES (:name, :username, :password, :email, :phone, :department, :matric)";
    $stmtAdd = $pdo->prepare($sqlAdd);
    $stmtAdd->execute([
        ':name' => $name,
        ':username' => $username,
        ':password' => $password,
        ':email' => $email,
        ':phone' => $phone,
        ':department' => $department,
        ':matric' => $matric
    ]);

    echo "<script>alert('User added.');</script>";
}

// Fetch users
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$sqlUsers = "SELECT * FROM user_reg WHERE name LIKE :searchTerm OR matric LIKE :searchTerm";
$stmtUsers = $pdo->prepare($sqlUsers);
$stmtUsers->execute([':searchTerm' => '%' . $searchTerm . '%']);
$users = $stmtUsers->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .navbar {
            z-index: 1050; /* Ensure navbar is on top */
            background-color: #f8f9fa;
            border-bottom: 1px solid #ddd;
        }

        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: 250px; /* Set width for the sidebar */
            z-index: 1000; /* Ensure sidebar is below the navbar */
            padding-top: 56px; /* Adjust padding to account for the navbar height */
            border-right: 1px solid #ddd;
            background-color: #343a40;
        }

        .sidebar-sticky {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            height: calc(100vh - 56px); /* Adjust height to account for the navbar height */
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

        /* Hover effect for cards */
        .card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Make all content items in a row */
        .content-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .content-item {
            flex: 1;
            min-width: 250px; /* Adjust this value as needed */
        }

        /* Card styles */
        .card {
            transition: transform 0.2s ease-in-out;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px; /* Increase padding for larger size */
        }

        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .card-title {
            font-size: 1.5rem; /* Increase font size */
        }

        .card-text {
            font-size: 1.2rem; /* Increase font size */
        }

        /* Layout adjustment */
        .row .col-md-4 {
            margin-bottom: 20px; /* Add margin between cards */
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Manage Users</a>
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
                <a class="nav-link" href="admin_dashboard.php">Dashboard</a>
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
                <a class="nav-link" href="manage_users.php">Manage Users</a>
            </li>
                    
              
                <!-- <ul class="dropdown-menu" aria-labelledby="manageUsersDropdown">
                    <li><a class="dropdown-item" href="manage_users_list.php">Users List</a></li>
                    <li><a class="dropdown-item" href="manage_users_add.php">Add User</a></li>
                </ul> -->
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
        <h1>Manage Users</h1>
        <form method="GET" action="">
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Search users by name or matric" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>">
                <button class="btn btn-outline-secondary" type="submit">Search</button>
            </div>
        </form>

        <h2>Users List</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Department</th>
                    <th>Matric</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                        <td><?php echo htmlspecialchars($user['department']); ?></td>
                        <td><?php echo htmlspecialchars($user['matric']); ?></td>
                        <td>
                            <!-- Delete button -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                <button type="submit" name="delete_user" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Add New User</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            <div class="mb-3">
                <label for="department" class="form-label">Department</label>
                <input type="text" class="form-control" id="department" name="department" required>
            </div>
            <div class="mb-3">
                <label for="matric" class="form-label">Matric Number</label>
                <input type="text" class="form-control" id="matric" name="matric" required>
            </div>
            <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
