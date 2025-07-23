<?php
// Prevent direct access
if (!defined('BASE_PATH')) {
    die('Direct access not permitted');
}

// Get data for reports
$appointments = readJsonFile(APPOINTMENTS_FILE);
$clients = readJsonFile(CLIENTS_FILE);
$services = readJsonFile(SERVICES_FILE);
$staff = readJsonFile(STAFF_FILE);

// Date filters
$filter_start = isset($_GET['start_date']) ? sanitize($_GET['start_date']) : date('Y-m-01'); // First day of current month
$filter_end = isset($_GET['end_date']) ? sanitize($_GET['end_date']) : date('Y-m-t'); // Last day of current month

// Filter appointments by date range
$filtered_appointments = array_filter($appointments, function($appointment) use ($filter_start, $filter_end) {
    return $appointment['date'] >= $filter_start && $appointment['date'] <= $filter_end;
});

// Calculate statistics
$total_appointments = count($filtered_appointments);
$completed_appointments = 0;
$cancelled_appointments = 0;
$noshow_appointments = 0;
$total_revenue = 0;

foreach ($filtered_appointments as $appointment) {
    $status = $appointment['status'] ?? 'pending';

    if ($status === 'completed') {
        $completed_appointments++;
        $total_revenue += $appointment['price'] ?? 0;
    } else if ($status === 'cancelled') {
        $cancelled_appointments++;
    } else if ($status === 'noshow') {
        $noshow_appointments++;
    }
}

// Calculate revenue by service
$revenue_by_service = [];
foreach ($filtered_appointments as $appointment) {
    if (($appointment['status'] ?? '') === 'completed') {
        $service_id = $appointment['service_id'] ?? 0;
        $service = findById($services, $service_id);
        $service_name = $service ? $service['name'] : 'Unknown';

        if (!isset($revenue_by_service[$service_name])) {
            $revenue_by_service[$service_name] = [
                'count' => 0,
                'revenue' => 0
            ];
        }

        $revenue_by_service[$service_name]['count']++;
        $revenue_by_service[$service_name]['revenue'] += $appointment['price'] ?? 0;
    }
}

// Sort services by revenue (highest first)
uasort($revenue_by_service, function($a, $b) {
    return $b['revenue'] - $a['revenue'];
});

// Calculate appointments by staff
$appointments_by_staff = [];
foreach ($filtered_appointments as $appointment) {
    $staff_id = $appointment['staff_id'] ?? 0;
    $staff_member = findById($staff, $staff_id);
    $staff_name = $staff_member ? $staff_member['name'] : 'Unknown';

    if (!isset($appointments_by_staff[$staff_name])) {
        $appointments_by_staff[$staff_name] = [
            'total' => 0,
            'completed' => 0,
            'cancelled' => 0,
            'noshow' => 0,
            'revenue' => 0
        ];
    }

    $appointments_by_staff[$staff_name]['total']++;

    $status = $appointment['status'] ?? 'pending';
    if ($status === 'completed') {
        $appointments_by_staff[$staff_name]['completed']++;
        $appointments_by_staff[$staff_name]['revenue'] += $appointment['price'] ?? 0;
    } else if ($status === 'cancelled') {
        $appointments_by_staff[$staff_name]['cancelled']++;
    } else if ($status === 'noshow') {
        $appointments_by_staff[$staff_name]['noshow']++;
    }
}

// Sort staff by revenue (highest first)
uasort($appointments_by_staff, function($a, $b) {
    return $b['revenue'] - $a['revenue'];
});
?>

