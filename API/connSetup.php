<?php
# establishes a connection to our database "group7"

$servername = "44.223.16.37";
$username = "root";
$password = "!T!Wwzckon7m";
$database = "group7";

$inData = json_decode(file_get_contents("php://input"), true);

$conn = new mysqli($servername, $username, $password, $database);
$conn = new mysqli('localhost', 'root', '', 'COP');
if ($conn->connect_error) {
    sendError("Database connection failed.");
    exit();
}

?>