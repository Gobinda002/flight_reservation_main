<?php
session_start();
require '../db.php';

// Default sort
$sort = $_GET['sort'] ?? 'oldest';
$sortOrder = ($sort === 'latest') ? 'desc' : 'asc';

// Fetch flights (without ORDER BY!)
$sql = "SELECT f.flight_id, f.plane_id, f.airline_id, f.origin, f.destination, 
               f.departure_time, f.arrival_time, f.total_seats, f.booked_seats, 
               a.airline_name, p.plane_number
        FROM flights f
        LEFT JOIN airlines a ON f.airline_id = a.airline_id
        LEFT JOIN planes p ON f.plane_id = p.plane_id";

$result = $conn->query($sql);
$flights = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

/* ===== Bubble Sort Function ===== */
function bubbleSortFlights($flights, $key, $order = 'asc')
{
    $n = count($flights);
    for ($i = 0; $i < $n - 1; $i++) {
        for ($j = 0; $j < $n - $i - 1; $j++) {
            $val1 = strtotime($flights[$j][$key]);
            $val2 = strtotime($flights[$j + 1][$key]);

            if (($order === 'asc' && $val1 > $val2) ||
                ($order === 'desc' && $val1 < $val2)) {
                $tmp = $flights[$j];
                $flights[$j] = $flights[$j + 1];
                $flights[$j + 1] = $tmp;
            }
        }
    }
    return $flights;
}

// Apply Bubble Sort on departure_time
$flights = bubbleSortFlights($flights, 'departure_time', $sortOrder);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Flights</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

  <!-- ====== NAVBAR ====== -->
  <div class="navbar bg-blue-500 text-white p-4 md:px-8 flex justify-between items-center flex-wrap gap-4 shadow-md">
      <div class="navbar-brand text-xl md:text-2xl font-bold">FlyHigh</div>
      
      <div class="navbar-menu flex gap-4 md:gap-6 flex-grow justify-start ml-0 md:ml-8 flex-wrap">
          <a href="dashboard.php" class="text-white hover:text-white font-medium text-base relative <?= basename($_SERVER['PHP_SELF'])=='dashboard.php' ? 'underline' : '' ?>">Dashboard</a>
          <a href="add_flight.php" class="text-white hover:text-white font-medium text-base relative <?= basename($_SERVER['PHP_SELF'])=='add_flight.php' ? 'underline' : '' ?>">Create Flight</a>
          <a href="flights.php" class="text-white hover:text-white font-medium text-base relative <?= basename($_SERVER['PHP_SELF'])=='flights.php' ? 'underline' : '' ?>">Flights</a>
          <a href="listairline.php" class="text-white hover:text-white font-medium text-base relative <?= basename($_SERVER['PHP_SELF'])=='airlines.php' ? 'underline' : '' ?>">Airlines</a>
          <a href="seebookings.php" class="text-white hover:text-white font-medium text-base relative <?= basename($_SERVER['PHP_SELF'])=='seebookings.php' ? 'underline' : '' ?>">See Bookings</a>
      </div>

      <div class="navbar-actions flex gap-4 items-center flex-wrap">
          <a href="add_airlines.php" 
             class="navbar-action-button bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm flex items-center gap-2 whitespace-nowrap">
              <i class="fas fa-plus text-sm"></i> Airlines/Planes
          </a>
          <a href="admin.php" 
             class="navbar-action-button bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm flex items-center gap-2 whitespace-nowrap">
              <i class="fas fa-user-shield text-sm"></i> Admin
          </a>
          <a href="logout.php" 
             class="navbar-action-button bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm flex items-center gap-2 whitespace-nowrap">
              <i class="fas fa-sign-out-alt text-sm"></i> Logout
          </a>
      </div>
  </div>

  <!-- ====== SORT DROPDOWN ====== -->
  <div class="max-w-6xl mx-auto mt-6">
    <div class="flex justify-end mb-3">
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
             class="block px-4 py-2 hover:bg-gray-100 <?= $sort==='latest' ? 'font-bold text-blue-600' : '' ?>">Latest</a>
          <a href="?sort=oldest" 
             class="block px-4 py-2 hover:bg-gray-100 <?= $sort==='oldest' ? 'font-bold text-blue-600' : '' ?>">Oldest</a>
        </div>
      </div>
    </div>

    <!-- ====== FLIGHTS TABLE ====== -->
    <table class="w-full border-collapse border border-gray-300 bg-white shadow-md rounded-md">
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
        <?php if(empty($flights)): ?>
          <tr>
            <td colspan="8" class="text-center py-4">No flights found.</td>
          </tr>
        <?php else: ?>
          <?php foreach($flights as $flight): ?>
            <tr id="row-<?= $flight['flight_id'] ?>" class="text-center">
              <td class="border px-4 py-2"><?= htmlspecialchars($flight['plane_number'] ?: 'N/A') ?></td>
              <td class="border px-4 py-2"><?= htmlspecialchars($flight['airline_name'] ?: 'N/A') ?></td>
              <td class="border px-4 py-2 font-bold"><?= htmlspecialchars($flight['origin']) ?></td>
              <td class="border px-4 py-2"><?= htmlspecialchars($flight['destination']) ?></td>
              <td class="border px-4 py-2"><?= $flight['departure_time'] ? date('Y-m-d H:i', strtotime($flight['departure_time'])) : 'N/A' ?></td>
              <td class="border px-4 py-2"><?= (int)$flight['total_seats'] ?></td>
              <td class="border px-4 py-2 font-bold"><?= (int)$flight['booked_seats'] ?></td>
              <td class="border px-4 py-2 space-x-2">
                <a href="edit_flight.php?id=<?= $flight['flight_id'] ?>" class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded">Edit</a>
                <button onclick="alert('Cancel Flight <?= $flight['flight_id'] ?>')" class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded">Cancel</button>
                <?php if(strtotime($flight['departure_time']) < time()): ?>
                  <button onclick="alert('Delete Flight <?= $flight['flight_id'] ?>')" class="bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded">Delete</button>
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
      document.getElementById('sortDropdown').classList.toggle('hidden');
    }

    // Close dropdown if clicked outside
    window.onclick = function(event) {
      if (!event.target.closest('#sortDropdown') && !event.target.closest('button')) {
        document.getElementById('sortDropdown').classList.add('hidden');
      }
    }
  </script>

</body>
</html>
