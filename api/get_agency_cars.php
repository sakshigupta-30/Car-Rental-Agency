<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/db.php';
include_once '../models/Car.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agency') {
    http_response_code(403);
    echo json_encode(array("message" => "Access denied."));
    exit();
}

$database = new Database();
$db = $database->getConnection();
$car = new Car($db);

$stmt = $car->readByAgency($_SESSION['user_id']);
$num = $stmt->rowCount();

$cars_arr = array();

if ($num > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($cars_arr, $row);
    }
    http_response_code(200);
    echo json_encode($cars_arr);
} else {
    http_response_code(200);
    echo json_encode(array());
}
?>
