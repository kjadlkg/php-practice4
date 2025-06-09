<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../db.php";

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('오류가 발생했습니다.');
    }

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        throw new Exception('오류가 발생했습니다.');
    }

    $board_id = filter_input(INPUT_POST, 'board_id', FILTER_VALIDATE_INT)
        ?: throw new Exception('유효하지 않은 게시글입니다.');
    $comment_id = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT)
        ?: throw new Exception('유효하지 않은 접근입니다.');

    if ($comment_id <= 0 || $board_id <= 0) {
        throw new Exception('유효하지 않은 접근입니다.');
    }

    $user_id = $_SESSION['id'] ?? '';
    $is_login = !empty($user_id);

    if ($is_login) {
        $comment_pw = '';
    } else {
        $comment_pw = filter_input(INPUT_POST, 'comment_pw', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
            ?: throw new Exception('비밀번호를 입력해주세요.');
    }

    // 댓글 비밀번호 조회
    $stmt = $db->prepare("SELECT comment_pw FROM comment WHERE comment_id = ?");
    $stmt->bind_param("i", $comment_id);
    if (!$stmt->execute()) {
        throw new Exception('오류가 발생했습니다.');
    }
    $result = $stmt->get_result();

    // 비밀번호 일치 확인
    if (!isset($_SESSION['id'])) {
        if ($row = $result->fetch_assoc()) {
            $hashed_pw = $row['comment_pw'];
            if (!password_verify($comment_pw, $hashed_pw)) {
                throw new Exception('비밀번호가 일치하지 않습니다.');
            }
        } else {
            throw new Exception('댓글이 존재하지 않습니다.');
        }
    }
    $stmt->close();

    // 댓글 삭제
    $stmt = $db->prepare("DELETE FROM comment WHERE comment_id = ?");
    $stmt->bind_param("i", $comment_id);
    if (!$stmt->execute()) {
        throw new Exception('오류가 발생했습니다.');
    }

    if (mysqli_stmt_affected_rows($stmt) === 0) {
        throw new Exception('댓글 삭제에 실패했습니다.');
    }

    header("Location: ../board/view.php?id=" . urlencode($board_id));
    exit;

} catch (Exception $e) {
    $error_message = $e->getMessage();
    echo "<script>alert('$error_message'); history.back();</script>";
    exit;
} finally {
    $stmt->close();
}
?>