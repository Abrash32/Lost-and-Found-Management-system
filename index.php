<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost & Found System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: #fff;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .navbar {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 15px 20px;
        }
        .navbar-brand {
            font-size: 2rem;
            font-weight: bold;
            color: #fff;
        }
        .navbar-nav .nav-link {
            color: #fff;
            font-size: 1.1rem;
            transition: color 0.3s;
        }
        .navbar-nav .nav-link:hover {
            color: #f8f9fa;
        }
        .hero-section {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: linear-gradient(to right, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                        url("./img/image1.jpg") no-repeat center center/cover;
            color: #fff;
            padding: 0 20px;
        }
        .hero-content {
            max-width: 700px;
            background: rgba(0, 0, 0, 0.5);
            padding: 30px;
            border-radius: 15px;
        }
        .hero-content h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
        }
        .hero-content p {
            font-size: 1.3rem;
            margin-bottom: 30px;
        }
        .hero-content .btn {
            font-size: 1.2rem;
            padding: 12px 35px;
            margin: 5px;
            border-radius: 50px;
        }
        .features-section {
            background-color: #f8f9fa;
            color: #333;
            padding: 60px 0;
        }
        .feature-box {
            text-align: center;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .feature-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }
        .feature-box i {
            font-size: 3.5rem;
            color: #2575fc;
            margin-bottom: 15px;
        }
        .feature-box h4 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        .feature-box p {
            font-size: 1.1rem;
        }
        .footer {
            background-color: #343a40;
            color: #f8f9fa;
            text-align: center;
            padding: 20px 0;
            position: relative;
        }
        .footer a {
            color: #f8f9fa;
            text-decoration: none;
            transition: color 0.3s;
        }
        .footer a:hover {
            color: #2575fc;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">Lost & Found</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="registration.php">Sign Up</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <h1>Welcome to the Lost & Found System</h1>
            <p>Efficiently manage and reclaim your lost items with our intuitive platform. Sign up today to get started or login to manage your account.</p>
            <a href="registration.php" class="btn btn-primary me-2"><i class="fas fa-user-plus"></i> Sign Up</a>
            <a href="login.php" class="btn btn-outline-light"><i class="fas fa-sign-in-alt"></i> Login</a>
        </div>
    </div>

    <!-- Features Section -->
    <div class="features-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-box">
                        <i class="fas fa-search"></i>
                        <h4>Search for Lost Items</h4>
                        <p>Quickly search through our database of lost items to find and reclaim what you’ve lost.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-box">
                        <i class="fas fa-upload"></i>
                        <h4>Report a Found Item</h4>
                        <p>Easily report any found items and help reunite them with their rightful owners.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-box">
                        <i class="fas fa-bell"></i>
                        <h4>Get Notified</h4>
                        <p>Receive instant notifications when an item matching your description is found.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Lost & Found System. All Rights Reserved.</p>
            <p>Developed by <a href="#">Zam'Ah and Abrash at TechXerm</a></p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
</body>
</html>
