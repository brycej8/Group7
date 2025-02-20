<?php

// Enable CORS headers to allow requests from any origin
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$inData = getRequestInfo();

$login = $inData["login"];
$password = $inData["password"];

$conn = new mysqli("44.223.16.37", "root", "!T!Wwzckon7m", "group7");

if ($conn->connect_error) {
    # echo("Connection Failed :[\n");
    returnWithError($conn->connect_error);
} else {
    # check that login isnt repeat (commented until it works)
    # checkForRepeat($login, $conn);


    # echo("Preparing sql INSERT\n");
    $stmt = $conn->prepare("INSERT INTO Users (Login, Password) VALUES (?, ?)");
    # echo("Binding Param\n");
    $stmt->bind_param("ss", $login, $password);

    $stmt->execute();

    $stmt->close();
    $conn->close();
    returnWithError("");
}

function getRequestInfo()
{
    return json_decode(file_get_contents('php://input'), true);
}

function sendResultInfoAsJson($obj)
{
    header('Content-type: application/json');
    echo ($obj);
}

function returnWithError($err)
{
    $retValue = '{"error":"' . $err . '"}';
    sendResultInfoAsJson($retValue);
}

function checkForRepeat($login, $conn)
{
    $stmt = $conn->prepare("SELECT * FROM Users WHERE login LIKE ?");
    $loginName = "%" . $login . "%";
    $stmt->bind_param("s", $loginName);
    $stmt->execute();

    $result = $stmt->get_result();
    echo $result;

    if($result == 0)
    {
        echo("login does not exist yet\n");
        return true;
    }
    else
    {
        echo("login does exist!\n");
        return false;
    }

    # if login exists
    return true;
}