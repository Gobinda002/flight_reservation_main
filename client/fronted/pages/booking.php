<?php
session_start();
require_once '../../../db.php';
include '../components/navbar.php'; // ✅ Added navbar

// Redirect if flight_id not set
if (!isset($_GET['flight_id'])) {
    header("Location: index.php");
    exit;
}

$flight_id = intval($_GET['flight_id']);

// Fetch flight details
$stmt = $conn->prepare("SELECT f.*, a.airline_name FROM flights f 
                        JOIN airlines a ON f.airline_id = a.airline_id
                        WHERE f.flight_id = ?");
$stmt->bind_param("i", $flight_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Flight not found!";
    exit;
}

$flight = $result->fetch_assoc();

// Handle booking submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passenger_name = trim($_POST['passenger_name']);
    $passenger_email = trim($_POST['passenger_email']);
    $passenger_phone = trim($_POST['passenger_phone']);
    $seats_booked = intval($_POST['seats_booked']);

    // Validate phone number (only 10 digits)
    if (!preg_match('/^\d{10}$/', $passenger_phone)) {
        $message = "Phone number must be exactly 10 digits.";
    } else {
        $stmt = $conn->prepare("INSERT INTO bookings (flight_id, passenger_name, passenger_email, passenger_phone, seats_booked) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $flight_id, $passenger_name, $passenger_email, $passenger_phone, $seats_booked);

        if ($stmt->execute()) {
            // Update booked seats in flights table
            $conn->query("UPDATE flights SET booked_seats = booked_seats + $seats_booked WHERE flight_id = $flight_id");
            $message = "Booking successful!";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Book Flight</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-xl mx-auto bg-white shadow rounded-lg p-6 mt-6">
    <h1 class="text-2xl font-bold mb-4">Book Flight</h1>

    <!-- Flight Info -->
    <div class="mb-6 p-4 border rounded-lg bg-gray-50">
        <p><strong>Airline:</strong> <?= htmlspecialchars($flight['airline_name']) ?></p>
        <p><strong>From:</strong> <?= htmlspecialchars($flight['origin']) ?></p>
        <p><strong>To:</strong> <?= htmlspecialchars($flight['destination']) ?></p>
        <p><strong>Departure:</strong> <?= date('j M, H:i', strtotime($flight['departure_time'])) ?></p>
        <p><strong>Arrival:</strong> <?= date('j M, H:i', strtotime($flight['arrival_time'])) ?></p>
        <p><strong>Price per seat:</strong> $<?= number_format($flight['price'], 2) ?></p>
    </div>

    <!-- Message -->
    <?php if ($message): ?>
        <div class="mb-4 text-white bg-blue-500 px-4 py-2 rounded">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Booking Form -->
    <form method="post" class="space-y-4">
        <div>
            <label class="block font-medium">Name</label>
            <input type="text" name="passenger_name" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block font-medium">Email</label>
            <input type="email" name="passenger_email" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block font-medium">Phone (10 digits)</label>
            <input type="tel" name="passenger_phone" required pattern="[0-9]{10}" maxlength="10"
                   class="w-full border rounded px-3 py-2"
                   title="Phone number must be exactly 10 digits">
        </div>
        <div>
            <label class="block font-medium">Seats to Book</label>
            <input type="number" name="seats_booked" min="1" value="1" required class="w-full border rounded px-3 py-2">
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Confirm Booking</button>
    </form>
</div>
</body>
</html>
