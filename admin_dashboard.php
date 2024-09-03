<?php
session_start();
include("conn.php");

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch admin details
$adminId = $_SESSION['admin_id'];
$sqlAdmin = "SELECT id, username FROM admins WHERE id = :adminId";
$stmtAdmin = $pdo->prepare($sqlAdmin);
$stmtAdmin->execute([':adminId' => $adminId]);
$admin = $stmtAdmin->fetch();

// Fetch claims
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$sqlClaims = "SELECT ic.*, li.item_name, li.item_description, ur.name, ur.matric, ur.phone, ur.profile_photo 
              FROM item_claims ic
              JOIN lost_items li ON ic.lost_item_id = li.id
              JOIN user_reg ur ON ic.user_id = ur.id
              WHERE ur.matric LIKE :searchTerm";
$stmtClaims = $pdo->prepare($sqlClaims);
$stmtClaims->execute([':searchTerm' => '%' . $searchTerm . '%']);
$claims = $stmtClaims->fetchAll();

// Update claim status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $claimId = $_POST['claim_id'];
    $newStatus = $_POST['status']; // Either 'approved' or 'not approved'

    // Update the claim status
    $sqlUpdate = "UPDATE item_claims SET status = :status WHERE id = :claimId";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->execute([':status' => $newStatus, ':claimId' => $claimId]);

    echo "<script>alert('Claim status updated.');</script>";
}

// Example PHP functions (expand these with actual functionality)
function getTotalReportedItems() {
    global $pdo;
    $sql = "SELECT COUNT(*) FROM lost_items";
    $stmt = $pdo->query($sql);
    return $stmt->fetchColumn();
}

function getTotalApprovedClaims() {
    global $pdo;
    $sql = "SELECT COUNT(*) FROM item_claims WHERE status = 'approved'";
    $stmt = $pdo->query($sql);
    return $stmt->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            background-image: url("./img/admindashboard.jpg");
            background-position: center;
            background-size: cover;
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
            /* background-image: url("./img/admindashboard.jpg");
            background-position: center;
            background-size: cover; */
        }
            .content-area h1{
                 color: white;
            }
               
        .navbar-toggler {
            z-index: 1051; /* Ensure toggler is on top */
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
                background-image: url("./img/admindashboard.jpg");
            background-position: center;
            background-size: cover;
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
                    <a class="nav-link" href="manage_users.php">Manage Users</a>
                </li>
                <?php if ($admin['id'] == 1) { ?>
                <li class="nav-item">
                    <a class="nav-link" href="admin_secrete_add.php">Add Admin</a>
                </li>
                <?php } ?>
                <li class="nav-item">
                    <a class="nav-link" href="analytics.php">Report & Analytics</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="content-area">
        <h1>Admin Dashboard</h1>
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Reported Found Items</h5>
                        <p class="card-text">Total Items: <?php echo getTotalReportedItems(); ?></p>
                        <a href="adminreview.php" class="btn btn-primary">Review Items</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Approve Claims</h5>
                        <p class="card-text">Total Approved Claims: <?php echo getTotalApprovedClaims(); ?></p>
                        <a href="manage_found.php" class="btn btn-primary">Manage Claims</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- <h2>Claim Requests</h2>
        <form method="get" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by matric number" value="<?php echo htmlspecialchars($searchTerm); ?>">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Item Description</th>
                    <th>Claimed By</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($claims as $claim): ?>
                <tr>
                    <td><?php echo htmlspecialchars($claim['id']); ?></td>
                    <td><?php echo htmlspecialchars($claim['item_name']); ?></td>
                    <td><?php echo htmlspecialchars($claim['item_description']); ?></td>
                    <td><?php echo htmlspecialchars($claim['name']); ?> (<?php echo htmlspecialchars($claim['matric']); ?>)</td>
                    <td><?php echo htmlspecialchars($claim['status']); ?></td>
                    <td>
                        <form method="post" class="d-inline">
                            <input type="hidden" name="claim_id" value="<?php echo htmlspecialchars($claim['id']); ?>">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="approved" <?php echo $claim['status'] === 'approved' ? 'selected' : ''; ?>>Approve</option>
                                <option value="not approved" <?php echo $claim['status'] === 'not approved' ? 'selected' : ''; ?>>Reject</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-warning btn-sm mt-2">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div> -->

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
