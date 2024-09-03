<?php
session_start();
include("conn.php");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handling lost item report submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture form data
    $userId = $_SESSION['user_id'];
    $itemName = $_POST['itemName'];
    $itemDescription = $_POST['itemDescription'];
    $lostDate = $_POST['lostDate'];
    $location = $_POST['location'];
    $contact = $_POST['contact'];
    $itemImage = $_FILES['itemImage']['name'];

    // Save item image to a directory
    if (!empty($itemImage)) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($itemImage);
        move_uploaded_file($_FILES['itemImage']['tmp_name'], $targetFile);
    }

    // Prepare SQL query
    $sql = "INSERT INTO lost_items (user_id, item_name, item_description, lost_date, location, contact, item_image) 
            VALUES (:userId, :itemName, :itemDescription, :lostDate, :location, :contact, :itemImage)";
    $stmt = $pdo->prepare($sql);

    // Execute the query with the captured data
    $stmt->execute([
        ':userId' => $userId,
        ':itemName' => $itemName,
        ':itemDescription' => $itemDescription,
        ':lostDate' => $lostDate,
        ':location' => $location,
        ':contact' => $contact,
        ':itemImage' => $itemImage
    ]);

    // Display success message and redirect to homepage
    echo "<script>alert('Don't Fret... we would notify you when an item you reported is being found!'); window.location.href = 'dashboard.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Lost Item - Lost & Found</title>
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
        .form-container {
            max-width: 600px;
            margin: auto;
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

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fa fa-compass"></i> Lost & Found</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="fa fa-home"></i> Home</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-search"></i> Search</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-bell"></i> Notifications</a>
                    </li> -->
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
        <a href="dashboard.php" class="active"><i class="fa fa-dashboard"></i> Dashboard</a>
        <a href="report.php"><i class="fa fa-pen"></i> Report Found Item</a>
        <a href="claim.php"><i class="fa fa-hand-holding"></i> Claim Items</a>
        <a href="lost_item.php"><i class="fa fa-box-open"></i> Report Lost Items</a>
        <a href="profile.php"><i class="fa fa-user"></i> Profile</a>
        <!-- <a href="faq.php"><i class="fa fa-question-circle"></i> FAQs</a>
        <a href="support.php"><i class="fa fa-life-ring"></i> Support</a> -->
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="container-fluid">
            <h2 class="mb-4"><i class="fa fa-pen"></i> Report a Lost Item</h2>
            <div class="form-container">
                <form id="lostItemForm" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="itemName" class="form-label">Item Name</label>
                        <input type="text" class="form-control" id="itemName" name="itemName" required>
                    </div>
                    <div class="mb-3">
                        <label for="itemDescription" class="form-label">Item Description</label>
                        <textarea class="form-control" id="itemDescription" name="itemDescription" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="lostDate" class="form-label">Date Lost</label>
                        <input type="date" class="form-control" id="lostDate" name="lostDate" required>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact" class="form-label">Contact Information</label>
                        <input type="text" class="form-control" id="contact" name="contact" required>
                    </div>
                    <div class="mb-3">
                        <label for="itemImage" class="form-label">Upload Item Image</label>
                        <input type="file" class="form-control" id="itemImage" name="itemImage">
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane"></i> Submit</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Lost & Found System. All Rights Reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const sidebar = document.querySelector('.sidebar');
            const toggleSidebar = document.querySelector('.navbar-toggler');

            toggleSidebar.addEventListener('click', function () {
                sidebar.classList.toggle('show');
            });

            // Show success modal if the item was successfully reported
            <?php if (isset($_GET['success'])): ?>
            new bootstrap.Modal(document.getElementById('successModal')).show();
            <?php endif; ?>
        });
    </script>
</body>
</html>
