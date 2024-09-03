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

// Fetch reports including ID card
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$sqlReports = "SELECT r.*, ur.name, ur.matric, ur.phone, ur.profile_photo, ic.id_card 
               FROM report r
               JOIN user_reg ur ON r.user_id = ur.id
               LEFT JOIN item_claims ic ON r.id = ic.lost_item_id
               WHERE ur.matric LIKE :searchTerm";
$stmtReports = $pdo->prepare($sqlReports);
$stmtReports->execute([':searchTerm' => '%' . $searchTerm . '%']);
$reports = $stmtReports->fetchAll();

// Update report status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $reportId = $_POST['report_id'];
    $newStatus = $_POST['status']; // Either 'reviewed' or 'resolved'

    // Update the report status
    $sqlUpdate = "UPDATE report SET report_status = :status WHERE id = :reportId";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->execute([':status' => $newStatus, ':reportId' => $reportId]);

    echo "<script>alert('Report status updated.');</script>";
}

// Example PHP functions (expand these with actual functionality)
function getTotalReportedItems() {
    global $pdo;
    $sql = "SELECT COUNT(*) FROM report";
    $stmt = $pdo->query($sql);
    return $stmt->fetchColumn();
}

function getTotalReviewedReports() {
    global $pdo;
    $sql = "SELECT COUNT(*) FROM report WHERE report_status = 'reviewed'";
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
            padding: 20px;
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
            }
        }

        .image-preview {
            max-width: 150px;
            cursor: pointer;
        }

        .modal-content img {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Manage Claims</a>
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
        <!-- Claim Management Section -->
        <h2>Manage Claims</h2>
        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Search by matric number" value="<?php echo htmlspecialchars($searchTerm); ?>">
                <button class="btn btn-outline-secondary" type="submit">Search</button>
            </div>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Description</th>
                    <th>User Name</th>
                    <th>Matric Number</th>
                    <th>Status</th>
                    <th>ID Card</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($report['item_name']); ?></td>
                        <td><?php echo htmlspecialchars($report['item_description']); ?></td>
                        <td><?php echo htmlspecialchars($report['name']); ?></td>
                        <td><?php echo htmlspecialchars($report['matric']); ?></td>
                        <td><?php echo htmlspecialchars($report['report_status']); ?></td>
                        <td>
                            <?php if (!empty($report['id_card'])): ?>
                                <img src="./uploads/<?php echo htmlspecialchars($report['id_card']); ?>" alt="ID Card" class="image-preview" data-bs-toggle="modal" data-bs-target="#idCardModal-<?php echo $report['id']; ?>">
                                <!-- Modal -->
                                <div class="modal fade" id="idCardModal-<?php echo $report['id']; ?>" tabindex="-1" aria-labelledby="idCardModalLabel-<?php echo $report['id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="idCardModalLabel-<?php echo $report['id']; ?>">ID Card</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <img src="./uploads/<?php echo htmlspecialchars($report['id_card']); ?>" alt="ID Card" class="img-fluid">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                No ID Card
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Change Status</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="reviewed" <?php echo $report['report_status'] == 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                                        <option value="resolved" <?php echo $report['report_status'] == 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                                    </select>
                                </div>
                                <button type="submit" name="update_status" class="btn btn-primary">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
