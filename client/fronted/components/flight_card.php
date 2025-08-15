<?php
if (!isset($flight) || !is_array($flight)) {
    return; // safety check
}

$seatsLeft = $flight['total_seats'] - $flight['booked_seats'];
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

        <!-- Middle: Duration (unchanged) -->
        <div class="flex flex-col items-center flex-1 my-2">
            <div class="relative flex items-center w-full justify-center">
                <div class="h-0.5 bg-blue-200 flex-1"></div>
                <span class="absolute bg-white px-2 text-xs text-gray-500">
                    <?= htmlspecialchars($flight['duration'] ?? 'N/A') ?>
                </span>
            </div>
            <?php if (!empty($flight['layover'])): ?>
                <p class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($flight['layover']) ?></p>
            <?php endif; ?>
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
            <button class="mt-2 bg-blue-500 text-white px-4 py-1 rounded-full text-sm">Select</button>
        </div>
    </div>

    <!-- Footer: Seats, Non-Refundable & View Details inside main card -->
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
        <a href="#" class="text-blue-500 font-semibold text-sm">View Details</a>
    </div>
</div>
