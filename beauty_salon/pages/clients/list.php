<?php
// Prevent direct access
if (!defined('BASE_PATH')) {
    die('Direct access not permitted');
}

// Sort clients by name
usort($clients, function($a, $b) {
    return strcmp($a['name'], $b['name']);
});

// Search functionality
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
if (!empty($search)) {
    $clients = array_filter($clients, function($client) use ($search) {
        return (
            stripos($client['name'], $search) !== false ||
            stripos($client['email'], $search) !== false ||
            stripos($client['phone'], $search) !== false
        );
    });
}
?>

<div class="clients-page">
    <div class="page-header">
        <h1>Client Management</h1>
        <a href="index.php?page=clients&action=add" class="btn">
            <i class="fas fa-plus"></i> Add New Client
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="search-filter">
                <form action="index.php" method="get" class="search-form">
                    <input type="hidden" name="page" value="clients">
                    <div class="form-group">
                        <input type="text" name="search" placeholder="Search clients..." value="<?php echo $search; ?>">
                        <button type="submit" class="btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <?php if (!empty($search)): ?>
                <div class="search-results">
                    Found <?php echo count($clients); ?> results for "<?php echo $search; ?>"
                    <a href="index.php?page=clients" class="clear-search">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <?php if (count($clients) > 0): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Gender</th>    
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $client): ?>
                        <tr>
                            <td><?php echo $client['name']; ?></td>
                             <td><?php echo $client['gender']; ?></td>    
                            <td><?php echo $client['email'] ?? 'N/A'; ?></td>
                            <td><?php echo $client['phone']; ?></td>
                            <td><?php echo formatDate($client['created_at'] ?? date('Y-m-d')); ?></td>
                            <td class="table-actions">
                                <a href="index.php?page=clients&action=view&id=<?php echo $client['id']; ?>" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="index.php?page=clients&action=edit&id=<?php echo $client['id']; ?>" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" onclick="deleteClient(<?php echo $client['id']; ?>, '<?php echo addslashes($client['name']); ?>')" class="delete" title="Delete">
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
                <i class="fas fa-users"></i>
                <p>No clients found. Add your first client to get started!</p>
                <a href="index.php?page=clients&action=add" class="btn">
                    <i class="fas fa-plus"></i> Add New Client
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Client Form (Hidden) -->
<form id="delete-client-form" action="index.php?page=clients&action=delete" method="post" style="display: none;">
    <input type="hidden" name="id" id="delete-client-id">
</form>

<script>
function deleteClient(id, name) {
    if (confirm(`Are you sure you want to delete client "${name}"? This action cannot be undone.`)) {
        document.getElementById('delete-client-id').value = id;
        document.getElementById('delete-client-form').submit();
    }
}
</script>
