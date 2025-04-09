<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User - Admin System</title>
    <link rel="stylesheet" href="../../styles/style.css">
</head>
<body>
    <?php
    // Start session at the beginning
    session_start();
    require_once '../../config/database.php';

    $errors = [];
    $success = false;

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get data from form
        $username = sanitize($_POST['username']);
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validate data
        if (empty($username)) {
            $errors[] = "Username cannot be empty";
        } elseif (strlen($username) < 3) {
            $errors[] = "Username must be at least 3 characters long";
        }
        
        if (empty($email)) {
            $errors[] = "Email cannot be empty";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email address";
        }
        
        if (empty($password)) {
            $errors[] = "Password cannot be empty";
        } elseif (strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters long";
        }
        
        if ($password !== $confirm_password) {
            $errors[] = "Password confirmation does not match";
        }
        
        // Check if username or email already exists
        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE username = ?");
                $stmt->execute([$username]);
                $username_exists = $stmt->fetch()['count'] > 0;
                
                if ($username_exists) {
                    $errors[] = "Username already exists";
                } else {
                    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE email = ?");
                    $stmt->execute([$email]);
                    $email_exists = $stmt->fetch()['count'] > 0;
                    
                    if ($email_exists) {
                        $errors[] = "Email already exists";
                    }
                }
            } catch (PDOException $e) {
                $errors[] = "Error: " . $e->getMessage();
            }
        }
        
        // Add user to the database if there are no errors
        if (empty($errors)) {
            try {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $result = $stmt->execute([$username, $email, $hashed_password]);
                
                if ($result) {
                    $success = true;
                    // Redirect after successful addition
                    header("Location: index.php?success=1");
                    exit;
                } else {
                    $errors[] = "Unable to add user. Please try again.";
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
                <h2>Add New User</h2>
                <a href="index.php" class="btn btn-secondary">Back to List</a>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success">User added successfully!</div>
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
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="username">Username <span class="required">*</span></label>
                        <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password <span class="required">*</span></label>
                        <input type="password" id="password" name="password" required>
                        <p class="form-hint">Password must be at least 6 characters long</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password <span class="required">*</span></label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
