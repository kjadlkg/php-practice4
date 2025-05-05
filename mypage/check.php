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
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if (!password_verify($pw, $row['user_pw'])) {
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
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/component.css">
    <link rel="stylesheet" href="../css/contents.css">
    <link rel="stylesheet" href="../css/page/login.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div id="top" class="width868 login_wrap">
        <header class="header bg">
            <div class="head">
                <h1 class="logo">
                    <a href="../main/index.php">메인페이지</a>
                </h1>
            </div>
        </header>
        <main id="container">
            <div class="content info_change">
                <article>
                    <h2 class="blind">개인 정보 변경</h2>
                </article>
                <section>
                    <div class="content_head">
                        <h3 class="head_title">비밀번호 확인</h3>
                    </div>
                    <form method="POST">
                        <input type="hidden" id="csrf_token" name="csrf_token" value="<?= get_csrf_token() ?>">
                        <div id="contentbox" class="content_box border">
                            <div class="con center">
                                <strong class="title">비밀번호를 입력해주세요</strong>
                                <div class="form_box">
                                    <input type="password" class="int" id="pw" name="pw" placeholder="비밀번호">
                                    <button type="submit" class="btn btn_blue small btn_wfull">확인</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </section>
            </div>
        </main>
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