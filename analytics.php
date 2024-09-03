<?php
session_start();
include("conn.php");

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch statistics
function getTotalItems() {
    global $pdo;
    $sql = "SELECT COUNT(*) FROM lost_items";
    $stmt = $pdo->query($sql);
    return $stmt->fetchColumn();
}

function getTotalClaims() {
    global $pdo;
    $sql = "SELECT COUNT(*) FROM item_claims";
    $stmt = $pdo->query($sql);
    return $stmt->fetchColumn();
}

function getTotalApprovedClaims() {
    global $pdo;
    $sql = "SELECT COUNT(*) FROM item_claims WHERE status = 'approved'";
    $stmt = $pdo->query($sql);
    return $stmt->fetchColumn();
}

function getTotalUsers() {
    global $pdo;
    $sql = "SELECT COUNT(*) FROM user_reg";
    $stmt = $pdo->query($sql);
    return $stmt->fetchColumn();
}

// Fetch data for charts
function getClaimsOverTimeData() {
    global $pdo;
    $sql = "SELECT DATE(claim_date) AS date, COUNT(*) AS claims FROM item_claims GROUP BY DATE(claim_date)";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getReportedItemsByLocation() {
    global $pdo;
    $sql = "SELECT location, COUNT(*) AS count FROM lost_items GROUP BY location";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$claimsOverTimeData = getClaimsOverTimeData();
$reportedItemsByLocation = getReportedItemsByLocation();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body, html {
            height: 100%;
            margin: 0;
        }

        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            border-right: 1px solid #ddd;
            background-color: #343a40;
        }

        .sidebar-sticky {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            height: calc(100vh - 48px);
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

        .navbar {
            margin-bottom: 20px;
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

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
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
                <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_logout">Logout</a>
                    </li>
                </ul>
            </div>
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
        <h1>Reports & Analytics</h1>
        <div class="row">
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Total Reported Items</h5>
                        <p class="card-text"><?php echo getTotalItems(); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Total Claims</h5>
                        <p class="card-text"><?php echo getTotalClaims(); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Approved Claims</h5>
                        <p class="card-text"><?php echo getTotalApprovedClaims(); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <p class="card-text"><?php echo getTotalUsers(); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <h2>Analytics</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Claims Over Time</h5>
                        <div class="chart-container">
                            <canvas id="claimsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Reported Items by Location</h5>
                        <div class="chart-container">
                            <canvas id="itemsLocationChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Claims Over Time Chart
        const claimsData = <?php echo json_encode($claimsOverTimeData); ?>;
        const claimsDates = claimsData.map(data => data.date);
        const claimsCounts = claimsData.map(data => data.claims);

        const ctx1 = document.getElementById('claimsChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: claimsDates,
                datasets: [{
                    label: 'Claims Over Time',
                    data: claimsCounts,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    x: { beginAtZero: true },
                    y: { beginAtZero: true }
                }
            }
        });

        // Reported Items by Location Chart
        const itemsData = <?php echo json_encode($reportedItemsByLocation); ?>;
        const locations = itemsData.map(data => data.location);
        const counts = itemsData.map(data => data.count);

        const ctx2 = document.getElementById('itemsLocationChart').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: locations,
                datasets: [{
                    label: 'Reported Items by Location',
                    data: counts,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    x: { beginAtZero: true },
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>
