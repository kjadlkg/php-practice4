<?php
session_start();
include "../db.php";

if (!isset($_SESSION['id'])) {
    echo "<script>alert('로그인이 필요합니다.'); history.back();</script>";
    exit;
}

$id = $_SESSION['id'];
$name = $_SESSION['name'];

$stmt = $db->prepare("
SELECT c.*,
(SELECT board_title FROM board WHERE board_id = c.board_id) AS board_title
FROM comment c
WHERE comment_writer = ?
ORDER BY comment_id DESC
");
$stmt->bind_param("s", $name);
$stmt->execute();
$comment_result = $stmt->get_result();
$stmt->close();


// paging
$list_num = 10;
$page_num = 10;

$page_stmt = $db->prepare("SELECT * FROM comment ORDER BY comment_id DESC LIMIT ?, ?");
$page_stmt->bind_param("ii", $start, $list_num);
$page_stmt->execute();
$result = $page_stmt->get_result();
$page_stmt->close();

$count_stmt = $db->prepare("SELECT COUNT(*) AS total FROM comment WHERE comment_writer = ?");
$count_stmt->bind_param("s", $name);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_rows = $count_result->fetch_assoc()['total'];
$count_stmt->close();

$total_page = max(1, ceil($total_rows / $list_num));

$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$start = max(0, ($page - 1) * $list_num);

$total_block = ceil($total_page / $page_num);
$now_block = ceil($page / $page_num);
$s_page = max(1, ($now_block - 1) * $page_num + 1);
$e_page = min($total_page, $s_page + $page_num - 1);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>댓글</title>
</head>

<body>

    <div>
        <h3>댓글(<?= $total_rows ?>)</h3>
        <?php if ($comment_result->num_rows == 0) { ?>
        <p>작성한 댓글이 없습니다</p>
        <?php } else { ?>
        <table>
            <?php while ($row = $comment_result->fetch_assoc()) { ?>
            <tr>
                <td>
                    <a href="../board/view.php?id=<? htmlspecialchars($row['comment_id']); ?>">
                        <?= htmlspecialchars($row['comment_content'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </td>
                <td><?= $row['created_at'] ?></td>
                <td><?= htmlspecialchars($row['board_title'], ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <?php } ?>
        </table>
        <?php } ?>
    </div>

    <div class="page">
        <?php if ($page > 1) { ?>
        <a href="comment.php?page=<?= $page - 1 ?>">이전</a>
        <?php } else { ?>
        <span>이전</span>
        <?php } ?>
        <?php for ($i = $s_page; $i <= $e_page; $i++) { ?>
        <?php if ($i == $page) { ?>
        <strong><?= $i ?></strong>
        <?php } else { ?>
        <a href="comment.php?page=<?= $i ?>"><?= $i ?></a>
        <?php } ?>
        <?php } ?>

        <?php if ($page < $total_page) { ?>
        <a href="comment.php?page=<?= $page + 1 ?>">다음</a>
        <?php } else { ?>
        <span>다음</span>
        <?php } ?>
    </div>
</body>

</html>