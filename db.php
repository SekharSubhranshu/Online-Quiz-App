<?php
$host = "localhost";
$user = "root";
$pass = "";  // Default password is empty
$db = "quiz_app";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>