<?php
// Menangani form submissions untuk CRUD operations
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'add' && isset($_POST['aquarium_id'], $_POST['check_date'], $_POST['temperature'], $_POST['ph'], $_POST['ammonia_level'])) {
        $water_quality_logs->addLog($_POST['aquarium_id'], $_POST['check_date'], $_POST['temperature'], $_POST['ph'], $_POST['ammonia_level']);
        header("Location: index.php?page=water_quality_logs");
        exit;
    } elseif ($_POST['action'] == 'update' && isset($_POST['id'], $_POST['aquarium_id'], $_POST['check_date'], $_POST['temperature'], $_POST['ph'], $_POST['ammonia_level'])) {
        $water_quality_logs->updateLog($_POST['id'], $_POST['aquarium_id'], $_POST['check_date'], $_POST['temperature'], $_POST['ph'], $_POST['ammonia_level']);
        header("Location: index.php?page=water_quality_logs");
        exit;
    } elseif ($_POST['action'] == 'delete' && isset($_POST['id'])) {
        $water_quality_logs->deleteLog($_POST['id']);
        header("Location: index.php?page=water_quality_logs");
        exit;
    }
}

// Menangani search dan date range filter
$allAquariums = $aquarium->getAllAquariums();
$logs = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $logs = $water_quality_logs->searchLogs($_GET['search']);
} elseif (isset($_GET['start_date']) && isset($_GET['end_date']) && !empty($_GET['start_date']) && !empty($_GET['end_date'])) {
    $logs = $water_quality_logs->searchLogsByDateRange($_GET['start_date'], $_GET['end_date']);
} else {
    $logs = $water_quality_logs->getAllLogs();
}

// Get log untuk pengeditan jika parameter edit disetel
$editLog = null;
if (isset($_GET['edit'])) {
    $editLog = $water_quality_logs->getLogById($_GET['edit']);
}

