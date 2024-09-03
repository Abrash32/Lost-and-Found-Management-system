<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'User'; // Ensure this is set during login

    // Database connection
    $conn = new mysqli("localhost", "root", "", "lost_found_db");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch the user's profile picture
    $sql = "SELECT profile_photo FROM user_reg WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $profile_photo = $row['profile_photo'];
        } else {
            // If no profile picture is found, use a default image
            $profile_photo = './img/default-avatar.png';
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
        exit;
    }

    // Fetch approved claims from the item_claims table
    $sql = "SELECT ic.item_picture, li.item_name, ic.status
            FROM item_claims ic
            JOIN lost_items li ON ic.lost_item_id = li.id
            WHERE ic.user_id = ? AND ic.status = 'approved'";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $approved_claims = [];
        while ($row = $result->fetch_assoc()) {
            $approved_claims[] = $row;
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
        exit;
    }

    $conn->close();
} else {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Lost & Found</title>
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
        .sidebar .user-profile {
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar .user-profile img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }
        .sidebar .user-profile h5 {
            color: #fff;
            margin: 0;
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
        .dark-mode {
            background-color: #343a40;
            color: #f8f9fa;
        }
        .dark-mode .navbar, .dark-mode .sidebar, .dark-mode footer {
            background-color: #212529;
            color: #f8f9fa;
        }
        .notification-list {
            list-style: none;
            padding: 0;
        }
        .notification-item {
            background-color: #fff;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            transition: background-color 0.3s;
        }
        .notification-item:hover {
            background-color: #e9ecef;
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
    </style>
</head>
<body>

    <!-- Static Top Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fa fa-compass"></i> Lost & Found</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="darkModeToggle"><i class="fa fa-moon"></i> Dark Mode</a>
                    </li>
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
        <!-- User Profile Section -->
        <div class="user-profile">
            <img src="<?php echo htmlspecialchars($profile_photo); ?>" alt="User Profile Picture">
            <h5><?php echo htmlspecialchars($username); ?></h5>
        </div>

        <a href="dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a>
        <a href="report.php"><i class="fa fa-pen"></i> Report Found Item</a>
        <a href="claim.php"><i class="fa fa-hand-holding"></i> Claim Items</a>
        <a href="lost_item.php"><i class="fa fa-box-open"></i> Report Lost Items</a>
        <a href="profile.php"><i class="fa fa-user"></i> Profile</a>
    </div>

    <!-- Main Content -->
    <div class="content mt-5">
        <div class="container-fluid">
            <h2 class="welcome-header"><i class="fa fa-bell"></i> Notifications</h2>
            
            <!-- Notifications List -->
            <ul class="notification-list">
                <?php if (!empty($approved_claims)): ?>
                    <?php foreach ($approved_claims as $claim): ?>
                        <li class="notification-item">
                            <strong><?php echo htmlspecialchars($claim['item_name']); ?></strong> - Your claim has been approved. Please come to the security unit to collect your item.
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="notification-item">No approved claims yet.</li>
                <?php endif; ?>
            </ul>

        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Lost & Found. All Rights Reserved.</p>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
    <script>
        // Dark mode toggle
        document.getElementById('darkModeToggle').addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
        });
    </script>
</body>
</html>
