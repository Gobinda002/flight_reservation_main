<?php
session_start();
require '../db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';
$duplicatePlane = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $admin_id = $_SESSION['admin_id'];

    if ($type === 'airline') {
        $name = trim($_POST['name'] ?? '');
        if (!$name) {
            $error = "Airline name is required.";
        } else {
            $stmtCheck = $conn->prepare("SELECT * FROM airlines WHERE airline_name = ?");
            $stmtCheck->bind_param("s", $name);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->get_result();
            if ($resultCheck->num_rows > 0) {
                $error = "Airline '$name' already exists!";
            } else {
                $stmt = $conn->prepare("INSERT INTO airlines (airline_name, added_by) VALUES (?, ?)");
                $stmt->bind_param("si", $name, $admin_id);
                if ($stmt->execute()) $success = "Airline '$name' added successfully.";
                else $error = "Database error: " . $stmt->error;
                $stmt->close();
            }
            $stmtCheck->close();
        }
    }
if ($type === 'plane') {
    $airline_id = $_POST['airline_id'] ?? '';
    $plane_number = trim($_POST['plane_number'] ?? '');
    $capacity = trim($_POST['capacity'] ?? '');

    if (!$airline_id || !$plane_number || !$capacity) {
        $error = "Airline, Plane number, and Capacity are required.";
    } else {
        // Check if plane number already exists
        $stmtCheck = $conn->prepare("SELECT * FROM planes WHERE plane_number = ?");
        $stmtCheck->bind_param("s", $plane_number);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
        if ($resultCheck->num_rows > 0) {
            $error = "Plane '$plane_number' already exists!";
        } else {
            $stmt = $conn->prepare("INSERT INTO planes (airline_id, plane_number, capacity, added_by) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isii", $airline_id, $plane_number, $capacity, $admin_id);
            if ($stmt->execute()) {
                $success = "Plane '$plane_number' added successfully.";
            } else {
                $error = "Database error: " . $stmt->error;
            }
            $stmt->close();
        }
        $stmtCheck->close();
    }
}

}

// Fetch airlines for dropdown
$airlinesResult = $conn->query("SELECT airline_id, airline_name FROM airlines");
$airlines = [];
while ($row = $airlinesResult->fetch_assoc()) {
    $airlines[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Add Airline / Plane</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
<div class="bg-white p-8 rounded-lg shadow-lg max-w-lg w-full">
    <h2 class="text-2xl font-bold mb-6 text-center">Add Airline / Plane</h2>
    <p class="mb-6 text-center">
        <a href="dashboard.php" class="text-blue-600 hover:underline">← Back to Dashboard</a>
    </p>

    <!-- Alerts -->
    <?php if ($error): ?>
        <div id="alert" class="mb-4 text-red-700 bg-red-100 border border-red-300 rounded px-4 py-2">
            <?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div id="alert" class="mb-4 text-green-700 bg-green-100 border border-green-300 rounded px-4 py-2">
            <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Toggle buttons -->
    <div class="flex justify-center gap-4 mb-6">
        <button id="toggleAirline" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Airline</button>
        <button id="togglePlane" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Add Plane</button>
    </div>

    <!-- Add Airline Form -->
    <form id="formAirline" method="post" class="space-y-5">
        <input type="hidden" name="type" value="airline">
        <div>
            <label for="name" class="block mb-1 font-semibold text-gray-700">Airline Name</label>
            <input type="text" name="name" id="name" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>
        <button type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded font-semibold transition">
            Add Airline
        </button>
    </form>

    <!-- Add Plane Form -->
    <form id="formPlane" method="post" class="space-y-5 hidden">
        <input type="hidden" name="type" value="plane">
        <div>
            <label for="airline_id" class="block mb-1 font-semibold text-gray-700">Select Airline</label>
            <select name="airline_id" id="airline_id" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="" disabled selected>Select an airline</option>
                <?php foreach ($airlines as $airline): ?>
                    <option value="<?= $airline['airline_id'] ?>"><?= htmlspecialchars($airline['airline_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="plane_number2" class="block mb-1 font-semibold text-gray-700">Plane Number</label>
            <input type="text" name="plane_number" maxlength="6" id="plane_number2" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 uppercase" />
        </div>
        <div>
            <label for="capacity" class="block mb-1 font-semibold text-gray-700">Capacity</label>
            <input type="number" name="capacity" id="capacity" min="1" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" />
        </div>
        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded font-semibold transition">
            Add Plane
        </button>
    </form>
</div>

<script>
const toggleAirline = document.getElementById('toggleAirline');
const togglePlane = document.getElementById('togglePlane');
const formAirline = document.getElementById('formAirline');
const formPlane = document.getElementById('formPlane');

toggleAirline.addEventListener('click', () => {
    formAirline.classList.remove('hidden');
    formPlane.classList.add('hidden');
});
togglePlane.addEventListener('click', () => {
    formPlane.classList.remove('hidden');
    formAirline.classList.add('hidden');
});

// Auto-hide alerts after 3 seconds
const alertBox = document.getElementById('alert');
if(alertBox){
    setTimeout(()=>{ alertBox.style.display='none'; }, 3000);
}
</script>
</body>
</html>
