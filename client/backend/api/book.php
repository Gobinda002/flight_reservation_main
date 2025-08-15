<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type');
    exit;
}

try {
    $db = get_db();
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $flightId = isset($input['flight_id']) ? (int)$input['flight_id'] : 0;
    $passengerName = isset($input['passenger_name']) ? trim((string)$input['passenger_name']) : '';
    $email = isset($input['email']) ? trim((string)$input['email']) : '';

    if ($flightId <= 0 || $passengerName === '' || $email === '') {
        json_response(['message' => 'Missing required fields'], 400);
    }

    // Ensure flight exists
    $stmt = $db->prepare('SELECT id, price FROM flights WHERE id = ?');
    $stmt->bind_param('i', $flightId);
    $stmt->execute();
    $result = $stmt->get_result();
    $flight = $result->fetch_assoc();
    if (!$flight) {
        json_response(['message' => 'Flight not found'], 404);
    }

    $db->begin_transaction();
    $ref = strtoupper(bin2hex(random_bytes(4)));

    $stmt = $db->prepare('INSERT INTO bookings (reference, flight_id, passenger_name, email, amount, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
    $status = 'CONFIRMED';
    $amount = (float)$flight['price'];
    $stmt->bind_param('sissds', $ref, $flightId, $passengerName, $email, $amount, $status);
    $stmt->execute();

    $db->commit();
    json_response(['reference' => $ref, 'message' => 'Booked']);
} catch (Throwable $e) {
    try { if (isset($db)) { $db->rollback(); } } catch (Throwable $ignored) {}
    json_response(['message' => 'Server error'], 500);
}


