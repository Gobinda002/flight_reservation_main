<?php
session_start();
require_once '../db.php';

// Default sort
$sort = $_GET['sort'] ?? 'oldest';
$sortOrder = ($sort === 'latest') ? "DESC" : "ASC";

// Fetch flights
$sql = "SELECT f.flight_id, f.plane_id, f.airline_id, f.origin, f.destination, 
               f.departure_time, f.arrival_time, f.total_seats, f.booked_seats, 
               a.airline_name, p.plane_number
        FROM flights f
        LEFT JOIN airlines a ON f.airline_id = a.airline_id
        LEFT JOIN planes p ON f.plane_id = p.plane_id
        ORDER BY f.departure_time $sortOrder";

$result = $conn->query($sql);
$flights = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Flights</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

  <div class="max-w-6xl mx-auto bg-white p-6 shadow-md rounded-lg">
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-2xl font-bold">Flights</h1>

      <!--  Sort Dropdown -->
      <div class="relative inline-block text-left">
        <button onclick="toggleDropdown()" 
          class="bg-gray-200 hover:bg-gray-300 p-2 rounded-full">
          <!-- Sort Icon -->
          <svg xmlns="http://www.w3.org/2000/svg" 
               class="h-6 w-6 text-gray-700" fill="none" 
               viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M3 4a1 1 0 011-1h4a1 1 0 011 1v16m0 0H4a1 1 0 01-1-1V4m5 16h4a1 1 0 001-1V4m0 0h4a1 1 0 011 1v16m-5-8h6"/>
          </svg>
        </button>

        <!-- Dropdown menu -->
        <div id="sortDropdown" 
          class="hidden absolute right-0 mt-2 w-40 bg-white border rounded-lg shadow-lg z-20">
          <a href="?sort=latest" 
             class="block px-4 py-2 hover:bg-gray-100 <?= $sort==='latest' ? 'font-bold text-blue-600' : '' ?>">
             Latest
          </a>
          <a href="?sort=oldest" 
             class="block px-4 py-2 hover:bg-gray-100 <?= $sort==='oldest' ? 'font-bold text-blue-600' : '' ?>">
             Oldest
          </a>
        </div>
      </div>
    </div>

    <!--  Flights Table -->
    <table class="w-full border-collapse border border-gray-300">
      <thead>
        <tr class="bg-gray-200 text-center">
          <th class="border px-4 py-2">Plane No</th>
          <th class="border px-4 py-2">Airline</th>
          <th class="border px-4 py-2">Origin</th>
          <th class="border px-4 py-2">Destination</th>
          <th class="border px-4 py-2">Departure Time</th>
          <th class="border px-4 py-2">Seats</th>
          <th class="border px-4 py-2">Booked</th>
          <th class="border px-4 py-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($flights)): ?>
          <tr>
            <td colspan="8" class="text-center py-4">No flights found.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($flights as $flight): ?>
            <tr id="row-<?= $flight['flight_id'] ?>" class="text-center">
              <td class="border px-4 py-2"><?= htmlspecialchars($flight['plane_number'] ?: 'N/A') ?></td>
              <td class="border px-4 py-2"><?= htmlspecialchars($flight['airline_name'] ?: 'N/A') ?></td>
              <td class="border px-4 py-2"><?= htmlspecialchars($flight['origin']) ?></td>
              <td class="border px-4 py-2"><?= htmlspecialchars($flight['destination']) ?></td>
              <td class="border px-4 py-2">
                <?= $flight['departure_time'] ? date('Y-m-d H:i', strtotime($flight['departure_time'])) : 'N/A' ?>
              </td>
              <td class="border px-4 py-2"><?= (int) $flight['total_seats'] ?></td>
              <td class="border px-4 py-2"><?= (int) $flight['booked_seats'] ?></td>
              <td class="border px-4 py-2 space-x-2">
                <a href="edit_flight.php?id=<?= $flight['flight_id'] ?>"
                   class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded">Edit</a>

                <button onclick="showCancelForm(<?= $flight['flight_id'] ?>)"
                   class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded">Cancel</button>

                <?php if (strtotime($flight['departure_time']) < time()): ?>
                  <button onclick="deleteFlight(<?= $flight['flight_id'] ?>)"
                     class="bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded">Delete</button>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <script>
    function toggleDropdown() {
      document.getElementById("sortDropdown").classList.toggle("hidden");
    }

    // Close dropdown if clicked outside
    window.onclick = function(event) {
      if (!event.target.closest('#sortDropdown') && !event.target.closest('button')) {
        document.getElementById("sortDropdown").classList.add("hidden");
      }
    }

    function showCancelForm(flightId) {
      alert("Cancel form for Flight ID: " + flightId);
    }

    function deleteFlight(flightId) {
      if (confirm("Are you sure you want to delete this flight?")) {
        alert("Deleting flight " + flightId);
      }
    }
  </script>

</body>
</html>
