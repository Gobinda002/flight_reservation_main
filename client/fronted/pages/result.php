<?php
session_start();
require_once '../../../db.php';

$sql = "SELECT f.*, a.airline_name
        FROM flights f
        JOIN airlines a ON f.airline_id = a.airline_id";
$result = $conn->query($sql);

$flights = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $flights[] = $row;
    }
}


// Get search inputs
$origin = $_GET['from'] ?? '';
$destination = $_GET['to'] ?? '';
$depart = $_GET['depart'] ?? '';

// Fetch matching flights
// Fetch matching flights with airline name
$sql = "SELECT f.*, a.airline_name
        FROM flights f
        JOIN airlines a ON f.airline_id = a.airline_id
        WHERE f.origin = ? 
        AND f.destination = ? 
        AND DATE(f.departure_time) = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $origin, $destination, $depart);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Flight Search Summary</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
  <div class="min-h-screen bg-gradient-to-br from-teal-500 via-white/5 to-green-400 relative overflow-hidden">

    <?php include '../components/navbar.php'; ?>

    <div class="max-w-7xl mx-auto bg-white rounded-2xl shadow mt-10 overflow-hidden px-6 py-8 flex flex-col md:flex-row gap-6">

      <!-- Sidebar search -->
      <div class="md:w-1/4 bg-white rounded-xl p-6 shadow">
        <h2 class="text-lg font-semibold mb-4">Your Search</h2>

        <!-- Input fields -->
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">From</label>
            <input
              type="text"
              value="Kathmandu"
              readonly
              class="w-full bg-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none"
            />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">To</label>
            <input
              type="text"
              value="Pokhara"
              readonly
              class="w-full bg-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none"
            />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Departure</label>
            <input
              type="date"
              value="2025-08-15"
              readonly
              class="w-full bg-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none"
            />
          </div>

          <!-- Remove this div if no round trip -->
          <div>
            <label class="block text-sm font-medium mb-1">Return</label>
            <input
              type="date"
              value="2025-08-20"
              readonly
              class="w-full bg-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none"
            />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Passengers</label>
            <input
              type="number"
              value="2"
              readonly
              class="w-full bg-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none"
            />
          </div>

          <button class="w-full mt-2 bg-black text-white py-2 rounded-md text-sm">
            Change Search
          </button>
        </div>
      </div>

      <!-- Results list placeholder -->
     <div class="flex-1 bg-gray-50 rounded-xl p-6 shadow">
    <h2 class="text-lg font-semibold mb-4">Flight Results</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($flight = $result->fetch_assoc()): ?>
            <?php include '../components/flight_card.php'; ?>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="bg-white shadow-lg rounded-lg p-6 text-center">
            <p class="text-gray-600 font-medium">No flights found for your search.</p>
        </div>
    <?php endif; ?>
</div>

    </div>
  </div>
</body>
</html>
