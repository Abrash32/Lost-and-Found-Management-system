<?php
session_start();
include("conn.php");

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch pending claims
$sql = "SELECT ic.*, li.item_name, li.item_description, ur.fullname, ur.matric_number, ur.phone, ur.profile_photo 
        FROM item_claims ic
        JOIN lost_items li ON ic.lost_item_id = li.id
        JOIN user_reg ur ON ic.user_id = ur.id
        WHERE ic.status = 'pending'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$claims = $stmt->fetchAll();

// Approve claim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve'])) {
    $claimId = $_POST['claim_id'];

    // Update the claim status to approved
    $sql = "UPDATE item_claims SET status = 'approved' WHERE id = :claimId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':claimId' => $claimId]);

    echo "<script>alert('Claim approved. The user has been notified.');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="mt-4">Pending Claims</h2>

        <?php if (count($claims) > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Item Description</th>
                        <th>Claimer Name</th>
                        <th>Matric Number</th>
                        <th>Contact Number</th>
                        <th>Profile Photo</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody> 
                    <?php foreach ($claims as $claim): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($claim['item_name']); ?></td>
                            <td><?php echo htmlspecialchars($claim['item_description']); ?></td>
                            <td><?php echo htmlspecialchars($claim['fullname']); ?></td>
                            <td><?php echo htmlspecialchars($claim['matric_number']); ?></td>
                            <td><?php echo htmlspecialchars($claim['phone']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($claim['profile_photo']); ?>" alt="Profile Photo" width="50" height="50"></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="claim_id" value="<?php echo $claim['id']; ?>">
                                    <button type="submit" name="approve" class="btn btn-success">Approve</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No pending claims.</p>
        <?php endif; ?>
    </div>
</body>
</html>
