<?php
// Prevent direct access
if (!defined('BASE_PATH')) {
    die('Direct access not permitted');
}

// Get clients data
$clients = readJsonFile(CLIENTS_FILE);

// Process form submissions
if (isPost()) {
    // Add or update client
    if ($action === 'add' || $action === 'edit') {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $name = sanitize($_POST['name'] ?? '');
        $gender = sanitize($_POST['gender'] ?? '');    
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        $notes = sanitize($_POST['notes'] ?? '');

        // Validate required fields
        if (empty($name) || empty($phone)) {
            setError('Name and phone are required fields');
        } else {
            $newClient = [
                'name' => $name,
                    'gender' => $gender,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'notes' => $notes,
                'updated_at' => date('Y-m-d')
            ];

            if ($action === 'add') {
                // Add new client
                $newClient['id'] = generateId($clients);
                $newClient['created_at'] = date('Y-m-d');

                $clients[] = $newClient;
                setSuccess('Client added successfully');
            } else {
                // Update existing client
                $found = false;
                foreach ($clients as $key => $client) {
                    if ($client['id'] == $id) {
                        $newClient['id'] = $id;
                        $newClient['created_at'] = $client['created_at'];
                        $clients[$key] = $newClient;
                        $found = true;
                        break;
                    }
                }

                if ($found) {
                    setSuccess('Client updated successfully');
                } else {
                    setError('Client not found');
                }
            }

            // Save to file
            if (writeJsonFile(CLIENTS_FILE, $clients)) {
                redirect('index.php?page=clients');
            } else {
                setError('Failed to save client data');
            }
        }
    }

    // Delete client
    if ($action === 'delete' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];

        // Check if client exists
        $found = false;
        foreach ($clients as $key => $client) {
            if ($client['id'] == $id) {
                unset($clients[$key]);
                $found = true;
                break;
            }
        }

        if ($found) {
            // Re-index array
            $clients = array_values($clients);

            // Save to file
            if (writeJsonFile(CLIENTS_FILE, $clients)) {
                setSuccess('Client deleted successfully');
                redirect('index.php?page=clients');
            } else {
                setError('Failed to delete client');
            }
        } else {
            setError('Client not found');
        }
    }
}

// Handle different actions
switch ($action) {
    case 'add':
        // Show add client form
        include_once BASE_PATH . '/pages/clients/add.php';
        break;

    case 'edit':
        // Show edit client form
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $client = null;

        // Find client by ID
        foreach ($clients as $c) {
            if ($c['id'] == $id) {
                $client = $c;
                break;
            }
        }

        if ($client) {
            include_once BASE_PATH . '/pages/clients/edit.php';
        } else {
            setError('Client not found');
            redirect('index.php?page=clients');
        }
        break;

    case 'view':
        // Show client details
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $client = null;

        // Find client by ID
        foreach ($clients as $c) {
            if ($c['id'] == $id) {
                $client = $c;
                break;
            }
        }

        if ($client) {
            // Get client's appointments
            $appointments = readJsonFile(APPOINTMENTS_FILE);
            $clientAppointments = array_filter($appointments, function($appt) use ($id) {
                return isset($appt['client_id']) && $appt['client_id'] == $id;
            });

            include_once BASE_PATH . '/pages/clients/view.php';
        } else {
            setError('Client not found');
            redirect('index.php?page=clients');
        }
        break;

    default:
        // List all clients
        include_once BASE_PATH . '/pages/clients/list.php';
}
