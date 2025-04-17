<?php
include "../db.php";

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search) {
    $stmt = $db->prepare("SELECT * FROM board WHERE board_title LIKE ? OR board_content LIKE ?");
    $search = "%$search%";
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    echo "<script>alert('검색어를 입력해주세요'); history.back();</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>검색결과</title>
</head>

<body>
    <h3>게시물</h3>
    <?php if ($result->num_rows == 0) { ?>
    <p>관련 게시물이 없습니다</p>
    <?php } else { ?>
    <div>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <a href="../board/view.php?id=<?= htmlspecialchars($row['board_id']) ?>">
            <?= htmlspecialchars($row['board_title'], ENT_QUOTES, 'UTF-8') ?>
        </a>
        <p><?= htmlspecialchars($row['board_content'], ENT_QUOTES, 'UTF-8') ?></p>
        <span><?= htmlspecialchars($row['created_at']) ?></span>
        <hr>
        <?php } ?>
    </div>
    <?php } ?>
</body>

</html>