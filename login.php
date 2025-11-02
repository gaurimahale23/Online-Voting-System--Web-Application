<?php
// login.php
session_start();
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mobile = htmlspecialchars(trim($_POST['mobile']));
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password, role, status, photo, votes FROM user WHERE mobile = ?");
    $stmt->bind_param("s", $mobile);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['userdata'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'mobile' => $mobile,
                'role' => $user['role'],
                'status' => $user['status'],
                'photo' => $user['photo'],
                'votes' => $user['votes']
            ];
            echo '<script>window.location.href = "../routes/dashboard.php";</script>';
        } else {
            echo '<script>alert("Incorrect password."); window.location.href="../index.html";</script>';
        }
    } else {
        echo '<script>alert("User not found."); window.location.href="../index.html";</script>';
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
