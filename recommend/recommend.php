<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
date_default_timezone_set('Asia/Seoul');
session_start();
include "../db.php";

$input = json_decode(file_get_contents('php://input'), true);
$board_id = isset($input['id']) ? (int) $input['id'] : 0;
$type = isset($input['type']) ? $input['type'] : '';
$captcha_input = isset($input['captcha']) ? $input['captcha'] : '';

if ($board_id <= 0 || !in_array($type, ['up', 'down'])) {
    echo json_encode(['success' => false, 'message' => '잘못된 요청입니다.']);
    exit;
}

$user_id = isset($_SESSION['id']) ? $_SESSION['id'] : '';
$user_ip = $_SERVER['REMOTE_ADDR'];

try {
    if (!$user_id && strtolower($captcha_input) !== strtolower($_SESSION['captcha_keystring'] ?? '')) {
        throw new Exception('자동입력 방지코드가 일치하지 않습니다.');
    }

    // 중복 추천 확인
    $sql = "SELECT COUNT(*) AS count FROM recommend
            WHERE board_id = ?
            AND type = ?
            AND (user_id = ? OR (user_id IS NULL AND ip = ?))
            AND DATE(created_at) = CURDATE()";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("isss", $board_id, $type, $user_id, $user_ip);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    $stmt->close();

    if ($count > 0) {
        throw new Exception('추천/비추천은 1일 1회만 가능합니다.');
    }

    // 추천 수 업데이트
    $column = $type === 'up' ? 'recommend_up' : 'recommend_down';
    $sql = "UPDATE board SET $column = $column + 1 WHERE board_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $board_id);
    $stmt->execute();
    $stmt->close();

    // 추천 기록 저장
    $sql = "INSERT INTO recommend (user_id, ip, board_id, type, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ssis", $user_id, $user_ip, $board_id, $type);
    $stmt->execute();
    $stmt->close();

    // 추천 값 조회
    $sql = "SELECT recommend_up, recommend_down FROM board WHERE board_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $board_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // 캡차 세션 초기화
    if (!$user_id) {
        unset($_SESSION['captcha_keystring']);
    }

    echo json_encode([
        'success' => true,
        'recommend_up' => $row['recommend_up'],
        'recommend_down' => $row['recommend_down']
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
} finally {
    $stmt->close();
}
?>