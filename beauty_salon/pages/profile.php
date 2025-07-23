<?php
// Prevent direct access
if (!defined('BASE_PATH')) {
    die('Direct access not permitted');
}

// Get current user
$currentUser = getCurrentUser();

if (!$currentUser) {
    setError('User not found');
    redirect('index.php?page=dashboard');
}

// Get all users
$users = readJsonFile(USERS_FILE);

// Process form submission
if (isPost()) {
    // Update profile
    if ($action === 'update_profile') {
        $name = sanitize($_POST['name'] ?? '');
        $username = sanitize($_POST['username'] ?? '');
        $email = sanitize($_POST['email'] ?? '');

        // Validate required fields
        if (empty($name) || empty($username)) {
            setError('Name and username are required fields');
        } else {
            // Check if username is taken by another user
            $usernameExists = false;
            foreach ($users as $user) {
                if ($user['username'] === $username && $user['id'] !== $currentUser['id']) {
                    $usernameExists = true;
                    break;
                }
            }

            if ($usernameExists) {
                setError('Username is already taken');
            } else {
                // Update user
                foreach ($users as $key => $user) {
                    if ($user['id'] === $currentUser['id']) {
                        $users[$key]['name'] = $name;
                        $users[$key]['username'] = $username;
                        $users[$key]['email'] = $email;
                        break;
                    }
                }

                // Save to file
                if (writeJsonFile(USERS_FILE, $users)) {
                    // Update session
                    $_SESSION['user_name'] = $name;
                    setSuccess('Profile updated successfully');
                    redirect('index.php?page=profile');
                } else {
                    setError('Failed to update profile');
                }
            }
        }
    }

    // Change password
    if ($action === 'change_password') {
        $current_password = sanitize($_POST['current_password'] ?? '');
        $new_password = sanitize($_POST['new_password'] ?? '');
        $confirm_password = sanitize($_POST['confirm_password'] ?? '');

        // Validate input
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            setError('All password fields are required');
        } else if ($new_password !== $confirm_password) {
            setError('New passwords do not match');
        } else if (md5($current_password) !== $currentUser['password']) {
            setError('Current password is incorrect');
        } else if (strlen($new_password) < 6) {
            setError('New password must be at least 6 characters long');
        } else {
            // Update password
            foreach ($users as $key => $user) {
                if ($user['id'] === $currentUser['id']) {
                    $users[$key]['password'] = md5($new_password);
                    break;
                }
            }

            // Save to file
            if (writeJsonFile(USERS_FILE, $users)) {
                setSuccess('Password changed successfully');
                redirect('index.php?page=profile');
            } else {
                setError('Failed to change password');
            }
        }
    }
}
?>

<div class="profile-page">
    <div class="page-header">
        <h1>My Profile</h1>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h2>Profile Information</h2>
                </div>
                <div class="card-body">
                    <form action="index.php?page=profile&action=update_profile" method="post" id="profile-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Name <span class="required">*</span></label>
                                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($currentUser['name']); ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="username">Username <span class="required">*</span></label>
                                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($currentUser['username']); ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="role">Role</label>
                                <input type="text" id="role" value="<?php echo ucfirst($currentUser['role']); ?>" readonly disabled>
                                <div class="form-hint">Role cannot be changed</div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h2>Change Password</h2>
                </div>
                <div class="card-body">
                    <form action="index.php?page=profile&action=change_password" method="post" id="password-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="current_password">Current Password <span class="required">*</span></label>
                                <div class="password-input">
                                    <input type="password" id="current_password" name="current_password" required>
                                    <i class="fas fa-eye password-toggle" onclick="togglePasswordVisibility('current_password')"></i>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="new_password">New Password <span class="required">*</span></label>
                                <div class="password-input">
                                    <input type="password" id="new_password" name="new_password" required>
                                    <i class="fas fa-eye password-toggle" onclick="togglePasswordVisibility('new_password')"></i>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password <span class="required">*</span></label>
                                <div class="password-input">
                                    <input type="password" id="confirm_password" name="confirm_password" required>
                                    <i class="fas fa-eye password-toggle" onclick="togglePasswordVisibility('confirm_password')"></i>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn">
                                <i class="fas fa-key"></i> Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('password-form').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;

    if (newPassword !== confirmPassword) {
        e.preventDefault();
        alert('New passwords do not match');
    } else if (newPassword.length < 6) {
        e.preventDefault();
        alert('New password must be at least 6 characters long');
    }
});

function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
    } else {
        input.type = 'password';
    }
}
</script>

<style>
.password-input {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #6c757d;
}

.form-hint {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
}
</style>
