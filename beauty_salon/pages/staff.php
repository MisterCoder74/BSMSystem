<?php
// Prevent direct access
if (!defined('BASE_PATH')) {
    die('Direct access not permitted');
}

// Get staff data
$staff = readJsonFile(STAFF_FILE);

// Process form submissions
if (isPost()) {
    // Add or update staff
    if ($action === 'add' || $action === 'edit') {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $position = sanitize($_POST['position'] ?? '');
        $specialization = sanitize($_POST['specialization'] ?? '');
        $bio = sanitize($_POST['bio'] ?? '');

        // Validate required fields
        if (empty($name) || empty($position)) {
            setError('Name and position are required fields');
        } else {
            $newStaff = [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'position' => $position,
                'specialization' => $specialization,
                'bio' => $bio,
                'updated_at' => date('Y-m-d')
            ];

            if ($action === 'add') {
                // Add new staff member
                $newStaff['id'] = generateId($staff);
                $newStaff['created_at'] = date('Y-m-d');

                $staff[] = $newStaff;
                setSuccess('Staff member added successfully');
            } else {
                // Update existing staff member
                $found = false;
                foreach ($staff as $key => $staff_member) {
                    if ($staff_member['id'] == $id) {
                        $newStaff['id'] = $id;
                        $newStaff['created_at'] = $staff_member['created_at'];
                        $staff[$key] = $newStaff;
                        $found = true;
                        break;
                    }
                }

                if ($found) {
                    setSuccess('Staff member updated successfully');
                } else {
                    setError('Staff member not found');
                }
            }

            // Save to file
            if (writeJsonFile(STAFF_FILE, $staff)) {
                redirect('index.php?page=staff');
            } else {
                setError('Failed to save staff data');
            }
        }
    }

    // Delete staff
    if ($action === 'delete' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];

        // Check if staff exists
        $found = false;
        foreach ($staff as $key => $staff_member) {
            if ($staff_member['id'] == $id) {
                unset($staff[$key]);
                $found = true;
                break;
            }
        }

        if ($found) {
            // Re-index array
            $staff = array_values($staff);

            // Save to file
            if (writeJsonFile(STAFF_FILE, $staff)) {
                setSuccess('Staff member deleted successfully');
                redirect('index.php?page=staff');
            } else {
                setError('Failed to delete staff member');
            }
        } else {
            setError('Staff member not found');
        }
    }
}

// Handle different actions
switch ($action) {
    case 'add':
        // Show add staff form
        include_once BASE_PATH . '/pages/staff/add.php';
        break;

    case 'edit':
        // Show edit staff form
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $staff_member = null;

        // Find staff by ID
        foreach ($staff as $s) {
            if ($s['id'] == $id) {
                $staff_member = $s;
                break;
            }
        }

        if ($staff_member) {
            include_once BASE_PATH . '/pages/staff/edit.php';
        } else {
            setError('Staff member not found');
            redirect('index.php?page=staff');
        }
        break;

    case 'view':
        // Show staff details
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $staff_member = null;

        // Find staff by ID
        foreach ($staff as $s) {
            if ($s['id'] == $id) {
                $staff_member = $s;
                break;
            }
        }

        if ($staff_member) {
            // Get staff's appointments
            $appointments = readJsonFile(APPOINTMENTS_FILE);
            $staffAppointments = array_filter($appointments, function($appt) use ($id) {
                return isset($appt['staff_id']) && $appt['staff_id'] == $id;
            });

            include_once BASE_PATH . '/pages/staff/view.php';
        } else {
            setError('Staff member not found');
            redirect('index.php?page=staff');
        }
        break;

    default:
        // List all staff
        include_once BASE_PATH . '/pages/staff/list.php';
}
