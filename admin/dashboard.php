<?php
session_start();
require '../db.php'; // path to your mysqli connection file

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

// Fetch counts for summary cards
$totalAirlines = $conn->query("SELECT COUNT(*) as cnt FROM airlines")->fetch_assoc()['cnt'];
$totalPlanes = $conn->query("SELECT COUNT(*) as cnt FROM planes")->fetch_assoc()['cnt'];
$totalFlights = $conn->query("SELECT COUNT(*) as cnt FROM flights")->fetch_assoc()['cnt'];
$totalBookings = $conn->query("SELECT COUNT(*) as cnt FROM bookings")->fetch_assoc()['cnt'];

// Fetch today's flights only
$today = date('Y-m-d'); // YYYY-MM-DD
$sql = "SELECT f.*, a.airline_name 
        FROM flights f 
        LEFT JOIN airlines a ON f.airline_id = a.airline_id 
        WHERE DATE(f.departure_time) = '$today'
        ORDER BY f.departure_time ASC";
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
        .card-icon-faded { color: rgba(255, 255, 255, 0.4); }
    </style>
</head>
<body class="bg-gray-200 flex flex-col min-h-screen">
    <!-- Navbar -->
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

    <!-- Summary Cards -->
    <div class="main-content flex-grow p-6 max-w-7xl mx-auto w-full box-border">
        <div class="summary-cards grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-10">
            <div class="card bg-blue-400 text-white p-5 rounded-lg flex flex-col items-center text-center shadow-md relative overflow-hidden justify-center min-h-[120px]">
                <i class="fas fa-building text-4xl mb-2 absolute top-4 left-5 card-icon-faded z-10"></i>
                <div class="card-content z-20 relative">
                    <div class="text-sm font-medium mb-1">Total Airlines</div>
                    <div class="text-3xl font-bold"><?= $totalAirlines ?></div>
                </div>
            </div>
            <div class="card bg-green-400 text-white p-5 rounded-lg flex flex-col items-center text-center shadow-md relative overflow-hidden justify-center min-h-[120px]">
                <i class="fas fa-plane text-4xl mb-2 absolute top-4 left-5 card-icon-faded z-10"></i>
                <div class="card-content z-20 relative">
                    <div class="text-sm font-medium mb-1">Total Planes</div>
                    <div class="text-3xl font-bold"><?= $totalPlanes ?></div>
                </div>
            </div>
            <div class="card bg-orange-400 text-white p-5 rounded-lg flex flex-col items-center text-center shadow-md relative overflow-hidden justify-center min-h-[120px]">
                <i class="fas fa-plane-departure text-4xl mb-2 absolute top-4 left-5 card-icon-faded z-10"></i>
                <div class="card-content z-20 relative">
                    <div class="text-sm font-medium mb-1">Total Flights</div>
                    <div class="text-3xl font-bold"><?= $totalFlights ?></div>
                </div>
            </div>
            <div class="card bg-purple-400 text-white p-5 rounded-lg flex flex-col items-center text-center shadow-md relative overflow-hidden justify-center min-h-[120px]">
                <i class="fas fa-users text-4xl mb-2 absolute top-4 left-5 card-icon-faded z-10"></i>
                <div class="card-content z-20 relative">
                    <div class="text-sm font-medium mb-1">Total Bookings</div>
                    <div class="text-3xl font-bold"><?= $totalBookings ?></div>
                </div>
            </div>
        </div>

        <!-- Today's Flights Table -->
        <div class="flights-section bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-bold text-gray-800 mb-5">Today's Flights</h3>
            <div class="flights-table-container overflow-x-auto">
                <table class="flights-table w-full border-collapse text-sm">
                    <thead>
                        <tr>
                            <th class="p-3 text-left bg-blue-500 text-white font-semibold uppercase whitespace-nowrap">#</th>
                            <th class="p-3 text-left bg-blue-500 text-white font-semibold uppercase whitespace-nowrap">Departure</th>
                            <th class="p-3 text-left bg-blue-500 text-white font-semibold uppercase whitespace-nowrap">Origin</th>
                            <th class="p-3 text-left bg-blue-500 text-white font-semibold uppercase whitespace-nowrap">Destination</th>
                            <th class="p-3 text-left bg-blue-500 text-white font-semibold uppercase whitespace-nowrap">Airlines</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($flights) === 0): ?>
                            <tr><td colspan="5" class="p-3 text-center text-gray-700">No flights today.</td></tr>
                        <?php else: ?>
                            <?php foreach ($flights as $index => $flight): ?>
                                <tr class="border-b border-gray-300 last:border-b-0">
                                    <td class="p-3 text-gray-700 whitespace-nowrap"><?= $index + 1 ?></td>
                                    <td class="p-3 text-gray-700 whitespace-nowrap"><?= htmlspecialchars($flight['departure_time']) ?></td>
                                    <td class="p-3 text-gray-700 whitespace-nowrap"><?= htmlspecialchars($flight['origin']) ?></td>
                                    <td class="p-3 text-gray-700 whitespace-nowrap"><?= htmlspecialchars($flight['destination']) ?></td>
                                    <td class="p-3 text-gray-700 whitespace-nowrap"><?= htmlspecialchars($flight['airline_name']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
