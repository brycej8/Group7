<?php
require 'connSetup.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

$inData = json_decode(file_get_contents("php://input"), true);

if (!isset($inData["userId"]) || !isset($inData["name"]) || !isset($inData["phone"]) || !isset($inData["email"])) {
    sendError("Missing required fields.");
    exit();
}

$userId = $inData["userId"];
$name = $inData["name"];
$phone = $inData["phone"];
$email = $inData["email"];

$stmt = $conn->prepare("INSERT INTO Contacts (Name, Phone, Email, UserID) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", $name, $phone, $email, $userId);

if ($stmt->execute()) {
    sendResponse(["message" => "Contact added successfully!"]);
} else {
    sendError("Failed to add contact.");
}

$stmt->close();
$conn->close();

function sendError($msg) {
    sendResponse(["error" => $msg]);
}

function sendResponse($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}
?>