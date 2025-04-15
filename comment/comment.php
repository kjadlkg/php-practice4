<?php
session_start();
include "../db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $board_id = $_POST['board_id'] ?? '';
    $name = $_POST['name'] ?? '';
    $pw = $_POST['pw'] ?? '';
    $captcha_input = $_POST['captcha'] ?? '';
    $correct_code = $_SESSION['captcha_keystring'] ?? '';
    $content = $_POST['content'] ?? '';

    if (!isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo "<script>alert('토큰 값이 유효하지 않습니다.'); location.href='../board/view.php?id={$board_id}';</script>";
        exit;
    }

    if (strtolower($captcha_input) !== strtolower($correct_code)) {
        echo "<script>alert('올바른 보안코드를 입력하세요.'); history.back();</script>";
        exit;
    }

    if (!empty($pw)) {
        $comment_pw = password_hash($pw, PASSWORD_DEFAULT);
        $ip = $_SERVER['REMOTE_ADDR'];
    } else {
        $comment_pw = "";
        $ip = "";
    }

    $stmt = $db->prepare("INSERT INTO comment (board_id, comment_writer, comment_pw, comment_content, ip) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $board_id, $name, $comment_pw, $content, $ip);

    if ($stmt->execute()) {
        header("Location: ../board/view.php?id=$board_id");
    } else {
        echo "<script>alert('댓글 작성에 실패했습니다.'); location.href='../board/view.php?id={$board_id}';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('잘못된 접근입니다.'); location.href='../main/index.php';</script>";
}
?>