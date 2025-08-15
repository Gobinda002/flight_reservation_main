<?php
require '../db.php';

if (!isset($_POST['flight_id'])) {
    echo "Invalid request";
    exit;
}

$flight_id = intval($_POST['flight_id']);

$stmt = $conn->prepare("DELETE FROM flights WHERE flight_id = ?");
$stmt->bind_param("i", $flight_id);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "Error deleting flight";
}

$stmt->close();
$conn->close();
