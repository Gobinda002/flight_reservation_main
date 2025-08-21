<?php
require '../db.php';

if (!isset($_POST['flight_id'])) {
    echo "Invalid request";
    exit;
}

$flight_id = intval($_POST['flight_id']);

// Check if the flight is in the past
$stmt = $conn->prepare("SELECT departure_time FROM flights WHERE flight_id = ?");
$stmt->bind_param("i", $flight_id);
$stmt->execute();
$stmt->bind_result($departure_time);
$stmt->fetch();
$stmt->close();

if (!$departure_time) {
    echo "Flight not found";
    exit;
}

if (strtotime($departure_time) >= time()) {
    echo "Cannot delete future flights";
    exit;
}

// Delete the flight
$stmt = $conn->prepare("DELETE FROM flights WHERE flight_id = ?");
$stmt->bind_param("i", $flight_id);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "Error deleting flight";
}

$stmt->close();
$conn->close();
