<?php
// Menangani form submissions untuk CRUD operations
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'add' && isset($_POST['name'], $_POST['size'], $_POST['location'])) {
        $aquarium->addAquarium($_POST['name'], $_POST['size'], $_POST['location']);
        header("Location: index.php?page=aquariums");
        exit;
    } elseif ($_POST['action'] == 'update' && isset($_POST['id'], $_POST['name'], $_POST['size'], $_POST['location'])) {
        $aquarium->updateAquarium($_POST['id'], $_POST['name'], $_POST['size'], $_POST['location']);
        header("Location: index.php?page=aquariums");
        exit;
    } elseif ($_POST['action'] == 'delete' && isset($_POST['id'])) {
        $aquarium->deleteAquarium($_POST['id']);
        header("Location: index.php?page=aquariums");
        exit;
    }
}

// Handle search
if (isset($_GET['search'])) {
    $data = $aquarium->searchAquariums($_GET['search']);
} else {
    $data = $aquarium->getAllAquariums();
}

// Get aquarium untuk editing jika edit parameter is set
$editAquarium = null;
if (isset($_GET['edit'])) {
    $editAquarium = $aquarium->getAquariumById($_GET['edit']);
}
?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold">Aquariums Management</h2>
    
    <!-- Search Form -->
    <form action="index.php" method="GET" class="flex items-center">
        <input type="hidden" name="page" value="aquariums">
        <input type="text" name="search" placeholder="Search by name or location" 
               value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
               class="border rounded py-1 px-3 mr-2">
        <button type="submit" class="bg-blue-500 text-white py-1 px-4 rounded hover:bg-blue-600">
            Search
        </button>
        <?php if (isset($_GET['search'])): ?>
            <a href="index.php?page=aquariums" class="ml-2 text-blue-500 hover:underline">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Form untuk menambahkan/editing aquariums -->
<div class="bg-gray-50 p-4 rounded-lg shadow mb-6">
    <h3 class="text-lg font-medium mb-3"><?= $editAquarium ? 'Edit Aquarium' : 'Add New Aquarium' ?></h3>
    <form action="index.php?page=aquariums<?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?>" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input type="hidden" name="action" value="<?= $editAquarium ? 'update' : 'add' ?>">
        <?php if ($editAquarium): ?>
            <input type="hidden" name="id" value="<?= $editAquarium['id'] ?>">
        <?php endif; ?>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input type="text" name="name" required 
                   value="<?= $editAquarium ? htmlspecialchars($editAquarium['name']) : '' ?>" 
                   class="w-full border rounded py-1 px-3">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Size (liters)</label>
            <input type="number" name="size" required 
                   value="<?= $editAquarium ? htmlspecialchars($editAquarium['size']) : '' ?>" 
                   class="w-full border rounded py-1 px-3">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
            <input type="text" name="location" required 
                   value="<?= $editAquarium ? htmlspecialchars($editAquarium['location']) : '' ?>" 
                   class="w-full border rounded py-1 px-3">
        </div>
        
        <div class="flex items-end">
            <button type="submit" class="bg-green-500 text-white py-1 px-4 rounded hover:bg-green-600">
                <?= $editAquarium ? 'Update' : 'Add' ?> Aquarium
            </button>
            <?php if ($editAquarium): ?>
                <a href="index.php?page=aquariums<?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?>" 
                   class="ml-2 text-gray-500 hover:underline">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Tabel Aquariums -->
<div class="overflow-x-auto">
    <table class="min-w-full bg-white border rounded shadow-md">
        <thead>
            <tr class="bg-blue-100 text-left">
                <th class="py-2 px-4">ID</th>
                <th class="py-2 px-4">Name</th>
                <th class="py-2 px-4">Size</th>
                <th class="py-2 px-4">Location</th>
                <th class="py-2 px-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($data) > 0): ?>
                <?php foreach ($data as $aq): ?>
                <tr class="border-b hover:bg-blue-50 transition">
                    <td class="py-2 px-4"><?= $aq['id'] ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($aq['name']) ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($aq['size']) ?> L</td>
                    <td class="py-2 px-4"><?= htmlspecialchars($aq['location']) ?></td>
                    <td class="py-2 px-4">
                        <div class="flex space-x-2">
                            <a href="index.php?page=aquariums&edit=<?= $aq['id'] ?><?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?>" 
                               class="text-blue-500 hover:underline">Edit</a>
                            
                            <form action="index.php?page=aquariums<?= isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?>" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this aquarium?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $aq['id'] ?>">
                                <button type="submit" class="text-red-500 hover:underline">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="py-4 px-4 text-center text-gray-500">No aquariums found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>