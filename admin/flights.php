<?php
session_start();
require '../db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Fetch flights
$sql = "SELECT f.flight_id, f.plane_id, f.airline_id, f.origin, f.destination, 
               f.departure_time, f.arrival_time, f.total_seats, f.booked_seats, 
               a.airline_name, p.plane_number
        FROM flights f
        LEFT JOIN airlines a ON f.airline_id = a.airline_id
        LEFT JOIN planes p ON f.plane_id = p.plane_id";

$result = $conn->query($sql);

$flights = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $flights[] = $row;
    }
    usort($flights, function($a, $b) {
        return strtotime($a['departure_time']) <=> strtotime($b['departure_time']);
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Flights List</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
function showCancelForm(flightId) {
    document.getElementById("cancel-form-" + flightId).classList.toggle("hidden");
}

function cancelFlight(flightId) {
    let reason = document.getElementById("cancel-reason-" + flightId).value.trim();
    if (reason === "") {
        alert("Please enter a reason for cancellation.");
        return;
    }

    if (!confirm("Are you sure you want to cancel this flight?")) {
        return;
    }

    fetch("delete_flight.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "flight_id=" + flightId + "&cancel_reason=" + encodeURIComponent(reason)
    })
    .then(response => response.text())
    .then(data => {
        if (data === "success") {
            document.getElementById("row-" + flightId).remove();
            document.getElementById("cancel-form-" + flightId).remove();
        } else {
            alert("Error: " + data);
        }
    })
    .catch(err => {
        alert("Request failed: " + err);
    });
}
</script>
</head>
<body class="bg-gray-100 p-4">

<div class="max-w-6xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">All Flights</h2>
    <table class="min-w-full table-auto border-collapse border border-gray-300">
        <thead>
            <tr class="bg-blue-500 text-white">
                <th class="border px-4 py-2">Flight Number</th>
                <th class="border px-4 py-2">Airline</th>
                <th class="border px-4 py-2">Origin</th>
                <th class="border px-4 py-2">Destination</th>
                <th class="border px-4 py-2">Departure Time</th>
                <th class="border px-4 py-2">Total Seats</th>
                <th class="border px-4 py-2">Booked Seats</th>
                <th class="border px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($flights)): ?>
            <?php foreach ($flights as $flight): ?>
            <tr id="row-<?= $flight['flight_id'] ?>" class="text-center">
               <td class="border px-4 py-2"><?= htmlspecialchars($flight['plane_number'] ?: 'N/A') ?></td>
               <td class="border px-4 py-2"><?= htmlspecialchars($flight['airline_name'] ?: 'N/A') ?></td>
               <td class="border px-4 py-2"><?= htmlspecialchars($flight['origin']) ?></td>
               <td class="border px-4 py-2"><?= htmlspecialchars($flight['destination']) ?></td>
               <td class="border px-4 py-2"><?= $flight['departure_time'] ? date('Y-m-d H:i', strtotime($flight['departure_time'])) : 'N/A' ?></td>
               <td class="border px-4 py-2"><?= (int)$flight['total_seats'] ?></td>
               <td class="border px-4 py-2"><?= (int)$flight['booked_seats'] ?></td>
               <td class="border px-4 py-2 space-x-2">
                    <a href="edit_flight.php?id=<?= $flight['flight_id'] ?>" 
   class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded">Edit</a>

                    <button onclick="showCancelForm(<?= $flight['flight_id'] ?>)" 
                       class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded">Cancel</button>
               </td>
            </tr>
            <tr id="cancel-form-<?= $flight['flight_id'] ?>" class="hidden bg-gray-50">
                <td colspan="8" class="border px-4 py-2">
                    <textarea id="cancel-reason-<?= $flight['flight_id'] ?>" 
                        placeholder="Enter reason for cancellation" required
                        class="border p-2 w-full rounded"></textarea>
                    <button onclick="cancelFlight(<?= $flight['flight_id'] ?>)" 
                        class="mt-2 bg-red-600 hover:bg-red-800 text-white px-4 py-2 rounded">
                        Confirm Cancel
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" class="border px-4 py-2 text-center">No flights found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
