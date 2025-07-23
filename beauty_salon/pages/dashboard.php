<?php
// Prevent direct access
if (!defined('BASE_PATH')) {
    die('Direct access not permitted');
}

// Get data for dashboard
$clients = readJsonFile(CLIENTS_FILE);
$appointments = readJsonFile(APPOINTMENTS_FILE);
$services = readJsonFile(SERVICES_FILE);
$staff = readJsonFile(STAFF_FILE);

// Get today's appointments
$today = date('Y-m-d');
$today_appointments = array_filter($appointments, function($appointment) use ($today) {
    return $appointment['date'] === $today;
});

// Get upcoming appointments (next 7 days)
$week_later = date('Y-m-d', strtotime('+7 days'));
$upcoming_appointments = array_filter($appointments, function($appointment) use ($today, $week_later) {
    return $appointment['date'] > $today && $appointment['date'] <= $week_later;
});

// Calculate revenue (simple calculation for demo)
$total_revenue = 0;
foreach ($appointments as $appointment) {
    if (isset($appointment['status']) && $appointment['status'] === 'completed') {
        $total_revenue += $appointment['price'] ?? 0;
    }
}
?>

<div class="dashboard">
    <h1>Dashboard</h1>
    <p>Welcome back, <?php echo $_SESSION['user_name']; ?>! Here's an overview of your salon.</p>

    <div class="dashboard-stats">
        <div class="stat-card">
            <i class="fas fa-users"></i>
            <h3>Total Clients</h3>
            <div class="number"><?php echo count($clients); ?></div>
        </div>

        <div class="stat-card">
            <i class="fas fa-calendar-check"></i>
            <h3>Today's Appointments</h3>
            <div class="number"><?php echo count($today_appointments); ?></div>
        </div>

        <div class="stat-card">
            <i class="fas fa-concierge-bell"></i>
            <h3>Services Offered</h3>
            <div class="number"><?php echo count($services); ?></div>
        </div>

        <div class="stat-card">
            <i class="fas fa-dollar-sign"></i>
            <h3>Total Revenue</h3>
            <div class="number">$<?php echo number_format($total_revenue, 2); ?></div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h2>Today's Appointments</h2>
                </div>
                <div class="card-body">
                    <?php if (count($today_appointments) > 0): ?>
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
                            <?php foreach ($today_appointments as $appointment): ?>
                            <tr>
                                <td><?php echo $appointment['time']; ?></td>
                                <td>
                                    <?php
                                    $client = findById($clients, $appointment['client_id'] ?? 0);
                                    echo $client ? $client['name'] : 'N/A';
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
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p>No appointments scheduled for today.</p>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="index.php?page=appointments" class="btn">View All Appointments</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h2>Quick Actions</h2>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <a href="index.php?page=appointments&action=add" class="quick-action-btn">
                            <i class="fas fa-calendar-plus"></i>
                            <span>New Appointment</span>
                        </a>
                        <a href="index.php?page=clients&action=add" class="quick-action-btn">
                            <i class="fas fa-user-plus"></i>
                            <span>Add Client</span>
                        </a>
                        <a href="index.php?page=services&action=add" class="quick-action-btn">
                            <i class="fas fa-plus-circle"></i>
                            <span>Add Service</span>
                        </a>
                        <a href="index.php?page=staff&action=add" class="quick-action-btn">
                            <i class="fas fa-user-plus"></i>
                            <span>Add Staff</span>
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
                    <?php if (count($upcoming_appointments) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Client</th>
                                <th>Service</th>
                                <th>Staff</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($upcoming_appointments as $appointment): ?>
                            <tr>
                                <td><?php echo formatDate($appointment['date']); ?></td>
                                <td><?php echo $appointment['time']; ?></td>
                                <td>
                                    <?php
                                    $client = findById($clients, $appointment['client_id'] ?? 0);
                                    echo $client ? $client['name'] : 'N/A';
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
                    <?php else: ?>
                    <p>No upcoming appointments for the next 7 days.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
