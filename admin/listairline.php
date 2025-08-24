<?php
require_once '../db.php';

// Handle airline deletion
if (isset($_GET['delete_airline'])) {
    $airline_id = intval($_GET['delete_airline']);

    $conn->query("DELETE FROM planes WHERE airline_id = $airline_id");
    $conn->query("DELETE FROM airlines WHERE airline_id = $airline_id");

    echo "<script>alert('Airline and its planes deleted successfully!'); window.location.href='listairline.php';</script>";
    exit;
}

// Handle plane deletion
if (isset($_GET['delete_plane'])) {
    $plane_id = intval($_GET['delete_plane']);
    $conn->query("DELETE FROM planes WHERE plane_id = $plane_id");

    echo "<script>alert('Plane deleted successfully!'); window.location.href='listairline.php';</script>";
    exit;
}

// Fetch airlines
$sql = "SELECT * FROM airlines";
$airlines = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Airlines & Planes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Roboto', sans-serif; }
        .navbar-brand { font-family: 'Montserrat', sans-serif; }
        .navbar-menu a { position: relative; padding-bottom: 5px; }
        .navbar-menu a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background-color: white;
            bottom: 0;
            left: 0;
            transition: width 0.3s ease;
        }
        .navbar-menu a:hover::after,
        .navbar-menu a.active::after { width: 100%; }
    </style>
</head>
<body class="bg-gray-200 flex flex-col min-h-screen">

    <!-- Navbar -->
    <div class="navbar bg-blue-500 text-white p-4 md:px-8 flex justify-between items-center flex-wrap gap-4 shadow-md">
        <div class="navbar-brand text-xl md:text-2xl font-bold">FlyHigh</div>
        <div class="navbar-menu flex gap-4 md:gap-6 flex-grow justify-start ml-0 md:ml-8 flex-wrap">
            <a href="dashboard.php" class="text-white hover:text-white font-medium text-base ">Dashboard</a>
            <a href="add_flight.php" class="text-white hover:text-white font-medium text-base ">Create Flight</a>
            <a href="flights.php" class="text-white hover:text-white font-medium text-base ">Flights</a>
            <a href="#" class="text-white hover:text-white font-medium text-base ">Airlines</a>
        </div>
        <div class="navbar-actions flex gap-4 items-center flex-wrap">
            <a href="add_airlines.php" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-plus text-sm"></i> Airlines/Planes
            </a>
            <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-user-shield text-sm"></i> Admin
            </a>
            <a href="logout.php" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-sign-out-alt text-sm"></i> Logout
            </a>
        </div>
    </div>

    <!-- Content -->
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md w-full max-w-5xl">
        <h1 class="text-2xl font-bold mb-6">Airlines & Planes</h1>

        <?php if ($airlines->num_rows > 0): ?>
            <?php while ($airline = $airlines->fetch_assoc()): ?>
                <div class="mb-4 border rounded-lg shadow-sm">
                    <div class="flex justify-between items-center bg-gray-100 p-4 cursor-pointer airline-header" 
                         onclick="togglePlanes(<?= $airline['airline_id'] ?>)">
                        <h2 class="text-xl font-semibold flex items-center gap-2">
                            <i class="fas fa-plane text-blue-500"></i> <?= htmlspecialchars($airline['airline_name']) ?>
                        </h2>
                        <div class="flex items-center gap-4">
                            <a href="?delete_airline=<?= $airline['airline_id'] ?>" 
                               onclick="return confirm('Are you sure you want to delete this airline and its planes?');"
                               class="text-red-600 hover:text-red-800 font-medium">
                                <i class="fas fa-trash"></i>
                            </a>
                            <i id="icon-<?= $airline['airline_id'] ?>" class="fas fa-chevron-down text-gray-600"></i>
                        </div>
                    </div>

                    <!-- Planes of this airline (initially hidden) -->
                    <div id="planes-<?= $airline['airline_id'] ?>" class="hidden p-4">
                        <?php
                        $airline_id = $airline['airline_id'];
                        $planes = $conn->query("SELECT * FROM planes WHERE airline_id = $airline_id");
                        ?>
                        <?php if ($planes->num_rows > 0): ?>
                            <table class="w-full border border-gray-300">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="p-2 border">Plane Number</th>
                                        <th class="p-2 border">Capacity</th>
                                        <th class="p-2 border">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($plane = $planes->fetch_assoc()): ?>
                                        <tr>
                                            <td class="p-2 border text-center font-bold"><?= htmlspecialchars($plane['plane_number']) ?></td>
                                            <td class="p-2 border text-center"><?= htmlspecialchars($plane['capacity']) ?></td>
                                            <td class="p-2 border text-center">
                                                <a href="?delete_plane=<?= $plane['plane_id'] ?>" 
                                                   onclick="return confirm('Are you sure you want to delete this plane?');"
                                                   class="text-red-600 hover:text-red-800 font-medium">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-gray-500">No planes available for this airline.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-600">No airlines found.</p>
        <?php endif; ?>
    </div>

    <script>
        function togglePlanes(id) {
            const planesDiv = document.getElementById('planes-' + id);
            const icon = document.getElementById('icon-' + id);
            if (planesDiv.classList.contains('hidden')) {
                planesDiv.classList.remove('hidden');
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                planesDiv.classList.add('hidden');
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }
    </script>

</body>
</html>
