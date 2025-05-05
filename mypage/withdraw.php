<?php
session_start();
include "../db.php";

if (!isset($_SESSION['id'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='../member/login/login.php';</script>";
    exit;
}

$id = $_SESSION['id'];
$name = $_SESSION['name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['term_agree'])) {
        echo "<script>alert('탈퇴 동의에 체크 해주시길 바랍니다.'); history.back();</script>";
        exit;
    }

    $is_deleted = 1;
    $deleted_at = date("Y-m-d H:i:s");

    $stmt = $db->prepare("UPDATE user SET is_deleted = ? , deleted_at = ? WHERE user_id = ?");
    $stmt->bind_param("iss", $is_deleted, $deleted_at, $id);
    $stmt->execute();
    $stmt->close();

    session_unset();
    session_destroy();

    echo "<script>alert('탈퇴가 완료되었습니다.'); location.href='../main/index.php';</script>";
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원 탈퇴</title>
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
            <div class="content withdraw">
                <article>
                    <h2 class="blind">회원 탈퇴</h2>
                    <section>
                        <div class="content_head">
                            <h3 class="head_title">회원 탈퇴</h3>
                        </div>
                        <div id="contentbox" class="content_box border">
                            <div class="con innerbox">
                                <form method="POST">
                                    <div class="info_text pit">
                                        <p>
                                            <em class="tip_deco_dot red big"></em>
                                            <b class="title font_red">탈퇴 시 계정 복구가 불가능합니다.</b>
                                        </p>
                                        <p>
                                            <em class="tip_deco_dot red big"></em>
                                            <b class="title font_red">탈퇴 시 해당 계정으로 작성된 게시물과 댓글은 자동으로 삭제되지 않습니다.</b>
                                        </p>
                                    </div>
                                    <div class="info_text">
                                        <p>
                                            <em class="tip_deco_dot"></em>
                                            탈퇴 처리된 아이디는 사용이 불가능합니다.
                                        </p>
                                    </div>
                                    <div class="bg_box">
                                        <div class="info">
                                            <div class="form_group id">
                                                <div class="form_title">아이디</div>
                                                <div class="form_text">
                                                    <b><?= htmlspecialchars($id) ?></b>
                                                    <a href="index.php" target="_blank"
                                                        class="btn btn_blue smallest btn_mypage">마이페이지 확인</a>
                                                </div>
                                            </div>
                                            <div class="form_group nick">
                                                <div class="form_title">닉네임</div>
                                                <div class="form_text">
                                                    <p><?= htmlspecialchars($name) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="btn_box clear">
                                        <div class="checkbox">
                                            <input type="checkbox" id="term_agree" name="term_agree">
                                            <label for="term_agree">위 내용을 확인했습니다</label>
                                        </div>
                                        <div class="fr">
                                            <button type="submit" class="btn btn_blue">탈퇴</button>
                                            <button type="button" class="btn btn_grey"
                                                onclick="location.href='../main/index.php'">취소</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </article>
            </div>
        </main>
        <script>
            $(function () {
                $('form').on('submit', function (e) {
                    if (!$('#term_agree').prop('checked')) {
                        alert("탈퇴 동의에 체크 해주시길 바랍니다.");
                        e.preventDefault();
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