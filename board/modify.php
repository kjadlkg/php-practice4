<?php
session_start();
include "../db.php";

$loginUser = $_SESSION['id'] ?? null;
$showEditForm = false;

$id = $_GET['id'] ?? $_POST['id'] ?? null;
$step = $_POST['step'] ?? $_GET['step'] ?? null;

if (!$id || !is_numeric($id)) {
    echo "<script>alert('잘못된 접근입니다.'); history.back();</script>";
    exit;
}

$id = (int) $id;

$stmt = $db->prepare("SELECT * FROM board WHERE board_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_array(MYSQLI_ASSOC);
$stmt->close();

if (!$row) {
    echo "<script>alert('게시글이 존재하지 않습니다.'); history.back();</script>";
    exit;
}

$boardPw = $row['board_pw'];
$boardWriter = $row['board_writer'];
$boardTitle = htmlspecialchars($row['board_title'], ENT_QUOTES, 'UTF-8');
$boardContent = htmlspecialchars($row['board_content'], ENT_QUOTES, 'UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'check') {
    if (empty($_POST['pw'])) {
        echo "<script>alert('비밀번호를 입력해주세요.'); history.back();</script>";
        exit;
    }

    $inputPw = $_POST['pw'];

    if (!password_verify($inputPw, $boardPw)) {
        echo "<script>alert('비밀번호가 일치하지 않습니다.'); history.back();</script>";
        exit;
    }
    $showEditForm = true;

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'edit') {
    $title = htmlspecialchars(strip_tags($_POST['title']), ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars(strip_tags($_POST['content']), ENT_QUOTES, 'UTF-8');

    if (empty($title) || empty($content)) {
        echo "<script>alert('제목과 내용을 입력해주세요.'); history.back();</script>";
        exit;
    }

    if (empty($boardPw)) {
        if (!$loginUser || $loginUser !== $boardWriter) {
            echo "<script>alert('수정 권한이 없습니다.'); history.back();</script>";
            exit;
        }
    }

    $stmt = $db->prepare("UPDATE board SET board_title = ?, board_content = ? WHERE board_id = ?");
    $stmt->bind_param("ssi", $title, $content, $id);

    try {
        if ($stmt->execute()) {
            header("Location: view.php?id=" . $id);
            exit;
        } else {
            echo "<script>alert('글 수정 중 오류가 발생했습니다.'); history.back();</script>";
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
    <title><?= $boardTitle ?></title>
</head>

<body>
    <h3>글 수정</h3>
    <?php if (!empty($boardPw) && !$showEditForm): ?>
        <form method="POST" action="modify.php?id=<?= $id ?>">
            <p>비밀번호를 입력하세요.</p>
            <input type="password" name="pw" required>
            <input type="hidden" name="step" value="check">
            <button type="button" onclick="history.back()">취소</button>
            <button type="submit">확인</button>
        </form>
    <?php else: ?>
        <form method="POST" action="modify.php?id=<?= $id ?>">
            <div>
                <div>
                    <label>제목<input type="text" name="title" value="<?= $boardTitle ?>" required></label>
                </div>
                <div>
                    <label>내용<textarea name="content" rows="5" cols="40" required><?= $boardContent ?></textarea></label>
                </div>
            </div>
            <input type="hidden" name="step" value="edit">
            <button type="button" onclick="location.href='view.php?id=<?= $id ?>'">취소</button>
            <button type="submit">수정</button>
        </form>
    <?php endif; ?>
    </div>
</body>

</html>