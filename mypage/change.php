<?php
session_start();
include "../resource/db.php";

if (!isset($_SESSION["id"])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='../member/login/login.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_SESSION["id"];
    $name = isset($_POST["name"]) ? trim($_POST["name"]) : '';
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : '';
    $pw = isset($_POST["pw"]) ? $_POST["pw"] : '';

    if (empty($name) || empty($email) || empty($pw)) {
        echo "<script>alert('빈칸이 존재합니다.'); history.back();</script>";
        exit;
    }

    $stmt = $db->prepare("SELECT user_pw FROM user WHERE user_id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->bind_result($user_pw);
    $stmt->fetch();
    $stmt->close();

    if (!$user_pw || !password_verify($pw, $user_pw)) {
        echo "<script>alert('비밀번호가 일치하지 않습니다.'); history.back();</script>";
        exit;
    }

    $update_stmt = $db->prepare("UPDATE user SET user_name = ?, user_email = ?  WHERE user_id = ?");
    $update_stmt->bind_param("sss", $name, $email, $id);

    if ($update_stmt->execute()) {
        $_SESSION["name"] = $name;
        echo "<script>alert('정보가 수정되었습니다.');</script>";
        header("Location: index.php");
    } else {
        echo "<script>alert('정보 수정에 실패했습니다.');</script>";
    }

    $update_stmt->close();
}

?>