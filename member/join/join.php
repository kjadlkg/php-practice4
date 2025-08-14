<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../../resource/db.php";

if (isset($_SESSION['id'])) {
    echo "<script>alert('이미 로그인 하셨습니다.');</script>";
    header("Location: ../../main/index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $pw = isset($_POST['pw']) ? $_POST['pw'] : '';
    $pwCheck = isset($_POST['pw_check']) ? $_POST['pw_check'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $captcha_input = $_POST['captcha'] ?? '';
    $correct_code = $_SESSION['captcha_keystring'] ?? '';

    if (empty($name) || empty($id) || empty($pw) || empty($pwCheck) || empty($email) || empty($captcha_input)) {
        echo "<script>alert('빈칸이 존재합니다.'); history.back();</script>";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('유효한 이메일을 입력해주세요.'); history.back();</script>";
        exit;
    }

    if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/', $pw)) {
        echo "<script>alert('영문, 숫자, 특수문자를 조합하여 8~20자 이내로 설정해주세요.'); history.back();</script>";
        exit;
    }

    if ($pw !== $pwCheck) {
        echo "<script>alert('비밀번호가 일치하지 않습니다.'); history.back();</script>";
        exit;
    }

    if (strtolower($captcha_input) !== strtolower($correct_code)) {
        echo "<script>alert('자동 입력 방지 코드가 일치하지 않습니다.'); history.back();</script>";
        exit;
    }

    unset($_SESSION['captcha_keystring']);

    // 중복 ID 체크
    $check_stmt = $db->prepare("SELECT COUNT(*) FROM user WHERE user_id = ?");
    $check_stmt->bind_param("s", $id);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        echo "<script>alert('이미 존재하는 아이디입니다.'); history.back();</script>";
        exit;
    }

    // 회원가입
    $bcrypt_pw = password_hash($pw, PASSWORD_DEFAULT);

    $stmt = $db->prepare("INSERT INTO user(user_name, user_id, user_pw, user_email) VALUES(?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $id, $bcrypt_pw, $email);
    $result = $stmt->execute();

    if ($result) {
        echo "<script>alert('회원가입이 완료되었습니다.'); location.href='../login/login.php';</script>";
    } else {
        echo "<script>alert('회원가입에 실패했습니다.'); history.back();</script>";
    }
    $stmt->close();

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원가입</title>
    <link rel="icon" href="../../resource/images/favicon.ico">
    <link rel="stylesheet" href="../../resource/css/base.css">
    <link rel="stylesheet" href="../../resource/css/common.css">
    <link rel="stylesheet" href="../../resource/css/component.css">
    <link rel="stylesheet" href="../../resource/css/contents.css">
    <link rel="stylesheet" href="../../resource/css/page/join.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div id="top" class="width868 join_wrap">
        <header class="header bg">
            <div class="head">
                <h1 class="logo">
                    <a href="../../main/index.php">
                        <img src="https://nstatic.dcinside.com/dc/w/images/dcin_logo2.png">
                    </a>
                </h1>
            </div>
        </header>
        <main id="container">
            <div class="content">
                <article>
                    <h2 class="blind">회원가입</h2>
                    <section>
                        <form method="post" action="join.php">
                            <div class="content_head">
                                <h3 class="head_title">기본 정보 입력</h3>
                            </div>
                            <div class="content_box border">
                                <div class="con joinform_box">
                                    <fieldset>
                                        <legend class="blind">회원가입 신청 폼</legend>
                                        <div class="form_group nick">
                                            <div class="form_title">닉네임</div>
                                            <div class="form_text">
                                                <input type="text" class="int" id="name" name="name" placeholder="닉네임">
                                            </div>
                                        </div>
                                        <div class="form_group id">
                                            <div class="form_title">아이디</div>
                                            <div class="form_text">
                                                <div class="clear">
                                                    <input type="text" class="int" id="id" name="id" maxlength="20"
                                                        placeholder="아이디">
                                                    <button type="button" class="btn btn_blue small fr" id="id_check">중복
                                                        확인</button>
                                                    <div class="tip_msgbox">
                                                        <p class="tip_msg mt6" id="idc_info">아이디 중복 확인을 체크해주세요</p>
                                                        <p class="tip_msg font_blue mt6" id="idc_enable"
                                                            style="display: none;">사용
                                                            가능한 아이디입니다</p>
                                                        <p class="tip_msg font_red mt6" id="idc_unable"
                                                            style="display: none;">중복된 아이디
                                                            입니다</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form_group pw">
                                            <div class="form_title">비밀번호</div>
                                            <div class="form_text">
                                                <input type="password" class="int" id="pw" name="pw" maxlength="20"
                                                    placeholder="비밀번호">
                                                <input type="password" class="int pw_check" id="pw_check"
                                                    name="pw_check" placeholder="비밀번호 확인">
                                                <div class="pw_rule">
                                                    <div class="pw_tip">
                                                        <p class="tip_title">비밀번호 필수 조건</p>
                                                        <div class="checkbox chkStr">
                                                            <p class="chklbl">영문, 숫자, 특수문자의 조합입니다</p>
                                                        </div>
                                                        <div class="checkbox chkLen">
                                                            <p class="chklbl">8~20자입니다</p>
                                                        </div>
                                                    </div>
                                                    <div class="pw_step">
                                                        <div class="step_box">
                                                            <div class="top">
                                                                <span class="step_title">안전 정도</span>
                                                            </div>
                                                            <div class="btm_box">
                                                                <span class="bar"></span>
                                                            </div>
                                                        </div>
                                                        <p class="impossible_text tip1" style="display: none;">
                                                            비밀번호가 일치하지 않습니다</p>
                                                        <p class="impossible_text tip2" style="display: none;">
                                                            닉네임, 아이디와 동일한 비밀번호는 사용할 수 없습니다</p>
                                                        <p class="impossible_text tip3" style="display: none;">
                                                            영문, 숫자의 동일 및 연속 3자리 또는 특수문자의 동일 3자리 사용이 불가능합니다
                                                            <br>
                                                            예) 123, 111, abc, aaa, !!!
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form_group email">
                                            <div class="form_title">이메일</div>
                                            <div class="form_text">
                                                <input type="email" class="int" id="email" name="email"
                                                    placeholder="이메일">
                                            </div>
                                        </div>
                                        <div class="form_group kcaptcha">
                                            <div class="form_title">자동 입력 방지 코드</div>
                                            <div class="form_text">
                                                <div class="kcaptcha_box">
                                                    <div class="kcaptcha_img">
                                                        <img src="../../resource/captcha_image.php?<?= time() ?>"
                                                            class="kcaptcha" alt="KCAPTCHA">
                                                    </div>
                                                    <input type="text" class="input_kcaptcha" id="captcha"
                                                        name="captcha" placeholder="코드 입력">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="btn_box clear">
                                <div class="fr">
                                    <button type="submit" class="btn btn_grey small" id="join_btn"
                                        name="join">회원가입</button>
                                </div>
                            </div>
                        </form>
                    </section>
                </article>
            </div>
        </main>
        <script>
            // 캡차 클릭 시 이미지 변경
            Array.from(document.getElementsByClassName('kcaptcha')).forEach(function (img) {
                img.addEventListener('click', function () {
                    this.src = '../../resource/captcha_image.php?' + Date.now();
                    document.getElementById('captcha_input').value = '';
                });
            });
            $(function () {
                var isIdChecked = false;

                $("#id_check").click(function () {
                    var user_id = $("#id").val().trim();

                    if (user_id === "") {
                        isIdChecked = false;
                        $('#id').focus();
                        $('#idc_info').show();
                        $('#idc_enable').hide();
                        $('#idc_unable').hide();
                        return;
                    }

                    $.ajax({
                        type: "POST",
                        url: "check.php",
                        data: {
                            id: user_id
                        },
                        success: function (response) {
                            if (response === "exists") {
                                isIdChecked = false;
                                $('#idc_info').hide();
                                $('#idc_enable').hide();
                                $('#idc_unable').show();

                            } else if (response === 'available') {
                                isIdChecked = true;
                                $('#idc_info').hide();
                                $('#idc_enable').show();
                                $('#idc_unable').hide();
                            } else {
                                isIdChecked = false;
                                $('#idc_info').show();
                                $('#idc_enable').hide();
                                $('#idc_unable').hide();
                            }
                        },
                        error: function () {
                            isIdChecked = false;
                            alert("서버에 오류가 발생했습니다.");
                            header("join.php");
                            exit();
                        }
                    });
                });

                $('#pw').on('input', function () {
                    const id = $('#id').val();
                    const nickname = $('#name').val();

                    const pw = $(this).val();
                    const hasUpper = /[A-Z]/;
                    const strPattern = /^(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*])/;
                    const lenPattern = /^.{8,20}$/;
                    const repeatPattern = /(.)\1\1/;
                    const sequencePattern =
                        /(?:(abc|bcd|cde|def|efg|fgh|ghi|hij|ijk|jkl|klm|lmn|mno|nop|opq|pqr|qrs|rst|stu|tuv|uvw|vwx|wxy|xyz|012|123|234|345|456|567|678|789|890))/i;

                    const isIncludeId = id && pw.includes(id);
                    const isIncludeNick = nickname && pw.includes(nickname);

                    // 비밀번호 필수 조건 충족 여부
                    if (strPattern.test(pw)) {
                        $('.chkStr').addClass('on');
                    } else {
                        $('.chkStr').removeClass('on');
                    }

                    if (lenPattern.test(pw)) {
                        $('.chkLen').addClass('on');
                    } else {
                        $('.chkLen').removeClass('on');
                    }

                    // 닉네임, 아이디 포함 여부
                    if (isIncludeId || isIncludeNick) {
                        $('.tip2').show();
                    } else {
                        $('.tip2').hide();
                    }

                    // 연속, 반복 3자리 포함 여부
                    if (repeatPattern.test(pw) || sequencePattern.test(pw)) {
                        $('.tip3').show();
                    } else {
                        $('.tip3').hide();
                    }

                    // 비밀번호 안전 정도 충족 여부
                    $('.step_box').removeClass('impossible normal safe');

                    if (!strPattern.test(pw) || !lenPattern.test(pw) || isIncludeId || isIncludeNick ||
                        repeatPattern.test(pw) || sequencePattern.test(pw)) {
                        $('.step_box').addClass('impossible');
                    } else if (hasUpper.test(pw)) {
                        $('.step_box').addClass('safe');
                    } else {
                        $('.step_box').addClass('normal');
                    }
                });

                $('#pw_check').on('keyup', function () {
                    const pw = $('#pw').val();
                    const pwCheck = $(this).val();

                    if (pwCheck !== '' && pw !== pwCheck) {
                        $('.tip1').show();
                    } else {
                        $('.tip1').hide();
                    }
                });


                $('form').on('submit', function (e) {
                    const pw = $('#pw').val().trim();
                    const pwCheck = $('#pw_check').val().trim();
                    const nickname = $('#name').val().trim();
                    const email = $('#email').val().trim();
                    const captcha = $('#captcha').val().trim();

                    // 아이디 중복 확인 여부
                    if (!isIdChecked) {
                        e.preventDefault();
                        $('#id').focus();
                        return;
                    }

                    // 빈칸 존재 여부
                    if (nickname === '') {
                        e.preventDefault();
                        $('#name').focus();
                        return;
                    }

                    if (email === '') {
                        e.preventDefault();
                        $('#email').focus();
                        return;
                    }

                    if (captcha === '') {
                        e.preventDefault();
                        $('#captcha').focus();
                        return;
                    }

                    if (pw === '') {
                        e.preventDefault();
                        $('#pw').focus();
                        return;
                    }

                    if (pwCheck === '') {
                        e.preventDefault();
                        $('#pw_check').focus();
                        return;
                    }

                    if (pw !== pwCheck) {
                        e.preventDefault();
                        $('#pw_check').focus();
                        return;
                    }

                    // impossible 클래스 존재 여부
                    if ($('.step_box').hasClass('impossible')) {
                        e.preventDefault();
                        $('#pw').focus();
                        return;
                    }
                });
            });
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