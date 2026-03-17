<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/db.php';
include_once '../models/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->name) && !empty($data->username) && !empty($data->password) && !empty($data->role)) {
    $user->name = $data->name;
    $user->username = $data->username;
    $user->password = $data->password;
    
    // role must be 'customer' or 'agency'
    if ($data->role == 'customer' || $data->role == 'agency') {
        $user->role = $data->role;

        if ($user->register()) {
            http_response_code(201);
            echo json_encode(array("message" => "User was registered."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to register user or username already exists."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Invalid role."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to register user. Data is incomplete."));
}
?>
