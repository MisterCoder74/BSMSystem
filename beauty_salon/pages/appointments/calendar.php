<?php
// Prevent direct access
if (!defined('BASE_PATH')) {
    die('Direct access not permitted');
}
$current_date = new DateTime();

// Ricava il numero di settimana corrente
$week = (int)$current_date->format('W');
//echo('<h1>week:</h1>' . $week);
// Get calendar view type (weekly or monthly)
$view_type = isset($_GET['view']) && $_GET['view'] === 'month' ? 'month' : 'week';

// Get month and year from URL parameters, or use current month/year
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

// Validate month and year
if ($month < 1 || $month > 12) {
    $month = (int)date('m');
}
if ($year < 2000 || $year > 2100) {
    $year = (int)date('Y');
}

// Get week for weekly view
$week = isset($_GET['week']) ? (int)$_GET['week'] : null;
$week_start_date = null;
$week_end_date = null;

// If no week is specified for weekly view, calculate the current week
if ($view_type === 'week' && is_null($week)) {
// Se non hai passato il parametro 'week', calcola la settimana corrente
$current_date = new DateTime();

// Ricava il numero di settimana corrente
$week = (int)$current_date->format('W');
//echo('<h1>week:</h1>' . $week);
// Ricava l'anno corrente, se vuoi essere molto preciso, puoi usare anche $current_date->format('Y')
$year = (int)$current_date->format('Y');

// Imposta la data di inizio settimana secondo questa data
$week_start_date = new DateTime();
$week_start_date->setISODate($year, $week); // questa è la data della domenica o lunedì( dipende dalla configurazione )

// La fine settimana
$week_end_date = clone $week_start_date;
$week_end_date->modify('+6 days');

// Aggiorna anche i parametri di mese e anno in modo coerente
$month = (int)$week_start_date->format('m');
$year = (int)$week_start_date->format('Y');
} else if ($view_type === 'week' && !is_null($week)) {
// Se hai passato week e anno
$week_start_date = new DateTime();
$week_start_date->setISODate($year, $week);
$week_end_date = clone $week_start_date;
$week_end_date->modify('+6 days');

$month = (int)$week_start_date->format('m');
$year = (int)$week_start_date->format('Y');
}


// Get previous and next navigation links
if ($view_type === 'month') {
        // Calcola la settimana corrente basata sul mese visualizzato
$current_date = new DateTime();

// Ricava il numero di settimana corrente
$week = (int)$current_date->format('W');  
        
    // Previous month
    $prev_month = $month - 1;
    $prev_year = $year;
    if ($prev_month < 1) {
        $prev_month = 12;
        $prev_year--;
    }

    // Next month
    $next_month = $month + 1;
    $next_year = $year;
    if ($next_month > 12) {
        $next_month = 1;
        $next_year++;
    }

    $prev_link = "index.php?page=appointments&action=calendar&view=month&month=$prev_month&year=$prev_year";
    $next_link = "index.php?page=appointments&action=calendar&view=month&month=$next_month&year=$next_year";
} else {
    // Previous week

        $prev_week = $week -1;


    // Next week

    $next_week = $week + 1;
    

    
        $prev_link = "index.php?page=appointments&action=calendar&view=week&week=$prev_week&year=$year";
    $next_link = "index.php?page=appointments&action=calendar&view=week&week=$next_week&year=$year";
}

// Format month name
$month_name = date('F', mktime(0, 0, 0, $month, 1, $year));

// Get appointments for the current month or week
if ($view_type === 'month') {
    // Get start and end dates for the month
    $start_date = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
    $end_date = date('Y-m-t', strtotime($start_date));
} else {
    // Get start and end dates for the week
    $start_date = $week_start_date->format('Y-m-d');
    $end_date = $week_end_date->format('Y-m-d');
}

