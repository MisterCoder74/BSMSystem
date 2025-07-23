<?php
// Prevent direct access to this file
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Set the salon name and other basic info
define('SALON_NAME', 'Beauty Salon Management System');
define('SALON_VERSION', '1.0.0');

// Data file paths
define('CLIENTS_FILE', BASE_PATH . '/data/clients.json');
define('APPOINTMENTS_FILE', BASE_PATH . '/data/appointments.json');
define('SERVICES_FILE', BASE_PATH . '/data/services.json');
define('STAFF_FILE', BASE_PATH . '/data/staff.json');
define('USERS_FILE', BASE_PATH . '/data/users.json');

// Set timezone
date_default_timezone_set('Europe/Rome');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
