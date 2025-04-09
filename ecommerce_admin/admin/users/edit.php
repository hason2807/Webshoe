<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Admin System</title>
    <link rel="stylesheet" href="../../styles/style.css">
</head>
<body>
    <?php
    session_start();
    require_once '../../config/database.php';

    $errors = [];
    $success = false;
    $user = [];

    // Check user ID
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header('Location: index.php');
        exit;
    }

    $user_id = (int)$_GET['id'];

    // Get user info
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if (!$user) {
            header('Location: index.php');
            exit;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = sanitize($_POST['username']);
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validation
        if (empty($username)) {
            $errors[] = "Username is required.";
        } elseif (strlen($username) < 3) {
            $errors[] = "Username must be at least 3 characters.";
        }
        
        if (empty($email)) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
        
        // Check if username or email already exists
        if (empty($errors)) {
            try {
                if ($username !== $user['username']) {
                    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE username = ? AND id != ?");
                    $stmt->execute([$username, $user_id]);
                    if ($stmt->fetch()['count'] > 0) {
                        $errors[] = "Username already exists.";
                    }
                }
                
                if ($email !== $user['email']) {
                    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE email = ? AND id != ?");
                    $stmt->execute([$email, $user_id]);
                    if ($stmt->fetch()['count'] > 0) {
                        $errors[] = "Email already exists.";
                    }
                }
            } catch (PDOException $e) {
                $errors[] = "Error: " . $e->getMessage();
            }
        }

        // Password handling
        $update_password = false;
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $errors[] = "Password must be at least 6 characters.";
            } elseif ($password !== $confirm_password) {
                $errors[] = "Password confirmation does not match.";
            } else {
                $update_password = true;
            }
        }

        // Update user in database
        if (empty($errors)) {
            try {
                if ($update_password) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
                    $result = $stmt->execute([$username, $email, $hashed_password, $user_id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                    $result = $stmt->execute([$username, $email, $user_id]);
                }

                if ($result) {
                    $success = true;
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                    $stmt->execute([$user_id]);
                    $user = $stmt->fetch();
                } else {
                    $errors[] = "Could not update user. Please try again.";
                }
            } catch (PDOException $e) {
                $errors[] = "Error: " . $e->getMessage();
            }
        }
    }
    ?>
    <div class="admin-container">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <header class="admin-header">
                <h1>User Management</h1>
                <div class="user-info">
                    <span>Hello, <?php echo isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin'; ?></span>
                    <a href="../logout.php" class="btn btn-sm btn-danger">Logout</a>
                </div>
            </header>
            
            <div class="content-header">
                <h2>Edit User</h2>
                <a href="index.php" class="btn btn-secondary">Back to List</a>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success">User updated successfully!</div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div class="form-container">
                <form action="edit.php?id=<?php echo $user_id; ?>" method="POST">
                    <div class="form-group">
                        <label for="username">Username <span class="required">*</span></label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password">
                        <p class="form-hint">Leave blank if you do not want to change the password</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password">
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
