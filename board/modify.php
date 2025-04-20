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
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/component.css">
    <link rel="stylesheet" href="../css/contents.css">
    <link rel="stylesheet" href="../css/page/board.css">
</head>

<body>
    <header></header>
    <main>
        <section>
            <header></header>
            <article>
                <h2 class="blind">글 수정</h2>
            </article>
            <?php if (!empty($boardPw) && !$showEditForm): ?>
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
                <article id="write_wrap" class="clear">
                    <legend class="blind">글쓰기 영역</legend>
                    <form method="POST">
                        <div class="clear">
                            <fieldset>
                                <div class="input_box input_write_title">
                                    <input class="input_text" type="text" name="title" value="<?= $boardTitle ?>">
                                </div>
                            </fieldset>
                        </div>
                        <div class="note_editor note_frame">
                            <!-- <div class="note_editable" contenteditable="true" role="textbox" aria-multiline="true"
                                spellcheck="true" autocorrect="true" style="height: 380px; min-height: 400px;">
                                <p></p>
                            </div> -->
                            <textarea class="input_text" name="content" rows="5" cols="40"><?= $boardContent ?></textarea>
                        </div>
                        <div>
                            <input type="hidden" name="step" value="edit">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        </div>
                        <div class="btn_box write fr">
                            <button type="button" class="btn btn_grey"
                                onclick="location.href='view.php?id=<?= $id ?>'">취소</button>
                            <button type="submit" class="btn btn_blue">수정</button>
                        </div>
                    </form>
                </article>
            <?php endif; ?>
        </section>
    </main>
    <footer></footer>
</body>

</html>