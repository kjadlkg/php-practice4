<?php
session_start();
include "../db.php";

if (!isset($_SESSION["id"])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='../member/login/login.php';</script>";
    exit;
}

$id = $_SESSION['id'];

// 게시글
$stmt = $db->prepare("
SELECT b.*,
(SELECT COUNT(*) FROM comment WHERE board_id = b.board_id) AS comment_count
FROM board b
WHERE board_writer = ?
ORDER BY board_id DESC
LIMIT 5
");
$stmt->bind_param("s", $name);
$stmt->execute();
$board_result = $stmt->get_result();
$stmt->close();

// 댓글
$stmt = $db->prepare("
SELECT c.*,
(SELECT board_title FROM board WHERE board_id = c.board_id) AS board_title
FROM comment c
WHERE comment_writer = ?
ORDER BY comment_id DESC
LIMIT 5
");
$stmt->bind_param("s", $name);
$stmt->execute();
$comment_result = $stmt->get_result();
$stmt->close();

// 게시글 개수
$stmt = $db->prepare("SELECT COUNT(*) AS total_post FROM board WHERE board_writer = ?");
$stmt->bind_param("s", $name);
$stmt->execute();
$post_count_result = $stmt->get_result();
$post_count = $post_count_result->fetch_assoc()['total_post'];
$stmt->close();

// 댓글 개수
$stmt = $db->prepare("SELECT COUNT(*) AS total_comment FROM comment WHERE comment_writer = ?");
$stmt->bind_param("s", $name);
$stmt->execute();
$comment_count_result = $stmt->get_result();
$comment_count = $comment_count_result->fetch_assoc()['total_comment'];
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>마이페이지</title>
</head>

<body>
    <div>
        <a href="../main/index.php">게시판 메인가기</a>
        <a href="check.php">내 정보</a>
    </div>
    <div>
        <h3>게시글(<?= $post_count ?>)</h3>
        <a href="posting.php">전체보기</a>
        <?php if ($board_result->num_rows == 0) { ?>
            <p>작성한 글이 없습니다</p>
        <?php } else { ?>
            <table>
                <?php while ($row = $board_result->fetch_assoc()) { ?>
                    <tr>
                        <td>
                            <a href="../board/view.php?id=<?= htmlspecialchars($row['board_id']); ?>">
                                <?= htmlspecialchars($row['board_title'], ENT_QUOTES, 'UTF-8') ?>
                                [<?= $row['comment_count'] ?>]
                            </a>
                        </td>
                        <td><?= $row['created_at'] ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
    </div>
    <div>
        <h3>댓글(<?= $comment_count ?>)</h3>
        <a href="comment.php">전체보기</a>
        <?php if ($comment_result->num_rows == 0) { ?>
            <p>작성한 댓글이 없습니다</p>
        <?php } else { ?>
            <table>
                <?php while ($row = $comment_result->fetch_assoc()) { ?>
                    <tr>
                        <td>
                            <a href="../board/view.php?id=<? htmlspecialchars($row['board_id']); ?>">
                                <?= htmlspecialchars($row['comment_content'], ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </td>
                        <td><?= $row['created_at'] ?></td>
                        <?php if ($row['board_title'] !== null) { ?>
                            <td><?= htmlspecialchars($row['board_title'], ENT_QUOTES, 'UTF-8') ?></td>
                        <?php } else { ?>
                            <td>※ 원본 게시글이 삭제되었습니다.</td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
    </div>
</body>

</html>