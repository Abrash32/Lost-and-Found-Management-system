<?php
session_start();
include("conn.php");

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_item'])) {
    $itemName = $_POST['item_name'];
    $itemDescription = $_POST['item_description'];
    $locationFound = $_POST['location_found'];
    $contact = $_POST['contact'];

    // Handle file upload
    $targetDir = "uploads/";
    $fileName = basename($_FILES["item_image"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    // Check if file is an image
    $check = getimagesize($_FILES["item_image"]["tmp_name"]);
    if ($check !== false) {
        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES["item_image"]["tmp_name"], $targetFilePath)) {
            // Insert the found item into the `report` table
            $sql = "INSERT INTO report (user_id, item_name, item_description, lost_date, location, contact, item_image, report_status, created_at)
                    VALUES (:user_id, :itemName, :itemDescription, NOW(), :locationFound, :contact, :itemImage, 'pending', NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':user_id' => $_SESSION['admin_id'], // Assuming admin_id is the user_id
                ':itemName' => $itemName,
                ':itemDescription' => $itemDescription,
                ':locationFound' => $locationFound,
                ':contact' => $contact,
                ':itemImage' => $fileName
            ]);

            // Redirect to the claims page
            echo "<script>alert('Item has been successfully reported and posted.'); window.location.href='admindashboard.php';</script>";
        } else {
            echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
        }
    } else {
        echo "<script>alert('File is not an image.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Found Item</title>
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

        .form-control {
            margin-bottom: 15px;
        }

        form {
            width: 50%;
            margin: auto;
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
                    <a class="nav-link active" href="admin_dashboard.php">
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="adminreport.php">
                        Report Found Items
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="adminreview.php">
                        Review Reported Found Items
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_found.php">
                        Approve Claims
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="adminview.php">
                        Reported Missing Items
                    </a>
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
        <h1>Report a Found Item</h1>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="item_name">Item Name</label>
                <input type="text" class="form-control" id="item_name" name="item_name" required>
            </div>
            <div class="form-group">
                <label for="item_description">Item Description</label>
                <textarea class="form-control" id="item_description" name="item_description" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="location_found">Location Found</label>
                <input type="text" class="form-control" id="location_found" name="location_found" required>
            </div>
            <div class="form-group">
                <label for="contact">Contact</label>
                <input type="text" class="form-control" id="contact" name="contact" required>
            </div>
            <div class="form-group">
                <label for="item_image">Upload Image</label>
                <input type="file" class="form-control" id="item_image" name="item_image" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary" name="report_item">Report Item</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
