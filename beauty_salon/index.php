<?php
// Define base path
define('BASE_PATH', __DIR__);

// Include configuration and functions
require_once BASE_PATH . '/includes/config.php';
require_once BASE_PATH . '/includes/functions.php';

// Get requested page or default to login/dashboard
$page = isset($_GET['page']) ? sanitize($_GET['page']) : (isLoggedIn() ? 'dashboard' : 'login');

// Define allowed pages
$allowed_pages = [
    'login', 'logout', 'dashboard', 'clients', 'appointments',
    'services', 'staff', 'reports', 'profile', 'marketing'
];

// Check if page is allowed
if (!in_array($page, $allowed_pages)) {
    $page = isLoggedIn() ? 'dashboard' : 'login';
}

// Page access control
if ($page !== 'login' && $page !== 'logout' && !isLoggedIn()) {
    setError('Please log in to access the system');
    redirect('index.php?page=login');
}

// Get action if available
$action = isset($_GET['action']) ? sanitize($_GET['action']) : 'list';

// Include header for all pages except login
if ($page !== 'login') {
    include_once BASE_PATH . '/includes/header.php';
}

// Process logout
if ($page === 'logout') {
    // Destroy session
    session_destroy();
    // Redirect to login
    redirect('index.php?page=login');
}

// Include the requested page
$page_file = BASE_PATH . '/pages/' . $page . '.php';

if (file_exists($page_file)) {
    include_once $page_file;
} else {
    echo '<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> Page not found</div>';
}

// Include footer for all pages except login
if ($page !== 'login') {
    include_once BASE_PATH . '/includes/footer.php';
}