// Filter appointments for the selected period
$period_appointments = array_filter($appointments, function($appointment) use ($start_date, $end_date) {
    return $appointment['date'] >= $start_date && $appointment['date'] <= $end_date;
});

// Group appointments by date
$appointments_by_date = [];
foreach ($period_appointments as $appointment) {
    $date = $appointment['date'];
    if (!isset($appointments_by_date[$date])) {
        $appointments_by_date[$date] = [];
    }
    $appointments_by_date[$date][] = $appointment;
}

// Sort appointments by time within each date
foreach ($appointments_by_date as $date => $date_appointments) {
    usort($date_appointments, function($a, $b) {
        return strcmp($a['time'], $b['time']);
    });
    $appointments_by_date[$date] = $date_appointments;
}

// For monthly view: Calculate the calendar grid
$first_day_of_month = mktime(0, 0, 0, $month, 1, $year);
$number_of_days = date('t', $first_day_of_month);
$first_day_of_week = date('N', $first_day_of_month);  // 1 (Monday) to 7 (Sunday)
$last_day_of_month = mktime(0, 0, 0, $month, $number_of_days, $year);
$last_day_of_week = date('N', $last_day_of_month);    // 1 (Monday) to 7 (Sunday)

// Calculate padding days for the grid
$padding_start = $first_day_of_week - 1;  // Days to pad at the beginning
$padding_end = 7 - $last_day_of_week;     // Days to pad at the end (if last day is Sunday, padding_end will be 0)
?>

