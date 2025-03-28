<?php
session_start();
include "../db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['id'])) {
        die("로그인이 필요합니다.");
    }

    $board_id = $_POST['board_id'] ?? '';
    $name = $_POST['name'] ?? '';
    $content = $_POST['content'] ?? '';

    if (empty($content)) {
        die("댓글을 입력하세요.");
    }

    $stmt = $db->prepare("INSERT INTO comment (board_id, comment_writer, comment_content) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $board_id, $name, $content);

    if ($stmt->execute()) {
        header("Location: ../board/view.php?id=$board_id");
        exit;
    } else {
        echo "댓글 작성에 실패했습니다.";
    }

    $stmt->close();
}
?>