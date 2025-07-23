<?php
// Prevent direct access
if (!defined('BASE_PATH')) {
    die('Direct access not permitted');
}

// Get appointments data
$appointments = readJsonFile(APPOINTMENTS_FILE);
// Get clients data for appointment forms
$clients = readJsonFile(CLIENTS_FILE);
// Get services data for appointment forms
$services = readJsonFile(SERVICES_FILE);
// Get staff data for appointment forms
$staff = readJsonFile(STAFF_FILE);

// Process form submissions
if (isPost()) {
    // Add or update appointment
    if ($action === 'add' || $action === 'edit') {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $client_id = (int)($_POST['client_id'] ?? 0);
        $service_id = (int)($_POST['service_id'] ?? 0);
        $staff_id = (int)($_POST['staff_id'] ?? 0);
        $date = sanitize($_POST['date'] ?? '');
        $time = sanitize($_POST['time'] ?? '');
        $status = sanitize($_POST['status'] ?? 'pending');
        $notes = sanitize($_POST['notes'] ?? '');

        // Get service price
        $price = 0;
        foreach ($services as $service) {
            if ($service['id'] == $service_id) {
                $price = $service['price'];
                break;
            }
        }

        // Validate required fields
        if (empty($date) || empty($time) || $client_id <= 0 || $service_id <= 0 || $staff_id <= 0) {
            setError('Please fill in all required fields');
        } else {
            $newAppointment = [
                'client_id' => $client_id,
                'service_id' => $service_id,
                'staff_id' => $staff_id,
                'date' => $date,
                'time' => $time,
                'status' => $status,
                'price' => $price,
                'notes' => $notes,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($action === 'add') {
                // Add new appointment
                $newAppointment['id'] = generateId($appointments);
                $newAppointment['created_at'] = date('Y-m-d H:i:s');

                $appointments[] = $newAppointment;
                setSuccess('Appointment scheduled successfully');
            } else {
                // Update existing appointment
                $found = false;
                foreach ($appointments as $key => $appointment) {
                    if ($appointment['id'] == $id) {
                        $newAppointment['id'] = $id;
                        $newAppointment['created_at'] = $appointment['created_at'];
                        $appointments[$key] = $newAppointment;
                        $found = true;
                        break;
                    }
                }

                if ($found) {
                    setSuccess('Appointment updated successfully');
                } else {
                    setError('Appointment not found');
                }
            }

            // Save to file
            if (writeJsonFile(APPOINTMENTS_FILE, $appointments)) {
                redirect('index.php?page=appointments');
            } else {
                setError('Failed to save appointment data');
            }
        }
    }

    // Delete appointment
    if ($action === 'delete' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];

        // Check if appointment exists
        $found = false;
        foreach ($appointments as $key => $appointment) {
            if ($appointment['id'] == $id) {
                unset($appointments[$key]);
                $found = true;
                break;
            }
        }

        if ($found) {
            // Re-index array
            $appointments = array_values($appointments);

            // Save to file
            if (writeJsonFile(APPOINTMENTS_FILE, $appointments)) {
                setSuccess('Appointment deleted successfully');
                redirect('index.php?page=appointments');
            } else {
                setError('Failed to delete appointment');
            }
        } else {
            setError('Appointment not found');
        }
    }

    // Update appointment status
    if ($action === 'update_status' && isset($_POST['id']) && isset($_POST['status'])) {
        $id = (int)$_POST['id'];
        $status = sanitize($_POST['status']);

        // Find appointment
        $found = false;
        foreach ($appointments as $key => $appointment) {
            if ($appointment['id'] == $id) {
                $appointments[$key]['status'] = $status;
                $appointments[$key]['updated_at'] = date('Y-m-d H:i:s');
                $found = true;
                break;
            }
        }

        if ($found) {
            // Save to file
            if (writeJsonFile(APPOINTMENTS_FILE, $appointments)) {
                setSuccess('Appointment status updated successfully');
                redirect('index.php?page=appointments&action=view&id=' . $id);
            } else {
                setError('Failed to update appointment status');
            }
        } else {
            setError('Appointment not found');
        }
    }
}

// Handle different actions
switch ($action) {
    case 'add':
        // Show add appointment form
        // Pre-select client if coming from client page
        $selected_client = null;
        if (isset($_GET['client_id'])) {
            $client_id = (int)$_GET['client_id'];
            foreach ($clients as $client) {
                if ($client['id'] == $client_id) {
                    $selected_client = $client;
                    break;
                }
            }
        }

        include_once BASE_PATH . '/pages/appointments/add.php';
        break;

    case 'edit':
        // Show edit appointment form
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $appointment = null;

        // Find appointment by ID
        foreach ($appointments as $appt) {
            if ($appt['id'] == $id) {
                $appointment = $appt;
                break;
            }
        }

        if ($appointment) {
            include_once BASE_PATH . '/pages/appointments/edit.php';
        } else {
            setError('Appointment not found');
            redirect('index.php?page=appointments');
        }
        break;

    case 'view':
        // Show appointment details
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $appointment = null;

        // Find appointment by ID
        foreach ($appointments as $appt) {
            if ($appt['id'] == $id) {
                $appointment = $appt;
                break;
            }
        }

        if ($appointment) {
            // Find related records
            $client = findById($clients, $appointment['client_id']);
            $service = findById($services, $appointment['service_id']);
            $staff_member = findById($staff, $appointment['staff_id']);

            include_once BASE_PATH . '/pages/appointments/view.php';
        } else {
            setError('Appointment not found');
            redirect('index.php?page=appointments');
        }
        break;

    case 'calendar':
        // Show calendar view
        $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
        $year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
                
        $current_date = new DateTime();
		$week = isset($_GET['week']) ? (int)$_GET['week'] : (int)date('W');        

        include_once BASE_PATH . '/pages/appointments/calendar.php';
        break;

    default:
        // List appointments (default view)
        // Filter by date
        $filter_date = isset($_GET['date']) ? sanitize($_GET['date']) : date('Y-m-d');
        $filtered_appointments = array_filter($appointments, function($appointment) use ($filter_date) {
            return $appointment['date'] === $filter_date;
        });

        // Sort by time
        usort($filtered_appointments, function($a, $b) {
            return strcmp($a['time'], $b['time']);
        });

        include_once BASE_PATH . '/pages/appointments/list.php';
}
