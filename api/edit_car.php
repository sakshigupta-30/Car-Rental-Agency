<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

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

// We expect form-data since it can contain files
$car_id = $_POST['car_id'] ?? '';
$model = $_POST['model'] ?? '';
$vehicle_number = $_POST['vehicle_number'] ?? '';
$seating_capacity = $_POST['seating_capacity'] ?? '';
$rent_per_day = $_POST['rent_per_day'] ?? '';

if (!empty($car_id) && !empty($model) && !empty($vehicle_number) && !empty($seating_capacity) && !empty($rent_per_day)) {
    
    // First, fetch existing car to preserve old image_path if no new one is uploaded
    $existingCar = $car->readOne($car_id);
    if (!$existingCar || $existingCar['agency_id'] != $_SESSION['user_id']) {
        http_response_code(404);
        echo json_encode(array("message" => "Car not found or access denied."));
        exit();
    }

    $car->id = $car_id;
    $car->agency_id = $_SESSION['user_id'];
    $car->model = $model;
    $car->vehicle_number = $vehicle_number;
    $car->seating_capacity = $seating_capacity;
    $car->rent_per_day = $rent_per_day;
    $car->image_path = $existingCar['image_path']; // preserve by default

    // Handle Image Upload if a new file is provided
    if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = $_FILES['car_image']['type'];
        
        if (in_array($fileType, $allowedTypes)) {
            $uploadDir = '../assets/img/cars/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = uniqid() . '_' . basename($_FILES['car_image']['name']);
            $targetFilePath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['car_image']['tmp_name'], $targetFilePath)) {
                $car->image_path = 'assets/img/cars/' . $fileName;
            } else {
                http_response_code(500);
                echo json_encode(array("message" => "Image upload failed."));
                exit();
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Invalid image format."));
            exit();
        }
    }

    if ($car->update()) {
        http_response_code(200);
        echo json_encode(array("message" => "Car details updated successfully."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to update car."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data. Required fields are missing."));
}
?>
