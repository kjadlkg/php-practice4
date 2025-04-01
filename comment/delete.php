<?php
session_start();
include "../db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['id'])) {
        echo "<script>alert('로그인이 필요합니다.');</script>";
        exit;
    }

    $comment_id = $_POST['comment_id'] ?? '';
    $board_id = $_POST['board_id'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (empty($comment_id) || empty($board_id) || !is_numeric($comment_id) || !is_numeric($board_id)) {
        die("잘못된 요청입니다.");
    }

    if (!isset($_SESSION['csrf_token']) || $csrf_token !== $_SESSION['csrf_token']) {
        die("CSRF 토큰이 유효하지 않습니다.");
    }

    $stmt = $db->prepare("DELETE FROM comment WHERE comment_id = ?");
    $stmt->bind_param("i", $comment_id);

    if ($stmt->execute()) {
        header("Location: ../board/view.php?id=$board_id");
        exit;
    } else {
        echo "댓글 삭제에 실패했습니다.";
    }

    $stmt->close();
} else {
    die("잘못된 접근입니다.");
}
?>