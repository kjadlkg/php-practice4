<?php
session_start();
include "../db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo "<script>alert('토큰 값이 유효하지 않습니다.'); location.href='../board/view.php?id={$board_id}';</script>";
        exit;
    }

    $board_id = $_POST['board_id'] ?? '';
    $name = $_POST['name'] ?? '';
    $pw = $_POST['pw'] ?? '';
    $content = $_POST['content'] ?? '';

    $stmt = $db->prepare("INSERT INTO comment (board_id, comment_writer, comment_content) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $board_id, $name, $content);

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