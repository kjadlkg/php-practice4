<?php
include "../../db.php";

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

    if (empty($name) || empty($id) || empty($pw) || empty($pwCheck) || empty($email)) {
        echo "<script>alert('빈칸이 존재합니다.'); history.back();</script>";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('유효한 이메일을 입력해주세요.'); history.back();</script>";
        exit;
    }

    if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d).{8,}$/', $pw)) {
        echo "<script>alert('비밀번호는 최소 8자 이상, 영문과 숫자를 포함해야 합니다.');</script>";
        exit;
    }

    if ($pw !== $pwCheck) {
        echo "<script>alert('비밀번호가 일치하지 않습니다.');</script>";
        exit;
    }

    $bcrypt_pw = password_hash($pw, PASSWORD_DEFAULT);

    $stmt = $db->prepare("INSERT INTO user(user_name, user_id, user_pw, user_email) VALUES(?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $id, $bcrypt_pw, $email);
    $result = $stmt->execute();

    if ($result) {
        echo "<script>alert('회원가입이 완료되었습니다.'); location.href='../login/login.php';</script>";
    } else {
        echo "<script>alert('회원가입에 실패했습니다. 오류: " . $stmt->error . "'); history.back();</script>";
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div>
        <h1>회원가입</h1>
        <form method="post" action="join.php">
            <input type="text" name="name" placeholder="이름" required />
            <div>
                <input type="text" id="id" name="id" placeholder="아이디" required />
                <input type="button" id="id_check" value="중복 확인" />
                <span id="id_result"></span>
            </div>
            <div>
                <span>최소 8자, 영문+숫자 조합</span>
                <input type="password" id="pw" name="pw" placeholder="비밀번호" required />
                <input type="password" id="pw_check" name="pw_check" placeholder="비밀번호 확인" required />
                <span id="pw_result"></span>
            </div>
            <input type="email" name="email" placeholder="이메일" required />
            <input type="submit" id="join_btn" name="join" value="회원가입" disabled />
        </form>
    </div>

    <script>
    $(document).ready(function() {
        var isIdChecked = false;

        $("#pw, #pw_check").on("keyup", function() {
            var pw = $("#pw").val();
            var pwCheck = $("#pw_check").val();
            var pwPattern = /^(?=.*[a-zA-Z])(?=.*\d).{8,}$/;

            if (!pwPattern.test(pw)) {
                $("#pw_result").text("최소 8자 이상, 영문과 숫자의 조합으로 작성해주세요").css("color", "black");
            } else if (pw !== pwCheck) {
                $("#pw_result").text("비밀번호가 불일치합니다").css("color", "red");
            } else {
                $("#pw_result").text("비밀번호가 일치합니다").css("color", "blue");
            }

            if (pwPattern.test(pw) && isIdChecked) {
                $("#join_btn").prop("disabled", false);
            } else {
                $("#join_btn").prop("disabled", true);
            }
        });

        $("#id_check").click(function() {
            var user_id = $("#id").val().trim();
            var pw = $("#pw").val();
            var pwPattern = /^(?=.*[a-zA-Z])(?=.*\d).{8,}$/;

            if (user_id === "") {
                $("#id_result").text("아이디를 입력해주세요").css("color", "black");
                isIdChecked = false;
                $("#join_btn").prop("disabled", true);
                return;
            }

            $.ajax({
                type: "POST",
                url: "check.php",
                data: {
                    id: user_id
                },
                success: function(response) {
                    if (response === "exists") {
                        $("#id_result").text("중복된 아이디입니다").css("color", "red");
                        isIdChecked = false;
                        $('#join_btn').prop("disabled", true);
                    } else if (response === "available") {
                        $("#id_result").text("사용 가능한 아이디입니다").css("color", "blue");
                        isIdChecked = true;
                        $('#join_btn').prop("disabled", false);

                        if (pwPattern.test(pw)) {
                            $("#join_btn").prop("disabled", false);
                        }
                    } else {
                        $("#id_result").text("아이디를 입력해주세요").css("color", "black");
                        isIdChecked = false;
                        $('#join_btn').prop("disabled", true);
                    }
                },
                error: function() {
                    $("#id_result").text("서버 오류가 발생했습니다").css("color", "red");
                    isIdChecked = false;
                    $('#join_btn').prop("disabled", true);
                }
            });
        });

        $("#id").on("input", function() {
            isIdChecked = false;
            $("#id_result").text("중복 확인을 해주세요").css("color", "black");
            $("#join_btn").prop("disabled", true);
        });

        $("form").submit(function(e) {
            if (!isIdChecked) {
                alert("아이디 중복 확인을 해주세요");
                e.preventDefault();
            }
        });
    });
    </script>
</body>

</html>