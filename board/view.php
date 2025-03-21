<?php
session_start();
include "../db.php";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        die("잘못된 접근입니다.");
    }

    $id = (int) $_GET["id"];

    $stmt = $db->prepare("UPDATE board SET board_views = board_views + 1 WHERE board_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    $stmt = $db->prepare(
        "SELECT b.*, u.user_name, user_id
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
        die("존재하지 않는 게시글입니다.");
    }

    $is_writer = isset($_SESSION['id']) && $_SESSION['id'] == $row['user_id'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $row['board_title'] ?></title>
</head>

<body>
    <div>
        <h3><?= htmlspecialchars($row['board_title']); ?></h3>
        <div>
            <p><?= htmlspecialchars($row['user_name']); ?> |
                <?= $row['created_at']; ?> | 조회 <?= $row['board_views']; ?>
            </p>
        </div>
        <br>
        <div>
            <?= nl2br(htmlspecialchars($row['board_content'])); ?>
        </div>
        <br>
        <div>
            <button onclick="location.href='../main/index.php'">목록</button>
            <?php if ($is_writer) { ?>
                <button onclick="location.href='modify.php?id<?= $id; ?>'">수정</button>
                <button onclick="location.href='delete.php?id<?= $id; ?>'">삭제</button>
            <?php } ?>
        </div>
    </div>
</body>

</html>