<?php
session_start();
include "../db.php";

$loginUser = $_SESSION['id'] ?? null;
$showEditForm = false;

$id = $_GET['id'] ?? null;
$step = $_POST['step'] ?? null;

if (!$id || !is_numeric($id)) {
    echo "<script>alert('잘못된 접근입니다.'); location.href='view.php?id={$id}';</script>";
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
    echo "<script>alert('게시글이 존재하지 않습니다.'); location.href='../main/index.php';</script>";
    exit;
}

$boardPw = $row['board_pw'];
$boardWriter = $row['board_writer'];
$boardTitle = htmlspecialchars($row['board_title'], ENT_QUOTES, 'UTF-8');
$boardContent = htmlspecialchars($row['board_content'], ENT_QUOTES, 'UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'check') {
    if (empty($_POST['pw'])) {
        echo "<script>alert('비밀번호를 입력해주세요.'); </script>";
    } else {
        $inputPw = $_POST['pw'];

        if (!password_verify($inputPw, $boardPw)) {
            echo "<script>alert('비밀번호가 일치하지 않습니다. 다시 시도해주세요.');</script>";
        } else {
            $showEditForm = true;
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'edit') {
    $title = htmlspecialchars(strip_tags($_POST['title']), ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars(strip_tags($_POST['content']), ENT_QUOTES, 'UTF-8');

    if (empty($title) || empty($content)) {
        echo "<script>alert('제목과 내용을 입력해주세요.');</script>";
    } else {
        if (empty($boardPw)) {
            if (!$loginUser || $loginUser !== $boardWriter) {
                echo "<script>alert('수정 권한이 없습니다.'); location.href='view.php?id={$id}';</script>";
                exit;
            }

            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                echo "<script>alert('잘못된 접근입니다.'); location.href='view.php?id={$id}';</script>";
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
                echo "<script>alert('글 수정 중 오류가 발생했습니다.'); location.href='view.php?id={$id}';</script>";
            }
        } finally {
            $stmt->close();
        }
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
    <?php if (!empty($boardPw) && !$showEditForm): ?>
    <form method="POST">
        <div>
            <p>비밀번호를 입력하세요.</p>
            <input type="password" name="pw">
            <input type="hidden" name="step" value="check">
        </div>
        <div>
            <button type="button" onclick="history.back()">취소</button>
            <button type="submit">확인</button>
        </div>
    </form>
    <?php else: ?>
    <h3>글 수정</h3>
    <form method="POST">
        <div>
            <div>
                <label>제목<input type="text" name="title" value="<?= $boardTitle ?>"></label>
            </div>
            <div>
                <label>내용<textarea name="content" rows="5" cols="40"><?= $boardContent ?></textarea></label>
            </div>
        </div>
        <div>
            <input type="hidden" name="step" value="edit">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        </div>
        <div>
            <button type="button" onclick="location.href='view.php?id=<?= $id ?>'">취소</button>
            <button type="submit">수정</button>
        </div>
    </form>
    <?php endif; ?>
    </div>
</body>

</html>