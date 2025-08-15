<?php
// components/flight-card.php
if (!isset($flight) || !is_array($flight)) {
    return; // safety check
}
?>

<div class="bg-white shadow-lg rounded-lg p-6">
    <h2 class="text-xl font-bold mb-2">
        <?= htmlspecialchars($flight['origin']) ?> → <?= htmlspecialchars($flight['destination']) ?>
    </h2>
    <p><strong>Departure:</strong> <?= date('Y-m-d H:i', strtotime($flight['departure_time'])) ?></p>
    
    <?php if (!empty($flight['arrival_time'])): ?>
        <p><strong>Arrival:</strong> <?= date('Y-m-d H:i', strtotime($flight['arrival_time'])) ?></p>
    <?php endif; ?>

    <p><strong>Seats Available:</strong> <?= htmlspecialchars($flight['total_seats'] - $flight['booked_seats']) ?></p>
    
    <button class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        Book Now
    </button>
</div>
