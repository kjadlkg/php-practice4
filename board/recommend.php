<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
session_start();
include "../db.php";
include "../function.php";

$input = json_decode(file_get_contents('php://input'), true);
$board_id = isset($input['id']) ? (int) $input['id'] : 0;
$type = isset($input['type']) ? $input['type'] : '';
$captcha_input = isset($input['captcha']) ? $input['captcha'] : '';

if ($board_id <= 0 || !in_array($type, ['up', 'down'])) {
    echo json_encode(['success' => false, 'message' => '잘못된 요청입니다.']);
    exit;
}

$user_id = isset($_SESSION['id']) ? $_SESSION['id'] : '';

try {
    if (!$user_id && strtolower($captcha_input) !== strtolower($_SESSION['captcha_keystring'] ?? '')) {
        throw new Exception('자동입력 방지코드가 일치하지 않습니다.');
    }

    $column = $type === 'up' ? 'recommend_up' : 'recommend_down';
    $sql = "UPDATE board SET $column = $column + 1 WHERE board_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $board_id);
    $stmt->execute();
    $stmt->close();

    $sql = "SELECT recommend_up, recommend_down FROM board WHERE board_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $board_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

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
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => '오류가 발생했습니다.']);
    exit;
} finally {
    $stmt->close();
}
?>