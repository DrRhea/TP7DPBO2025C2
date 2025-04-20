<?php
// Menangani form submissions untuk CRUD operations
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'add' && isset($_POST['fish_id'], $_POST['feeding_time'], $_POST['food_type'])) {
        $feeding_logs->addLog($_POST['fish_id'], $_POST['feeding_time'], $_POST['food_type']);
        header("Location: index.php?page=feeding_logs");
        exit;
    } elseif ($_POST['action'] == 'update' && isset($_POST['id'], $_POST['fish_id'], $_POST['feeding_time'], $_POST['food_type'])) {
        $feeding_logs->updateLog($_POST['id'], $_POST['fish_id'], $_POST['feeding_time'], $_POST['food_type']);
        header("Location: index.php?page=feeding_logs");
        exit;
    } elseif ($_POST['action'] == 'delete' && isset($_POST['id'])) {
        $feeding_logs->deleteLog($_POST['id']);
        header("Location: index.php?page=feeding_logs");
        exit;
    }
}

// Handle search dan date range filter
$allFish = $fish->getAllFish();
$logs = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $logs = $feeding_logs->searchLogs($_GET['search']);
} elseif (isset($_GET['start_date']) && isset($_GET['end_date']) && !empty($_GET['start_date']) && !empty($_GET['end_date'])) {
    $logs = $feeding_logs->searchLogsByDateRange($_GET['start_date'], $_GET['end_date']);
} else {
    $logs = $feeding_logs->getAllLogs();
}

// Get log untuk editing jika edit parameter is set
$editLog = null;
if (isset($_GET['edit'])) {
    $editLog = $feeding_logs->getLogById($_GET['edit']);
}

