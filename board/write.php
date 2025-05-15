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
        $user_id = $_SESSION['id'];
        $user_name = $_SESSION['name'];
        $board_pw = null;
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] !== $csrf_token) {
            echo "<script>alert('잘못된 접근입니다.'); location.href='../main/index.php';</script>";
            exit;
        }
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
        $user_name = $_POST['name'] ?? '';
        $user_pw = $_POST['pw'] ?? '';
        $captcha_input = $_POST['captcha'] ?? '';
        $correct_code = $_SESSION['captcha_keystring'] ?? '';

        if (empty($user_name) || empty($user_pw)) {
            echo "<script>alert('닉네임과 비밀번호를 입력해주세요.'); location.href='write.php';</script>";
            exit;
        }

        if (strtolower($captcha_input) !== strtolower($correct_code)) {
            echo "<script>alert('자동입력 방지코드가 일치하지 않습니다.'); history.back();</script>";
            exit;
        }

        unset($_SESSION['captcha_keystring']);

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

    $stmt = $db->prepare("INSERT INTO board (board_title, board_content, board_writer, board_writer_id, board_pw, ip) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $title, $content, $user_name, $user_id, $board_pw, $ip);

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
                <h2 class="blind">글 작성</h2>
            </article>
            <article id="write_wrap" class="clear">
                <form method="POST" onsubmit="return confirm_empty(this)">
                    <?php if (isset($_SESSION['id'])) { ?>
                    <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
                    <?php } ?>
                    <div>
                        <div class="clear">
                            <fieldset>
                                <?php if (!isset($_SESSION['id'])) { ?>
                                <div class="input_box input_info">
                                    <label for="name" class="text_placeholder">닉네임</label>
                                    <input id="name" class="input_text" type="text" name="name">
                                </div>
                                <div class="input_box input_info">
                                    <label for="password" class="text_placeholder">비밀번호</label>
                                    <input id="password" class="input_text" type="password" name="pw" maxlength="20">
                                </div>
                                <div class="input_box input_info">
                                    <img src="../captcha_image.php?<?= time() ?>" alt="KCAPTCHA"
                                        onclick="this.src='../captcha_image.php?' + new Date().getTime()"
                                        style="cursor:pointer;">
                                    <label for="captcha" class="text_placeholder">코드 입력</label>
                                    <input id="captcha" class="input_text" type="text" name="captcha">
                                </div>
                                <?php } ?>
                                <div class="input_box input_write_title">
                                    <label for="title" class="text_placeholder">제목을 입력하세요</label>
                                    <input id="title" class="input_text" type="text" name="title" maxlength="40">
                                </div>
                            </fieldset>
                        </div>
                        <div class="note_editor note_frame">
                            <!-- <div class="note_editable" contenteditable="true" role="textbox" aria-multiline="true"
                                spellcheck="true" autocorrect="true" style="height: 380px; min-height: 400px;">
                                <p></p>
                            </div> -->
                            <textarea id="content" name="content" rows="5" cols="40" placeholder="내용을 입력하세요"></textarea>
                        </div>
                    </div>
                    <div class="btn_box write fr">
                        <button type="button" class="btn btn_grey"
                            onclick="location.href='../main/index.php'">취소</button>
                        <button type="submit" class="btn btn_blue">등록</button>
                    </div>
                </form>
            </article>
        </section>
    </main>
    <footer></footer>
</body>
<script>
function confirm_empty(form) {
    const isLogin = <?= isset($_SESSION['id']) ? 'true' : 'false' ?>;
    const title = form.getElementId('#title').value.trim();
    const content = form.getElementId('#content').value.trim();

    if (!isLogin) {
        const username = form.getElementId('#name').value.trim();
        const password = form.getElementId('#password').value.trim();
        if (!username || !pw) {
            alert("닉네임과 비밀번호를 입력해주세요.");
            return false;
        }
    }

    if (!title || !content) {
        alert("제목과 내용을 입력해주세요.");
        return false;
    }

    return true;
}
</script>

</html>