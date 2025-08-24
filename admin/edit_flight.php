<?php
session_start();
require '../db.php'; 

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$success = '';

// Fetch flight with airline & plane name
$stmt = $conn->prepare("
    SELECT f.*, a.airline_name, p.plane_number 
    FROM flights f
    JOIN airlines a ON f.airline_id = a.airline_id
    JOIN planes p ON f.plane_id = p.plane_id
    WHERE f.flight_id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$flight = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$flight) {
    header("Location: dashboard.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $price = (int)($_POST['price'] ?? 0);

    if ($price < 1) {
        $error = "Price must be valid (Price ≥ 1).";
    } else {
        // Update flight price only
        $stmt = $conn->prepare("UPDATE flights SET price=? WHERE flight_id=?");
        $stmt->bind_param("di", $price, $id);
        if ($stmt->execute()) {
            $success = "Flight updated successfully.";

            // Refresh flight data
            $stmt = $conn->prepare("
                SELECT f.*, a.airline_name, p.plane_number 
                FROM flights f
                JOIN airlines a ON f.airline_id = a.airline_id
                JOIN planes p ON f.plane_id = p.plane_id
                WHERE f.flight_id = ?
            ");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $flight = $stmt->get_result()->fetch_assoc();
            $stmt->close();
        } else {
            $error = "Database error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Flight</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
<div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
    <h2 class="text-2xl font-bold mb-6 text-center">Edit Flight</h2>
    <p class="mb-6 text-center"><a href="dashboard.php" class="text-blue-600 hover:underline">← Back to Dashboard</a></p>

    <?php if($error): ?>
        <div class="mb-4 text-red-700 bg-red-100 border border-red-300 rounded px-4 py-2"><?= htmlspecialchars($error) ?></div>
    <?php elseif($success): ?>
        <div class="mb-4 text-green-700 bg-green-100 border border-green-300 rounded px-4 py-2"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form id="updateFlightForm" method="post" class="space-y-5" autocomplete="off">
        <div>
            <label class="block mb-1 font-semibold text-gray-700">Airline</label>
            <input type="text" value="<?= htmlspecialchars($flight['airline_name']) ?>" readonly
                class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-300"/>
        </div>

        <div>
            <label class="block mb-1 font-semibold text-gray-700">Plane Number</label>
            <input type="text" value="<?= htmlspecialchars($flight['plane_number']) ?>" readonly
                class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-300"/>
        </div>

        <div>
            <label class="block mb-1 font-semibold text-gray-700">Origin</label>
            <input type="text" value="<?= htmlspecialchars($flight['origin']) ?>" readonly
                class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-300"/>
        </div>

        <div>
            <label class="block mb-1 font-semibold text-gray-700">Destination</label>
            <input type="text" value="<?= htmlspecialchars($flight['destination']) ?>" readonly
                class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-300"/>
        </div>

        <div>
            <label for="price" class="block mb-1 font-semibold text-gray-700">Price</label>
            <input type="number" name="price" id="price" min="1" step="1"
                value="<?= (int)$flight['price'] ?>" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
        </div>

        <div>
            <label class="block mb-1 font-semibold text-gray-700">Total Seats</label>
            <input type="number" value="<?= (int)$flight['total_seats'] ?>" readonly
                class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-300"/>
            <small>Booked Seats: <?= (int)$flight['booked_seats'] ?></small>
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded font-semibold transition">
            Update Flight
        </button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('updateFlightForm');
    const priceInput = document.getElementById('price');
    
    // Store the original value
    const originalPrice = priceInput.value;

    form.addEventListener('submit', (e) => {
        const newPrice = priceInput.value;

        if (newPrice === originalPrice) {
            e.preventDefault();
            alert("No changes detected.");
        } else {
            e.preventDefault(); // prevent immediate submit
            alert(`You have changed the price from ${originalPrice} to ${newPrice}.`);

            // After alert, submit the form
            form.submit();
        }
    });
});
</script>
</body>
</html>
