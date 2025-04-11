<?php
session_start();
include "../db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $comment_id = $_POST['comment_id'] ?? '';
    $board_id = $_POST['board_id'] ?? '';
    $pw = $_POST['pw'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo "<script>alert('토큰 값이 유효하지 않습니다.'); history.back();</script>";
        exit;
    }

    if (empty($comment_id) || empty($board_id) || !is_numeric($comment_id) || !is_numeric($board_id)) {
        echo "<script>alert('잘못된 요청입니다.'); history.back();</script>";
        exit;
    }

    $stmt = $db->prepare("SELECT comment_pw FROM comment WHERE comment_id = ?");
    $stmt->bind_param("i", $comment_id);
    if ($stmt->execute()) {
        $stmt->bind_result($comment_pw);
        $stmt->fetch();
        $stmt->close();
    } else {
        echo "<script>alert('오류가 발생했습니다.'); location.href='../board/view.php?id={$board_id}';</script>";
        exit;
    }

    if (!password_verify($pw, $comment_pw)) {
        echo "<script>alert('비밀번호가 일치하지 않습니다.'); history.back();</script>";
        exit;
    }

    $stmt = $db->prepare("DELETE FROM comment WHERE comment_id = ?");
    $stmt->bind_param("i", $comment_id);

    if ($stmt->execute()) {
        header("Location: ../board/view.php?id={$board_id}");
    } else {
        echo "<script>alert('삭제 실패했습니다.'); history.back();';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('잘못된 접근입니다.'); location.href='../main/index.php';</script>";
}
?>