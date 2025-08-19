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
<body>

<div class="max-w-5xl mx-auto p-6">

    <h1 class="text-2xl font-bold mb-6">My Bookings</h1>

    <?php if ($result->num_rows > 0): ?>
        <div class="space-y-6">
        <?php while ($booking = $result->fetch_assoc()): ?>
            <div class="bg-white p-6 rounded-lg shadow relative" id="ticket-<?= $booking['booking_id'] ?>">
                <h2 class="text-lg font-bold mb-2"><?= htmlspecialchars(strtoupper($booking['airline_name'])) ?></h2>
                <p><strong>Plane Number:</strong> <?= htmlspecialchars(strtoupper($booking['plane_number'])) ?></p>
                <p><strong>From:</strong> <?= htmlspecialchars($booking['origin']) ?> <strong>To:</strong> <?= htmlspecialchars($booking['destination']) ?></p>
                <p><strong>Departure:</strong> <?= date('j M, H:i', strtotime($booking['departure_time'])) ?></p>
                <p><strong>Arrival:</strong> <?= date('j M, H:i', strtotime($booking['arrival_time'])) ?></p>
                <p><strong>Seats Booked:</strong> <?= intval($booking['seats']) ?></p>
                <p><strong>Total Paid:</strong> $<?= number_format(intval($booking['seats']) * floatval($booking['price']), 2) ?></p>

                <button onclick="downloadPDF(<?= $booking['booking_id'] ?>)" class="absolute top-4 right-4 bg-blue-500 text-white px-3 py-1 rounded">
                    Download PDF
                </button>
            </div>
        <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-600 font-medium">You have no bookings yet.</p>
    <?php endif; ?>

</div>

<script>
function downloadPDF(bookingId) {
    const { jsPDF } = window.jspdf;
    const ticket = document.getElementById('ticket-' + bookingId);
    const doc = new jsPDF();

    doc.html(ticket, {
        callback: function (doc) {
            doc.save('ticket-' + bookingId + '.pdf');
        },
        x: 10,
        y: 10,
        width: 180,
    });
}
</script>

</body>
</html>
