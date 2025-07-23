<?php
// Prevent direct access
if (!defined('BASE_PATH')) {
    die('Direct access not permitted');
}

// Sort staff by name
usort($staff, function($a, $b) {
    return strcmp($a['name'], $b['name']);
});

// Group staff by position
$staff_by_position = [];
foreach ($staff as $staff_member) {
    $position = $staff_member['position'] ?? 'Other';
    if (!isset($staff_by_position[$position])) {
        $staff_by_position[$position] = [];
    }
    $staff_by_position[$position][] = $staff_member;
}
ksort($staff_by_position);
?>

<div class="staff-page">
    <div class="page-header">
        <h1>Staff Management</h1>
        <a href="index.php?page=staff&action=add" class="btn">
            <i class="fas fa-plus"></i> Add New Staff
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (count($staff) > 0): ?>
                <div class="staff-grid">
                    <?php foreach ($staff as $staff_member): ?>
                    <div class="staff-card">
                        <div class="staff-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="staff-details">
                            <h3 class="staff-name"><?php echo $staff_member['name']; ?></h3>
                            <div class="staff-position"><?php echo $staff_member['position']; ?></div>
                            <?php if (!empty($staff_member['specialization'])): ?>
                            <div class="staff-specialization"><?php echo $staff_member['specialization']; ?></div>
                            <?php endif; ?>
                            <?php if (!empty($staff_member['phone'])): ?>
                            <div class="staff-phone"><i class="fas fa-phone"></i> <?php echo $staff_member['phone']; ?></div>
                            <?php endif; ?>
                            <?php if (!empty($staff_member['email'])): ?>
                            <div class="staff-email"><i class="fas fa-envelope"></i> <?php echo $staff_member['email']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="staff-actions">
                            <a href="index.php?page=staff&action=view&id=<?php echo $staff_member['id']; ?>" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="index.php?page=staff&action=edit&id=<?php echo $staff_member['id']; ?>" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" onclick="deleteStaff(<?php echo $staff_member['id']; ?>, '<?php echo addslashes($staff_member['name']); ?>')" class="delete" title="Delete">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
            <div class="no-records">
                <i class="fas fa-user-tie"></i>
                <p>No staff members found. Add your first staff member to get started!</p>
                <a href="index.php?page=staff&action=add" class="btn">
                    <i class="fas fa-plus"></i> Add New Staff
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Staff Form (Hidden) -->
<form id="delete-staff-form" action="index.php?page=staff&action=delete" method="post" style="display: none;">
    <input type="hidden" name="id" id="delete-staff-id">
</form>

<script>
function deleteStaff(id, name) {
    if (confirm(`Are you sure you want to delete staff member "${name}"? This action cannot be undone.`)) {
        document.getElementById('delete-staff-id').value = id;
        document.getElementById('delete-staff-form').submit();
    }
}
</script>

<style>
.staff-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.staff-card {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    position: relative;
    transition: transform 0.2s, box-shadow 0.2s;
}

.staff-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.staff-avatar {
    font-size: 3rem;
    color: #ff758c;
    text-align: center;
    margin-bottom: 1rem;
}

.staff-details {
    text-align: center;
}

.staff-name {
    margin: 0 0 0.5rem;
    font-size: 1.2rem;
}

.staff-position {
    color: #ff758c;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.staff-specialization {
    margin-bottom: 0.5rem;
    font-style: italic;
    color: #666;
}

.staff-phone, .staff-email {
    font-size: 0.9rem;
    margin-top: 0.25rem;
    color: #555;
}

.staff-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 1.5rem;
    border-top: 1px solid #eee;
    padding-top: 1rem;
}

.staff-actions a {
    font-size: 1.1rem;
    color: #6c757d;
    transition: color 0.2s;
}

.staff-actions a:hover {
    color: #ff758c;
}

.staff-actions a.delete:hover {
    color: #dc3545;
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

@media (max-width: 768px) {
    .staff-grid {
        grid-template-columns: 1fr;
    }
}
</style>
