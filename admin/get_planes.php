<?php
require '../db.php';

$airline_id = (int)($_GET['airline_id'] ?? 0);
$planes = [];

if ($airline_id > 0) {
    $stmt = $conn->prepare("SELECT plane_id, plane_number FROM planes WHERE airline_id = ?");
    $stmt->bind_param("i", $airline_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $planes[] = ['id' => $row['plane_id'], 'number' => $row['plane_number']];
    }
    $stmt->close();
}

echo json_encode($planes);
