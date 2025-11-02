<?php
session_start();

// 🔄 Simulate session data for testing (only if not already set)
if (!isset($_SESSION['userdata'])) {
    $_SESSION['userdata'] = [
        'id' => 1,
        'name' => 'John Doe',
        'mobile' => '1234567890',
        'role' => 1, // 1 = Voter, 2 = Group
        'photo' => 'default.png',
        'status' => 0, // 0 = Not Voted, 1 = Voted
    ];
    $_SESSION['groupdata'] = [
        [
            'id' => 101,
            'name' => 'Group A',
            'photo' => 'default.png',
            'votes' => 5,
        ],
        [
            'id' => 102,
            'name' => 'Group B',
            'photo' => 'default.png',
            'votes' => 3,
        ]
    ];
}

$userdata = $_SESSION['userdata'];
$groupdata = $_SESSION['groupdata'] ?? [];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Online Voting System - Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        #Profile, #Group {
            background-color: #e0f7fa;
            padding: 15px;
            margin: 10px;
            border-radius: 10px;
        }
        #Profile {
            width: 30%;
            float: left;
        }
        #Group {
            width: 60%;
            float: right;
        }
        .group-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .group-container img {
            border-radius: 5px;
            margin-right: 15px;
        }
        .group-info {
            flex: 1;
        }
        #votebtn {
            padding: 5px 15px;
            font-size: 15px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        #votebtn[disabled] {
            background-color: gray;
            cursor: not-allowed;
        }
        .message {
            font-weight: bold;
            padding: 10px;
        }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>

<body>

<h1>Online Voting System</h1>
<hr>

<!-- ✅ Flash Messages -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="message success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="message error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<!-- ✅ User Profile Section -->
<div id="Profile">
    <h3>Profile</h3>
    <img src="../uploads/<?= htmlspecialchars($userdata['photo']); ?>" height="100" width="100" alt="User Photo"><br><br>
    <strong>Name:</strong> <?= htmlspecialchars($userdata['name']); ?><br>
    <strong>Mobile:</strong> <?= htmlspecialchars($userdata['mobile']); ?><br>
    <strong>Role:</strong> <?= $userdata['role'] == 1 ? "Voter" : "Group"; ?><br>
    <strong>Status:</strong> <?= $userdata['status'] == 0 ? "Not Voted" : "Voted"; ?><br>

    <?php if ($userdata['status'] == 1 && isset($userdata['voted_for'])): ?>
        <strong>Voted For:</strong>
        <?php
        foreach ($groupdata as $group) {
            if ($group['id'] == $userdata['voted_for']) {
                echo htmlspecialchars($group['name']);
                break;
            }
        }
        ?>
    <?php endif; ?>
</div>

<!-- ✅ Group List Section -->
<div id="Group">
    <h3>Groups</h3>
    <?php if (!empty($groupdata)): ?>
        <?php foreach ($groupdata as $group): ?>
            <div class="group-container">
                <img src="../uploads/<?= htmlspecialchars($group['photo']); ?>" height="100" width="100" alt="Group Photo">
                <div class="group-info">
                    <strong>Name:</strong> <?= htmlspecialchars($group['name']); ?><br>
                    <strong>Votes:</strong> <?= htmlspecialchars($group['votes']); ?>
                </div>
                <form action="../api/vote.php" method="POST">
                <input type="hidden" name="candidate_id" value="<?= $group['id']; ?>">


                    <?php
                    if ($userdata['role'] == 1) {
                        if ($userdata['status'] == 0) {
                            // Not voted yet
                            echo '<input type="submit" id="votebtn" value="Vote">';
                        } else {
                            // Already voted
                            if (isset($userdata['voted_for']) && $userdata['voted_for'] == $group['id']) {
                                // This is the group user voted for
                                echo '<button id="votebtn" disabled>Voted</button>';
                            } else {
                                // Other groups – disable vote button
                                echo '<button id="votebtn" disabled style="opacity: 0.5; cursor: not-allowed;">Vote</button>';
                            }
                        }
                    }
                    ?>
                </form>
            </div>
            <hr>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No groups available.</p>
    <?php endif; ?>
</div>


</body>
</html>
