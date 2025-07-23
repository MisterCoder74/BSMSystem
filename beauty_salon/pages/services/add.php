<?php
// Prevent direct access
if (!defined('BASE_PATH')) {
    die('Direct access not permitted');
}

// Get all available categories from existing services
$categories = [];
foreach ($services as $service) {
    if (!empty($service['category']) && !in_array($service['category'], $categories)) {
        $categories[] = $service['category'];
    }
}
sort($categories);
?>

<div class="services-page">
    <div class="page-header">
        <h1>Add New Service</h1>
        <a href="index.php?page=services" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Services
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="index.php?page=services&action=add" method="post" id="add-service-form">
                <div class="form-group">
                    <label for="name">Service Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Price ($) <span class="required">*</span></label>
                        <input type="number" id="price" name="price" step="0.01" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="duration">Duration (minutes) <span class="required">*</span></label>
                        <input type="number" id="duration" name="duration" min="5" step="5" value="30" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <div class="category-input">
                        <select id="category" name="category">
                            <option value="">-- Select Category --</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></option>
                            <?php endforeach; ?>
                        </select>

                        <div>
                            <span>Or add new: </span>
                            <input type="text" id="new-category" placeholder="New category name">
                            <button type="button" onclick="addNewCategory()" class="btn btn-small">Add</button>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn">
                        <i class="fas fa-save"></i> Save Service
                    </button>
                    <a href="index.php?page=services" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function addNewCategory() {
    const newCategory = document.getElementById('new-category').value.trim();
    if (newCategory) {
        const categorySelect = document.getElementById('category');

        // Check if category already exists
        let exists = false;
        for (let i = 0; i < categorySelect.options.length; i++) {
            if (categorySelect.options[i].value === newCategory) {
                exists = true;
                categorySelect.selectedIndex = i;
                break;
            }
        }

        if (!exists) {
            // Add new option
            const option = document.createElement('option');
            option.value = newCategory;
            option.textContent = newCategory;
            categorySelect.appendChild(option);

            // Select the new option
            categorySelect.value = newCategory;
        }

        // Clear input
        document.getElementById('new-category').value = '';
    }
}

document.getElementById('add-service-form').addEventListener('submit', function(e) {
    // Basic form validation
    const name = document.getElementById('name').value.trim();
    const price = parseFloat(document.getElementById('price').value);
    const duration = parseInt(document.getElementById('duration').value);

    if (!name || isNaN(price) || price <= 0 || isNaN(duration) || duration <= 0) {
        e.preventDefault();
        alert('Please fill in all required fields correctly. Price and duration must be positive numbers.');
    }
});
</script>

<style>
.category-input {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.btn-small {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .category-input {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
