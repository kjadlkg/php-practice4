<?php
session_start();
include "../db.php";
include "../function.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';

    if (empty($title) || empty($content)) {
        echo "<script>alert('제목과 내용을 입력해주세요.'); location.href='write.php';</script>";
        exit;
    }

    $title = htmlspecialchars(strip_tags($title), ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars(strip_tags($content), ENT_QUOTES, 'UTF-8');

    if (isset($_SESSION['id'])) {
        $user_name = $_SESSION['name'];
        $board_pw = null;
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] !== $csrf_token) {
            echo "<script>alert('잘못된 접근입니다.'); location.href='../main/index.php';</script>";
            exit;
        }
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
        $user_id = $ip . "_" . time();
        $user_name = $_POST['name'] ?? '';
        $user_pw = $_POST['pw'] ?? '';

        if (empty($user_name) || empty($user_pw)) {
            echo "<script>alert('닉네임과 비밀번호를 입력해주세요.'); location.href='write.php';</script>";
            exit;
        }

        $user_name = htmlspecialchars(strip_tags($user_name), ENT_QUOTES, 'UTF-8');
        $board_pw = password_hash($user_pw, PASSWORD_DEFAULT);

        $stmt = $db->prepare("SELECT COUNT(*) FROM user WHERE user_name = ?");
        $stmt->bind_param("s", $user_name);
        $stmt->execute();
        $stmt->bind_result($user_count);
        $stmt->fetch();
        $stmt->close();

        if ($user_count == 0) {
            $stmt = $db->prepare("INSERT INTO user (user_id, user_name, user_pw) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $user_id, $user_name, $board_pw);
            $stmt->execute();
            $stmt->close();
        }
    }

    $stmt = $db->prepare("INSERT INTO board (board_title, board_content, board_writer, board_pw, ip) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $content, $user_name, $board_pw, $ip);

    try {
        if ($stmt->execute()) {
            header("Location: ../main/index.php");
            exit;
        } else {
            echo "<script>alert('글 작성 중 오류가 발생했습니다.'); location.href='../main/index.php';</script>";
        }
    } finally {
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>글쓰기</title>
</head>

<body>
    <div>
        <h1>글 작성</h1>
        <form method="POST" onsubmit="return validateForm()">
            <?php if (isset($_SESSION['id'])) { ?>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?>">
            <?php } ?>
            <div>
                <div>
                    <?php if (!isset($_SESSION['id'])) { ?>
                    <input type="text" name="name" placeholder="닉네임">
                    <input type="password" name="pw" placeholder="비밀번호">
                    <?php } ?>
                </div>
                <div>
                    <input type="text" name="title" placeholder="제목을 입력하세요">
                </div>
                <div>
                    <textarea name="content" rows="5" cols="40" placeholder="내용을 입력하세요"></textarea>
                </div>
            </div>
            <div>
                <button type="button" onclick="location.href='../main/index.php'">취소</button>
                <button type="submit">작성</button>
            </div>
        </form>
    </div>
</body>
<script>
function validateForm() {
    const title = document.querySelector('input[name="title"]').value.trim();
    const content = document.querySelector('input[name="content"]').value.trim();

    <?php if (!isset($_SESSION['id'])) { ?>
    const name = document.querySelector('input[name="name"]').value.trim();
    const pw = document.querySelector('input["pw"]').value.trim();
    if (!name || !pw) {
        alert("닉네임과 비밀번호를 입력해주세요.");
        return false;
    }
    <?php } ?>

    if (!title || !content) {
        alert("제목과 내용을 입력해주세요.");
        return false;
    }
    return true;
}
</script>

</html>