// Get current date dan time untuk form-nya
$currentDateTime = date('Y-m-d\TH:i');
?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold">Water Quality Logs</h2>
    
    <!-- Search dan Filter Forms -->
    <div class="flex space-x-2">
        <!-- Text Search -->
        <form action="index.php" method="GET" class="flex items-center">
            <input type="hidden" name="page" value="water_quality_logs">
            <input type="text" name="search" placeholder="Search by aquarium name" 
                   value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
                   class="border rounded py-1 px-3 mr-2">
            <button type="submit" class="bg-blue-500 text-white py-1 px-4 rounded hover:bg-blue-600">
                Search
            </button>
        </form>
        
        <!-- Date Range Filter -->
        <form action="index.php" method="GET" class="flex items-center">
            <input type="hidden" name="page" value="water_quality_logs">
            <input type="date" name="start_date" 
                   value="<?= isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : date('Y-m-d', strtotime('-7 days')) ?>"
                   class="border rounded py-1 px-2 mr-1">
            <span class="mx-1">to</span>
            <input type="date" name="end_date" 
                   value="<?= isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : date('Y-m-d') ?>"
                   class="border rounded py-1 px-2 mr-2">
            <button type="submit" class="bg-blue-500 text-white py-1 px-4 rounded hover:bg-blue-600">
                Filter
            </button>
        </form>
        
        <!-- Clear Filters -->
        <?php if (isset($_GET['search']) || isset($_GET['start_date'])): ?>
            <a href="index.php?page=water_quality_logs" class="flex items-center text-blue-500 hover:underline">
                Clear Filters
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Form untuk menambahkan/editing water quality logs -->
<div class="bg-gray-50 p-4 rounded-lg shadow mb-6">
    <h3 class="text-lg font-medium mb-3"><?= $editLog ? 'Edit Water Quality Log' : 'Add New Water Quality Log' ?></h3>
    <form action="index.php?page=water_quality_logs<?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?><?= isset($_GET['start_date']) ? '&start_date='.urlencode($_GET['start_date']).'&end_date='.urlencode($_GET['end_date']) : '' ?>" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <input type="hidden" name="action" value="<?= $editLog ? 'update' : 'add' ?>">
        <?php if ($editLog): ?>
            <input type="hidden" name="id" value="<?= $editLog['id'] ?>">
        <?php endif; ?>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Aquarium</label>
            <select name="aquarium_id" required class="w-full border rounded py-1 px-3">
                <option value="">Select Aquarium</option>
                <?php foreach ($allAquariums as $a): ?>
                    <option value="<?= $a['id'] ?>" <?= ($editLog && $editLog['aquarium_id'] == $a['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Check Date & Time</label>
            <input type="datetime-local" name="check_date" required 
                   value="<?= $editLog ? date('Y-m-d\TH:i', strtotime($editLog['check_date'])) : $currentDateTime ?>" 
                   class="w-full border rounded py-1 px-3">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Temperature (°C)</label>
            <input type="number" name="temperature" required step="0.1" min="0" max="40"
                   value="<?= $editLog ? $editLog['temperature'] : '' ?>" 
                   class="w-full border rounded py-1 px-3">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">pH Level</label>
            <input type="number" name="ph" required step="0.1" min="0" max="14"
                   value="<?= $editLog ? $editLog['ph'] : '' ?>" 
                   class="w-full border rounded py-1 px-3">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ammonia Level (ppm)</label>
            <input type="number" name="ammonia_level" required step="0.01" min="0" max="10"
                   value="<?= $editLog ? $editLog['ammonia_level'] : '' ?>" 
                   class="w-full border rounded py-1 px-3">
        </div>
        
        <div class="md:col-span-5 flex items-end">
            <button type="submit" class="bg-blue-500 text-white py-1 px-4 rounded hover:bg-blue-600">
                <?= $editLog ? 'Update' : 'Add' ?> Water Quality Log
            </button>
            <?php if ($editLog): ?>
                <a href="index.php?page=water_quality_logs<?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?><?= isset($_GET['start_date']) ? '&start_date='.urlencode($_GET['start_date']).'&end_date='.urlencode($_GET['end_date']) : '' ?>" 
                   class="ml-2 text-gray-500 hover:underline">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- tabel Water quality logs -->
<div class="overflow-x-auto">
    <table class="min-w-full bg-white border rounded shadow-md">
        <thead>
            <tr class="bg-blue-100 text-left">
                <th class="py-2 px-4">ID</th>
                <th class="py-2 px-4">Aquarium</th>
                <th class="py-2 px-4">Check Date</th>
                <th class="py-2 px-4">Temp (°C)</th>
                <th class="py-2 px-4">pH</th>
                <th class="py-2 px-4">Ammonia (ppm)</th>
                <th class="py-2 px-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($logs) > 0): ?>
                <?php foreach ($logs as $log): ?>
                <tr class="border-b hover:bg-blue-50 transition">
                    <td class="py-2 px-4"><?= $log['id'] ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($log['aquarium_name']) ?></td>
                    <td class="py-2 px-4">
                        <?= date('M d, Y H:i', strtotime($log['check_date'])) ?>
                        <span class="text-xs text-gray-500">
                            (<?= date('g:i A', strtotime($log['check_date'])) ?>)
                        </span>
                    </td>
                    <td class="py-2 px-4">
                        <span class="<?= ($log['temperature'] < 24) ? 'text-blue-600' : (($log['temperature'] > 28) ? 'text-red-600' : 'text-green-600') ?>">
                            <?= number_format($log['temperature'], 1) ?>°C
                        </span>
                    </td>
                    <td class="py-2 px-4">
                        <span class="<?= ($log['ph'] < 6.5) ? 'text-amber-600' : (($log['ph'] > 8.0) ? 'text-purple-600' : 'text-green-600') ?>">
                            <?= number_format($log['ph'], 1) ?>
                        </span>
                    </td>
                    <td class="py-2 px-4">
                        <span class="px-2 py-1 rounded text-xs font-medium
                            <?php 
                            if ($log['ammonia_level'] < 0.25) {
                                echo 'bg-green-100 text-green-800';
                            } elseif ($log['ammonia_level'] < 0.5) {
                                echo 'bg-yellow-100 text-yellow-800'; 
                            } elseif ($log['ammonia_level'] < 1) {
                                echo 'bg-orange-100 text-orange-800';
                            } else {
                                echo 'bg-red-100 text-red-800';
                            }
                            ?>">
                            <?= number_format($log['ammonia_level'], 2) ?> ppm
                        </span>
                    </td>
                    <td class="py-2 px-4">
                        <div class="flex space-x-2">
                            <a href="index.php?page=water_quality_logs&edit=<?= $log['id'] ?><?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?><?= isset($_GET['start_date']) ? '&start_date='.urlencode($_GET['start_date']).'&end_date='.urlencode($_GET['end_date']) : '' ?>" 
                               class="text-blue-500 hover:underline">Edit</a>
                            
                            <form action="index.php?page=water_quality_logs<?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?><?= isset($_GET['start_date']) ? '&start_date='.urlencode($_GET['start_date']).'&end_date='.urlencode($_GET['end_date']) : '' ?>" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this water quality log?')">
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
                    <td colspan="7" class="py-4 px-4 text-center text-gray-500">No water quality logs found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>