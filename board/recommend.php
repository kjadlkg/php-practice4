<?php
session_start();
include "../db.php";
include "../function.php";

$board_id = $_POST['board_id'] ?? null;
$type = $_POST['type'] ?? '';
$is_login = isset($_SESSION['id']);

if (!$is_login) {
    $captcha = $_POST['captcha'] ?? '';
    if (empty($captcha) || empty($_SESSION['captcha_keystring']) || $captcha !== $_SESSION['captcha_keystring']) {
        echo json_encode(['success' => false, 'message' => '올바른 보안코드를 입력하세요.']);
        exit;
    }
}

if (!is_numeric($board_id) || !in_array($type, ['up', 'down'])) {
    echo json_encode(['success' => false, 'message' => '잘못된 요청입니다.']);
    exit;
}

if (update_recommend($db, (int) $board_id, $type)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => '처리에 실패했습니다.']);
}
?>