<div class="appointments-page">
    <div class="page-header">
        <h1>Appointment Calendar</h1>
        <div class="page-actions">
            <a href="index.php?page=appointments&action=add" class="btn">
                <i class="fas fa-plus"></i> New Appointment
            </a>
            <a href="index.php?page=appointments" class="btn btn-secondary">
                <i class="fas fa-list"></i> List View
            </a>
        </div>
    </div>

    <!-- Calendar view type toggle -->
    <div class="view-toggle">
        <a href="index.php?page=appointments&action=calendar&view=month&month=<?php echo $month; ?>&year=<?php echo $year; ?>" class="<?php echo $view_type === 'month' ? 'active' : ''; ?>">Month View</a>
        <a href="index.php?page=appointments&action=calendar&view=week&week=<?php echo $week; ?>&year=<?php echo $year; ?>" class="<?php echo $view_type === 'week' ? 'active' : ''; ?>">Week View</a>
    </div>

    <div class="card">
        <div class="card-header calendar-header">
            <a href="<?php echo $prev_link; ?>" class="nav-btn">
                <i class="fas fa-chevron-left"></i>
            </a>

            <h2 id="current-month" data-month="<?php echo $month; ?>" data-year="<?php echo $year; ?>">
                <?php if ($view_type === 'month'): ?>
                    <?php echo $month_name . ' ' . $year; ?>
                <?php else: ?>
                    Week <?php echo $week; ?> (<?php echo $week_start_date->format('M j'); ?> - <?php echo $week_end_date->format('M j, Y'); ?>)
                <?php endif; ?>
            </h2>

            <a href="<?php echo $next_link; ?>" class="nav-btn">
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>

        <div class="card-body">
            <div class="calendar-container">
                <div class="calendar-days-header">
                    <div class="day-header">Monday</div>
                    <div class="day-header">Tuesday</div>
                    <div class="day-header">Wednesday</div>
                    <div class="day-header">Thursday</div>
                    <div class="day-header">Friday</div>
                    <div class="day-header">Saturday</div>
                    <div class="day-header">Sunday</div>
                </div>

                <?php if ($view_type === 'month'): ?>
                <!-- Month View -->
                <div class="calendar-grid month-view">
                    <?php
                    // Add padding days for the start of the month
                    for ($i = 0; $i < $padding_start; $i++) {
                        $prev_month_day = date('j', strtotime('-' . ($padding_start - $i) . ' days', $first_day_of_month));
                        echo '<div class="calendar-day padding-day">';
                        echo '<div class="day-number">' . $prev_month_day . '</div>';
                        echo '</div>';
                    }

                    // Add actual days of the month
                    $today = date('Y-m-d');
                    for ($day = 1; $day <= $number_of_days; $day++) {
                        $date = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
                        $day_class = $date === $today ? 'calendar-day today' : 'calendar-day';

                        echo '<div class="' . $day_class . '">';
                        echo '<div class="day-number">' . $day . '</div>';

                        // Display appointments for this day
                        if (isset($appointments_by_date[$date])) {
                            echo '<div class="day-appointments">';
                            foreach ($appointments_by_date[$date] as $appointment) {
                                $client = findById($clients, $appointment['client_id'] ?? 0);
                                $service = findById($services, $appointment['service_id'] ?? 0);

                                echo '<div class="appointment status-' . strtolower($appointment['status'] ?? 'pending') . '" data-id="' . $appointment['id'] . '">';
                                echo '<div class="appointment-time">' . date('g:i A', strtotime($appointment['time'])) . '</div>';
                                echo '<div class="appointment-client">' . ($client ? $client['name'] : 'N/A') . '</div>';
                                echo '<div class="appointment-service">' . ($service ? $service['name'] : 'N/A') . '</div>';
                                echo '<div class="appointment-actions">';
                                echo '<a href="index.php?page=appointments&action=view&id=' . $appointment['id'] . '" title="View"><i class="fas fa-eye"></i></a>';
                                echo '</div>';
                                echo '</div>';
                            }
                            echo '</div>';
                        }

                        // Add "+" button for adding new appointment on this day
                        echo '<a href="index.php?page=appointments&action=add&date=' . $date . '" class="add-appointment-btn" title="Add Appointment">';
                        echo '<i class="fas fa-plus"></i>';
                        echo '</a>';

                        echo '</div>';
                    }

                    // Add padding days for the end of the month
                    for ($i = 0; $i < $padding_end; $i++) {
                        $next_month_day = $i + 1;
                        echo '<div class="calendar-day padding-day">';
                        echo '<div class="day-number">' . $next_month_day . '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
                <?php else: ?>
                <!-- Week View -->
                <div class="calendar-grid week-view">
                    <?php
                    $current_date = clone $week_start_date;
                    $today = date('Y-m-d');

                    // Create 7 columns for the days of the week
                    for ($i = 0; $i < 7; $i++) {
                        $date = $current_date->format('Y-m-d');
                        $day_class = $date === $today ? 'calendar-day today' : 'calendar-day';

                        echo '<div class="' . $day_class . '">';
                        echo '<div class="day-number">' . $current_date->format('j') . '</div>';

                        // Display appointments for this day
                        if (isset($appointments_by_date[$date])) {
                            echo '<div class="day-appointments">';
                            foreach ($appointments_by_date[$date] as $appointment) {
                                $client = findById($clients, $appointment['client_id'] ?? 0);
                                $service = findById($services, $appointment['service_id'] ?? 0);

                                echo '<div class="appointment status-' . strtolower($appointment['status'] ?? 'pending') . '" data-id="' . $appointment['id'] . '">';
                                echo '<div class="appointment-time">' . date('g:i A', strtotime($appointment['time'])) . '</div>';
                                echo '<div class="appointment-client">' . ($client ? $client['name'] : 'N/A') . '</div>';
                                echo '<div class="appointment-service">' . ($service ? $service['name'] : 'N/A') . '</div>';
                                echo '<div class="appointment-actions">';
                                echo '<a href="index.php?page=appointments&action=view&id=' . $appointment['id'] . '" title="View"><i class="fas fa-eye"></i></a>';
                                echo '</div>';
                                echo '</div>';
                            }
                            echo '</div>';
                        }

                        // Add "+" button for adding new appointment on this day
                        echo '<a href="index.php?page=appointments&action=add&date=' . $date . '" class="add-appointment-btn" title="Add Appointment">';
                        echo '<i class="fas fa-plus"></i>';
                        echo '</a>';

                        echo '</div>';

                        // Move to the next day
                        $current_date->modify('+1 day');
                    }
                    ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Make appointments clickable to view details
    const appointments = document.querySelectorAll('.appointment');
    appointments.forEach(function(appointment) {
        appointment.addEventListener('click', function(event) {
            // Ignore if the click was on the action links
            if (event.target.tagName === 'A' || event.target.tagName === 'I') {
                return;
            }

            const id = this.getAttribute('data-id');
            window.location.href = 'index.php?page=appointments&action=view&id=' + id;
        });
    });
});
</script>

