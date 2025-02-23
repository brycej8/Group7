<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET");

require 'connSetup.php';

// Get search term from query string (e.g., ?name=John)
$searchTerm = isset($_GET['name']) ? $_GET['name'] : '';
if (empty($searchTerm)) {
    returnWithError("No search term provided");
    exit();
}

// Prepare the SQL query to search for the contact by name
$stmt = $conn->prepare("SELECT Name, Phone, Email, UserID FROM Contacts WHERE Name LIKE ?");
$searchTerm = "%" . $searchTerm . "%"; // Add wildcards for partial matching
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

// Check if any results were found
if ($result->num_rows > 0) {
    $contacts = [];
    while ($row = $result->fetch_assoc()) {
        $contacts[] = $row;
    }
    returnWithSuccess($contacts);
} else {
    returnWithError("No contacts found");
}

$stmt->close();
$conn->close();

function returnWithError($err)
{
    $retValue = ["error" => $err];
    sendResultInfoAsJson($retValue);
}

function returnWithSuccess($contacts)
{
    $retValue = ["contacts" => $contacts];
    sendResultInfoAsJson($retValue);
}

function sendResultInfoAsJson($obj)
{
    header('Content-type: application/json');
    echo json_encode($obj);
}
?>
