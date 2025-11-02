<?php
// register.php
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $mobile = htmlspecialchars(trim($_POST['mobile']));
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    $address = htmlspecialchars(trim($_POST['address']));
    $role = htmlspecialchars(trim($_POST['role']));
    $image = $_FILES['photo']['name'];
    $tmp_name = $_FILES['photo']['tmp_name'];
    $fileType = mime_content_type($tmp_name);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

    if ($password !== $cpassword) {
        echo '<script>alert("Passwords do not match!"); window.location.href="../routes/register.html";</script>';
        exit();
    }

    if (!in_array($fileType, $allowedTypes)) {
        echo '<script>alert("Only JPEG, PNG, and GIF files are allowed!"); window.location.href="../routes/register.html";</script>';
        exit();
    }

    $uploadDir = "../uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $uniqueImageName = uniqid() . "_" . basename($image);
    $destination = $uploadDir . $uniqueImageName;

    if (!move_uploaded_file($tmp_name, $destination)) {
        echo '<script>alert("Failed to upload image."); window.location.href="../routes/register.html";</script>';
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if mobile already exists
    $check = $conn->prepare("SELECT id FROM user WHERE mobile = ?");
    $check->bind_param("s", $mobile);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo '<script>alert("Mobile number already registered."); window.location.href="../routes/register.html";</script>';
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO user (name, mobile, address, password, photo, role, status, votes) VALUES (?, ?, ?, ?, ?, ?, 0, 0)");
    $stmt->bind_param("ssssss", $name, $mobile, $address, $hashedPassword, $uniqueImageName, $role);

    if ($stmt->execute()) {
        echo '<script>alert("Registration Successful!"); window.location.href="../";</script>';
    } else {
        echo '<script>alert("Database error: ' . $stmt->error . '"); window.location.href="../routes/register.html";</script>';
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
