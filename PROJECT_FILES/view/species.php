<?php
// Menangani form submissions untuk CRUD operations
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'add' && isset($_POST['name'], $_POST['origin'], $_POST['care_level'])) {
        $species->addSpecies($_POST['name'], $_POST['origin'], $_POST['care_level']);
        header("Location: index.php?page=species");
        exit;
    } elseif ($_POST['action'] == 'update' && isset($_POST['id'], $_POST['name'], $_POST['origin'], $_POST['care_level'])) {
        $species->updateSpecies($_POST['id'], $_POST['name'], $_POST['origin'], $_POST['care_level']);
        header("Location: index.php?page=species");
        exit;
    } elseif ($_POST['action'] == 'delete' && isset($_POST['id'])) {
        $species->deleteSpecies($_POST['id']);
        header("Location: index.php?page=species");
        exit;
    }
}

// Handle search
if (isset($_GET['search'])) {
    $data = $species->searchSpecies($_GET['search']);
} else {
    $data = $species->getAllSpecies();
}

// Get species untuk editing jika edit parameter is set
$editSpecies = null;
if (isset($_GET['edit'])) {
    $editSpecies = $species->getSpeciesById($_GET['edit']);
}
?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold">Species Management</h2>
    
    <!-- Search Form -->
    <form action="index.php" method="GET" class="flex items-center">
        <input type="hidden" name="page" value="species">
        <input type="text" name="search" placeholder="Search by name or origin" 
               value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
               class="border rounded py-1 px-3 mr-2">
        <button type="submit" class="bg-green-500 text-white py-1 px-4 rounded hover:bg-green-600">
            Search
        </button>
        <?php if (isset($_GET['search'])): ?>
            <a href="index.php?page=species" class="ml-2 text-green-500 hover:underline">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Form untuk menambahkan/editing species -->
<div class="bg-gray-50 p-4 rounded-lg shadow mb-6">
    <h3 class="text-lg font-medium mb-3"><?= $editSpecies ? 'Edit Species' : 'Add New Species' ?></h3>
    <form action="index.php?page=species<?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?>" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input type="hidden" name="action" value="<?= $editSpecies ? 'update' : 'add' ?>">
        <?php if ($editSpecies): ?>
            <input type="hidden" name="id" value="<?= $editSpecies['id'] ?>">
        <?php endif; ?>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input type="text" name="name" required 
                   value="<?= $editSpecies ? htmlspecialchars($editSpecies['name']) : '' ?>" 
                   class="w-full border rounded py-1 px-3">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Origin</label>
            <input type="text" name="origin" required 
                   value="<?= $editSpecies ? htmlspecialchars($editSpecies['origin']) : '' ?>" 
                   class="w-full border rounded py-1 px-3">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Care Level</label>
            <select name="care_level" required class="w-full border rounded py-1 px-3">
                <option value="">Select Care Level</option>
                <option value="Easy" <?= ($editSpecies && $editSpecies['care_level'] == 'Easy') ? 'selected' : '' ?>>Easy</option>
                <option value="Moderate" <?= ($editSpecies && $editSpecies['care_level'] == 'Moderate') ? 'selected' : '' ?>>Moderate</option>
                <option value="Difficult" <?= ($editSpecies && $editSpecies['care_level'] == 'Difficult') ? 'selected' : '' ?>>Difficult</option>
                <option value="Expert" <?= ($editSpecies && $editSpecies['care_level'] == 'Expert') ? 'selected' : '' ?>>Expert</option>
            </select>
        </div>
        
        <div class="flex items-end">
            <button type="submit" class="bg-green-500 text-white py-1 px-4 rounded hover:bg-green-600">
                <?= $editSpecies ? 'Update' : 'Add' ?> Species
            </button>
            <?php if ($editSpecies): ?>
                <a href="index.php?page=species<?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?>" 
                   class="ml-2 text-gray-500 hover:underline">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Tabel Species -->
<div class="overflow-x-auto">
    <table class="min-w-full bg-white border rounded shadow-md">
        <thead>
            <tr class="bg-green-100 text-left">
                <th class="py-2 px-4">ID</th>
                <th class="py-2 px-4">Name</th>
                <th class="py-2 px-4">Origin</th>
                <th class="py-2 px-4">Care Level</th>
                <th class="py-2 px-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($data) > 0): ?>
                <?php foreach ($data as $s): ?>
                <tr class="border-b hover:bg-green-50 transition">
                    <td class="py-2 px-4"><?= $s['id'] ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($s['name']) ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($s['origin']) ?></td>
                    <td class="py-2 px-4">
                        <span class="px-2 py-1 rounded text-xs font-medium
                            <?php 
                            switch($s['care_level']) {
                                case 'Easy': echo 'bg-green-100 text-green-800'; break;
                                case 'Moderate': echo 'bg-blue-100 text-blue-800'; break;
                                case 'Difficult': echo 'bg-yellow-100 text-yellow-800'; break;
                                case 'Expert': echo 'bg-red-100 text-red-800'; break;
                                default: echo 'bg-gray-100 text-gray-800';
                            }
                            ?>">
                            <?= htmlspecialchars($s['care_level']) ?>
                        </span>
                    </td>
                    <td class="py-2 px-4">
                        <div class="flex space-x-2">
                            <a href="index.php?page=species&edit=<?= $s['id'] ?><?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?>" 
                               class="text-blue-500 hover:underline">Edit</a>
                            
                            <form action="index.php?page=species<?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?>" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this species?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                <button type="submit" class="text-red-500 hover:underline">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="py-4 px-4 text-center text-gray-500">No species found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>