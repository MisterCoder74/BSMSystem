<?php
// Prevent direct access
if (!defined('BASE_PATH')) {
    die('Direct access not permitted');
}

// Date navigation
$prev_date = date('Y-m-d', strtotime($filter_date . ' -1 day'));
$next_date = date('Y-m-d', strtotime($filter_date . ' +1 day'));
$today = date('Y-m-d');
$formatted_date = date('l, F j, Y', strtotime($filter_date));
?>

<div class="appointments-page">
    <div class="page-header">
        <h1>Appointment Management</h1>
        <div class="page-actions">
            <a href="index.php?page=appointments&action=add" class="btn">
                <i class="fas fa-plus"></i> New Appointment
            </a>
            <a href="index.php?page=appointments&action=calendar" class="btn btn-secondary">
                <i class="fas fa-calendar-alt"></i> Calendar View
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header date-navigation">
            <a href="index.php?page=appointments&date=<?php echo $prev_date; ?>" class="nav-btn">
                <i class="fas fa-chevron-left"></i>
            </a>

            <h2>
                <?php if ($filter_date === $today): ?>
                <span class="today-badge">Today</span>
                <?php endif; ?>
                <?php echo $formatted_date; ?>
            </h2>

            <a href="index.php?page=appointments&date=<?php echo $next_date; ?>" class="nav-btn">
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>

        <div class="card-body">
            <div class="date-picker-container">
                <form action="index.php" method="get" id="date-picker-form">
                    <input type="hidden" name="page" value="appointments">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="date">Jump to Date:</label>
                            <input type="date" id="date" name="date" value="<?php echo $filter_date; ?>" onchange="this.form.submit()">
                        </div>

                        <button type="submit" class="btn btn-small">Go</button>

                        <a href="index.php?page=appointments&date=<?php echo $today; ?>" class="btn btn-small <?php if ($filter_date === $today) echo 'disabled'; ?>">
                            Today
                        </a>
                    </div>
                </form>
            </div>

            <?php if (count($filtered_appointments) > 0): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Client</th>
                            <th>Service</th>
                            <th>Staff</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($filtered_appointments as $appointment): ?>
                        <tr>
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
                                <?php
                                $staff_member = findById($staff, $appointment['staff_id'] ?? 0);
                                echo $staff_member ? $staff_member['name'] : 'N/A';
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
                                <a href="#" onclick="deleteAppointment(<?php echo $appointment['id']; ?>, '<?php echo $appointment['time']; ?>')" class="delete" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="no-records">
                <i class="fas fa-calendar-day"></i>
                <p>No appointments scheduled for <?php echo date('F j, Y', strtotime($filter_date)); ?>.</p>
                <a href="index.php?page=appointments&action=add&date=<?php echo $filter_date; ?>" class="btn">
                    <i class="fas fa-plus"></i> Add New Appointment
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Appointment Form (Hidden) -->
<form id="delete-appointment-form" action="index.php?page=appointments&action=delete" method="post" style="display: none;">
    <input type="hidden" name="id" id="delete-appointment-id">
</form>

<script>
function deleteAppointment(id, time) {
    if (confirm(`Are you sure you want to delete the appointment at ${time}? This action cannot be undone.`)) {
        document.getElementById('delete-appointment-id').value = id;
        document.getElementById('delete-appointment-form').submit();
    }
}
</script>

<style>
.date-navigation {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.today-badge {
    display: inline-block;
    background-color: #ff758c;
    color: white;
    font-size: 0.75rem;
    padding: 0.15rem 0.5rem;
    border-radius: 12px;
    margin-right: 0.5rem;
    vertical-align: middle;
}

.nav-btn {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
    border-radius: 50%;
    color: #555;
    text-decoration: none;
    transition: all 0.2s;
}

.nav-btn:hover {
    background-color: #ff758c;
    color: white;
}

.date-picker-container {
    margin-bottom: 1.5rem;
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

.no-records {
    text-align: center;
    padding: 2rem;
}

.no-records i {
    font-size: 3rem;
    color: #ddd;
    margin-bottom: 1rem;
}

.btn-small {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
