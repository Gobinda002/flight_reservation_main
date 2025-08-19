<?php
if (!isset($flight) || !is_array($flight)) {
    return; // safety check
}

$seatsLeft = $flight['total_seats'] - $flight['booked_seats'];
$modalId = "flightModal" . $flight['flight_id']; // Unique ID for each flight

// for the duration 
if (!empty($flight['departure_time']) && !empty($flight['arrival_time'])) {
    $depTime = new DateTime($flight['departure_time']);
    $arrTime = new DateTime($flight['arrival_time']);
    $interval = $depTime->diff($arrTime);
    $duration = $interval->format('%h hrs %i mins');
} else {
    $duration = 'N/A';
}
?>

<div class="bg-white rounded-xl shadow p-4 mb-4 transition-transform transform hover:scale-[1.02] flex flex-col">
    <!-- Top Section: Flight Info -->
    <div class="flex items-center justify-between">
        <!-- Left: Airline & Departure -->
        <div class="flex items-center space-x-4">
            <div>
                <div class="flex items-center space-x-2 font-semibold text-lg">
                    <span><?= htmlspecialchars($flight['origin']) ?></span>
                    <span>-</span>
                    <span><?= htmlspecialchars($flight['destination']) ?></span>
                </div>
                <p class="text-gray-500 text-sm"><?= strtoupper(htmlspecialchars($flight['airline_name'])) ?></p>
            </div>
            <div class="ml-6">
                <p class="font-bold text-lg"><?= date('H:i', strtotime($flight['departure_time'])) ?></p>
                <p class="text-xs text-gray-500">
                    <?= date('j M, l', strtotime($flight['departure_time'])) ?><br>
                    <?= htmlspecialchars($flight['origin']) ?>
                </p>
            </div>
        </div>

        <!-- Middle: Duration -->
        <div class="flex flex-col items-center flex-1 my-2">
            <div class="relative flex items-center w-full justify-center">
                <div class="h-0.5 bg-blue-200 flex-1"></div>
                <span class="absolute bg-white px-2 text-xs text-gray-500">
                    <?= htmlspecialchars($flight['duration'] ?? 'N/A') ?>
                </span>
            </div>
        </div>

        <!-- Right: Arrival -->
        <div class="text-right mr-6">
            <p class="font-bold text-lg"><?= date('H:i', strtotime($flight['arrival_time'])) ?></p>
            <p class="text-xs text-gray-500">
                <?= date('j M, l', strtotime($flight['arrival_time'])) ?><br>
                <?= htmlspecialchars($flight['destination']) ?>
            </p>
        </div>

        <!-- Price & Button -->
        <div class="text-center">
            <p class="font-bold text-xl">$<?= htmlspecialchars(number_format($flight['price'], 2)) ?></p>

            <button onclick="handleBooking(<?= $flight['flight_id'] ?>)"
                class="mt-2 bg-blue-500 text-white px-4 py-1 rounded-full text-sm hover:bg-blue-600 transition">
                Select
            </button>
        </div>

    </div>

    <!-- Footer: Seats & View Details -->
    <div class="flex justify-between items-center mt-4 bg-blue-50 px-4 py-2 rounded-lg">
        <div class="flex space-x-2 items-center">
            <span class="text-yellow-600 bg-yellow-100 px-3 py-1 rounded-full text-xs font-medium">
                <?= $seatsLeft ?> Seats Available
            </span>
            <?php if (!empty($flight['non_refundable'])): ?>
                <span class="text-gray-500 bg-gray-200 px-3 py-1 rounded-full text-xs font-medium">
                    Non Refundable
                </span>
            <?php endif; ?>
        </div>
        <button onclick="document.getElementById('<?= $modalId ?>').classList.remove('hidden')"
            class="text-blue-500 font-semibold text-sm">
            View Details
        </button>
    </div>
</div>

<!-- Modal -->
<div id="<?= $modalId ?>" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 w-96 relative">
        <button onclick="document.getElementById('<?= $modalId ?>').classList.add('hidden')"
            class="absolute top-2 right-2 text-gray-500 hover:text-gray-800">&times;</button>
        <h2 class="text-xl font-bold mb-4"><?= htmlspecialchars($flight['airline_name']) ?></h2>

        <!-- ✅ Fixed Plane Number -->
        <p><strong>Plane Number:</strong> <?= htmlspecialchars(strtoupper($flight['plane_number'] ?? 'N/A')) ?></p>
        <p><strong>Luggage:</strong> 15 kg</p>
        <p><strong>Duration:</strong> <?= $duration ?></p>
        <p><strong>Departure:</strong> <?= date('j M, H:i', strtotime($flight['departure_time'])) ?></p>
        <p><strong>Arrival:</strong> <?= date('j M, H:i', strtotime($flight['arrival_time'])) ?></p>
        <p class="text-red-500"><strong>Non-Refundable</strong></p>
    </div>
</div>