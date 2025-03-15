<?php
session_start();
include "../../db.php";

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_pw = $_POST['pw'];
    $new_pw_check = $_POST['pw_check'];
    $id = $_SESSION["id"];

    if ($new_pw !== $new_pw_check) {
        echo "<script>alert('비밀번호가 일치하지 않습니다.');</script>";
    } else {
        $hashed_pw = password_hash($new_pw, PASSWORD_DEFAULT);

        $stmt = $db->prepare("UPDATE user SET user_pw = ? WHERE user_id =  ?");
        $stmt->bind_param("ss", $hashed_pw, $id);

        if ($stmt->execute()) {
            echo "<script>alert('비밀번호가 변경되었습니다.'); location.href='../login/login.php';</script>";
            unset($_SESSION['id']);
        } else {
            echo "<script>alert('오류가 발생했습니다. 다시 시도해주세요.');</script>";
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
            <input type="password" name="pw" placeholder="새 비밀번호" required />
            <input type="pasword" name="pw_check" placeholder="새 비밀번호 확인" required />
            <input type="submit" value="변경" />
        </form>
    </div>
</body>

</html>