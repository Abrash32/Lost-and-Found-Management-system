<?php
// Database connection
$servername = "localhost";
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "lost_found_db"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $department = $_POST['department'];
    $matric = $_POST['matric'];
    $profilePhoto = $_FILES['profilePhoto']['name'];

    // Handle profile photo upload
    $profilePhoto = null;
    if (isset($_FILES['profilePhoto']) && $_FILES['profilePhoto']['error'] == UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['profilePhoto']['tmp_name'];
        $name = basename($_FILES['profilePhoto']['name']);
        $profilePhoto = 'uploads/' . $name;
        move_uploaded_file($tmp_name, $profilePhoto);
    }

    // Insert data into database
    $sql = "INSERT INTO user_reg (name, username, password, email, phone, department, matric, profile_photo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $name, $username, $password, $email, $phone, $department, $matric, $profilePhoto);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! Redirecting to login page...'); window.location.href = 'login.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(to right, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
            url("./img/reg.jpg") no-repeat center center/cover;
            padding-top: 80px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .navbar {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            background: #ffffff;
        }
        .card-header {
            background-color: #524a49;
            color: white;
            border-radius: 10px 10px 0 0;
            text-align: center;
            padding: 20px;
        }
        .input-group-text {
            background-color: #524a49;
            color: white;
            border: none;
        }
        .form-control {
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn-primary {
            background-color: #524a49;
            border: none;
            margin-bottom: 10px;
            width: 100%;
            font-size: 1.1rem;
            border-radius: 50px;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            border: none;
            width: 100%;
            font-size: 1.1rem;
            border-radius: 50px;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .profile-photo-container {
            text-align: center;
            margin-bottom: 15px;
        }
        .profile-photo-container img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 15px;
        }
        .profile-photo-container input {
            display: none;
        }
        .upload-photo-label {
            display: inline-block;
            cursor: pointer;
            padding: 10px 20px;
            background-color: #524a49;
            color: white;
            border-radius: 50px;
            font-size: 1rem;
        }
        @media (max-width: 768px) {
            .card {
                width: 90%;
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
                        <a class="nav-link active" aria-current="page" href="index.php"><i class="fa fa-home"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="card">
        <div class="card-header">
            <h4>Sign up</h4>
        </div>
        <div class="card-body p-4">
            <form id="registrationForm" enctype="multipart/form-data" method="post">
                <div class="profile-photo-container">
                    <img src="profile-photo.jpg" id="profilePreview" alt="Profile Photo">
                    <input type="file" id="profilePhoto" name="profilePhoto" accept="image/*" onchange="previewProfilePhoto()">
                    <label for="profilePhoto" class="upload-photo-label"><i class="fa fa-camera"></i> Upload Profile Picture</label>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                        <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm Password" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-phone"></i></span>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone Number" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-building"></i></span>
                        <input type="text" class="form-control" id="department" name="department" placeholder="Department" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-id-badge"></i></span>
                        <input type="text" class="form-control" id="matric" name="matric" placeholder="Matric Number" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
                <!-- <i class="fa fa-sign-in-alt "></i> Login</a></button> -->
            </form>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
    <script>
        function previewProfilePhoto() {
            const file = document.getElementById('profilePhoto').files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profilePreview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
