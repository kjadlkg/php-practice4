<?php
session_start();
include "../db.php";
include "../function.php";

header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$board_id = $_POST['board_id'] ?? null;
$type = $_POST['type'] ?? '';
$is_login = isset($_SESSION['id']);

// file_put_contents('recommend_log.txt', "Login: $is_login, Board: $board_id, Type: $type\n", FILE_APPEND);

if (!$is_login) {
    $captcha_input = $_POST['captcha'] ?? '';
    $correct_code = $_SESSION['captcha_keystring'] ?? '';
    // file_put_contents('recommend_log.txt', "Captcha: $captcha_input, Expected: $correct_code\n", FILE_APPEND);

    if (empty($captcha_input) || strtolower($captcha_input) !== strtolower($correct_code)) {
        echo json_encode(['success' => false, 'message' => '올바른 보안코드를 입력하세요.']);
        exit;
    }
}

if (!is_numeric($board_id) || !in_array($type, ['up', 'down'])) {
    echo json_encode(['success' => false, 'message' => '잘못된 요청입니다.']);
    exit;
}

if (update_recommend($db, (int) $board_id, $type)) {
    if (!$is_login) {
        unset($_SESSION['captcha_keystring']); // 캡차 세션 초기화
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => '처리에 실패했습니다.']);
}
?>