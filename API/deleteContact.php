<?php
require 'connSetup.php';

// Enable CORS headers to allow requests from any origin
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

    // Get request parameters
$nameToDelete = $_GET['name']; // Get the name to delete from the query string

// Prepare the DELETE query
$stmt = $conn->prepare("DELETE FROM Contacts WHERE Name = ?");
$stmt->bind_param("s", $nameToDelete); // Bind the name parameter

if ($stmt->execute()) {
    // If successful, return success message
    returnWithSuccess("Contact deleted successfully");
} else {
    // If there's an error, return error message
    returnWithError("Error deleting contact: " . $stmt->error);
}

$stmt->close();
$conn->close();


// Helper functions for returning JSON responses
function returnWithError($err) {
    $retValue = ["error" => $err];
    sendResultInfoAsJson($retValue);
}

function returnWithSuccess($msg) {
    $retValue = ["message" => $msg];
    sendResultInfoAsJson($retValue);
}

// Function to send the result as JSON
function sendResultInfoAsJson($obj) {
    header('Content-type: application/json');
    echo json_encode($obj);
}
?>
