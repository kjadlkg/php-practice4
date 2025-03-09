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

function query($sql, $params = [], $types = "")
{
    global $db;

    if ($stmt = $db->prepare($sql)) {
        if (!empty(($params))) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    } else {
        die("SQL Error: " . $db->error);
    }
}
?>