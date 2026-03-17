<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/db.php';
include_once '../models/Booking.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(array("message" => "Please login to rent a car.", "redirect" => "login.php"));
    exit();
}

if ($_SESSION['role'] === 'agency') {
    http_response_code(403);
    echo json_encode(array("message" => "Agencies are not allowed to rent cars."));
    exit();
}

$database = new Database();
$db = $database->getConnection();
$booking = new Booking($db);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->car_id) && !empty($data->start_date) && !empty($data->days)) {
    $booking->car_id = $data->car_id;
    $booking->customer_id = $_SESSION['user_id'];
    $booking->start_date = $data->start_date;
    $booking->days = $data->days;

    if ($booking->create()) {
        http_response_code(201);
        echo json_encode(array("message" => "Car booked successfully."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to book car. It might be already booked."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete booking details."));
}
?>
