<?php
session_start();
include "../resource/db.php";

if (!isset($_SESSION['id'], $_SESSION['name'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='../member/login/login.php';</script>";
    exit;
}

$id = $_SESSION['id'];
$name = $_SESSION['name'];

$stmt = $db->prepare("SELECT user_email FROM user WHERE user_id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "<script>alert('유저 정보를 찾을 수 없습니다.'); location.href='../main/index.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>내 정보</title>
    <link rel="icon" href="../resource/images/favicon.ico">
    <link rel="stylesheet" href="../resource/css/base.css">
    <link rel="stylesheet" href="../resource/css/common.css">
    <link rel="stylesheet" href="../resource/css/component.css">
    <link rel="stylesheet" href="../resource/css/contents.css">
    <link rel="stylesheet" href="../resource/css/page/login.css">
</head>

<body>
    <div id="top" class="width868 login_wrap">
        <header class="header bg">
            <div class="head">
                <h1 class="logo">
                    <a href="../main/index.php">
                        <img src="https://nstatic.dcinside.com/dc/w/images/dcin_logo2.png">
                    </a>
                </h1>
            </div>
        </header>
        <main id="container">
            <div class="content info_change">
                <article>
                    <h2 class="blind">개인정보 변경</h2>
                    <section class="pagemenu">
                        <h2 class="blind">페이지 메뉴</h2>
                        <div class="page_menu fr clear">
                            <a href="../member/forgot/index2.php">비밀번호 변경</a>
                            <a href="withdraw.php">회원 탈퇴</a>
                        </div>
                    </section>
                    <section>
                        <div class="content_head">
                            <h3 class="head_title">기본 정보 변경</h3>
                        </div>
                        <div class="content_box border nomin">
                            <div class="con changeform_box">
                                <form method="POST" action="change.php">
                                    <fieldset>
                                        <legend class="blind">기본 정보 변경 폼</legend>
                                        <div class="form_group nick">
                                            <div class="form_title">닉네임</div>
                                            <div class="form_text">
                                                <input type="text" class="int" id="name" name="name"
                                                    value="<?php echo htmlspecialchars($name) ?>">
                                            </div>
                                        </div>
                                        <div class="form_group id">
                                            <div class="form_title">아이디</div>
                                            <div class="form_text">
                                                <input id="id" class="int bg" name="id"
                                                    value="<?php echo htmlspecialchars($id) ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="form_group pw">
                                            <div class="form_title">비밀번호</div>
                                            <div class="form_text">
                                                <input type="password" class="int" id="pw" name="pw"
                                                    placeholder="비밀번호 확인">
                                            </div>
                                        </div>
                                        <div class="form_group email">
                                            <div class="form_title">이메일</div>
                                            <div class="form_text">
                                                <input type="email" class="int" id="email" name="email"
                                                    value="<?php echo htmlspecialchars($user["user_email"]) ?>">
                                            </div>
                                        </div>
                                        <div class="btn_box clear">
                                            <div class="fr">
                                                <button type="submit" class="btn btn_blue">확인</button>
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                    </section>
                </article>
            </div>
        </main>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(function () {
                $('form').on('submit', function (e) {
                    const name = $('#name').val().trim();
                    const pw = $('#pw').val().trim();
                    const email = $('#email').val().trim();

                    if (name === '') {
                        e.preventDefault();
                        $('#name').focus();
                        return;
                    }

                    if (pw === '') {
                        e.preventDefault();
                        $('#pw').focus();
                        return;
                    }

                    if (email === '') {
                        e.preventDefault();
                        $('#email').focus();
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