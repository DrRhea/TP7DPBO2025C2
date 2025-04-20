<?php
// Menangani form submissions untuk CRUD operations
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'add' && isset($_POST['name'], $_POST['species_id'], $_POST['aquarium_id'], $_POST['age'], $_POST['gender'])) {
        $fish->addFish($_POST['name'], $_POST['species_id'], $_POST['aquarium_id'], $_POST['age'], $_POST['gender']);
        header("Location: index.php?page=fish");
        exit;
    } elseif ($_POST['action'] == 'update' && isset($_POST['id'], $_POST['name'], $_POST['species_id'], $_POST['aquarium_id'], $_POST['age'], $_POST['gender'])) {
        $fish->updateFish($_POST['id'], $_POST['name'], $_POST['species_id'], $_POST['aquarium_id'], $_POST['age'], $_POST['gender']);
        header("Location: index.php?page=fish");
        exit;
    } elseif ($_POST['action'] == 'delete' && isset($_POST['id'])) {
        $fish->deleteFish($_POST['id']);
        header("Location: index.php?page=fish");
        exit;
    }
}

// Handle search
if (isset($_GET['search'])) {
    $data = $fish->searchFish($_GET['search']);
} else {
    $data = $fish->getAllFish();
}

// Get fish untuk editing jika edit parameter is set
$editFish = null;
if (isset($_GET['edit'])) {
    $editFish = $fish->getFishById($_GET['edit']);
}

// Get all species dan aquariums untuk dropdowns
$allSpecies = $species->getAllSpecies();
$allAquariums = $aquarium->getAllAquariums();
?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold">Fish Management</h2>
    
    <!-- Search Form -->
    <form action="index.php" method="GET" class="flex items-center">
        <input type="hidden" name="page" value="fish">
        <input type="text" name="search" placeholder="Search by fish, species, or aquarium name" 
               value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
               class="border rounded py-1 px-3 mr-2">
        <button type="submit" class="bg-purple-500 text-white py-1 px-4 rounded hover:bg-purple-600">
            Search
        </button>
        <?php if (isset($_GET['search'])): ?>
            <a href="index.php?page=fish" class="ml-2 text-purple-500 hover:underline">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Form untuk menambahkan/editing fish -->
<div class="bg-gray-50 p-4 rounded-lg shadow mb-6">
    <h3 class="text-lg font-medium mb-3"><?= $editFish ? 'Edit Fish' : 'Add New Fish' ?></h3>
    <form action="index.php?page=fish<?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?>" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="hidden" name="action" value="<?= $editFish ? 'update' : 'add' ?>">
        <?php if ($editFish): ?>
            <input type="hidden" name="id" value="<?= $editFish['id'] ?>">
        <?php endif; ?>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input type="text" name="name" required 
                   value="<?= $editFish ? htmlspecialchars($editFish['name']) : '' ?>" 
                   class="w-full border rounded py-1 px-3">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Species</label>
            <select name="species_id" required class="w-full border rounded py-1 px-3">
                <option value="">Select Species</option>
                <?php foreach ($allSpecies as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= ($editFish && $editFish['species_id'] == $s['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['name']) ?> (<?= htmlspecialchars($s['origin']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Aquarium</label>
            <select name="aquarium_id" required class="w-full border rounded py-1 px-3">
                <option value="">Select Aquarium</option>
                <?php foreach ($allAquariums as $a): ?>
                    <option value="<?= $a['id'] ?>" <?= ($editFish && $editFish['aquarium_id'] == $a['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a['name']) ?> (<?= htmlspecialchars($a['location']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Age (months)</label>
            <input type="number" name="age" required min="0" 
                   value="<?= $editFish ? htmlspecialchars($editFish['age']) : '' ?>" 
                   class="w-full border rounded py-1 px-3">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
            <select name="gender" required class="w-full border rounded py-1 px-3">
                <option value="">Select Gender</option>
                <option value="Male" <?= ($editFish && $editFish['gender'] == 'Male') ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= ($editFish && $editFish['gender'] == 'Female') ? 'selected' : '' ?>>Female</option>
            </select>
        </div>
        
        <div class="flex items-end">
            <button type="submit" class="bg-purple-500 text-white py-1 px-4 rounded hover:bg-purple-600">
                <?= $editFish ? 'Update' : 'Add' ?> Fish
            </button>
            <?php if ($editFish): ?>
                <a href="index.php?page=fish<?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?>" 
                   class="ml-2 text-gray-500 hover:underline">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Tabel Fish -->
<div class="overflow-x-auto">
    <table class="min-w-full bg-white border rounded shadow-md">
        <thead>
            <tr class="bg-purple-100 text-left">
                <th class="py-2 px-4">ID</th>
                <th class="py-2 px-4">Name</th>
                <th class="py-2 px-4">Species</th>
                <th class="py-2 px-4">Aquarium</th>
                <th class="py-2 px-4">Age</th>
                <th class="py-2 px-4">Gender</th>
                <th class="py-2 px-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($data) > 0): ?>
                <?php foreach ($data as $f): ?>
                <tr class="border-b hover:bg-purple-50 transition">
                    <td class="py-2 px-4"><?= $f['id'] ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($f['name']) ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($f['species_name']) ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($f['aquarium_name']) ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($f['age']) ?> months</td>
                    <td class="py-2 px-4">
                        <span class="px-2 py-1 rounded text-xs font-medium
                            <?php 
                            switch($f['gender']) {
                                case 'Male': echo 'bg-blue-100 text-blue-800'; break;
                                case 'Female': echo 'bg-pink-100 text-pink-800'; break;
                                default: echo 'bg-gray-100 text-gray-800';
                            }
                            ?>">
                            <?= htmlspecialchars($f['gender']) ?>
                        </span>
                    </td>
                    <td class="py-2 px-4">
                        <div class="flex space-x-2">
                            <a href="index.php?page=fish&edit=<?= $f['id'] ?><?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?>" 
                               class="text-blue-500 hover:underline">Edit</a>
                            
                            <form action="index.php?page=fish<?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?>" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this fish?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $f['id'] ?>">
                                <button type="submit" class="text-red-500 hover:underline">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="py-4 px-4 text-center text-gray-500">No fish found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>