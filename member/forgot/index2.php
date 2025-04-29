<?php
session_start();
include "../../db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = trim($_POST['id']);
    $email = trim($_POST['email']);

    if (empty($id) || empty($email)) {
        echo "<script>alert('빈칸이 존재합니다.'); history.back();</script>";
        exit;
    }

    $stmt = $db->prepare("SELECT user_id FROM user WHERE user_id = ? AND user_email = ?");
    $stmt->bind_param("ss", $id, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        header("Location: forgot.php?id={$id}");
        exit;
    } else {
        echo "<script>alert('일치하는 정보가 없습니다.');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>비밀번호 재설정</title>
    <link rel="stylesheet" href="../../css/base.css">
    <link rel="stylesheet" href="../../css/common.css">
    <link rel="stylesheet" href="../../css/component.css">
    <link rel="stylesheet" href="../../css/contents.css">
    <link rel="stylesheet" href="../../css/page/login.css">
</head>

<body>
    <div id="top" class="width868 login_wrap">
        <header class="header bg">
            <div class="head">
                <h1 class="logo">
                    <a href="../../main/index.php">메인페이지</a>
                </h1>
            </div>
        </header>
        <main id="container">
            <div class="content repw">
                <article>
                    <h2 class="blind">비밀번호 찾기</h2>
                    <section>
                        <div class="content_head">
                            <h3 class="head_title">비밀번호 재설정</h3>
                        </div>
                        <div id="contentbox" class="content_box border">
                            <div class="con innerbox">
                                <div class="bg_box">
                                    <div class="form_box">
                                        <form method="post">
                                            <input type="text" class="int" name="id" placeholder="아이디">
                                            <input type="email" class="int" name="email" placeholder="이메일">
                                            <button type="submit" class="btn btn_blue small btn_wfull">확인</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </article>
            </div>
        </main>
        <footer class="footer">
            <div class="info_policy">
                <a href="">회사소개</a>
                <a href="">제휴안내</a>
                <a href="">광고안내</a>
                <a href="">이용약관</a>
                <a href="">개인정보처리방침</a>
                <a href="">청소년보호정책</a>
            </div>
            <div class="copyright">Copyright ⓒ</div>
        </footer>
    </div>
</body>

</html>