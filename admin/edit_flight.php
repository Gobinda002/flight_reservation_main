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

// Fetch airlines
$airlines = [];
$result = $conn->query("SELECT airline_id, airline_name FROM airlines ORDER BY airline_name ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $airlines[] = $row;
    }
    $result->free();
}

// Fetch flight
$stmt = $conn->prepare("SELECT * FROM flights WHERE flight_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$flight = $result->fetch_assoc();
$stmt->close();

if (!$flight) {
    header("Location: dashboard.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $airline_id = (int)($_POST['airline'] ?? 0);
    $plane_id = (int)($_POST['flight_number'] ?? 0);
    $origin = trim($_POST['origin'] ?? '');
    $destination = trim($_POST['destination'] ?? '');
    $departure_time = $_POST['departure_time'] ?? '';
    $total_seats = (int)($_POST['total_seats'] ?? 0);

    $now = date('Y-m-d H:i');

    if (!$airline_id || !$plane_id || !$origin || !$destination || !$departure_time || $total_seats <= 0) {
        $error = "All fields are required and seats must be positive.";
    } elseif ($departure_time < $now) {
        $error = "Departure time cannot be in the past.";
    } elseif ($total_seats < $flight['booked_seats']) {
        $error = "Total seats cannot be less than already booked seats ({$flight['booked_seats']}).";
    } else {
        // Update flight
        $stmt = $conn->prepare("UPDATE flights SET airline_id=?, plane_id=?, origin=?, destination=?, departure_time=?, total_seats=? WHERE id=?");
        $stmt->bind_param("isssiii", $airline_id, $plane_id, $origin, $destination, $departure_time, $total_seats, $id);
        if ($stmt->execute()) {
            $success = "Flight updated successfully.";
            // Refresh flight data
            $stmt->close();
            $stmt = $conn->prepare("SELECT * FROM flights WHERE id=?");
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

    <form method="post" class="space-y-5" autocomplete="off">
        <div>
            <label for="airline" class="block mb-1 font-semibold text-gray-700">Airline</label>
            <select name="airline" id="airline" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="" disabled>Select an airline</option>
                <?php foreach($airlines as $airline): ?>
                    <option value="<?= $airline['airline_id'] ?>" <?= $flight['airline_id']==$airline['airline_id']?'selected':'' ?>><?= htmlspecialchars($airline['airline_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="flight_number" class="block mb-1 font-semibold text-gray-700">Plane Number</label>
            <select name="flight_number" id="flight_number" required disabled
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Select Plane Number</option>
            </select>
        </div>

        <div>
            <label for="origin" class="block mb-1 font-semibold text-gray-700">Origin</label>
            <input type="text" name="origin" id="origin" value="<?= htmlspecialchars($flight['origin']) ?>" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
        </div>

        <div>
            <label for="destination" class="block mb-1 font-semibold text-gray-700">Destination</label>
            <input type="text" name="destination" id="destination" value="<?= htmlspecialchars($flight['destination']) ?>" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
        </div>

        <div>
            <label for="departure_time" class="block mb-1 font-semibold text-gray-700">Departure Time</label>
            <input type="datetime-local" name="departure_time" id="departure_time" value="<?= date('Y-m-d\TH:i', strtotime($flight['departure_time'])) ?>" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
        </div>

        <div>
            <label for="total_seats" class="block mb-1 font-semibold text-gray-700">Total Seats</label>
            <input type="number" name="total_seats" id="total_seats" min="<?= $flight['booked_seats'] ?>" value="<?= (int)$flight['total_seats'] ?>" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            <small>Booked Seats: <?= (int)$flight['booked_seats'] ?></small>
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded font-semibold transition">
            Update Flight
        </button>
    </form>
</div>

<script>
const airlineSelect = document.getElementById('airline');
const flightSelect = document.getElementById('flight_number');
const selectedPlane = <?= $flight['plane_id'] ?>;

// Load planes for selected airline
function loadPlanes(airlineId){
    flightSelect.innerHTML = '<option>Loading...</option>';
    flightSelect.disabled = true;
    fetch(`get_planes.php?airline_id=${airlineId}`)
        .then(res => res.json())
        .then(data => {
            flightSelect.innerHTML = '<option value="">Select Plane Number</option>';
            data.forEach(plane => {
                const option = document.createElement('option');
                option.value = plane;
                option.textContent = plane;
                if(plane == selectedPlane) option.selected = true;
                flightSelect.appendChild(option);
            });
            flightSelect.disabled = false;
        }).catch(err => {
            flightSelect.innerHTML = '<option>Error loading planes</option>';
            console.error(err);
        });
}

// Initial load
if(airlineSelect.value) loadPlanes(airlineSelect.value);
airlineSelect.addEventListener('change', () => loadPlanes(airlineSelect.value));

// Prevent past dates
window.addEventListener('DOMContentLoaded', () => {
    const departureInput = document.getElementById('departure_time');
    const now = new Date();
    const pad = n => n<10?'0'+n:n;
    departureInput.min = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
});
</script>
</body>
</html>
