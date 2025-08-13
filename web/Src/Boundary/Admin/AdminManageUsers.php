<?php
include '../../header.php';
require_once dirname(__DIR__, 2) . '/Controllers/Admin/AdminManageUsersCtrl.php';

$controller = new AdminManageUsersCtrl();
$error = '';
$success = '';

// Handle suspend/unsuspend actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $userId = intval($_POST['user_id']);
    
    try {
        if ($_POST['action'] === 'suspend') {
            $result = $controller->suspendUser($userId);
            $success = $result ? "User suspended successfully" : "Failed to suspend user";
        } elseif ($_POST['action'] === 'unsuspend') {
            $result = $controller->unsuspendUser($userId);
            $success = $result ? "User unsuspended successfully" : "Failed to unsuspend user";
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get all users
$users = $controller->getAllUsers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Panel</title>
    <link rel="stylesheet" href="../../CSS/style.css">
    <style>
        .container {
            margin-top: 140px;
            max-width: 1200px;
            width: 100%;
            padding: 0 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255,255,255,0.9);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #e67e22;
            color: white;
        }
        .status-active {
            color: #2ecc71;
            font-weight: bold;
        }
        .status-suspended {
            color: #e74c3c;
            font-weight: bold;
        }
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            color: white;
        }
        .btn-suspend {
            background-color: #e74c3c;
        }
        .btn-unsuspend {
            background-color: #2ecc71;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .error {
            background-color: #ffebee;
            color: #e74c3c;
            border-left: 4px solid #e74c3c;
        }
        .success {
            background-color: #e8f5e9;
            color: #2ecc71;
            border-left: 4px solid #2ecc71;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Users</h1>
        
        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td class="<?php echo $user['status'] ? 'status-active' : 'status-suspended'; ?>">
                            <?php echo $user['status'] ? 'Active' : 'Suspended'; ?>
                        </td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <?php if ($user['status']): ?>
                                    <button type="submit" name="action" value="suspend" class="btn btn-suspend">Suspend</button>
                                <?php else: ?>
                                    <button type="submit" name="action" value="unsuspend" class="btn btn-unsuspend">Unsuspend</button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>