<style>
.view-toggle {
    display: flex;
    justify-content: center;
    margin-bottom: 1rem;
}

.view-toggle a {
    padding: 0.5rem 1rem;
    margin: 0 0.25rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    color: #555;
    background-color: #f8f9fa;
}

.view-toggle a.active {
    background-color: #ff758c;
    color: white;
    border-color: #ff758c;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
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

.calendar-container {
    margin-top: 1rem;
}

.calendar-days-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    background-color: #f8f9fa;
    border-top: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
}

.day-header {
    padding: 0.5rem;
    text-align: center;
    font-weight: 600;
    font-size: 0.9rem;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    border-left: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
}

.calendar-day {
    min-height: 120px;
    border-right: 1px solid #ddd;
    border-top: 1px solid #ddd;
    padding: 0.5rem;
    position: relative;
}

.calendar-day.today {
    background-color: #fff8f9;
}

.padding-day {
    background-color: #f8f9fa;
    color: #aaa;
}

.day-number {
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.day-appointments {
    margin-top: 0.5rem;
}

.appointment {
    background-color: #ffe9ee;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    margin-bottom: 0.25rem;
    font-size: 0.85rem;
    cursor: pointer;
    border-left: 3px solid #ff758c;
    position: relative;
}

.appointment-time {
    font-weight: 600;
    font-size: 0.75rem;
}

.appointment-client {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.appointment-service {
    font-size: 0.75rem;
    color: #666;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.appointment-actions {
    display: none;
    position: absolute;
    right: 0.25rem;
    top: 0.25rem;
}

.appointment:hover .appointment-actions {
    display: block;
}

.appointment.status-completed {
    background-color: #d4edda;
    border-left-color: #28a745;
}

.appointment.status-cancelled {
    background-color: #f8d7da;
    border-left-color: #dc3545;
    text-decoration: line-through;
}

.appointment.status-noshow {
    background-color: #f8d7da;
    border-left-color: #dc3545;
}

.appointment.status-confirmed {
    background-color: #d1ecf1;
    border-left-color: #17a2b8;
}

.add-appointment-btn {
    position: absolute;
    right: 0.5rem;
    bottom: 0.5rem;
    width: 24px;
    height: 24px;
    background-color: #ff758c;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 0.875rem;
    opacity: 0.7;
    transition: opacity 0.2s;
}

.add-appointment-btn:hover {
    opacity: 1;
}

/* Week view specific styles */
.week-view .calendar-day {
    min-height: 200px;
}

@media (max-width: 768px) {
    .calendar-days-header,
    .calendar-grid {
        grid-template-columns: repeat(1, 1fr);
    }

    .calendar-days-header .day-header {
        display: none;
    }

    .calendar-day {
        display: flex;
        flex-direction: column;
        border-top: 1px solid #ddd;
    }

    .day-number::before {
        content: attr(data-day);
        margin-right: 0.5rem;
        font-weight: normal;
    }

    .padding-day {
        display: none;
    }

    .calendar-day.today .day-number::after {
        content: " (Today)";
        color: #ff758c;
        font-weight: normal;
        font-size: 0.875rem;
    }
}
</style>
