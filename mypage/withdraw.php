<?php
session_start();
include "../db.php";

if (!isset($_SESSION['id'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='../member/login/login.php';</script>";
    exit;
}

$id = $_SESSION['id'];
$name = $_SESSION['name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $is_deleted = 1;
    $deleted_at = date("Y-m-d H:i:s");

    $stmt = $db->prepare("UPDATE user SET is_deleted = ? , deleted_at = ? WHERE user_id = ?");
    $stmt->bind_param("iss", $is_deleted, $deleted_at, $id);
    $stmt->execute();
    $stmt->close();

    session_unset();
    session_destroy();

    echo "<script>alert('탈퇴가 완료되었습니다.); location.href='../main/index.php';</script>";
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원 탈퇴</title>
</head>

<body>
    <div>
        <form method="POST">
            <p>탈퇴 시 계정 복구가 불가능합니다.</p>
            <p>탈퇴 시 해당 계정으로 작성된 게시물과 댓글은 자동으로 삭제되지 않습니다.</p>
            <p>탈퇴 하시겠습니까?</p>
            <div>
                <div>
                    <div>아이디</div>
                    <p><?= htmlspecialchars($id) ?></p>
                </div>
                <div>
                    <div>닉네임</div>
                    <p><?= htmlspecialchars($name) ?></p>
                </div>
            </div>
            <div>
                <button type="submit">탈퇴</button>
                <button type="button" onclick="location.href='../main/index.php'">취소</button>
            </div>
        </form>
    </div>
</body>

</html>