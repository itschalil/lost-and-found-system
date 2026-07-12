<?php
// config/db_connect.php

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'lost_and_found';
$port = 3306;  // Palitan ng 3307 kung pinalitan mo ang MySQL port

$conn = new mysqli($host, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>