<?php
include "../../db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = isset($_POST['name']) ? $_POST['name'] : null;
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $pw = isset($_POST['pw']) ? $_POST['pw'] : null;
    $pwCheck = isset($_POST['pw_check']) ? $_POST['pw_check'] : null;
    $email = isset($_POST['email']) ? $_POST['email'] : null;

    if (empty($name) || empty($id) || empty($pw) || empty($pwCheck) || empty($email)) {
        echo "<script>alert('빈칸이 존재합니다.');</script>";
        exit;
    }

    $bcrypt_pw = password_hash($pw, PASSWORD_DEFAULT);

    $stmt = $db->prepare("INSERT INTO user(user_name, user_id, user_pw, user_email) VALUES(?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $id, $bcrypt_pw, $email);
    $result = $stmt->execute();

    if ($result) {
        echo "<script>alert('회원가입이 완료되었습니다.');location.href='../../main/index.php';</script>";
    } else {
        echo "<script>alert('회원가입에 실패했습니다. 오류: " . $stmt->error . "');</script>";
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
            <input type="text" name="name" placeholder="닉네임" required />
            <div>
                <input type="text" id="id" name="id" placeholder="아이디" required />
                <input type="button" id="id_check" value="중복 확인" />
                <span id="id_result"></span>
            </div>
            <input type="password" name="pw" placeholder="비밀번호" required />
            <input type="password" name="pw_check" placeholder="비밀번호 확인" required />
            <input type="email" name="email" placeholder="이메일" required />
            <input type="submit" name="join" value="회원가입" />
        </form>
    </div>

    <script>
    $(document).ready(function() {
        $("#id_check").click(function() {
            var user_id = $("#id").val().trim();

            if (user_id === "") {
                $("#id_result").text("아이디를 입력해주세요.");
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
                        $("#id_result").text("중복된 아이디입니다.").css("color", "red");
                    } else if (response === "available") {
                        $("#id_result").text("사용 가능한 아이디입니다.").css("color", "blue");
                    } else {
                        $("#id_result").text("아이디를 입력해주세요.");
                    }
                },
                error: function() {
                    $("#id_result").text("서버 오류가 발생했습니다").css("color", "red");
                }
            });
        });
    });
    </script>
</body>

</html>