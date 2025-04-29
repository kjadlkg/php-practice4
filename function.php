<?php
function getMaskedUserId($user_id)
{
    $parts = explode('_', $user_id);
    if (count($parts) < 2)
        return '';
    return mask_ip($parts[0]);
}

function get_csrf_token()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function mask_ip($ip)
{
    if (empty($ip)) {
        return '';
    }

    // IPv6 -> IPv4
    if ($ip === '::1') {
        $ip = '127.0.0.1';
    }

    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        return '';
    }

    $ip_parts = explode('.', $ip);
    if (count($ip_parts) < 2) {
        return '';
    }
    return $ip_parts[0] . '.' . $ip_parts[1];
}

function update_recommend($db, $board_id, $type)
{
    if (!in_array($type, ['up', 'down'])) {
        return false;
    }

    $column = $type === 'up' ? 'recommend_up' : 'recommend_down';
    $stmt = $db->prepare("UPDATE board SET $column = $column + 1 WHERE board_id = ?");
    $stmt->bind_param("i", $board_id);
    $result = $stmt->execute();
    $stmt->close();

    return $result;
}
?>