<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/db.php';
include_once '../models/Car.php';

$database = new Database();
$db = $database->getConnection();
$car = new Car($db);

$search = isset($_GET['search']) ? $_GET['search'] : "";
$stmt = $car->readAll($search);
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
