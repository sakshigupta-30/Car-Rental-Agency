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

// Check if data is coming as JSON (fallback) or form-data
$isJson = strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false;

if ($isJson) {
    $data = json_decode(file_get_contents("php://input"));
    $model = $data->model ?? '';
    $vehicle_number = $data->vehicle_number ?? '';
    $seating_capacity = $data->seating_capacity ?? '';
    $rent_per_day = $data->rent_per_day ?? '';
} else {
    $model = $_POST['model'] ?? '';
    $vehicle_number = $_POST['vehicle_number'] ?? '';
    $seating_capacity = $_POST['seating_capacity'] ?? '';
    $rent_per_day = $_POST['rent_per_day'] ?? '';
}

if (!empty($model) && !empty($vehicle_number) && !empty($seating_capacity) && !empty($rent_per_day)) {
    $car->agency_id = $_SESSION['user_id'];
    $car->model = $model;
    $car->vehicle_number = $vehicle_number;
    $car->seating_capacity = $seating_capacity;
    $car->rent_per_day = $rent_per_day;

    // Handle Image Upload
    $image_path = null;
    if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = $_FILES['car_image']['type'];
        
        if (in_array($fileType, $allowedTypes)) {
            $uploadDir = '../assets/img/cars/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Create a unique filename
            $fileName = uniqid() . '_' . basename($_FILES['car_image']['name']);
            $targetFilePath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['car_image']['tmp_name'], $targetFilePath)) {
                // Save relative path to DB
                $image_path = 'assets/img/cars/' . $fileName;
            } else {
                http_response_code(500);
                echo json_encode(array("message" => "Image upload failed."));
                exit();
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Invalid image format. Only JPG, PNG, GIF, and WebP are allowed."));
            exit();
        }
    }
    
    $car->image_path = $image_path;

    if ($car->create()) {
        http_response_code(201);
        echo json_encode(array("message" => "Car added successfully."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to add car. It may already exist."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data. Required fields are missing."));
}
?>
