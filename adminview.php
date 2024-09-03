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

// Fetch missing items reported by users
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$sqlItems = "SELECT li.*, ur.name AS reported_by 
             FROM lost_items li 
             JOIN user_reg ur ON li.user_id = ur.id
             WHERE li.item_name LIKE :searchTerm 
             OR li.item_description LIKE :searchTerm 
             OR li.location LIKE :searchTerm";
$stmtItems = $pdo->prepare($sqlItems);
$stmtItems->execute([':searchTerm' => '%' . $searchTerm . '%']);
$lost_items = $stmtItems->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reported Missing Items</title>
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

        .table img {
            max-width: 100px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Review Found items</a>
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
        <h1>Reported Missing Items</h1>
        
        <!-- Search form -->
        <form method="get" class="mb-4">
            <input type="text" name="search" placeholder="Search by item name, description, or location" class="form-control" value="<?php echo htmlspecialchars($searchTerm); ?>">
        </form>

        <!-- Table displaying the reported missing items -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Item Name</th>
                    <th>Description</th>
                    <th>Lost Date</th>
                    <th>Location</th>
                    <th>Contact</th>
                    <th>Reported By</th>
                    <th>Image</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lost_items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['id']); ?></td>
                    <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['item_description']); ?></td>
                    <td><?php echo htmlspecialchars($item['lost_date']); ?></td>
                    <td><?php echo htmlspecialchars($item['location']); ?></td>
                    <td><?php echo htmlspecialchars($item['contact']); ?></td>
                    <td><?php echo htmlspecialchars($item['reported_by']); ?></td>
                    <td>
                        
                        <?php if (!empty($item['item_image'])): ?>
                            <img src="<?php echo htmlspecialchars($item['item_image']); ?>" alt="Item Image" class="img-thumbnail">
                        <?php else: ?>
                            <span>No Image Available</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
