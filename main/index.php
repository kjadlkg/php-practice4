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
            <div>
                <a href="../member/login/login.php">로그인</a>
                <a href="../member/join/join.php">회원가입</a>
            </div>
        <?php } else { ?>
            <div>
                <p><a href="../mypage/index.php"><?php echo $_SESSION['name']; ?></a> 님</p>
                <a href="../mypage/index.php">마이페이지</a>
                <a href="../member/login/logout.php">로그아웃</a>
            </div>
        <?php } ?>
    </div>

    <div>
        <h1>게시판</h1>
    </div>
</body>

</html>