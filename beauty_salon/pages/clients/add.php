<?php
// Prevent direct access
if (!defined('BASE_PATH')) {
    die('Direct access not permitted');
}
?>

<div class="clients-page">
    <div class="page-header">
        <h1>Add New Client</h1>
        <a href="index.php?page=clients" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Clients
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="index.php?page=clients&action=add" method="post" id="add-client-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" required>
                    </div>
                </div>
<!-- New Gender Field -->
<div class="form-group">
<label>Gender <span class="required">*</span></label>
<div>
<input type="radio" id="gender-m" name="gender" value="M" required> M
</div>
<div>
<input type="radio" id="gender-f" name="gender" value="F" required> F
</div>
</div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email">
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone <span class="required">*</span></label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="2"></textarea>
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" rows="3"></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn">
                        <i class="fas fa-save"></i> Save Client
                    </button>
                    <a href="index.php?page=clients" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('add-client-form').addEventListener('submit', function(e) {
    // Basic form validation
    const name = document.getElementById('name').value.trim();
    const phone = document.getElementById('phone').value.trim();

    if (!name || !phone) {
        e.preventDefault();
        alert('Please fill in all required fields.');
    }
});
</script>
