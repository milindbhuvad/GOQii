<?php
$host = "localhost";
$user = "root";
$pass = "";
$db_name = "user_api";

$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>