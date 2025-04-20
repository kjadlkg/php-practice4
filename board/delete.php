<?php
session_start();
include "../db.php";

$loginUser = $_SESSION['id'] ?? null;
$showConfirmForm = false;

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'check') {
    if (empty($_POST['pw'])) {
        echo "<script>alert('비밀번호를 입력해주세요.');</script>";
    } else {
        $inputPw = $_POST['pw'];

        if (!password_verify($inputPw, $boardPw)) {
            echo "<script>alert('비밀번호가 일치하지 않습니다. 다시 시도해주세요.');</script>";
        } else {
            $showConfirmForm = true;
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'confirm') {
    if (empty($boardPw)) {
        if (!$loginUser || $loginUser !== $boardWriter) {
            echo "<script>alert('삭제 권한이 없습니다.'); location.href='view.php?id={$id}';</script>";
            exit;
        }

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            echo "<script>alert('잘못된 접근입니다.'); location.href='view.php?id={$id}';</script>";
            exit;
        }
    }

    $stmt = $db->prepare("DELETE FROM board WHERE board_id = ?");
    $stmt->bind_param("i", $id);

    try {
        if ($stmt->execute()) {
            echo "<script>alert('삭제되었습니다.');</script>";
            header("Location: ../main/index.php");
            exit;
        } else {
            echo "<script>alert('글 삭제 중 오류가 발생했습니다.'); location.href='view.php?id={$id}'</script>";
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
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/component.css">
    <link rel="stylesheet" href="../css/contents.css">
    <link rel="stylesheet" href="../css/popup.css">
    <link rel="stylesheet" href="../css/page/board.css">
</head>

<body>
    <header></header>
    <main>
        <section>
            <?php if (!empty($boardPw) && !$showConfirmForm): ?>
                <article>
                    <div class="nonmember_wrap">
                        <div class="nonmember_content">
                            <h3 class="blind">비회원 글 수정, 삭제</h3>
                            <form method="POST">
                                <div class="inner">
                                    <b class="text">비밀번호를 입력하세요.</b>
                                    <input type="password" class="password" name="pw">
                                    <input type="hidden" name="step" value="check">
                                    <div class="btn_box">
                                        <button type="button" class="btn btn_grey small"
                                            onclick="history.back()">취소</button>
                                        <button type="submit" class="btn btn_blue small">확인</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </article>
            <?php else: ?>
                <form method="POST">
                    <div class="empty_page_wrap">
                        <div class="pop_wrap type5">
                            <div class="pop_content robot">
                                <div class="inner">
                                    <b>삭제된 게시물은 복구할 수 없습니다.</b>
                                    <br>
                                    <b>게시물을 삭제하시겠습니까?</b>
                                    <input type="hidden" name="step" value="confirm">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                </div>
                                <div class="btn_box">
                                    <button type="button" class="btn btn_grey small"
                                        onclick="location.href='view.php?id=<?= $id ?>'">이전</button>
                                    <button type="submit" class="btn btn_blue small">삭제</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </section>
    </main>
</body>

</html>