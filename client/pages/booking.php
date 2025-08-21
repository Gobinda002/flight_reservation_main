<?php
session_start();
require_once '../../db.php';
include '../components/navbar.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo "<script>
        alert('Please login first!');
        window.location.href = 'logreg.php';
    </script>";
    exit;
}

$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'] ?? 0;

// Redirect if flight_id not set
if (!isset($_GET['flight_id'])) {
    header("Location: index.php");
    exit;
}

$flight_id = intval($_GET['flight_id']);

// ✅ Get selected nofpassenger from index.php (if provided)
$selectednofpassenger = isset($_GET['nofpassenger']) ? intval($_GET['nofpassenger']) : 1;
if ($selectednofpassenger < 1) $selectednofpassenger = 1;

// Fetch flight details
$stmt = $conn->prepare("SELECT f.*, a.airline_name, p.plane_number 
                        FROM flights f 
                        JOIN airlines a ON f.airline_id = a.airline_id
                        JOIN planes p ON f.plane_id = p.plane_id
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
$bookingSuccess = false;
$errornofpassenger = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passenger_name = trim($_POST['passenger_name']);
    $passenger_email = trim($_POST['passenger_email']);
    $passenger_phone = trim($_POST['passenger_phone']);
    $nofpassenger_booked = intval($_POST['nofpassenger_booked']);

    // Validate phone number (10 digits)
    if (!preg_match('/^\d{10}$/', $passenger_phone)) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Invalid Phone',
                text: 'Phone number must be exactly 10 digits.'
            });
        </script>";
    } else {
        // Check available seats
        $availablenofpassenger = $flight['total_nofpassenger'] - $flight['booked_nofpassenger'];
        if ($nofpassenger_booked > $availablenofpassenger) {
            $errornofpassenger = true;
        } else {
            // --- Greedy Seat Assignment ---
            // 1. Get already assigned seats for this flight
            $stmtSeats = $conn->prepare("SELECT seatnumber FROM bookings WHERE flight_id = ?");
            $stmtSeats->bind_param("i", $flight_id);
            $stmtSeats->execute();
            $resSeats = $stmtSeats->get_result();

            $assignedSeats = [];
            while ($row = $resSeats->fetch_assoc()) {
                if (!empty($row['seatnumber'])) {
                    $seats = explode(',', $row['seatnumber']);
                    foreach ($seats as $s) {
                        $assignedSeats[] = (int)$s;
                    }
                }
            }

            // 2. Assign the next available seats (greedy)
            $seatNumbers = [];
            for ($i = 1; $i <= $flight['total_nofpassenger']; $i++) {
                if (!in_array($i, $assignedSeats)) {
                    $seatNumbers[] = $i;
                    if (count($seatNumbers) == $nofpassenger_booked) break;
                }
            }

            // Convert seat numbers to comma-separated string
            $seatNumbersStr = implode(',', $seatNumbers);

            // --- Insert booking ---
            $stmt = $conn->prepare("INSERT INTO bookings 
                (user_id, flight_id, passenger_name, passenger_email, passenger_phone, nofpassenger, seatnumber, booking_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");

            $stmt->bind_param(
                "iisssis",
                $user_id,
                $flight_id,
                $passenger_name,
                $passenger_email,
                $passenger_phone,
                $nofpassenger_booked,
                $seatNumbersStr
            );

            if ($stmt->execute()) {
                // Update booked nofpassenger in flights
                $update = $conn->prepare("UPDATE flights SET booked_nofpassenger = booked_nofpassenger + ? WHERE flight_id = ?");
                $update->bind_param("ii", $nofpassenger_booked, $flight_id);
                $update->execute();

                $bookingSuccess = true;
            }
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gradient-to-br from-teal-500 via-white/5 to-green-400 ">

    <div class="max-w-xl mx-auto bg-white shadow rounded-lg p-6 mt-20">

        <h1 class="text-2xl font-bold mb-6">Book Your Flight</h1>

        <!-- Flight Info -->
        <div class="mb-6 p-4 border rounded-lg bg-gray-50">
            <p><strong>Airline:</strong> <?= htmlspecialchars($flight['airline_name']) ?></p>
            <p><strong>Plane Number:</strong> <?= htmlspecialchars(strtoupper($flight['plane_number'])) ?></p>
            <p><strong>From:</strong> <?= htmlspecialchars($flight['origin']) ?></p>
            <p><strong>To:</strong> <?= htmlspecialchars($flight['destination']) ?></p>
            <p><strong>Departure:</strong> <?= date('j M, H:i', strtotime($flight['departure_time'])) ?></p>
            <p><strong>Arrival:</strong> <?= date('j M, H:i', strtotime($flight['arrival_time'])) ?></p>
            <p><strong>Price per seat:</strong> $<?= number_format($flight['price'], 2) ?></p>
           
        </div>

        <!-- Booking Form -->
        <?php if (!$bookingSuccess): ?>
            <form method="post" class="space-y-4">
                <div>
                    <label class="block font-medium">Name</label>
                    <input type="text" name="passenger_name" required readonly class="w-full border rounded px-3 py-2"
                        value="<?= htmlspecialchars($username) ?>">
                </div>

                <div>
                    <label class="block font-medium">Email</label>
                    <input type="email" name="passenger_email" required class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium">Phone</label>
                    <input type="tel" name="passenger_phone" required pattern="[0-9]{10}" maxlength="10"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full border rounded px-3 py-2"
                        title="Phone number must be exactly 10 digits">
                </div>
                <div>
                    <label class="block font-medium">nofpassenger to Book</label>
                    <input type="number" name="nofpassenger_booked" min="1" value="<?= $selectednofpassenger ?>" required
                        class="w-full border rounded px-3 py-2">
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Confirm Booking</button>
            </form>
        <?php else: ?>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Booking Successful!',
                    text: 'Your flight has been booked successfully.',
                    showCancelButton: true,
                    confirmButtonText: 'View My Bookings',
                    cancelButtonText: 'Close',
                    confirmButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'my_bookings.php';
                    } else {
                        window.location.href = '../index.php';
                    }
                });
            </script>
        <?php endif; ?>

        <?php if ($errornofpassenger): ?>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Not Enough nofpassenger!',
                    text: 'Sorry, only <?= $flight['total_nofpassenger'] - $flight['booked_nofpassenger'] ?> nofpassenger are available.',
                    confirmButtonText: 'OK'
                });
            </script>
        <?php endif; ?>

    </div>

</body>

</html>
