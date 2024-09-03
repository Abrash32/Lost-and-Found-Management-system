<?php
session_start();
include("conn.php");

// Handle search
$searchQuery = '';
if (isset($_POST['search'])) {
    $searchQuery = $_POST['search_query'];
}

// Fetch reviewed items from the report table with search functionality
$sqlItems = "
    SELECT id AS item_id, item_name, item_description, location, item_image
    FROM report
    WHERE report_status = 'reviewed'
    AND (item_name LIKE :searchQuery 
         OR location LIKE :searchQuery 
         OR item_description LIKE :searchQuery);
";

$stmtItems = $pdo->prepare($sqlItems);
$stmtItems->execute([':searchQuery' => '%' . $searchQuery . '%']);
$items = $stmtItems->fetchAll();

// Handle claim submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['claim'])) {
    $lostItemId = $_POST['item_id'];
    $userId = $_SESSION['user_id'];
    $idCard = '';

    // Handle ID card upload
    if (isset($_FILES['id_card']) && $_FILES['id_card']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['id_card']['tmp_name'];
        $fileName = $_FILES['id_card']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $uploadFileDir = './uploads/id_cards/';
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $idCard = $newFileName;
        } else {
            echo "<script>alert('Error uploading ID card.');</script>";
        }
    }

    // Insert a new claim with the ID card
    $sql = "INSERT INTO item_claims (lost_item_id, user_id, item_picture) VALUES (:lostItemId, :userId, :idCard)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':lostItemId' => $lostItemId, ':userId' => $userId, ':idCard' => $idCard]);
        echo "<script>alert('Claim submitted.');</script>";
    } catch (PDOException $e) {
        echo 'SQL Error: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Claim Approved Items</title>
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

        .sidebar a:hover,
        .sidebar a.active {
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

        .dark-mode .navbar,
        .dark-mode .sidebar,
        .dark-mode footer {
            background-color: #212529;
            color: #f8f9fa;
        }

        footer {
            background-color: #343a40;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            margin-top: auto;
            width: 100%;
        }

        .item-image {
            max-width: 100px;
            height: auto;
            cursor: pointer;
        }

        .modal-img {
            max-width: 100%;
            height: auto;
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
        <a href="dashboard.php" class="active"><i class="fa fa-dashboard"></i> Dashboard</a>
        <a href="report.php"><i class="fa fa-pen"></i> Report Found Item</a>
        <a href="claim.php"><i class="fa fa-hand-holding"></i> Claim Items</a>
        <a href="lost_item.php"><i class="fa fa-box-open"></i> Report Lost Items</a>
        <a href="profile.php"><i class="fa fa-user"></i> Profile</a>
    </div>

    <!-- Main Content -->
    <div class="content mt-5">
        <div class="container-fluid">
            <h2 class="mt-4">Approved Items for Claim</h2>

            <!-- Search Form -->
            <form method="POST" action="claim.php" class="mb-4">
                <div class="input-group">
                    <input type="text" name="search_query" class="form-control" placeholder="Search by name, location, or description" value="<?php echo htmlspecialchars($searchQuery); ?>">
                    <button class="btn btn-primary" type="submit" name="search">Search</button>
                </div>
            </form>

            <?php if (count($items) > 0): ?>
                <div class="row">
                    <?php foreach ($items as $item): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($item['item_name']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($item['item_description']); ?></p>
                                    <p class="card-text"><strong>Location:</strong> <?php echo htmlspecialchars($item['location']); ?></p>
                                    <p class="card-text">
                                        <?php if (!empty($item['item_image']) && file_exists('./uploads/items/' . $item['item_image'])): ?>
                                            <img src="./uploads/items/<?php echo htmlspecialchars($item['item_image']); ?>" alt="Item Image" class="item-image" data-bs-toggle="modal" data-bs-target="#imageModal<?php echo htmlspecialchars($item['item_id']); ?>">
                                        <?php else: ?>
                                            <i class="fa fa-image"></i> No Image Available
                                        <?php endif; ?>
                                    </p>
                                    <form method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['item_id']); ?>">
                                        <div class="mb-3">
                                            <label for="id_card" class="form-label">Upload ID Card</label>
                                            <input type="file" class="form-control" name="id_card" id="id_card" required>
                                        </div>
                                        <button type="submit" name="claim" class="btn btn-primary">Claim Item</button>
                                    </form>
                                </div>
                            </div>

                            <!-- Modal for Image Preview -->
                            <div class="modal fade" id="imageModal<?php echo htmlspecialchars($item['item_id']); ?>" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="imageModalLabel">Item Image</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <img src="./uploads/items/<?php echo htmlspecialchars($item['item_image']); ?>" alt="Item Image" class="modal-img">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info" role="alert">
                    No items found.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Lost & Found System. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
    <script>
        // Dark mode toggle
        document.getElementById('darkModeToggle').addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
        });
    </script>
</body>
</html>
