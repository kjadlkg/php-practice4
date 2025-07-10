<?php
include "../../resource/db.php";

if (isset($_POST['id'])) {
    $id = trim($_POST['id']);

    if ($id == '') {
        echo "empty";
        exit;
    }

    $stmt = $db->prepare("SELECT COUNT(user_id) FROM user WHERE user_id = ?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $stmt->bind_result($user_count);
    $stmt->fetch();
    $stmt->close();

    echo ($user_count > 0) ? "exists" : "available";
}