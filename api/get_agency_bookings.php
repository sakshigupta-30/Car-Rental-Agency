<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/db.php';
include_once '../models/Booking.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agency') {
    http_response_code(403);
    echo json_encode(array("message" => "Access denied."));
    exit();
}

$database = new Database();
$db = $database->getConnection();
$booking = new Booking($db);

$stmt = $booking->readByAgency($_SESSION['user_id']);
$num = $stmt->rowCount();

$bookings_arr = array();

if ($num > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($bookings_arr, $row);
    }
    http_response_code(200);
    echo json_encode($bookings_arr);
} else {
    http_response_code(200);
    echo json_encode(array());
}
?>
