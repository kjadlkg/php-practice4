<?php
session_start();
include "../db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['id'])) {
        die("로그인이 필요합니다.");
    }

    $loginUser = $_SESSION['id'];

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF 토근이 유효하지 않습니다.");
    }

    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        die("잘못된 접근입니다.");
    }

    $id = (int) $_POST['id'];

    $stmt = $db->prepare("SELECT board_writer FROM board WHERE board_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $stmt->close();

    if (!$row) {
        die("게시글이 존재하지 않습니다.");
    }

    if ($row['board_writer'] !== $loginUser) {
        die("이 게시글을 삭제할 권한이 없습니다.");
    }

    $stmt = $db->prepare("DELETE FROM board WHERE board_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('삭제되었습니다.');</script>";
        header("Location: ../main/index.php");
        exit;
    } else {
        echo "<script>alert('오류가 발생했습니다.'); history.back();</script>";
        $stmt->close();
    }
}
?>