<?php
session_start();
require '../db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plane_id = (int)($_POST['plane_id'] ?? 0);
    $airline_id = (int)($_POST['airline_id'] ?? 0);
    $origin = trim($_POST['origin'] ?? '');
    $destination = trim($_POST['destination'] ?? '');
    $departure_time = $_POST['departure_time'] ?? '';
    $arrival_time = $_POST['arrival_time'] ?? '';
    $total_seats = (int)($_POST['total_seats'] ?? 0);
    $price = (int)($_POST['price'] ?? 0);

    if (!$plane_id || !$origin || !$destination || !$departure_time || !$arrival_time || $total_seats <= 0 || $price <= 0) {
        $error = "All fields are required, and seats/price must be positive.";
    } elseif (strtotime($arrival_time) <= strtotime($departure_time)) {
        $error = "Arrival time must be greater than departure time.";
    } else {

        // ✅ Fetch plane capacity
        $stmtCapacity = $conn->prepare("SELECT capacity FROM planes WHERE plane_id = ?");
        $stmtCapacity->bind_param("i", $plane_id);
        $stmtCapacity->execute();
        $resultCapacity = $stmtCapacity->get_result();
        $planeData = $resultCapacity->fetch_assoc();
        $stmtCapacity->close();

        if (!$planeData) {
            $error = "Selected plane does not exist.";
        } elseif ($total_seats > (int)$planeData['capacity']) {
            $error = "Total seats cannot exceed plane capacity of " . $planeData['capacity'] . ".";
        } else {

            // Check if same plane already has flight at the same departure time
            $stmtCheck = $conn->prepare("SELECT * FROM flights WHERE plane_id = ? AND departure_time = ?");
            $stmtCheck->bind_param("is", $plane_id, $departure_time);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->get_result();

            if ($resultCheck->num_rows > 0) {
                $error = "This plane already has a flight scheduled at this time!";
            } else {
                $stmt = $conn->prepare("INSERT INTO flights 
                    (plane_id, airline_id, origin, destination, departure_time, arrival_time, total_seats, price, booked_seats) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)");
                $stmt->bind_param("iissssii", $plane_id, $airline_id, $origin, $destination, $departure_time, $arrival_time, $total_seats, $price);

                if ($stmt->execute()) {
                    $success = "Flight added successfully.";
                    $_POST = []; // clear form
                } else {
                    $error = "Database error: " . $stmt->error;
                }
                $stmt->close();
            }
            $stmtCheck->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Add Flight</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
<div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
    <h2 class="text-2xl font-bold mb-6 text-center">Add New Flight</h2>
    <p class="mb-6 text-center">
        <a href="dashboard.php" class="text-blue-600 hover:underline">← Back to Dashboard</a>
    </p>

    <?php if ($error): ?>
        <div id="alert" class="mb-4 text-red-700 bg-red-100 border border-red-300 rounded px-4 py-2">
            <?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div id="alert" class="mb-4 text-green-700 bg-green-100 border border-green-300 rounded px-4 py-2">
            <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" class="space-y-5" autocomplete="off" id="flightForm">

        <div>
            <label for="airline" class="block mb-1 font-semibold text-gray-700">Airline</label>
            <select id="airline" name="airline_id" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="" disabled selected>Select an airline</option>
                <?php foreach ($airlines as $airline): ?>
                    <option value="<?= (int)$airline['airline_id'] ?>"><?= htmlspecialchars($airline['airline_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="plane_id" class="block mb-1 font-semibold text-gray-700">Plane Number</label>
            <select name="plane_id" id="plane_id" required disabled
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="" disabled selected>Select Plane</option>
            </select>
        </div>

        <div>
            <label for="origin" class="block mb-1 font-semibold text-gray-700">Origin</label>
            <input type="text" name="origin" id="origin" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <div>
            <label for="destination" class="block mb-1 font-semibold text-gray-700">Destination</label>
            <input type="text" name="destination" id="destination" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <div>
            <label for="departure_time" class="block mb-1 font-semibold text-gray-700">Departure Time</label>
            <input type="datetime-local" name="departure_time" id="departure_time" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <div>
            <label for="arrival_time" class="block mb-1 font-semibold text-gray-700">Arrival Time</label>
            <input type="datetime-local" name="arrival_time" id="arrival_time" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <div>
            <label for="total_seats" class="block mb-1 font-semibold text-gray-700">Total Seats</label>
            <input type="number" name="total_seats" id="total_seats" min="1" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <div>
            <label for="price" class="block mb-1 font-semibold text-gray-700">Price</label>
            <input type="number" name="price" id="price" min="1" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <button type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded font-semibold transition">
            Add Flight
        </button>
    </form>
</div>

<script>
const airlineSelect = document.getElementById('airline');
const planeSelect = document.getElementById('plane_id');

airlineSelect.addEventListener('change', () => {
    const airlineId = airlineSelect.value;
    planeSelect.innerHTML = '<option value="" disabled selected>Loading...</option>';
    planeSelect.disabled = true;

    fetch(`get_planes.php?airline_id=${airlineId}`)
        .then(res => res.json())
        .then(data => {
            planeSelect.innerHTML = '<option value="" disabled selected>Select Plane</option>';
            if (data.length === 0) {
                const option = document.createElement('option');
                option.textContent = 'No planes found';
                option.disabled = true;
                planeSelect.appendChild(option);
            } else {
                data.forEach(plane => {
                    const option = document.createElement('option');
                    option.value = plane.id;         // plane_id
                    option.textContent = plane.number; // plane_number
                    planeSelect.appendChild(option);
                });
            }
            planeSelect.disabled = false;
        })
        .catch(err => {
            planeSelect.innerHTML = '<option value="" disabled selected>Error loading planes</option>';
            console.error(err);
        });
});

// Auto-hide alerts
const alertBox = document.getElementById('alert');
if(alertBox){
    setTimeout(()=>{ alertBox.style.display='none'; }, 3000);
}

// Prevent past dates + validate arrival > departure
window.addEventListener('DOMContentLoaded', () => {
    const departureInput = document.getElementById('departure_time');
    const arrivalInput = document.getElementById('arrival_time');
    const form = document.getElementById('flightForm');

    const now = new Date();
    const pad = num => num < 10 ? '0'+num : num;
    departureInput.min = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;

    form.addEventListener('submit', e => {
        const dep = new Date(departureInput.value);
        const arr = new Date(arrivalInput.value);
        if (arr <= dep) {
            e.preventDefault();
            alert("Arrival time must be later than departure time.");
        }
    });
});
</script>
</body>
</html>