<div class="reports-page">
    <div class="page-header">
        <h1>Reports</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Date Range Filter</h2>
        </div>
        <div class="card-body">
            <form action="index.php" method="get" id="date-filter-form">
                <input type="hidden" name="page" value="reports">
                <div class="form-row">
                    <div class="form-group">
                        <label for="start_date">Start Date:</label>
                        <input type="date" id="start_date" name="start_date" value="<?php echo $filter_start; ?>">
                    </div>

                    <div class="form-group">
                        <label for="end_date">End Date:</label>
                        <input type="date" id="end_date" name="end_date" value="<?php echo $filter_end; ?>">
                    </div>

                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn">Apply Filter</button>
                    </div>

                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" onclick="setDateRange('month')" class="btn btn-secondary">This Month</button>
                        <button type="button" onclick="setDateRange('year')" class="btn btn-secondary">This Year</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="reports-summary">
        <h2>Summary for <?php echo formatDate($filter_start); ?> to <?php echo formatDate($filter_end); ?></h2>

        <div class="summary-cards">
            <div class="summary-card">
                <div class="summary-title">Total Revenue</div>
                <div class="summary-value">$<?php echo number_format($total_revenue, 2); ?></div>
            </div>

            <div class="summary-card">
                <div class="summary-title">Total Appointments</div>
                <div class="summary-value"><?php echo $total_appointments; ?></div>
            </div>

            <div class="summary-card">
                <div class="summary-title">Completed Appointments</div>
                <div class="summary-value"><?php echo $completed_appointments; ?></div>
                <div class="summary-percentage">
                    <?php echo $total_appointments > 0 ? round(($completed_appointments / $total_appointments) * 100) : 0; ?>%
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-title">Cancelled/No-Show</div>
                <div class="summary-value"><?php echo $cancelled_appointments + $noshow_appointments; ?></div>
                <div class="summary-percentage">
                    <?php echo $total_appointments > 0 ? round((($cancelled_appointments + $noshow_appointments) / $total_appointments) * 100) : 0; ?>%
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h2>Revenue by Service</h2>
                </div>
                <div class="card-body">
                    <?php if (count($revenue_by_service) > 0): ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Appointments</th>
                                    <th>Revenue</th>
                                    <th>% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($revenue_by_service as $service_name => $data): ?>
                                <tr>
                                    <td><?php echo $service_name; ?></td>
                                    <td><?php echo $data['count']; ?></td>
                                    <td>$<?php echo number_format($data['revenue'], 2); ?></td>
                                    <td>
                                        <?php echo $total_revenue > 0 ? round(($data['revenue'] / $total_revenue) * 100) : 0; ?>%
                                        <div class="percentage-bar">
                                            <div class="percentage-fill" style="width: <?php echo $total_revenue > 0 ? ($data['revenue'] / $total_revenue) * 100 : 0; ?>%;"></div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p>No revenue data available for the selected period.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h2>Staff Performance</h2>
                </div>
                <div class="card-body">
                    <?php if (count($appointments_by_staff) > 0): ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Staff Member</th>
                                    <th>Total Appointments</th>
                                    <th>Completed</th>
                                    <th>Cancelled/No-Show</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($appointments_by_staff as $staff_name => $data): ?>
                                <tr>
                                    <td><?php echo $staff_name; ?></td>
                                    <td><?php echo $data['total']; ?></td>
                                    <td>
                                        <?php echo $data['completed']; ?>
                                        <span class="percentage">
                                            (<?php echo $data['total'] > 0 ? round(($data['completed'] / $data['total']) * 100) : 0; ?>%)
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo $data['cancelled'] + $data['noshow']; ?>
                                        <span class="percentage">
                                            (<?php echo $data['total'] > 0 ? round((($data['cancelled'] + $data['noshow']) / $data['total']) * 100) : 0; ?>%)
                                        </span>
                                    </td>
                                    <td>$<?php echo number_format($data['revenue'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p>No staff performance data available for the selected period.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h2>Actions</h2>
                </div>
                <div class="card-body">
                    <div class="actions-grid">
                        <button type="button" class="btn" onclick="window.print()">
                            <i class="fas fa-print"></i> Print Report
                        </button>
                        <!-- Add more actions here if needed -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setDateRange(period) {
    const today = new Date();
    let startDate, endDate;

    if (period === 'month') {
        startDate = new Date(today.getFullYear(), today.getMonth(), 1);
        endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    } else if (period === 'year') {
        startDate = new Date(today.getFullYear(), 0, 1);
        endDate = new Date(today.getFullYear(), 11, 31);
    }

    document.getElementById('start_date').value = formatDateForInput(startDate);
    document.getElementById('end_date').value = formatDateForInput(endDate);
    document.getElementById('date-filter-form').submit();
}

function formatDateForInput(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}
</script>

<style>
.reports-summary {
    margin: 1.5rem 0;
}

.summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.summary-card {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    text-align: center;
}

.summary-title {
    font-weight: 600;
    color: #555;
    margin-bottom: 0.5rem;
}

.summary-value {
    font-size: 2rem;
    font-weight: 700;
    color: #ff758c;
    margin-bottom: 0.5rem;
}

.summary-percentage {
    font-size: 1.2rem;
    color: #6c757d;
}

.percentage {
    font-size: 0.85rem;
    color: #6c757d;
}

.percentage-bar {
    height: 10px;
    background-color: #f1f1f1;
    border-radius: 5px;
    margin-top: 5px;
    overflow: hidden;
}

.percentage-fill {
    height: 100%;
    background-color: #ff758c;
    border-radius: 5px;
}

.actions-grid {
    display: flex;
    gap: 1rem;
}

/* Print styles */
@media print {
    nav, .page-actions, .card-header, .actions-grid {
        display: none;
    }

    body, .container {
        background-color: white;
        padding: 0;
        margin: 0;
    }

    .card {
        box-shadow: none;
        margin-bottom: 1rem;
        page-break-inside: avoid;
    }

    a {
        text-decoration: none;
        color: black;
    }

    .reports-page h1 {
        text-align: center;
        margin-bottom: 2rem;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    th, td {
        border: 1px solid #ddd;
    }
}
</style>
