<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

try {
    $db = get_db();
    $sql = 'SELECT id, code, city, name FROM airports ORDER BY city';
    $result = $db->query($sql);
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    json_response($rows);
} catch (Throwable $e) {
    json_response(['message' => 'Server error'], 500);
}


