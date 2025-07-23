<?php
// Prevent direct access
if (!defined('BASE_PATH')) {
    die('Direct access not permitted');
}

// Ensure $client is available
if (!isset($client) || !is_array($client)) {
    setError('Client not found');
    redirect('index.php?page=clients');
}

// Get services data for appointment details
$services = readJsonFile(SERVICES_FILE);
// Get staff data for appointment details
$staff = readJsonFile(STAFF_FILE);
?>

<div class="clients-page">
    <div class="page-header">
        <h1>Client Details</h1>
        <div class="page-actions">
            <a href="index.php?page=clients&action=edit&id=<?php echo $client['id']; ?>" class="btn">
                <i class="fas fa-edit"></i> Edit Client
            </a>
            <a href="index.php?page=clients" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Clients
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h2>Personal Information</h2>
                </div>
                <div class="card-body">
                    <div class="client-info">
                        <div class="info-row">
                            <div class="info-label">Name:</div>
                            <div class="info-value"><?php echo $client['name']; ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Gender:</div>
                            <div class="info-value"><?php echo $client['gender']; ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Email:</div>
                            <div class="info-value"><?php echo $client['email'] ?? 'N/A'; ?></div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Phone:</div>
                            <div class="info-value"><?php echo $client['phone']; ?></div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Address:</div>
                            <div class="info-value"><?php echo $client['address'] ?? 'N/A'; ?></div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Notes:</div>
                            <div class="info-value"><?php echo nl2br($client['notes'] ?? 'N/A'); ?></div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Client Since:</div>
                            <div class="info-value"><?php echo formatDate($client['created_at'] ?? date('Y-m-d')); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h2>Appointment History</h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($clientAppointments)): ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Service</th>
                                    <th>Staff</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Sort appointments by date (newest first)
                                usort($clientAppointments, function($a, $b) {
                                    if ($a['date'] === $b['date']) {
                                        return strcmp($a['time'], $b['time']);
                                    }
                                    return strcmp($b['date'], $a['date']);
                                });

                                foreach ($clientAppointments as $appointment):
                                ?>
                                <tr>
                                    <td><?php echo formatDate($appointment['date']); ?></td>
                                    <td><?php echo $appointment['time']; ?></td>
                                    <td>
                                        <?php
                                        $service = findById($services, $appointment['service_id'] ?? 0);
                                        echo $service ? $service['name'] : 'N/A';
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $staff_member = findById($staff, $appointment['staff_id'] ?? 0);
                                        echo $staff_member ? $staff_member['name'] : 'N/A';
                                        ?>
                                    </td>
                                    <td>$<?php echo number_format($appointment['price'] ?? 0, 2); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($appointment['status'] ?? 'pending'); ?>">
                                            <?php echo ucfirst($appointment['status'] ?? 'Pending'); ?>
                                        </span>
                                    </td>
                                    <td class="table-actions">
                                        <a href="index.php?page=appointments&action=view&id=<?php echo $appointment['id']; ?>" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="index.php?page=appointments&action=edit&id=<?php echo $appointment['id']; ?>" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p>No appointment history found for this client.</p>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="index.php?page=appointments&action=add&client_id=<?php echo $client['id']; ?>" class="btn">
                        <i class="fas fa-calendar-plus"></i> Schedule New Appointment
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.client-info {
    display: grid;
    gap: 1rem;
}

.info-row {
    display: grid;
    grid-template-columns: 120px 1fr;
    border-bottom: 1px solid #eee;
    padding-bottom: 0.5rem;
}

.info-label {
    font-weight: 600;
    color: #666;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.85rem;
}

.status-completed {
    background-color: #d4edda;
    color: #155724;
}

.status-pending {
    background-color: #fff3cd;
    color: #856404;
}

.status-cancelled {
    background-color: #f8d7da;
    color: #721c24;
}

.status-noshow {
    background-color: #f8d7da;
    color: #721c24;
}
</style>
