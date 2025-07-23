<?php
// Prevent direct access
if (!defined('BASE_PATH')) {
    die('Direct access not permitted');
}

// Process login form
if (isPost()) {
    $username = sanitize($_POST['username'] ?? '');
    $password = sanitize($_POST['password'] ?? '');

    // Validate inputs
    if (empty($username) || empty($password)) {
        setError('Please enter both username and password');
    } else {
        // Authenticate user
        $users = readJsonFile(USERS_FILE);
        $authenticated = false;

        foreach ($users as $user) {
            // Check username and password (md5 hashed)
            if ($user['username'] === $username && $user['password'] === $password) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                $authenticated = true;
                break;
            }
        }

        if ($authenticated) {
            // Redirect to dashboard
            redirect('index.php?page=dashboard');
        } else {
            setError('Invalid username or password');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SALON_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-header">
            <h2><?php echo SALON_NAME; ?></h2>
            <p>Enter your credentials to access the management system</p>
        </div>

        <?php if ($error = getError()): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
        <?php endif; ?>

        <form action="index.php?page=login" method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-input">
                    <input type="password" id="password" name="password" required>
                    <i class="fas fa-eye password-toggle" onclick="togglePasswordVisibility('password')"></i>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-block">Login</button>
            </div>
        </form>

        <div class="login-footer">
            <p></p>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>
