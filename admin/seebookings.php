<?php
session_start();
require_once '../db.php';

// Optional: check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Fetch all bookings with related info
$sql = "SELECT 
            b.booking_id, b.passenger_name, b.passenger_email, b.passenger_phone, 
            b.nofpassenger, b.seatnumber, b.booking_date,
            u.name,
            a.airline_name, 
            p.plane_number,
            f.origin, f.destination, f.departure_time
        FROM bookings b
        JOIN users u ON b.user_id = u.user_id
        JOIN flights f ON b.flight_id = f.flight_id
        JOIN airlines a ON f.airline_id = a.airline_id
        JOIN planes p ON f.plane_id = p.plane_id
        ORDER BY b.booking_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - User Bookings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
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
        <a href="seebookings.php" class="text-white hover:text-white font-medium text-base relative">See Bookings</a>
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

<!-- Page Content -->
<div class="max-w-7xl mx-auto py-12 flex-1">

    <h1 class="text-3xl font-bold mb-6 text-center">User Bookings</h1>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg shadow overflow-hidden">
            <thead class="bg-blue-500 text-white">
                <tr>
                    <th class="py-3 px-6 text-left">Booking ID</th>
                    <th class="py-3 px-6 text-left">Username</th>
                    <th class="py-3 px-6 text-left">Airline</th>
                    <th class="py-3 px-6 text-left">Aeroplane</th>
                    <th class="py-3 px-6 text-left">Origin</th>
                    <th class="py-3 px-6 text-left">Destination</th>
                    <th class="py-3 px-6 text-left">Departure Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-100">
                            <td class="py-3 px-6"><?= $row['booking_id'] ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($row['name']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($row['airline_name']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($row['plane_number']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($row['origin']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($row['destination']) ?></td>
                            <td class="py-3 px-6"><?= date('Y-m-d H:i', strtotime($row['departure_time'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-6 text-gray-500">No bookings found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
