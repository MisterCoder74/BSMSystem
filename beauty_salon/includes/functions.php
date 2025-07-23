<?php
// Prevent direct access to this file
if (!defined('BASE_PATH')) {
    die('Direct access not permitted');
}

/**
 * Read data from a JSON file
 *
 * @param string $file Path to the JSON file
 * @return array Data from the JSON file
 */
function readJsonFile($file) {
    if (file_exists($file)) {
        $jsonData = file_get_contents($file);
        return json_decode($jsonData, true) ?: [];
    }
    return [];
}

/**
 * Write data to a JSON file
 *
 * @param string $file Path to the JSON file
 * @param array $data Data to write to the file
 * @return bool True if write was successful, false otherwise
 */
function writeJsonFile($file, $data) {
    $jsonData = json_encode($data, JSON_PRETTY_PRINT);
    return file_put_contents($file, $jsonData) !== false;
}

/**
 * Generate a unique ID for new records
 *
 * @param array $data Existing data array
 * @return int New unique ID
 */
function generateId($data) {
    if (empty($data)) {
        return 1;
    }

    $ids = array_column($data, 'id');
    return max($ids) + 1;
}

/**
 * Find an item by ID in a data array
 *
 * @param array $data Data array to search
 * @param int $id ID to find
 * @return array|null Found item or null
 */
function findById($data, $id) {
    foreach ($data as $item) {
        if ($item['id'] == $id) {
            return $item;
        }
    }
    return null;
}

/**
 * Update an item in a data array
 *
 * @param array $data Data array to update
 * @param int $id ID of item to update
 * @param array $newData New data for the item
 * @return array Updated data array
 */
function updateItem($data, $id, $newData) {
    foreach ($data as $key => $item) {
        if ($item['id'] == $id) {
            $data[$key] = array_merge($item, $newData);
            break;
        }
    }
    return $data;
}

/**
 * Delete an item from a data array
 *
 * @param array $data Data array to update
 * @param int $id ID of item to delete
 * @return array Updated data array
 */
function deleteItem($data, $id) {
    foreach ($data as $key => $item) {
        if ($item['id'] == $id) {
            unset($data[$key]);
            break;
        }
    }
    return array_values($data); // Re-index array
}

/**
 * Check if user is logged in
 *
 * @return bool True if logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current logged in user
 *
 * @return array|null User data or null if not logged in
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }

    $users = readJsonFile(USERS_FILE);
    return findById($users, $_SESSION['user_id']);
}

/**
 * Redirect to a URL
 *
 * @param string $url URL to redirect to
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * Format date for display
 *
 * @param string $date Date string
 * @param string $format Output format
 * @return string Formatted date
 */
function formatDate($date, $format = 'Y-m-d') {
    return date($format, strtotime($date));
}

/**
 * Sanitize input data
 *
 * @param string $data Data to sanitize
 * @return string Sanitized data
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Check if request is POST
 *
 * @return bool True if POST request, false otherwise
 */
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Add error message to session
 *
 * @param string $message Error message
 */
function setError($message) {
    $_SESSION['error'] = $message;
}

/**
 * Add success message to session
 *
 * @param string $message Success message
 */
function setSuccess($message) {
    $_SESSION['success'] = $message;
}

/**
 * Get error message from session and clear it
 *
 * @return string|null Error message or null
 */
function getError() {
    $error = $_SESSION['error'] ?? null;
    unset($_SESSION['error']);
    return $error;
}

/**
 * Get success message from session and clear it
 *
 * @return string|null Success message or null
 */
function getSuccess() {
    $success = $_SESSION['success'] ?? null;
    unset($_SESSION['success']);
    return $success;
}