// Get current date dan time untuk the form
$currentDateTime = date('Y-m-d\TH:i');
?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold">Feeding Logs</h2>
    
    <!-- Search dan Filter Forms -->
    <div class="flex space-x-2">
        <!-- Text Search -->
        <form action="index.php" method="GET" class="flex items-center">
            <input type="hidden" name="page" value="feeding_logs">
            <input type="text" name="search" placeholder="Search by fish or food type" 
                   value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
                   class="border rounded py-1 px-3 mr-2">
            <button type="submit" class="bg-yellow-500 text-white py-1 px-4 rounded hover:bg-yellow-600">
                Search
            </button>
        </form>
        
        <!-- Date Range Filter -->
        <form action="index.php" method="GET" class="flex items-center">
            <input type="hidden" name="page" value="feeding_logs">
            <input type="date" name="start_date" 
                   value="<?= isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : date('Y-m-d', strtotime('-7 days')) ?>"
                   class="border rounded py-1 px-2 mr-1">
            <span class="mx-1">to</span>
            <input type="date" name="end_date" 
                   value="<?= isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : date('Y-m-d') ?>"
                   class="border rounded py-1 px-2 mr-2">
            <button type="submit" class="bg-yellow-500 text-white py-1 px-4 rounded hover:bg-yellow-600">
                Filter
            </button>
        </form>
        
        <!-- Clear Filters -->
        <?php if (isset($_GET['search']) || isset($_GET['start_date'])): ?>
            <a href="index.php?page=feeding_logs" class="flex items-center text-yellow-500 hover:underline">
                Clear Filters
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Form untuk menambahkan/editing feeding logs -->
<div class="bg-gray-50 p-4 rounded-lg shadow mb-6">
    <h3 class="text-lg font-medium mb-3"><?= $editLog ? 'Edit Feeding Log' : 'Add New Feeding Log' ?></h3>
    <form action="index.php?page=feeding_logs<?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?><?= isset($_GET['start_date']) ? '&start_date='.urlencode($_GET['start_date']).'&end_date='.urlencode($_GET['end_date']) : '' ?>" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input type="hidden" name="action" value="<?= $editLog ? 'update' : 'add' ?>">
        <?php if ($editLog): ?>
            <input type="hidden" name="id" value="<?= $editLog['id'] ?>">
        <?php endif; ?>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Fish</label>
            <select name="fish_id" required class="w-full border rounded py-1 px-3">
                <option value="">Select Fish</option>
                <?php foreach ($allFish as $f): ?>
                    <option value="<?= $f['id'] ?>" <?= ($editLog && $editLog['fish_id'] == $f['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($f['name']) ?> (<?= htmlspecialchars($f['species_name']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Feeding Time</label>
            <input type="datetime-local" name="feeding_time" required 
                   value="<?= $editLog ? date('Y-m-d\TH:i', strtotime($editLog['feed_time'])) : $currentDateTime ?>" 
                   class="w-full border rounded py-1 px-3">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Food Type</label>
            <select name="food_type" required class="w-full border rounded py-1 px-3">
                <option value="">Select Food Type</option>
                <option value="Flakes" <?= ($editLog && $editLog['food_type'] == 'Flakes') ? 'selected' : '' ?>>Flakes</option>
                <option value="Pellets" <?= ($editLog && $editLog['food_type'] == 'Pellets') ? 'selected' : '' ?>>Pellets</option>
                <option value="Live Food" <?= ($editLog && $editLog['food_type'] == 'Live Food') ? 'selected' : '' ?>>Live Food</option>
                <option value="Frozen Food" <?= ($editLog && $editLog['food_type'] == 'Frozen Food') ? 'selected' : '' ?>>Frozen Food</option>
                <option value="Vegetables" <?= ($editLog && $editLog['food_type'] == 'Vegetables') ? 'selected' : '' ?>>Vegetables</option>
                <option value="Other" <?= ($editLog && $editLog['food_type'] == 'Other') ? 'selected' : '' ?>>Other</option>
            </select>
        </div>
        
        <div class="flex items-end">
            <button type="submit" class="bg-yellow-500 text-white py-1 px-4 rounded hover:bg-yellow-600">
                <?= $editLog ? 'Update' : 'Add' ?> Feeding Log
            </button>
            <?php if ($editLog): ?>
                <a href="index.php?page=feeding_logs<?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?><?= isset($_GET['start_date']) ? '&start_date='.urlencode($_GET['start_date']).'&end_date='.urlencode($_GET['end_date']) : '' ?>" 
                   class="ml-2 text-gray-500 hover:underline">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Tabel Feeding logs -->
<div class="overflow-x-auto">
    <table class="min-w-full bg-white border rounded shadow-md">
        <thead>
            <tr class="bg-yellow-100 text-left">
                <th class="py-2 px-4">ID</th>
                <th class="py-2 px-4">Fish</th>
                <th class="py-2 px-4">Feeding Time</th>
                <th class="py-2 px-4">Food Type</th>
                <th class="py-2 px-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($logs) > 0): ?>
                <?php foreach ($logs as $log): ?>
                <tr class="border-b hover:bg-yellow-50 transition">
                    <td class="py-2 px-4"><?= $log['id'] ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($log['fish_name']) ?></td>
                    <td class="py-2 px-4">
                        <?= date('M d, Y H:i', strtotime($log['feed_time'])) ?>
                        <span class="text-xs text-gray-500">
                            (<?= date('g:i A', strtotime($log['feed_time'])) ?>)
                        </span>
                    </td>
                    <td class="py-2 px-4">
                        <span class="px-2 py-1 rounded text-xs font-medium
                            <?php 
                            switch($log['food_type']) {
                                case 'Flakes': echo 'bg-orange-100 text-orange-800'; break;
                                case 'Pellets': echo 'bg-amber-100 text-amber-800'; break;
                                case 'Live Food': echo 'bg-red-100 text-red-800'; break;
                                case 'Frozen Food': echo 'bg-blue-100 text-blue-800'; break;
                                case 'Vegetables': echo 'bg-green-100 text-green-800'; break;
                                default: echo 'bg-gray-100 text-gray-800';
                            }
                            ?>">
                            <?= htmlspecialchars($log['food_type']) ?>
                        </span>
                    </td>
                    <td class="py-2 px-4">
                        <div class="flex space-x-2">
                            <a href="index.php?page=feeding_logs&edit=<?= $log['id'] ?><?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?><?= isset($_GET['start_date']) ? '&start_date='.urlencode($_GET['start_date']).'&end_date='.urlencode($_GET['end_date']) : '' ?>" 
                               class="text-blue-500 hover:underline">Edit</a>
                            
                            <form action="index.php?page=feeding_logs<?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?><?= isset($_GET['start_date']) ? '&start_date='.urlencode($_GET['start_date']).'&end_date='.urlencode($_GET['end_date']) : '' ?>" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this feeding log?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $log['id'] ?>">
                                <button type="submit" class="text-red-500 hover:underline">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="py-4 px-4 text-center text-gray-500">No feeding logs found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>