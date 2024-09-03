<?php
session_start();
include("conn.php");

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle item approval or rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve'])) {
        $reportId = $_POST['report_id'];
        $sqlApprove = "UPDATE report SET report_status = 'reviewed' WHERE id = :reportId";
        $stmtApprove = $pdo->prepare($sqlApprove);
        $stmtApprove->execute([':reportId' => $reportId]);
    } elseif (isset($_POST['reject'])) {
        $reportId = $_POST['report_id'];
        $sqlReject = "UPDATE report SET report_status = 'resolved' WHERE id = :reportId";
        $stmtReject = $pdo->prepare($sqlReject);
        $stmtReject->execute([':reportId' => $reportId]);
    }
}

// Fetch reported items awaiting approval
$sqlReports = "SELECT * FROM report WHERE report_status = 'pending'";
$stmtReports = $pdo->prepare($sqlReports);
$stmtReports->execute();
$reports = $stmtReports->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Reported Items</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
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
            width: 250px;
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

        .item-image {
            max-width: 100px;
            height: auto;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Review Reported Found Item</a>
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
        <h2 class="mt-4">Review Found Item Reports</h2>

        <?php if (count($reports) > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Description</th>
                        <th>Location</th>
                        <th>Item Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reports as $report): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($report['item_name']); ?></td>
                            <td><?php echo htmlspecialchars($report['item_description']); ?></td>
                            <td><?php echo htmlspecialchars($report['location']); ?></td>
                            <td>
                                <?php if (!empty($report['item_image']) && file_exists('./uploads/' . $report['item_image'])): ?>
                                    <img src="./uploads/<?php echo htmlspecialchars($report['item_image']); ?>" alt="Item Image" class="item-image">
                                <?php else: ?>
                                    <span>No image available</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
                                    <button type="submit" name="approve" class="btn btn-success">Approve</button>
                                    <button type="submit" name="reject" class="btn btn-danger">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No reports available for approval.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
