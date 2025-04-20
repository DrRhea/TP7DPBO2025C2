<?php
require_once 'class/Aquarium.php';
require_once 'class/Species.php';
require_once 'class/Fish.php';
require_once 'class/FeedingLog.php';
require_once 'class/WaterQualityLog.php';

$aquarium = new Aquarium();
$species = new Species();
$fish = new Fish();
$feeding_logs = new FeedingLog();
$water_quality_logs = new WaterQualityLog();

// Memproses action permintaan apa pun terlebih dahulu sebelum redirecting atau displaying konten
if (isset($_POST['action'])) {
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 'aquariums';
    $searchParam = isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '';
    $editParam = isset($_GET['edit']) ? '&edit=' . $_GET['edit'] : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Aquarium Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/framer-motion/dist/framer-motion.umd.js"></script>
</head>
<body class="bg-gradient-to-br from-sky-100 to-blue-200 min-h-screen text-gray-800">

    <!-- Header -->
    <header class="bg-white shadow-md p-6">
        <h1 class="text-3xl font-bold text-center text-blue-600">Aquarium Management System</h1>
    </header>

    <!-- Navigation -->
    <nav class="flex justify-center gap-4 mt-6 text-lg">
        <?php
        $currentPage = isset($_GET['page']) ? $_GET['page'] : '';
        $pages = [
            'aquariums' => 'Aquariums',
            'species' => 'Species',
            'fish' => 'Fish',
            'feeding_logs' => 'Feeding',
            'water_quality_logs' => 'Water Quality'
        ];
        
        $links = [];
        foreach ($pages as $page => $label) {
            $activeClass = ($currentPage == $page) ? 'font-bold text-blue-600' : 'text-blue-500 hover:text-blue-700';
            $links[] = "<a href=\"?page=$page\" class=\"$activeClass\">$label</a>";
        }
        echo implode(' | ', $links);
        ?>
    </nav>

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto mt-10 bg-white p-6 rounded-xl shadow-xl animate-fade-in">
        <?php
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            if ($page == 'aquariums') include 'view/aquariums.php';
            elseif ($page == 'species') include 'view/species.php';
            elseif ($page == 'fish') include 'view/fish.php';
            elseif ($page == 'feeding_logs') include 'view/feeding_logs.php';
            elseif ($page == 'water_quality_logs') include 'view/water_quality_logs.php';
        } else {
            // Default ke aquariums page
            $_GET['page'] = 'aquariums';
            include 'view/aquariums.php';
        }
        ?>
    </main>

    <!-- Footer -->
    <footer class="text-center mt-10 py-4 text-sm text-gray-500">
       with ❤️ by Rhea^^.
    </footer>

    <!-- Simple animation on load -->
    <style>
        .animate-fade-in {
            animation: fadeIn 0.6s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</body>
</html>