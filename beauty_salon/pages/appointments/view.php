<?php
// Prevent direct access
if (!defined('BASE_PATH')) {
    die('Direct access not permitted');
}

// Ensure $appointment is available
if (!isset($appointment) || !is_array($appointment)) {
    setError('Appointment not found');
    redirect('index.php?page=appointments');
}

// Ensure $client, $service, and $staff_member are available
if (!isset($client) || !isset($service) || !isset($staff_member)) {
    setError('Appointment data is incomplete');
    redirect('index.php?page=appointments');
}
?>

<div class="appointments-page">
    <div class="page-header">
        <h1>Appointment Details</h1>
        <div class="page-actions">
            <a href="index.php?page=appointments&action=edit&id=<?php echo $appointment['id']; ?>" class="btn">
                <i class="fas fa-edit"></i> Edit Appointment
            </a>
            <a href="index.php?page=appointments" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Appointments
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h2>
                        <span class="status-badge status-<?php echo strtolower($appointment['status'] ?? 'pending'); ?>">
                            <?php echo ucfirst($appointment['status'] ?? 'Pending'); ?>
                        </span>
                        Appointment on <?php echo formatDate($appointment['date']); ?> at <?php echo $appointment['time']; ?>
                    </h2>
                </div>
                <div class="card-body">
                    <div class="appointment-info">
                        <div class="info-section">
                            <h3>Appointment Details</h3>
                            <div class="info-row">
                                <div class="info-label">Date:</div>
                                <div class="info-value"><?php echo formatDate($appointment['date']); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Time:</div>
                                <div class="info-value"><?php echo $appointment['time']; ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Service:</div>
                                <div class="info-value"><?php echo $service ? $service['name'] : 'N/A'; ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Duration:</div>
                                <div class="info-value"><?php echo $service ? $service['duration'] . ' minutes' : 'N/A'; ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Price:</div>
                                <div class="info-value">$<?php echo number_format($appointment['price'] ?? 0, 2); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Status:</div>
                                <div class="info-value">
                                    <span class="status-badge status-<?php echo strtolower($appointment['status'] ?? 'pending'); ?>">
                                        <?php echo ucfirst($appointment['status'] ?? 'Pending'); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Staff:</div>
                                <div class="info-value"><?php echo $staff_member ? $staff_member['name'] : 'N/A'; ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Created:</div>
                                <div class="info-value"><?php echo formatDate($appointment['created_at'] ?? date('Y-m-d H:i:s'), 'Y-m-d H:i'); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Last Updated:</div>
                                <div class="info-value"><?php echo formatDate($appointment['updated_at'] ?? date('Y-m-d H:i:s'), 'Y-m-d H:i'); ?></div>
                            </div>

                            <?php if (!empty($appointment['notes'])): ?>
                            <div class="info-row notes">
                                <div class="info-label">Notes:</div>
                                <div class="info-value"><?php echo nl2br(htmlspecialchars($appointment['notes'])); ?></div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="info-section">
                            <h3>Client Information</h3>
                            <?php if ($client): ?>
                            <div class="info-row">
                                <div class="info-label">Name:</div>
                                <div class="info-value">
                                    <a href="index.php?page=clients&action=view&id=<?php echo $client['id']; ?>">
                                        <?php echo $client['name']; ?>
                                    </a>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Phone:</div>
                                <div class="info-value"><?php echo $client['phone']; ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Email:</div>
                                <div class="info-value"><?php echo $client['email'] ?? 'N/A'; ?></div>
                            </div>
                            <?php else: ?>
                            <p>Client information not available</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="action-panel">
                        <h3>Update Status</h3>
                        <form action="index.php?page=appointments&action=update_status" method="post" class="status-form">
                            <input type="hidden" name="id" value="<?php echo $appointment['id']; ?>">
                            <div class="status-buttons">
                                <button type="submit" name="status" value="pending" class="btn btn-status btn-pending <?php if (($appointment['status'] ?? '') === 'pending') echo 'active'; ?>">
                                    Pending
                                </button>
                                <button type="submit" name="status" value="confirmed" class="btn btn-status btn-confirmed <?php if (($appointment['status'] ?? '') === 'confirmed') echo 'active'; ?>">
                                    Confirmed
                                </button>
                                <button type="submit" name="status" value="completed" class="btn btn-status btn-completed <?php if (($appointment['status'] ?? '') === 'completed') echo 'active'; ?>">
                                    Completed
                                </button>
                                <button type="submit" name="status" value="cancelled" class="btn btn-status btn-cancelled <?php if (($appointment['status'] ?? '') === 'cancelled') echo 'active'; ?>">
                                    Cancelled
                                </button>
                                <button type="submit" name="status" value="noshow" class="btn btn-status btn-noshow <?php if (($appointment['status'] ?? '') === 'noshow') echo 'active'; ?>">
                                    No-Show
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="footer-actions">
                        <a href="index.php?page=appointments&action=edit&id=<?php echo $appointment['id']; ?>" class="btn">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="#" onclick="deleteAppointment(<?php echo $appointment['id']; ?>)" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                        <?php if ($client): ?>
                        <a href="index.php?page=clients&action=view&id=<?php echo $client['id']; ?>" class="btn btn-secondary">
                            <i class="fas fa-user"></i> View Client
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Appointment Form (Hidden) -->
<form id="delete-appointment-form" action="index.php?page=appointments&action=delete" method="post" style="display: none;">
    <input type="hidden" name="id" id="delete-appointment-id" value="<?php echo $appointment['id']; ?>">
</form>

<script>
function deleteAppointment(id) {
    if (confirm('Are you sure you want to delete this appointment? This action cannot be undone.')) {
        document.getElementById('delete-appointment-form').submit();
    }
}
</script>

<style>
.appointment-info {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.info-section {
    margin-bottom: 1.5rem;
}

.info-section h3 {
    margin-top: 0;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #eee;
}

.info-row {
    display: grid;
    grid-template-columns: 120px 1fr;
    margin-bottom: 0.5rem;
}

.info-row.notes {
    grid-template-columns: 120px 1fr;
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

.action-panel {
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.status-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.btn-status {
    flex: 1;
    min-width: 100px;
    text-align: center;
    font-size: 0.9rem;
    opacity: 0.7;
}

.btn-status.active {
    opacity: 1;
    font-weight: bold;
}

.btn-pending {
    background-color: #ffc107;
    color: #212529;
}

.btn-confirmed {
    background-color: #17a2b8;
    color: #fff;
}

.btn-completed {
    background-color: #28a745;
    color: #fff;
}

.btn-cancelled {
    background-color: #dc3545;
    color: #fff;
}

.btn-noshow {
    background-color: #dc3545;
    color: #fff;
}

.footer-actions {
    display: flex;
    gap: 0.5rem;
}

@media (max-width: 768px) {
    .appointment-info {
        grid-template-columns: 1fr;
    }

    .status-buttons {
        flex-direction: column;
    }
}
</style>
