<?php
require_once("db.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? trim($_POST['id']) : null;
    $pw = isset($_POST['pw']) ? $_POST['pw'] : null;
    $csrf_token = $_POST['csrf_token'] ?? '';

    // CSRF 토큰 검증
    if (!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] !== $csrf_token) {
        die("잘못된 접근입니다.");
    }

    if (empty($id) || empty($pw)) {
        $_SESSION['error'] = "아이디 또는 비밀번호를 입력해주세요.";
        header("Location: login.php");
        exit;
    }

    if (!$db) {
        $_SESSION['error'] = "데이터베이스 연결에 실패했습니다.";
        header("Location: login.php");
        exit;
    }

    $stmt = $db->prepare("SELECT userid, userpw FROM user WHERE userid = ?");
    if ($stmt) {
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($userid, $userpw);
            $stmt->fetch();

            if (password_verify($pw, $userpw)) {
                $_SESSION["id"] = $userid;

                if (!empty($_POST['idsave'])) {
                    setcookie("saved_id", $id, time() + 60 * 60 * 24 * 30, "/", "", false, true);
                } else {
                    setcookie("saved_id", "", time() - 3600, "/", "", false, true);
                }

                header("Location: ../../main/index.php");
                exit;
            }
        }
        $stmt->close();
    }

    $_SESSION['error'] = "아이디 또는 비밀번호가 일치하지 않습니다.";
    header("Location: login.php");
    exit;
}

// CSRF 토큰 생성
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인</title>
</head>

<body>
    <div>
        <h1>로그인</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <p style="color: red;"><?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="post" action="login.php">
            <input type="text" name="id" placeholder="아이디" required
                value="<?= isset($_COOKIE['saved_id']) ? htmlspecialchars($_COOKIE['saved_id'], ENT_QUOTES, 'UTF-8') : '' ?>" />
            <input type="password" name="pw" placeholder="비밀번호" required />
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>" />
            <input type="submit" value="로그인" />
            <label for="idsave">
                <input type="checkbox" id="idsave" />아이디 저장
            </label>
            <a href="../join/join.php">회원가입</a>
            <a href="../../forgot/index.php">비밀번호 찾기</a>
        </form>
    </div>
</body>

</html>