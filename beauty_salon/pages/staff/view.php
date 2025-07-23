<?php
// Prevent direct access
if (!defined('BASE_PATH')) {
    die('Direct access not permitted');
}

// Ensure $staff_member is available
if (!isset($staff_member) || !is_array($staff_member)) {
    setError('Staff member not found');
    redirect('index.php?page=staff');
}

// Get services data for appointment details
$services = readJsonFile(SERVICES_FILE);
// Get clients data for appointment details
$clients = readJsonFile(CLIENTS_FILE);
?>

<div class="staff-page">
    <div class="page-header">
        <h1>Staff Details</h1>
        <div class="page-actions">
            <a href="index.php?page=staff&action=edit&id=<?php echo $staff_member['id']; ?>" class="btn">
                <i class="fas fa-edit"></i> Edit Staff
            </a>
            <a href="index.php?page=staff" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Staff
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h2><?php echo $staff_member['name']; ?></h2>
                </div>
                <div class="card-body">
                    <div class="staff-info">
                        <div class="staff-avatar-large">
                            <i class="fas fa-user-circle"></i>
                        </div>

                        <div class="staff-details-full">
                            <div class="info-row">
                                <div class="info-label">Position:</div>
                                <div class="info-value"><?php echo $staff_member['position']; ?></div>
                            </div>

                            <?php if (!empty($staff_member['specialization'])): ?>
                            <div class="info-row">
                                <div class="info-label">Specialization:</div>
                                <div class="info-value"><?php echo $staff_member['specialization']; ?></div>
                            </div>
                            <?php endif; ?>

                            <div class="info-row">
                                <div class="info-label">Email:</div>
                                <div class="info-value"><?php echo $staff_member['email'] ?? 'N/A'; ?></div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Phone:</div>
                                <div class="info-value"><?php echo $staff_member['phone'] ?? 'N/A'; ?></div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Since:</div>
                                <div class="info-value"><?php echo formatDate($staff_member['created_at'] ?? date('Y-m-d')); ?></div>
                            </div>

                            <?php if (!empty($staff_member['bio'])): ?>
                            <div class="staff-bio">
                                <h3>Bio</h3>
                                <div><?php echo nl2br(htmlspecialchars($staff_member['bio'])); ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="footer-actions">
                        <a href="index.php?page=staff&action=edit&id=<?php echo $staff_member['id']; ?>" class="btn">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="#" onclick="deleteStaff(<?php echo $staff_member['id']; ?>, '<?php echo addslashes($staff_member['name']); ?>')" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h2>Upcoming Appointments</h2>
                </div>
                <div class="card-body">
                    <?php
                    // Filter and sort upcoming appointments
                    $today = date('Y-m-d');
                    $upcoming = array_filter($staffAppointments, function($appt) use ($today) {
                        return $appt['date'] >= $today;
                    });

                    // Sort by date and time
                    usort($upcoming, function($a, $b) {
                        if ($a['date'] === $b['date']) {
                            return strcmp($a['time'], $b['time']);
                        }
                        return strcmp($a['date'], $b['date']);
                    });

                    if (count($upcoming) > 0):
                    ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Client</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($upcoming as $appointment): ?>
                                <tr>
                                    <td><?php echo formatDate($appointment['date']); ?></td>
                                    <td><?php echo $appointment['time']; ?></td>
                                    <td>
                                        <?php
                                        $client = findById($clients, $appointment['client_id'] ?? 0);
                                        if ($client) {
                                            echo '<a href="index.php?page=clients&action=view&id=' . $client['id'] . '">' .
                                                $client['name'] . '</a>';
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $service = findById($services, $appointment['service_id'] ?? 0);
                                        echo $service ? $service['name'] : 'N/A';
                                        ?>
                                    </td>
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
                    <p>No upcoming appointments for this staff member.</p>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="index.php?page=appointments&action=add&staff_id=<?php echo $staff_member['id']; ?>" class="btn">
                        <i class="fas fa-calendar-plus"></i> Schedule Appointment
                    </a>
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
                    <?php
                    // Filter and sort past appointments
                    $past = array_filter($staffAppointments, function($appt) use ($today) {
                        return $appt['date'] < $today;
                    });

                    // Sort by date and time (newest first)
                    usort($past, function($a, $b) {
                        if ($a['date'] === $b['date']) {
                            return strcmp($b['time'], $a['time']);
                        }
                        return strcmp($b['date'], $a['date']);
                    });

                    if (count($past) > 0):
                    ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Client</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($past as $appointment): ?>
                                <tr>
                                    <td><?php echo formatDate($appointment['date']); ?></td>
                                    <td><?php echo $appointment['time']; ?></td>
                                    <td>
                                        <?php
                                        $client = findById($clients, $appointment['client_id'] ?? 0);
                                        if ($client) {
                                            echo '<a href="index.php?page=clients&action=view&id=' . $client['id'] . '">' .
                                                $client['name'] . '</a>';
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $service = findById($services, $appointment['service_id'] ?? 0);
                                        echo $service ? $service['name'] : 'N/A';
                                        ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($appointment['status'] ?? 'pending'); ?>">
                                            <?php echo ucfirst($appointment['status'] ?? 'Pending'); ?>
                                        </span>
                                    </td>
                                    <td class="table-actions">
                                        <a href="index.php?page=appointments&action=view&id=<?php echo $appointment['id']; ?>" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p>No appointment history found for this staff member.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Staff Form (Hidden) -->
<form id="delete-staff-form" action="index.php?page=staff&action=delete" method="post" style="display: none;">
    <input type="hidden" name="id" id="delete-staff-id" value="<?php echo $staff_member['id']; ?>">
</form>

<script>
function deleteStaff(id, name) {
    if (confirm(`Are you sure you want to delete staff member "${name}"? This action cannot be undone.`)) {
        document.getElementById('delete-staff-form').submit();
    }
}
</script>

<style>
.staff-info {
    display: flex;
    gap: 2rem;
    margin-bottom: 1.5rem;
}

.staff-avatar-large {
    font-size: 6rem;
    color: #ff758c;
    text-align: center;
}

.staff-details-full {
    flex: 1;
}

.info-row {
    display: grid;
    grid-template-columns: 130px 1fr;
    margin-bottom: 0.5rem;
}

.info-label {
    font-weight: 600;
    color: #666;
}

.staff-bio {
    margin-top: 1.5rem;
    border-top: 1px solid #eee;
    padding-top: 1rem;
}

.staff-bio h3 {
    margin-top: 0;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.footer-actions {
    display: flex;
    gap: 0.5rem;
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

.status-confirmed {
    background-color: #d1ecf1;
    color: #0c5460;
}

.status-cancelled {
    background-color: #f8d7da;
    color: #721c24;
}

.status-noshow {
    background-color: #f8d7da;
    color: #721c24;
}

@media (max-width: 768px) {
    .staff-info {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .info-row {
        grid-template-columns: 1fr;
    }

    .info-label {
        margin-bottom: 0.25rem;
    }
}
</style>
