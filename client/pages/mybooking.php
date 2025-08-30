<?php
session_start();
require_once '../../db.php';
include '../components/navbar.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: logreg.php");
    exit;
}

$user_id = $_SESSION['user_id'] ?? 0;

// Fetch user's bookings
$sql = "SELECT b.*, f.origin, f.destination, f.departure_time, f.arrival_time, f.price, f.flight_id, a.airline_name, p.plane_number
        FROM bookings b
        JOIN flights f ON b.flight_id = f.flight_id
        JOIN airlines a ON f.airline_id = a.airline_id
        JOIN planes p ON f.plane_id = p.plane_id
        WHERE b.user_id = ?
        ORDER BY b.booking_id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Bookings</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body class="bg-[#B3D7FF]">

<div class="max-w-5xl mx-auto p-6 mt-12">
    <h1 class="text-3xl font-bold mb-8 text-center">My Flight Tickets</h1>

    <?php if ($result->num_rows > 0): ?>
        <div class="space-y-8">
        <?php while ($booking = $result->fetch_assoc()): ?>
            
            <!-- Ticket -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden relative" id="ticket-<?= $booking['booking_id'] ?>">
                <!-- Top Section -->
                <div class="flex justify-between items-center px-6 py-4 bg-blue-600 text-white">
                    <div>
                        <h2 class="text-xl font-bold"><?= htmlspecialchars(strtoupper($booking['airline_name'])) ?></h2>
                        <p class="text-sm">Plane: <?= htmlspecialchars($booking['plane_number']) ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm">Booking ID: #<?= $booking['booking_id'] ?></p>
                        <p class="text-sm">nofpassenger: <?= intval($booking['nofpassenger']) ?></p>
                    </div>
                </div>

                <!-- Middle Section -->
                <div class="grid grid-cols-3 divide-x divide-dashed divide-gray-300">
                    <div class="p-6 text-center">
                        <p class="text-sm text-gray-500">From</p>
                        <h3 class="text-2xl font-bold"><?= htmlspecialchars($booking['origin']) ?></h3>
                        <p class="text-sm"><?= date('j M, H:i', strtotime($booking['departure_time'])) ?></p>
                    </div>
                    <div class="p-6 text-center flex flex-col justify-center">
                        <p class="text-gray-400">✈️</p>
                        <p class="text-sm text-gray-500">Duration</p>
                        <p class="font-semibold">
                            <?php
                                $dep = strtotime($booking['departure_time']);
                                $arr = strtotime($booking['arrival_time']);
                                $duration = gmdate("H\h i\m", $arr - $dep);
                                echo $duration;
                            ?>
                        </p>
                    </div>
                    <div class="p-6 text-center">
                        <p class="text-sm text-gray-500">To</p>
                        <h3 class="text-2xl font-bold"><?= htmlspecialchars($booking['destination']) ?></h3>
                        <p class="text-sm"><?= date('j M, H:i', strtotime($booking['arrival_time'])) ?></p>
                    </div>
                </div>

                <!-- Bottom Section -->
                <div class="px-6 py-4 bg-gray-50">
                    <p class="text-sm text-gray-600">Total Paid: 
                        <span class="font-bold text-lg text-green-600">
                            $<?= number_format(intval($booking['nofpassenger']) * floatval($booking['price']), 2) ?>
                        </span>
                    </p>
                </div>
            </div>
            <!-- End Ticket -->
        <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-600 font-medium text-center">You have no bookings yet.</p>
    <?php endif; ?>

</div>

</body>
</html>
