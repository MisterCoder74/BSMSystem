<?php
ob_start();
// Prevent direct access
if (!defined('BASE_PATH')) {
    die('Direct access not permitted');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SALON_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <h1><?php echo SALON_NAME; ?></h1>
            </div>

            <?php if (isLoggedIn()): ?>
            <nav>
                <ul>
                    <li><a href="index.php?page=dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="index.php?page=appointments"><i class="fas fa-calendar-alt"></i> Appointments</a></li>
                    <li><a href="index.php?page=clients"><i class="fas fa-users"></i> Clients</a></li>
                    <li><a href="index.php?page=services"><i class="fas fa-concierge-bell"></i> Services</a></li>
                    <li><a href="index.php?page=staff"><i class="fas fa-user-tie"></i> Staff</a></li>
                    <li><a href="index.php?page=reports"><i class="fas fa-chart-bar"></i> Reports</a></li>
                    <li><a href="index.php?page=marketing"><i class="fas fa-chart-bar"></i> Marketing</a></li>                        
                    <li><a href="index.php?page=profile"><i class="fas fa-user-circle"></i> Profile</a></li>
                    <li><a href="index.php?page=logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
            <?php endif; ?>
        </header>

        <main>
            <?php
            // Display error messages
            if ($error = getError()):
            ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
            <?php
            endif;

            // Display success messages
            if ($success = getSuccess()):
            ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
            <?php endif; ?>
