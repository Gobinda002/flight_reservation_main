<?php
session_start();
require '../db.php'; // path to your mysqli connection file

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

// Fetch flights from database
$sql = "SELECT * FROM flights ORDER BY departure_time ASC";
$result = $conn->query($sql);

$flights = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $flights[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>FlyHigh - Admin Dashboard</title>
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
        .actions-menu .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 100px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 10;
            right: 0;
            border-radius: 8px;
            overflow: hidden;
            top: 100%;
        }
        .actions-menu:hover .dropdown-content { display: block; }
        .dropdown-content a {
            color: #333;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
            font-size: 14px;
        }
        .dropdown-content a:hover { background-color: #E0E0E0; }
        .card-icon-faded { color: rgba(255, 255, 255, 0.4); }
    </style>
</head>
<body class="bg-gray-200 flex flex-col min-h-screen">
    <div class="navbar bg-blue-500 text-white p-4 md:px-8 flex justify-between items-center flex-wrap gap-4 shadow-md">
        <div class="navbar-brand text-xl md:text-2xl font-bold">FlyHigh</div>
        <div class="navbar-menu flex gap-4 md:gap-6 flex-grow justify-start ml-0 md:ml-8 flex-wrap">
            <a href="#" class="text-white hover:text-white font-medium text-base relative active">Dashboard</a>
            <a href="add_flight.php" class="text-white hover:text-white font-medium text-base relative">Create Flight</a>
            <a href="flights.php" class="text-white hover:text-white font-medium text-base relative">Flights</a>
            <a href="listairline.php" class="text-white hover:text-white font-medium text-base relative">Airlines</a>
        </div>
        <div class="navbar-actions flex gap-4 items-center flex-wrap">
            <a href="add_airlines.php" class="navbar-action-button bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-plus text-sm"></i> Airlines/Planes
            </a>
            <a href="#" class="navbar-action-button bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-user-shield text-sm"></i> Admin
            </a>
            <a href="logout.php" class="navbar-action-button bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-sign-out-alt text-sm"></i> Logout
            </a>
        </div>
    </div>

    <div class="main-content flex-grow p-6 max-w-7xl mx-auto w-full box-border">
        <!-- Summary cards (static data here, replace with real data if you want) -->
        <div class="summary-cards grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-10">
            <div class="card bg-blue-400 text-white p-5 rounded-lg flex flex-col items-center text-center shadow-md relative overflow-hidden justify-center min-h-[120px]">
                <i class="fas fa-users text-4xl mb-2 absolute top-4 left-5 card-icon-faded z-10"></i>
                <div class="card-content z-20 relative">
                    <div class="text-sm font-medium mb-1">Total Passengers</div>
                    <div class="text-3xl font-bold">5</div>
                </div>
            </div>
            
            <div class="card bg-blue-400 text-white p-5 rounded-lg flex flex-col items-center text-center shadow-md relative overflow-hidden justify-center min-h-[120px]">
                <i class="fas fa-plane-departure text-4xl mb-2 absolute top-4 left-5 card-icon-faded z-10"></i>
                <div class="card-content z-20 relative">
                    <div class="text-sm font-medium mb-1">Flights</div>
                    <div class="text-3xl font-bold"><?= count($flights) ?></div>
                </div>
            </div>
            <div class="card bg-blue-400 text-white p-5 rounded-lg flex flex-col items-center text-center shadow-md relative overflow-hidden justify-center min-h-[120px]">
                <i class="fas fa-plane text-4xl mb-2 absolute top-4 left-5 card-icon-faded z-10"></i>
                <div class="card-content z-20 relative">
                    <div class="text-sm font-medium mb-1">Available Airlines</div>
                    <div class="text-3xl font-bold">3</div>
                </div>
            </div>
        </div>

        <div class="flights-section bg-white p-6 rounded-lg shadow-md">
            <div class="flights-header flex justify-between items-center mb-5 flex-wrap gap-4">
                <h3 class="text-xl font-bold text-gray-800 m-0">Today's Flights</h3>
                <button class="filter-button bg-blue-500 hover:bg-blue-600 text-white border-none py-2 px-3 rounded-lg cursor-pointer text-sm transition">
                    <i class="fas fa-filter"></i>
                </button>
            </div>
            <div class="flights-table-container overflow-x-auto">
                <table class="flights-table w-full border-collapse text-sm">
                    <thead>
                        <tr>
                            <th class="p-3 text-left bg-blue-500 text-white font-semibold uppercase whitespace-nowrap">#</th>
                            <th class="p-3 text-left bg-blue-500 text-white font-semibold uppercase whitespace-nowrap">Departure</th>
                            <th class="p-3 text-left bg-blue-500 text-white font-semibold uppercase whitespace-nowrap">Destination</th>
                            <th class="p-3 text-left bg-blue-500 text-white font-semibold uppercase whitespace-nowrap">Source</th>
                            <th class="p-3 text-left bg-blue-500 text-white font-semibold uppercase whitespace-nowrap">Airlines</th>
                            <th class="p-3 text-left bg-blue-500 text-white font-semibold uppercase whitespace-nowrap"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($flights) === 0): ?>
                        <tr><td colspan="7" class="p-3 text-center text-gray-700">No flights found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($flights as $index => $flight): ?>
                                <tr class="border-b border-gray-300 last:border-b-0">
                                    <td class="p-3 text-gray-700 whitespace-nowrap"><?= $index + 1 ?></td>
                                    
                                    <td class="p-3 text-gray-700 whitespace-nowrap"><?= htmlspecialchars($flight['departure_time']) ?></td>
                                    <td class="p-3 text-gray-700 whitespace-nowrap"><?= htmlspecialchars($flight['destination']) ?></td>
                                    <td class="p-3 text-gray-700 whitespace-nowrap"><?= htmlspecialchars($flight['airline_id']) ?></td>
                                    <td class="p-3 text-gray-700 whitespace-nowrap">
                                        <div class="actions-menu relative inline-block">
                                            <button class="actions-button bg-none border-none text-xl cursor-pointer text-gray-700">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-content absolute bg-white min-w-[100px] shadow-md z-10 right-0 rounded-lg overflow-hidden hidden">
                                                <a href="view_flight.php?id=<?= $flight['flight_id'] ?>" class="block text-gray-800 p-2 text-sm hover:bg-gray-100">View</a>
                                                <a href="edit_flight.php?id=<?= $flight['flight_id'] ?>" class="block text-gray-800 p-2 text-sm hover:bg-gray-100">Edit</a>
                                                <a href="delete_flight.php?id=<?= $flight['flight_id'] ?>" class="block text-gray-800 p-2 text-sm hover:bg-gray-100" onclick="return confirm('Are you sure you want to delete this flight?')">Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Toggle dropdown on click, hide on outside click
        document.querySelectorAll('.actions-button').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const dropdown = btn.nextElementSibling;
                if (!dropdown) return;

                const isVisible = dropdown.style.display === 'block';
                document.querySelectorAll('.dropdown-content').forEach(dc => dc.style.display = 'none');
                dropdown.style.display = isVisible ? 'none' : 'block';
            });
        });

        // Close dropdown if clicked outside
        document.addEventListener('click', e => {
            if (!e.target.closest('.actions-menu')) {
                document.querySelectorAll('.dropdown-content').forEach(dc => dc.style.display = 'none');
            }
        });
    </script>
</body>
</html>
