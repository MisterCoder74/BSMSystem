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

// Get all available positions from existing staff
$positions = [];
foreach ($staff as $s) {
    if (!empty($s['position']) && !in_array($s['position'], $positions)) {
        $positions[] = $s['position'];
    }
}
sort($positions);
?>

<div class="staff-page">
    <div class="page-header">
        <h1>Edit Staff</h1>
        <a href="index.php?page=staff" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Staff
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="index.php?page=staff&action=edit" method="post" id="edit-staff-form">
                <input type="hidden" name="id" value="<?php echo $staff_member['id']; ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($staff_member['name']); ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="position">Position <span class="required">*</span></label>
                        <div class="position-input">
                            <select id="position" name="position" required>
                                <option value="">-- Select Position --</option>
                                <?php foreach ($positions as $position): ?>
                                <option value="<?php echo htmlspecialchars($position); ?>" <?php if ($staff_member['position'] === $position) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($position); ?>
                                </option>
                                <?php endforeach; ?>

                                <?php
                                // Add the current position if it's not in the list
                                if (!empty($staff_member['position']) && !in_array($staff_member['position'], $positions)):
                                ?>
                                <option value="<?php echo htmlspecialchars($staff_member['position']); ?>" selected>
                                    <?php echo htmlspecialchars($staff_member['position']); ?>
                                </option>
                                <?php endif; ?>
                            </select>

                            <div>
                                <span>Or add new: </span>
                                <input type="text" id="new-position" placeholder="New position">
                                <button type="button" onclick="addNewPosition()" class="btn btn-small">Add</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($staff_member['email'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($staff_member['phone'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="specialization">Specialization</label>
                    <input type="text" id="specialization" name="specialization" value="<?php echo htmlspecialchars($staff_member['specialization'] ?? ''); ?>" placeholder="e.g. Hair Coloring, Nail Art, etc.">
                </div>

                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea id="bio" name="bio" rows="4" placeholder="Staff member's biography or description..."><?php echo htmlspecialchars($staff_member['bio'] ?? ''); ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn">
                        <i class="fas fa-save"></i> Update Staff
                    </button>
                    <a href="index.php?page=staff" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function addNewPosition() {
    const newPosition = document.getElementById('new-position').value.trim();
    if (newPosition) {
        const positionSelect = document.getElementById('position');

        // Check if position already exists
        let exists = false;
        for (let i = 0; i < positionSelect.options.length; i++) {
            if (positionSelect.options[i].value === newPosition) {
                exists = true;
                positionSelect.selectedIndex = i;
                break;
            }
        }

        if (!exists) {
            // Add new option
            const option = document.createElement('option');
            option.value = newPosition;
            option.textContent = newPosition;
            positionSelect.appendChild(option);

            // Select the new option
            positionSelect.value = newPosition;
        }

        // Clear input
        document.getElementById('new-position').value = '';
    }
}

document.getElementById('edit-staff-form').addEventListener('submit', function(e) {
    // Basic form validation
    const name = document.getElementById('name').value.trim();
    const position = document.getElementById('position').value.trim();

    if (!name || !position) {
        e.preventDefault();
        alert('Please fill in all required fields.');
    }
});
</script>

<style>
.position-input {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.btn-small {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .position-input {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
