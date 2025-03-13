<?php
session_start();
include "../db.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시판</title>
</head>

<body>
    <div>
        <?php if (!isset($_SESSION["id"])) { ?>
            <a href="../member/login/login.php">로그인</a>
            <a hreg="../member/join/join.php">회원가입</a>
        <?php } else { ?>
            <a href="../member/login/logout.php">로그아웃</a>
        <?php } ?>
    </div>

    <div>
        <h1>게시판</h1>
    </div>
</body>

</html>