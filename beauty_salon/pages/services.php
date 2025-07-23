<?php
// Prevent direct access
if (!defined('BASE_PATH')) {
    die('Direct access not permitted');
}

// Get services data
$services = readJsonFile(SERVICES_FILE);

// Process form submissions
if (isPost()) {
    // Add or update service
    if ($action === 'add' || $action === 'edit') {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $name = sanitize($_POST['name'] ?? '');
        $price = (float)sanitize($_POST['price'] ?? 0);
        $duration = (int)sanitize($_POST['duration'] ?? 30);
        $description = sanitize($_POST['description'] ?? '');
        $category = sanitize($_POST['category'] ?? '');

        // Validate required fields
        if (empty($name) || $price <= 0) {
            setError('Name and price are required fields, and price must be greater than 0');
        } else {
            $newService = [
                'name' => $name,
                'price' => $price,
                'duration' => $duration,
                'description' => $description,
                'category' => $category,
                'updated_at' => date('Y-m-d')
            ];

            if ($action === 'add') {
                // Add new service
                $newService['id'] = generateId($services);
                $newService['created_at'] = date('Y-m-d');

                $services[] = $newService;
                setSuccess('Service added successfully');
            } else {
                // Update existing service
                $found = false;
                foreach ($services as $key => $service) {
                    if ($service['id'] == $id) {
                        $newService['id'] = $id;
                        $newService['created_at'] = $service['created_at'];
                        $services[$key] = $newService;
                        $found = true;
                        break;
                    }
                }

                if ($found) {
                    setSuccess('Service updated successfully');
                } else {
                    setError('Service not found');
                }
            }

            // Save to file
            if (writeJsonFile(SERVICES_FILE, $services)) {
                redirect('index.php?page=services');
            } else {
                setError('Failed to save service data');
            }
        }
    }

    // Delete service
    if ($action === 'delete' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];

        // Check if service exists
        $found = false;
        foreach ($services as $key => $service) {
            if ($service['id'] == $id) {
                unset($services[$key]);
                $found = true;
                break;
            }
        }

        if ($found) {
            // Re-index array
            $services = array_values($services);

            // Save to file
            if (writeJsonFile(SERVICES_FILE, $services)) {
                setSuccess('Service deleted successfully');
                redirect('index.php?page=services');
            } else {
                setError('Failed to delete service');
            }
        } else {
            setError('Service not found');
        }
    }
}

// Handle different actions
switch ($action) {
    case 'add':
        // Show add service form
        include_once BASE_PATH . '/pages/services/add.php';
        break;

    case 'edit':
        // Show edit service form
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $service = null;

        // Find service by ID
        foreach ($services as $s) {
            if ($s['id'] == $id) {
                $service = $s;
                break;
            }
        }

        if ($service) {
            include_once BASE_PATH . '/pages/services/edit.php';
        } else {
            setError('Service not found');
            redirect('index.php?page=services');
        }
        break;

    default:
        // List all services
        include_once BASE_PATH . '/pages/services/list.php';
}
