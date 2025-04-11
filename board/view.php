<?php
session_start();
include "../db.php";
include "../function.php";

$id = $_GET['id'] ?? null;
$is_login = isset($_SESSION['id']);

if (!$id || !is_numeric($id)) {
    echo "<script>alert('잘못된 접근입니다.'); location.href='../main/index.php';</script>";
    exit;
}

$id = (int) $id;

// view count
$stmt = $db->prepare("UPDATE board SET board_views = board_views + 1 WHERE board_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

// board
$stmt = $db->prepare(
    "SELECT b.*, u.user_id
        FROM board b
        JOIN user u
        ON b.board_writer = u.user_name
        WHERE b.board_id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_array(MYSQLI_ASSOC);
$stmt->close();

if (!$row) {
    echo "<script>alert('존재하지 않는 게시글입니다.'); location.href='../main/index.php';</script>";
    exit;
}

$boardPw = $row['board_pw'];
$boardWriter = $row['board_writer'];
$is_writer = $is_login && $_SESSION['id'] == $row['user_id'];
$boardTitle = htmlspecialchars($row['board_title'], ENT_QUOTES, 'UTF-8');
$boardContent = htmlspecialchars($row['board_content'], ENT_QUOTES, 'UTF-8');

// comment
$stmt = $db->prepare("
    SELECT c.*, u.user_name
    FROM comment c JOIN user u
    ON c.comment_writer = u.user_name
    WHERE c.board_id = ? ORDER BY c.created_at
    ");
$stmt->bind_param("i", $id);
$stmt->execute();
$comment_result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $boardTitle ?></title>
</head>

<body>
    <div>
        <h3><?= $boardTitle ?></h3>
        <div>
            <p><?= $boardWriter ?>
                <?php if (!empty($row['ip'])) {
                    $mask_ip = mask_ip($row['ip']);
                    if (!empty($mask_ip)) {
                        echo "($mask_ip)";
                    }
                } ?>
                | <?= $row['created_at'] ?> | 조회 <?= (int) $row['board_views'] ?>
            </p>
        </div>
        <br>
        <div>
            <?= $boardContent ?>
        </div>
        <br>
        <div>
            <button onclick="location.href='../main/index.php'">목록</button>
            <?php if ($is_writer || !empty($boardPw)) { ?>
            <form method="POST" action="modify.php?id=<?= $id ?>">
                <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
                <button type="submit">수정</button>
            </form>
            <form method="POST" action="delete.php?id=<?= $id ?>">
                <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
                <button type="submit">삭제</button>
            </form>
            <?php } ?>
        </div>
    </div>

    <!-- comment -->
    <h3>댓글 목록</h3>
    <?php while ($comment = $comment_result->fetch_assoc()) { ?>
    <div class="comment_delete">
        <span><?= htmlspecialchars($comment['comment_writer'], ENT_QUOTES, 'UTF-8') ?></span>
        <p><?= nl2br(htmlspecialchars($comment['comment_content'], ENT_QUOTES, 'UTF-8')) ?></p>
        <span><?= $comment['created_at'] ?></span>
        <!-- delete -->
        <?php
            $is_comment_writer = $is_login && $_SESSION['name'] === $comment['comment_writer'];
            $comment_id = $comment['comment_id'];
            if ($is_comment_writer): ?>
        <form method="POST" action="../comment/delete.php">
            <div>
                <input type="hidden" name="comment_id" value="<?= $comment_id ?>">
                <input type="hidden" name="board_id" value="<?= $id ?>">
                <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
            </div>
            <div>
                <button type="submit" onclick="return confirm('댓글을 삭제하시겠습니까?')">X</button>
            </div>
        </form>
        <?php elseif (!empty($comment['comment_pw'])): ?>
        <button onclick="showPasswordForm(<?= $comment_id ?>)">X</button>
        <div id="delete-box-<?= $comment_id ?>" class="delete-box" style="display: none;">
            <form method="POST" action="../comment/delete.php" onsubmit="return checkDeletePassword()">
                <div>
                    <input type="hidden" name="comment_id" value="<?= $comment_id ?>">
                    <input type="hidden" name="board_id" value="<?= $id ?>">
                    <input type="password" name="pw" placeholder="비밀번호">
                    <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
                </div>
                <div>
                    <button type="submit">확인</button>
                    <button type="button" onclick="hidePasswordForm(<?= $comment_id ?>)">X</button>
                </div>
            </form>
        </div>
        <?php endif; ?>
        <hr>
    </div>
    <?php } ?>

    <div class="comment_add">
        <?php if ($is_login) { ?>
        <form method="POST" action="../comment/comment.php" onsubmit="return confirm_empty(this)">
            <div>
                <input type="hidden" name="board_id" value="<?= $id ?>">
                <input type="hidden" name="name" value="<?= htmlspecialchars($_SESSION['name']) ?>">
            </div>
            <div>
                <textarea name="content" autocomplete="off"></textarea>
                <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
            </div>
            <div>
                <button type="submit">등록</button>
            </div>
        </form>
        <?php } else { ?>
        <form method="POST" action="../comment/comment.php" onsubmit="return confirm_empty(this)">
            <div>
                <input type="hidden" name="board_id" value="<?= $id ?>">
                <input type="text" name="name" placeholder="닉네임">
                <input type="password" name="pw" placeholder="비밀번호">
            </div>
            <div>
                <textarea name="content" autocomplete="off"></textarea>
                <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
            </div>
            <div>
                <button type="submit">등록</button>
            </div>
        </form>
        <?php } ?>
    </div>
</body>
<script>
function checkDeletePassword(form) {
    const password = form.querySelector('input[name="pw"]');
    if (!password || password.value.trim() === "") {
        alert("비밀번호를 입력하세요.");
        password.focus();
        return false;
    }
    return true;
}

function confirm_empty(form) {
    const isLogin = <?= isset($_SESSION['id']) ? 'true' : 'false' ?>;
    const username = form.querySelector('input[name = "name"]');
    const password = form.querySelector('input[name = "pw"]');
    const content = form.querySelector('textarea[name = "content"]');

    if (isLogin) {
        if (username && username.value.trim() === "") {
            alert("닉네임을 입력하세요.");
            username.focus();
            return false;
        }

        if (password && password.value.trim() === "") {
            alert("비밀번호를 입력하세요.");
            password.focus();
            return false;
        }
    }

    if (content.value.trim() === "") {
        alert("내용을 입력하세요.");
        content.focus();
        return false;
    }

    return true;
}

function showPasswordForm(id) {
    document.getElementById('delete-box-' + id).style.display = 'inline-block';
}

function hidePasswordForm(id) {
    document.getElementById('delete-box-' + id).style.display = 'none';
}
</script>

</html>