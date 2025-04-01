<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "test3";

$db = new mysqli($servername, $username, $password, $database);

if ($db->connect_error) {
    die("Database connection failed: " . $db->connect_error);
}

$db->set_charset("utf8mb4");
?>