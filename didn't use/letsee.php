<?php
session_start();
include("conn.php");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user ID
$userId = $_SESSION['user_id'];

// Fetch approved items
$sqlItems = "
    SELECT ic.id AS claim_id, l.id AS item_id, l.item_name, l.item_description, l.location, l.item_image
    FROM item_claims ic
    JOIN lost_items l ON ic.lost_item_id = l.id
    WHERE ic.status = 'approved' AND ic.user_id IS NULL
";
$stmtItems = $pdo->prepare($sqlItems);
$stmtItems->execute();
$items = $stmtItems->fetchAll();

// Handle item application
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
    $itemId = $_POST['item_id'];
    $claimId = $_POST['claim_id'];

    // Insert claim application into the item_claims table
    $sqlApply = "UPDATE item_claims SET user_id = :userId WHERE id = :claimId";
    $stmtApply = $pdo->prepare($sqlApply);
    $stmtApply->execute([':userId' => $userId, ':claimId' => $claimId]);

    echo "<script>alert('Your application has been submitted.');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Approved Items</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 20px;
        }
        .item-image {
            max-width: 100px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mt-4">Apply for Approved Items</h2>

        <?php if (count($items) > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Description</th>
                        <th>Location</th>
                        <th>Item Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['item_description']); ?></td>
                            <td><?php echo htmlspecialchars($item['location']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($item['item_image']); ?>" alt="Item Image" class="item-image"></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                                    <input type="hidden" name="claim_id" value="<?php echo $item['claim_id']; ?>">
                                    <button type="submit" name="apply" class="btn btn-primary">Apply</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No approved items available for application.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
