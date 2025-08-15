<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

function get_param(string $key): ?string {
    return isset($_GET[$key]) && $_GET[$key] !== '' ? trim((string)$_GET[$key]) : null;
}

try {
    $db = get_db();

    $origin = get_param('origin');
    $destination = get_param('destination');
    $date = get_param('date'); // YYYY-MM-DD

    $conditions = [];
    $types = '';
    $values = [];

    if ($origin) {
        $conditions[] = 'f.origin = ?';
        $types .= 's';
        $values[] = $origin;
    }
    if ($destination) {
        $conditions[] = 'f.destination = ?';
        $types .= 's';
        $values[] = $destination;
    }
    if ($date) {
        $conditions[] = 'DATE(f.departure_time) = ?';
        $types .= 's';
        $values[] = $date;
    }

    $where = $conditions ? ('WHERE ' . implode(' AND ', $conditions)) : '';

    $sql = "
        SELECT f.id, f.flight_number, f.airline, f.origin, f.destination, f.departure_time, f.arrival_time,
               f.price, f.cabin_class
        FROM flights f
        $where
        ORDER BY f.departure_time ASC
        LIMIT 100
    ";

    $stmt = $db->prepare($sql);
    if ($types !== '') {
        $stmt->bind_param($types, ...$values);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    json_response($rows);
} catch (Throwable $e) {
    json_response(['message' => 'Server error'], 500);
}


