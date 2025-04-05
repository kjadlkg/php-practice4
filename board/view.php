<?php
session_start();
include "../db.php";
include "../function.php";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        echo "<script>alert('잘못된 접근입니다.'); history.back();</script>";
        exit;
    }

    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    $id = (int) $_GET["id"];

    $stmt = $db->prepare("UPDATE board SET board_views = board_views + 1 WHERE board_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

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
    $row = $result->fetch_assoc();
    $stmt->close();

    if (!$row) {
        echo "<script>alert('존재하지 않는 게시글입니다.'); history.back();</script>";
        exit;
    }

    $is_writer = isset($_SESSION['id']) && $_SESSION['id'] == $row['user_id'];
    $boardTitle = htmlspecialchars($row['board_title'], ENT_QUOTES, 'UTF-8');
    $boardContent = htmlspecialchars($row['board_content'], ENT_QUOTES, 'UTF-8');
    $boardPw = $row['board_pw'];
    $boardWriter = $row['board_writer'];

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
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $boardTitle; ?></title>
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
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <button type="submit">수정</button>
                </form>
                <form method="POST" action="delete.php?id=<?= $id ?>">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <button type="submit">삭제</button>
                </form>
            <?php } ?>
        </div>
    </div>

    <!-- comment -->
    <h3>댓글 목록</h3>
    <?php while ($comment = $comment_result->fetch_assoc()) { ?>
        <div>
            <p>
                <?= htmlspecialchars($comment['comment_writer'], ENT_QUOTES, 'UTF-8'); ?>
                (<?= $comment['created_at'] ?>)
            </p>
            <p><?= nl2br(htmlspecialchars($comment['comment_content'], ENT_QUOTES, 'UTF-8')); ?></p>
            <?php
            $is_comment_writer = isset($_SESSION['name']) && $_SESSION['name'] === $comment['comment_writer'];
            if ($is_comment_writer) { ?>
                <form method="POST" action="../comment/delete.php">
                    <input type="hidden" name="comment_id" value="<?= $comment['comment_id'] ?>">
                    <input type="hidden" name="board_id" value="<?= $id; ?>">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <button type="submit" onclick="return confirm('댓글을 삭제하시겠습니까?');">삭제</button>
                </form>
            <?php } ?>
            <hr>
        </div>
    <?php } ?>

    <div>
        <?php if (isset($_SESSION['id'])) { ?>
            <form method="POST" action="../comment/comment.php">
                <input type="hidden" name="board_id" value="<?= $id; ?>">
                <input type="hidden" name="name" value="<?= htmlspecialchars($_SESSION['name']); ?>">
                <textarea name="content" autocomplete="off" required></textarea>
                <button type="submit">댓글 작성</button>
            </form>
        <?php } else { ?>
            <p>로그인 후 댓글을 작성할 수 있습니다</p>
        <?php } ?>
    </div>
</body>

</html>