<?php
header('Content-Type: application/json');

// ini_set('display_errors', 0);
// ini_set('log_errors', 1);
// ini_set('error_log', __DIR__ . '/report_error.log');
// error_reporting(E_ALL);

session_start();
include "../resource/db.php";
include "../resource/function.php";

$input = json_decode(file_get_contents('php://input'), true);

$board_id = intval($input['id']);
$report_type = trim($input['type']);
$user_id = $_SESSION['id'] ?? null;
$user_ip = $_SERVER['REMOTE_ADDR'] ?? null;

if (!$board_id || !$report_type) {
   echo json_encode(['success' => false, 'message' => '잘못된 요청입니다.']);
   exit;
}

// 중복 신고 체크
if ($user_id) {
   $stmt = $db->prepare("SELECT COUNT(*) FROM report WHERE board_id = ? AND report_type = ? AND user_id = ?");
   $stmt->bind_param("iss", $board_id, $report_type, $user_id);
} else {
   $stmt = $db->prepare("SELECT COUNT(*) FROM report WHERE board_id = ? AND report_type = ? AND ip = ?");
   $stmt->bind_param("iss", $board_id, $report_type, $user_ip);
}
$stmt->execute();
$stmt->bind_result($total_rows);
$stmt->fetch();
$stmt->close();
if ($total_rows > 0) {
   echo json_encode(['success' => false, 'message' => '이미 신고한 글입니다.']);
   exit;
}

// 신고 저장
$stmt = $db->prepare("INSERT INTO report (board_id, report_type, user_id, ip) VALUES (?, ?, ?, ?) ");
$stmt->bind_param("isss", $board_id, $report_type, $user_id, $user_ip);
$stmt->execute();
$stmt->close();

// 로그 파일 기록
// $log = "[" . date('Y-m-d H:i:s') . "] 신고 - 게시글: {$board_id}, 사유: {$report_type}, IP: {$user_ip}, 사용자: " . ($user_id ?? '비회원') . "\n";
// file_put_contents(__DIR__ . '/report_log.txt', $log, FILE_APPEND);

echo json_encode(['success' => true]);
?>