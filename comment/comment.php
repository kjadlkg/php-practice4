<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../resource/db.php";

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('오류가 발생했습니다.');
    }

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        throw new Exception('오류가 발생했습니다.');
    }

    $board_id = filter_input(INPUT_POST, 'board_id', FILTER_VALIDATE_INT)
        ?: throw new Exception('유효하지 않은 게시물입니다.');
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
        ?: throw new Exception('닉네임을 입력해주세요.');
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
        ?: throw new Exception('내용을 입력해주세요.');

    if ($board_id <= 0) {
        throw new Exception('유효하지 않은 게시물입니다.');
    }

    $parent_id = $_POST['parent_id'] ?? null;

    if ($parent_id !== null) {
        $parent_id = filter_var($parent_id, FILTER_VALIDATE_INT);
        if ($parent_id === false) {
            throw new Exception('유효하지 않은 게시물입니다.');
        }
    }

    $user_id = $_SESSION['id'] ?? '';
    $is_login = !empty($user_id);

    if ($is_login) {
        $pw = '';
        $captcha_input = '';
    } else {
        $pw = filter_input(INPUT_POST, 'pw', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
            ?: throw new Exception('비밀번호를 입력해주세요.');
        $captcha_input = filter_input(INPUT_POST, 'captcha', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
            ?: throw new Exception('자동입력 방지코드를 입력해주세요.');

        if (!$user_id && strtolower($captcha_input) !== strtolower($_SESSION['captcha_keystring'] ?? '')) {
            throw new Exception('자동입력 방지코드가 일치하지 않습니다.');
        }
    }

    $user_ip = $_SERVER['REMOTE_ADDR'];
    $comment_pw = $pw ? password_hash($pw, PASSWORD_DEFAULT) : '';
    $user_ip = $comment_pw ? $user_ip : '';

    // 댓글 저장
    $stmt = $db->prepare("INSERT INTO comment (board_id, parent_id, comment_writer, comment_writer_id, comment_pw, comment_content, ip) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssss", $board_id, $parent_id, $name, $user_id, $comment_pw, $content, $user_ip);
    if (!$stmt->execute()) {
        throw new Exception('오류가 발생했습니다.');
    }



    $_SESSION['scrollToComment'] = true;
    $redirect_url = "../board/view.php?id=$board_id&sort=" . urlencode($_GET['sort'] ?? 'created_asc') . "&page=" . urlencode($_GET['page'] ?? 1);
    header("Location: $redirect_url");
    exit;

} catch (Exception $e) {
    $error_message = $e->getMessage();
    echo "<script>alert('$error_message'); history.back();</script>";
    exit;
} finally {
    $stmt->close();
}
?>