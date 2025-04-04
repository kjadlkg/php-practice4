<?php
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

function getMaskedUserId($user_id)
{
    $parts = explode('_', $user_id);
    if (count($parts) < 2)
        return '';
    return mask_ip($parts[0]);
}
?>