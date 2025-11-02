<?php
session_start();
include("connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['userdata'])) {
        $_SESSION['error'] = "User not logged in!";
        header("Location: ../");
        exit;
    }

    $userid = $_SESSION['userdata']['id'];
    $candidateId = $_POST['candidate_id'] ?? null;

    if (!$candidateId) {
        $_SESSION['error'] = "No candidate selected!";
        header("Location: ../routes/dashboard.php");
        exit;
    }

    // ✅ Update vote count for the selected group
    $stmt = $conn->prepare("UPDATE user SET votes = votes + 1 WHERE id = ? AND role = 2");
    if (!$stmt) {
        $_SESSION['error'] = "Vote update failed: " . $conn->error;
        header("Location: ../routes/dashboard.php");
        exit;
    }
    $stmt->bind_param("i", $candidateId);
    $voteUpdateSuccess = $stmt->execute();
    $stmt->close();

    // ✅ Update voter's status
    $stmt2 = $conn->prepare("UPDATE user SET status = 1, voted_for = ? WHERE id = ?");
    if (!$stmt2) {
        $_SESSION['error'] = "User status update failed: " . $conn->error;
        header("Location: ../routes/dashboard.php");
        exit;
    }
    $stmt2->bind_param("ii", $candidateId, $userid);
    $userStatusUpdateSuccess = $stmt2->execute();
    $stmt2->close();

    if ($voteUpdateSuccess && $userStatusUpdateSuccess) {
        // ✅ Update session
        $_SESSION['userdata']['status'] = 1;
        $_SESSION['userdata']['voted_for'] = $candidateId;

        // ✅ Refresh group data
        $groupStmt = $conn->prepare("SELECT id, name, photo, votes FROM user WHERE role = 2");
        if ($groupStmt) {
            $groupStmt->execute();
            $groupResult = $groupStmt->get_result();
            $groups = [];

            while ($row = $groupResult->fetch_assoc()) {
                $groups[] = $row;
            }

            $_SESSION['groupdata'] = $groups;
            $groupStmt->close();
        }

        $_SESSION['success'] = "Vote submitted successfully!";
        header("Location: ../routes/dashboard.php");
        exit;
    } else {
        $_SESSION['error'] = "Failed to submit vote!";
        header("Location: ../routes/dashboard.php");
        exit;
    }

} else {
    $_SESSION['error'] = "Invalid request!";
    header("Location: ../routes/dashboard.php");
    exit;
}
?>
