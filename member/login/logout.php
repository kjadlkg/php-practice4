<?php
session_start();
session_unset();    // 변수 제거
session_destroy();  // 세션 삭제

// 쿠키 삭제
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}



echo "<script>alert('로그아웃 되었습니다.'); location.href='../../main/index.php';</script>";
exit;
?>