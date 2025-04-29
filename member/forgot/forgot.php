<?php
session_start();
include "../../db.php";

$id = $_GET['id'] ?? null;
$pwPattern = "/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/";


if (empty($id)) {
    echo "<script>alert('아이디, 이메일 인증이 필요합니다.'); location.href='index.php';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_pw = $_POST['pw'] ?? '';
    $new_pw_check = $_POST['pw_check'] ?? '';

    if (empty($new_pw) || empty($new_pw_check)) {
        echo "<script>alert('빈칸이 존재합니다.'); history.back();</script>";
        exit;
    }

    if (!preg_match($pwPattern, $new_pw)) {
        echo "<script>alert('영문, 숫자, 특수문자를 조합하여 8~20자로 설정해주세요.'); history.back();</script>";
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
    if (!$stmt->execute()) {
        echo "<script>alert('쿼리 실행 중 오류가 발생했습니다.'); history.back();</script>";
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

        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");
        header("Expires: 0");
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
    <title>비밀번호 재설정</title>
    <link rel="stylesheet" href="../../css/base.css">
    <link rel="stylesheet" href="../../css/common.css">
    <link rel="stylesheet" href="../../css/component.css">
    <link rel="stylesheet" href="../../css/contents.css">
    <link rel="stylesheet" href="../../css/page/login.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                                <h4 class="title">비밀번호 규칙은 다음과 같습니다.</h4>
                                <div class="info_text">
                                    <p>영문, 숫자, 특수문자를 조합하여 8~20자 이내로 설정해주세요</p>
                                </div>
                                <div class="bg_box">
                                    <div class="form_box">
                                        <form method="post">
                                            <input type="password" class="int" id="pw" name="pw" placeholder="새 비밀번호">
                                            <input type="password" class="int" id="pw_check" name="pw_check"
                                                placeholder="새 비밀번호 확인">
                                            <div class="tip_msgbox">
                                                <p class="tip_msg" id="pwc_info" style="display: block">비밀번호를 다시 입력해주세요
                                                </p>
                                                <p class="tip_msg error" id="pwc_unable" style="display: none;">비밀번호가
                                                    일치하지 않습니다</p>
                                            </div>
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
        <script>
            $(function () {
                $('form').on('submit', function (e) {
                    const pw = $('#pw').val().trim();
                    const pwc = $('#pw_check').val().trim();
                    const pwPattern = /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/;

                    if (pw === '') {
                        e.preventDefault();
                        $('#pw').focus();
                        return;
                    }

                    if (pwc === '') {
                        e.preventDefault();
                        $('#pw_check').focus();
                        return;
                    }

                    if (!pwPattern.test(pw)) {
                        e.preventDefault();
                        $('#pw').focus();
                        return;
                    }

                    if (pw !== pwc) {
                        e.preventDefault();
                        $('#pw_check').focus();
                        $('#pwc_info').hide();
                        $('#pwc_unable').show();
                        return;
                    }
                });

                $('#pw_check').on('keyup', function () {
                    const pw = $('#pw').val();
                    const pwc = $(this).val();

                    if (pw !== '' && pw === pwc) {
                        $('#pwc_info').hide();
                        $('#pwc_unable').hide();
                    } else if (pw === '' && pwc === '') {
                        $('#pwc_info').show();
                        $('#pwc_unable').hide();
                    } else {
                        $('#pwc_info').hide();
                        $('#pwc_unable').show();
                    }
                })
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