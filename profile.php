<?php
include("conn.php");

// Assume the user is logged in and their ID is stored in a session variable
session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username']; // Make sure to set this in login.php
} else {
    header("Location: login.php");
    exit;
}

// Fetch user data
$sql = "SELECT * FROM user_reg WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle profile update
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $profilePhoto = $_FILES['profilePhoto'];

    // Update the user information
    $update_sql = "UPDATE user_reg SET name = :name, email = :email, phone = :phone WHERE id = :id";
    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->execute([
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'id' => $user_id
    ]);

    // Handle profile photo upload if a file was uploaded
    if ($profilePhoto['size'] > 0) {
        $photoPath = 'uploads/' . basename($profilePhoto['name']);
        move_uploaded_file($profilePhoto['tmp_name'], $photoPath);

        $update_photo_sql = "UPDATE user_reg SET profile_photo = :profile_photo WHERE id = :id";
        $update_photo_stmt = $pdo->prepare($update_photo_sql);
        $update_photo_stmt->execute([
            'profile_photo' => $photoPath,
            'id' => $user_id
        ]);
    }

    // Redirect or refresh the page after updating
    header('Location: profile.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Lost & Found</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: background-color 0.3s;
        }
        .navbar {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        .sidebar {
            position: fixed;
            top: 56px;
            left: 0;
            height: 100%;
            width: 240px;
            background-color: #343a40;
            padding-top: 20px;
            z-index: 999;
            transition: transform 0.3s ease;
        }
        .sidebar a {
            text-decoration: none;
            color: #fff;
            padding: 10px;
            display: block;
            margin: 10px 0;
            transition: background-color 0.3s, padding-left 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #495057;
            padding-left: 20px;
        }
        .sidebar a i {
            margin-right: 10px;
        }
        .content {
            margin-left: 240px;
            padding: 20px;
            margin-top: 56px;
            flex: 1;
        }
        footer {
            background-color: #343a40;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            margin-top: auto;
            width: 100%;
        }
        @media (max-width: 992px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                display: none;
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .content {
                margin-left: 0;
            }
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fa fa-compass"></i> Lost & Found</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-bell"></i><span class="badge bg-danger">3</span>
                        </a> -->
                        <!-- <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                            <li><a class="dropdown-item" href="#">3 new found items</a></li>
                            <li><a class="dropdown-item" href="#">2 items claimed</a></li>
                        </ul> -->
                    <!-- </li> -->
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php"><i class="fa fa-user"></i> Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a>
        <a href="report.php"><i class="fa fa-pen"></i> Report Found Item</a>
        <a href="claim.php"><i class="fa fa-hand-holding"></i> Claim Items</a>
        <a href="lost_item.php"><i class="fa fa-box-open"></i> Report Lost Items</a>
        <a href="profile.php" class="active"><i class="fa fa-user"></i> Profile</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="container-fluid">
            <h2 class="mt-4">Profile</h2>
            <div class="form-container">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="profilePhoto" class="form-label">Profile Photo</label>
                        <input type="file" class="form-control" id="profilePhoto" name="profilePhoto">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Lost & Found Management System. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
