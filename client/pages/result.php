<?php
session_start();
$isLoggedIn = isset($_SESSION['username']);
require_once '../../db.php';
include '../components/navbar.php';

// Get search inputs
$origin = strtolower(trim($_GET['from'] ?? ''));
$destination = strtolower(trim($_GET['to'] ?? ''));
$depart = $_GET['depart'] ?? '';
$passengers = $_GET['passengers'] ?? 1;

// Fetch all flights (with airline + plane info)
$sql = "SELECT f.*, a.airline_name, p.plane_number
        FROM flights f
        JOIN airlines a ON f.airline_id = a.airline_id
        JOIN planes p ON f.plane_id = p.plane_id";
$result = $conn->query($sql);

$matchingFlights = [];

// 🔍 Linear search in PHP
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    if (
      strtolower($row['origin']) === $origin &&
      strtolower($row['destination']) === $destination &&
      date("Y-m-d", strtotime($row['departure_time'])) === $depart
    ) {
      $matchingFlights[] = $row;
    }
  }
}
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
    <div
      class="max-w-7xl mx-auto bg-white rounded-2xl shadow mt-20 overflow-hidden px-6 pt-12 pb-20 flex flex-col md:flex-row gap-6">

      <!-- Sidebar search -->
      <div class="md:w-1/4 bg-white rounded-xl p-6 shadow">
        <h2 class="text-lg font-semibold mb-4">Your Search</h2>

        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">From</label>
            <input type="text" value="<?= htmlspecialchars($_GET['from'] ?? '') ?>" readonly
              class="w-full bg-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none" />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">To</label>
            <input type="text" value="<?= htmlspecialchars($_GET['to'] ?? '') ?>" readonly
              class="w-full bg-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none" />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Departure</label>
            <input type="date" value="<?= htmlspecialchars($_GET['depart'] ?? '') ?>" readonly
              class="w-full bg-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none" />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Passengers</label>
            <input type="number" value="<?= htmlspecialchars($_GET['passengers'] ?? 1) ?>" readonly
              class="w-full bg-gray-100 rounded-md px-3 py-2 text-sm focus:outline-none" />
          </div>

          <button onclick="window.location.href='../index.php#search-section'"
            class="w-full mt-2 bg-black text-white py-2 rounded-md text-sm">
            Change Search
          </button>
        </div>
      </div>

      <!-- Results list -->
      <div class="flex-1 bg-gray-50 rounded-xl p-6 shadow max-h-[70vh] overflow-y-auto">
        <h2 class="text-lg font-semibold mb-4">Flight Results</h2>

        <?php if (count($matchingFlights) > 0): ?>
          <?php foreach ($matchingFlights as $flight): ?>
            <?php include '../components/flight_card.php'; ?>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="bg-white shadow-lg rounded-lg p-6 text-center">
            <p class="text-gray-600 font-medium">No flights found for your search.</p>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    const isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;

    function handleBooking(flightId) {
      if (!isLoggedIn) {
        Swal.fire({
          icon: "warning",
          title: "Please Login First",
          text: "You need to login before reserving a plane.",
          confirmButtonText: "Go to Login / Register",
          confirmButtonColor: "#3085d6"
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = "logreg.php";
          }
        });
      } else {
        // proceed to booking
        window.location.href = "booking.php?flight_id=" + flightId;
      }
    }
  </script>

</body>
</html>
