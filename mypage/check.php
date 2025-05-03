<?php
session_start();
include "../db.php";
include "../function.php";

if (!isset($_SESSION["id"])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='../member/login/login.php';</script>";
    exit;
}

$id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pw = $_POST['pw'] ?? '';

    if (empty($pw)) {
        echo "<Script>alert('비밀번호를 입력해주세요.'); location.href='check.php';</script>";
        exit;
    }

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo "<script>alert('잘못된 접근입니다.'); location.href='../main/index.php';</script>";
        exit;
    }

    $stmt = $db->prepare("SELECT user_pw FROM user WHERE user_id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->bind_result($hashed_pw);
    $stmt->close();

    if (!password_verify($pw, $hashed_pw)) {
        echo "<script>alert('비밀번호를 확인해주세요.');</script>";
    } else {
        header("Location: info.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>비밀번호 확인</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div>
        <p>비밀번호를 입력해주세요</p>
        <form method="POST">
            <input type="hidden" id="csrf_token" name="csrf_token" value="<?= get_csrf_token() ?>">
            <input type="password" id="pw" name="pw" placeholder="비밀번호">
            <button type="submit">확인</button>
        </form>
    </div>
    <script>
        $(function () {
            $('form').on('submit', function (e) {
                const pw = $('#pw').val().trim();

                if (pw === '') {
                    alert("비밀번호를 입력해주세요.");
                    e.preventDefault();
                    $('#pw').focus();
                    return;
                }
            });
        })
    </script>
</body>

</html>