<?php
// Prevent direct access
if (!defined('BASE_PATH')) {
    die('Direct access not permitted');
}

// Group services by category
$categorized_services = [];
foreach ($services as $service) {
    $category = !empty($service['category']) ? $service['category'] : 'Uncategorized';
    if (!isset($categorized_services[$category])) {
        $categorized_services[$category] = [];
    }
    $categorized_services[$category][] = $service;
}

// Sort categories alphabetically
ksort($categorized_services);

// Sort services within categories by name
foreach ($categorized_services as &$category_services) {
    usort($category_services, function($a, $b) {
        return strcmp($a['name'], $b['name']);
    });
}
?>

<div class="services-page">
    <div class="page-header">
        <h1>Service Management</h1>
        <a href="index.php?page=services&action=add" class="btn">
            <i class="fas fa-plus"></i> Add New Service
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (count($services) > 0): ?>
                <?php foreach ($categorized_services as $category => $category_services): ?>
                <div class="service-category">
                    <h2><?php echo $category; ?></h2>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Service Name</th>
                                    <th>Duration</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($category_services as $service): ?>
                                <tr>
                                    <td>
                                        <div class="service-name"><?php echo $service['name']; ?></div>
                                        <?php if (!empty($service['description'])): ?>
                                        <div class="service-description"><?php echo $service['description']; ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $service['duration']; ?> min</td>
                                    <td>$<?php echo number_format($service['price'], 2); ?></td>
                                    <td class="table-actions">
                                        <a href="index.php?page=services&action=edit&id=<?php echo $service['id']; ?>" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" onclick="deleteService(<?php echo $service['id']; ?>, '<?php echo addslashes($service['name']); ?>')" class="delete" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
            <div class="no-records">
                <i class="fas fa-concierge-bell"></i>
                <p>No services found. Add your first service to get started!</p>
                <a href="index.php?page=services&action=add" class="btn">
                    <i class="fas fa-plus"></i> Add New Service
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Service Form (Hidden) -->
<form id="delete-service-form" action="index.php?page=services&action=delete" method="post" style="display: none;">
    <input type="hidden" name="id" id="delete-service-id">
</form>

<script>
function deleteService(id, name) {
    if (confirm(`Are you sure you want to delete service "${name}"? This action cannot be undone.`)) {
        document.getElementById('delete-service-id').value = id;
        document.getElementById('delete-service-form').submit();
    }
}
</script>

<style>
.service-category {
    margin-bottom: 2rem;
}

.service-category h2 {
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #eee;
    color: #ff758c;
}

.service-name {
    font-weight: 600;
}

.service-description {
    font-size: 0.85rem;
    color: #666;
    margin-top: 0.25rem;
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
</style>
