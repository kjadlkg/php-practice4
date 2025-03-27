<?php
session_start();
include "../../db.php";

if (!isset($_SESSION['id'])) {
    echo "<script>alert('아이디, 이메일 인증이 필요합니다.'); location.href='index.php';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_pw = ($_POST['pw']) ?? '';
    $new_pw_check = $_POST['pw_check'] ?? '';
    $id = $_SESSION["id"];

    if (empty($new_pw) || empty($new_pw_check)) {
        echo "<script>alert('빈칸이 존재합니다.'); history.back();</script>";
        exit;
    }

    if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d).{8,}$/', $new_pw)) {
        echo "<script>alert('비밀번호는 최소 8자 이상, 영문과 숫자를 포함해야 합니다.'); history.back();</script>";
        exit;
    }

    if ($new_pw !== $new_pw_check) {
        echo "<script>alert('비밀번호가 일치하지 않습니다.'); history.back();</script>";
        exit;
    }

    $stmt = $db->prepare("SELECT user_pw FROM user WHERE user_id = ?");
    if (!$stmt) {
        echo "<script>alert('데이터베이스 오류가 발생했습니다.'); history.back();</script>";
        exit;
    }

    $stmt->bind_param("s", $id);
    if ($stmt->execute()) {
        echo "<script>alert('쿼리 실행 중  오류가 발생했습니다.'); history.back();</script>";
        exit;
    }

    $stmt->bind_result($hashed_old_pw);
    $stmt->fetch();
    $stmt->close();

    if (!$hashed_old_pw) {
        echo "<script>alert('사용자 정보가 존재하지 않습니다.'); history.back();</script>";
        exit;
    }

    if (password_verify($new_pw, $hashed_old_pw)) {
        echo "<script>alert('이전과 동일한 비밀번호로 변경할 수 없습니다.'); history.back();</script>";
        exit;
    }

    $hashed_pw = password_hash($new_pw, PASSWORD_DEFAULT);

    $stmt = $db->prepare("UPDATE user SET user_pw = ? WHERE user_id =  ?");
    $stmt->bind_param("ss", $hashed_pw, $id);

    if ($stmt->execute()) {
        session_unset();
        session_destroy();
        echo "<script>alert('비밀번호가 변경되었습니다. 다시 로그인해주세요.'); location.href='../login/login.php';</script>";
    } else {
        echo "<script>alert('오류가 발생했습니다. 다시 시도해주세요.'); history.back();</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>비밀번호 변경</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div>
        <h1>비밀번호 변경</h1>
        <form method="post">
            <span>최소 8자, 영문+숫자 조합</span>
            <input type="password" id="pw" name="pw" placeholder="새 비밀번호" required />
            <input type="password" id="pw_check" name="pw_check" placeholder="새 비밀번호 확인" required />
            <span id="pw_result"></span>
            <input type="submit" value="변경" />
        </form>
    </div>

    <script>
        $(document).ready(function () {
            $("#pw, #pw_check").on("keyup", function () {
                var new_pw = $("#pw").val();
                var new_pw_check = $("#pw_check").val();
                var pwPattern = /^(?=.*[a-zA-Z])(?=.*\d).{8,}$/;

                if (!pwPattern.test(new_pw)) {
                    $("#pw_result").text("최소 8자 이상, 영문과 숫자의 조합으로 작성해주세요").css("color", "black");
                } else if (new_pw !== new_pw_check) {
                    $("#pw_result").text("비밀번호가 불일치합니다").css("color", "red");
                } else {
                    $("#pw_result").text("비밀번호가 일치합니다").css("color", "blue");
                }
            });
        });
    </script>
</body>

</html>