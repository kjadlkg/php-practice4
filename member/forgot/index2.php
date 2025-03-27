<?php
session_start();
include "../../db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = trim($_POST['id']);
    $email = trim($_POST['email']);

    if (!empty($id) && !empty($email)) {
        $stmt = $db->prepare("SELECT user_id FROM user WHERE user_id = ? AND user_email = ?");
        $stmt->bind_param("ss", $id, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION['id'] = $id;
            header("Location: forgot.php");
            exit;
        } else {
            echo "<script>alert('일치하는 정보가 없습니다.');</script>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>비밀번호 변경</title>
</head>

<body>
    <div>
        <h1>비밀번호 변경</h1>
        <form method="post">
            <input type="text" name="id" placeholder="아이디" required />
            <input type="email" name="email" placeholder="이메일" required />
            <input type="submit" value="확인" />
        </form>
    </div>
</body>

</html>