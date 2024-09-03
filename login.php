<?php
session_start();
include("conn.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL query to fetch the user
    $sql = "SELECT * FROM user_reg WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);

    // Check if the user exists
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify the password
        if ($password == $user['password']){
            // Password is correct, start the session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Redirect to the dashboard or home page
            header("Location: dashboard.php?welcome");
            exit();
        } else {
            // Incorrect password
            $error = "Invalid email or password.";
        }
    } else {
        // User not found
        $error = "Invalid email or password.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Lost & Found</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            background: linear-gradient(to right, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
            url("./img/image1.jpg") no-repeat center center/cover;
            padding-top: 80px;
        }
        .navbar {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        .card-header {
            background-color: #554c4b;
            color: white;
            border-radius: 10px 10px 0 0;
            text-align: center;
            padding: 20px;
        }
        .input-group-text {
            background-color: #554c4b;
            color: white;
            border: none;
        }
        .form-control {
            border: 1px solid #ddd;
        }
        .btn-primary {
            background-color: #554c4b;
            border: none;
            width: 100%;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .form-footer {
            text-align: center;
            margin-top: 15px;
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
                        <a class="nav-link active" aria-current="page" href="index.php"><i class="fa fa-home"></i> Home</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-search"></i> Search</a>
                    </li> -->
                    <li class="nav-item">
                        <a class="nav-link" href="registration.php"><i class="fa fa-user"></i> Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php"><i class="fa fa-sign-in-alt"></i> Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Login Form -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fa fa-sign-in-alt"></i> Login</h4>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" id="email" placeholder="Email" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
                                </div>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">Remember Me</label>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-sign-in-alt"></i> Login</button>
                            </div>
                        </form>
                        <div class="form-footer">
                            <!-- <a href="#">Forgot Password?</a> -->
                            <p>Don't have an account? <a href="registration.php">Register Now</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
