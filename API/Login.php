<?php
require 'connSetup.php';
session_start(); // Start a session

// Enable CORS headers to allow requests from any origin
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

$inData = getRequestInfo();

if (!isset($inData["login"]) || !isset($inData["password"])) {
    returnWithError("Missing login or password.");
    exit();
}

$login = $inData["login"];
$password = $inData["password"];

$stmt = $conn->prepare("SELECT ID, firstName, lastName FROM Users WHERE Login=? AND Password=?");
$stmt->bind_param("ss", $login, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $_SESSION['userID'] = $row['ID']; // Store user ID in session
    $_SESSION['firstName'] = $row['firstName'];
    $_SESSION['lastName'] = $row['lastName'];
    
    returnWithInfo($row['firstName'], $row['lastName'], $row['ID']);
} else {
    returnWithError("Incorrect username or password.");
}

$stmt->close();
$conn->close();

function getRequestInfo() {
    return json_decode(file_get_contents('php://input'), true);
}

function returnWithError($err) {
    sendResultInfoAsJson(["id" => 0, "firstName" => "", "lastName" => "", "error" => $err]);
}

function returnWithInfo($firstName, $lastName, $id) {
    sendResultInfoAsJson(["id" => $id, "firstName" => $firstName, "lastName" => $lastName, "error" => ""]);
}

function sendResultInfoAsJson($obj) {
    header('Content-type: application/json');
    echo json_encode($obj);
    exit();
}
?>
