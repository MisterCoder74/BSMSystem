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

// Sort services by category
$categorized_services = [];
foreach ($services as $service) {
    $category = !empty($service['category']) ? $service['category'] : 'Uncategorized';
    if (!isset($categorized_services[$category])) {
        $categorized_services[$category] = [];
    }
    $categorized_services[$category][] = $service;
}
ksort($categorized_services);

// Get current service details for summary
$current_service = findById($services, $appointment['service_id'] ?? 0);
?>

<div class="appointments-page">
    <div class="page-header">
        <h1>Edit Appointment</h1>
        <a href="index.php?page=appointments" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Appointments
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="index.php?page=appointments&action=edit" method="post" id="edit-appointment-form">
                <input type="hidden" name="id" value="<?php echo $appointment['id']; ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label for="client_id">Client <span class="required">*</span></label>
                        <select name="client_id" id="client_id" required>
                            <option value="">-- Select Client --</option>
                            <?php foreach ($clients as $client): ?>
                            <option value="<?php echo $client['id']; ?>" <?php if ($client['id'] == ($appointment['client_id'] ?? 0)) echo 'selected'; ?>>
                                <?php echo $client['name']; ?> (<?php echo $client['phone']; ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="service_id">Service <span class="required">*</span></label>
                        <select name="service_id" id="service_id" required>
                            <option value="">-- Select Service --</option>
                            <?php foreach ($categorized_services as $category => $category_services): ?>
                            <optgroup label="<?php echo $category; ?>">
                                <?php foreach ($category_services as $service): ?>
                                <option value="<?php echo $service['id']; ?>"
                                        data-price="<?php echo $service['price']; ?>"
                                        data-duration="<?php echo $service['duration']; ?>"
                                        <?php if ($service['id'] == ($appointment['service_id'] ?? 0)) echo 'selected'; ?>>
                                    <?php echo $service['name']; ?> ($<?php echo number_format($service['price'], 2); ?>, <?php echo $service['duration']; ?> min)
                                </option>
                                <?php endforeach; ?>
                            </optgroup>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="staff_id">Staff <span class="required">*</span></label>
                        <select name="staff_id" id="staff_id" required>
                            <option value="">-- Select Staff --</option>
                            <?php foreach ($staff as $staff_member): ?>
                            <option value="<?php echo $staff_member['id']; ?>" <?php if ($staff_member['id'] == ($appointment['staff_id'] ?? 0)) echo 'selected'; ?>>
                                <?php echo $staff_member['name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="date">Date <span class="required">*</span></label>
                        <input type="date" id="date" name="date" value="<?php echo $appointment['date']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="time">Time <span class="required">*</span></label>
                        <input type="time" id="time" name="time" value="<?php echo $appointment['time']; ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status">
                            <option value="pending" <?php if (($appointment['status'] ?? '') === 'pending') echo 'selected'; ?>>Pending</option>
                            <option value="confirmed" <?php if (($appointment['status'] ?? '') === 'confirmed') echo 'selected'; ?>>Confirmed</option>
                            <option value="completed" <?php if (($appointment['status'] ?? '') === 'completed') echo 'selected'; ?>>Completed</option>
                            <option value="cancelled" <?php if (($appointment['status'] ?? '') === 'cancelled') echo 'selected'; ?>>Cancelled</option>
                            <option value="noshow" <?php if (($appointment['status'] ?? '') === 'noshow') echo 'selected'; ?>>No-Show</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" rows="3"><?php echo htmlspecialchars($appointment['notes'] ?? ''); ?></textarea>
                </div>

                <div class="appointment-summary">
                    <h3>Appointment Summary</h3>
                    <div class="summary-row">
                        <div class="summary-label">Service:</div>
                        <div class="summary-value" id="summary-service">
                            <?php echo $current_service ? $current_service['name'] : '-'; ?>
                        </div>
                    </div>
                    <div class="summary-row">
                        <div class="summary-label">Price:</div>
                        <div class="summary-value" id="summary-price">
                            <?php echo $current_service ? '$' . number_format($current_service['price'], 2) : '-'; ?>
                        </div>
                    </div>
                    <div class="summary-row">
                        <div class="summary-label">Duration:</div>
                        <div class="summary-value" id="summary-duration">
                            <?php echo $current_service ? $current_service['duration'] . ' minutes' : '-'; ?>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn">
                        <i class="fas fa-save"></i> Update Appointment
                    </button>
                    <a href="index.php?page=appointments" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const serviceSelect = document.getElementById('service_id');
    const summaryService = document.getElementById('summary-service');
    const summaryPrice = document.getElementById('summary-price');
    const summaryDuration = document.getElementById('summary-duration');

    // Update summary when service changes
    serviceSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];

        if (this.value) {
            const serviceName = selectedOption.textContent.split('(')[0].trim();
            const price = selectedOption.getAttribute('data-price');
            const duration = selectedOption.getAttribute('data-duration');

            summaryService.textContent = serviceName;
            summaryPrice.textContent = '$' + parseFloat(price).toFixed(2);
            summaryDuration.textContent = duration + ' minutes';
        } else {
            summaryService.textContent = '-';
            summaryPrice.textContent = '-';
            summaryDuration.textContent = '-';
        }
    });

    // Form validation
    document.getElementById('edit-appointment-form').addEventListener('submit', function(e) {
        const client = document.getElementById('client_id').value;
        const service = document.getElementById('service_id').value;
        const staff = document.getElementById('staff_id').value;
        const date = document.getElementById('date').value;
        const time = document.getElementById('time').value;

        if (!client || !service || !staff || !date || !time) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
});
</script>

<style>
.form-hint {
    margin-top: 0.25rem;
    font-size: 0.875rem;
}

.form-hint a {
    color: #ff758c;
    text-decoration: none;
}

.form-hint a:hover {
    text-decoration: underline;
}

.appointment-summary {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    margin: 1.5rem 0;
}

.appointment-summary h3 {
    margin-top: 0;
    margin-bottom: 1rem;
    font-size: 1.1rem;
    color: #555;
}

.summary-row {
    display: flex;
    margin-bottom: 0.5rem;
}

.summary-label {
    width: 100px;
    font-weight: 600;
}

.summary-value {
    flex: 1;
}
</style>
