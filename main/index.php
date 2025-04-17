<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
include "../db.php";
include "../function.php";

if (!isset($_SESSION["id"])) {
    unset($_SESSION["name"]);
}

// paging
$list_num = 10;
$page_num = 10;

$page_stmt = $db->prepare("
SELECT b.*,
(SELECT COUNT(*) FROM comment WHERE board_id = b.board_id) AS comment_count
FROM board b
ORDER BY b.board_id DESC
LIMIT ?, ?
");
$page_stmt->bind_param("ii", $start, $list_num);
$page_stmt->execute();
$result = $page_stmt->get_result();
$page_stmt->close();

$count_stmt = $db->prepare("SELECT COUNT(*) AS total FROM board");
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
    <title>게시판</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/component.css">
    <link rel="stylesheet" href="../css/page/main.css">
</head>

<body>
    <header>
        <div>
            <?php if (!isset($_SESSION["id"])) { ?>
            <div>
                <a href="../member/login/login.php">로그인</a>
                <a href="../member/join/join.php">회원가입</a>
            </div>
            <?php } else { ?>
            <div>
                <p><a href="../mypage/index.php"><?= htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8') ?></a> 님
                </p>
                <a href="../mypage/index.php">마이페이지</a>
                <a href="../member/login/logout.php">로그아웃</a>
            </div>
            <?php } ?>
        </div>
    </header>
    <main>
        <div>
            <form method="GET" action="../search/search.php">
                <input type="text" class="search" name="search" placeholder="제목+내용 검색">
                <button type="submit" class="btn btn_blue">검색</button>
            </form>
        </div>

        <div>
            <div class="page_head clear">
                <div>
                    <h1>게시판</h1>
                </div>
            </div>

            <button type="button" class="btn btn_blue" onclick="location.href='../board/write.php'">글쓰기</button>

            <table>
                <tr>
                    <th>번호</th>
                    <th>제목</th>
                    <th>작성자</th>
                    <th>작성일</th>
                    <th>조회수</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['board_id'] ?></td>
                    <td>
                        <a href="../board/view.php?id=<?= $row['board_id'] ?>">
                            <?= htmlspecialchars($row['board_title'], ENT_QUOTES, 'UTF-8') ?>
                            [<?= $row['comment_count'] ?>]
                        </a>
                    </td>
                    <td>
                        <?= htmlspecialchars($row['board_writer'], ENT_QUOTES, 'UTF-8') ?>
                        <?php if (!empty($row['ip'])) {
                                $mask_ip = mask_ip($row['ip']);
                                if (!empty($mask_ip)) {
                                    echo "($mask_ip)";
                                }
                            } ?>
                    </td>
                    <td><?= $row['created_at'] ?></td>
                    <td><?= $row['board_views'] ?></td>
                </tr>
                <?php } ?>
            </table>

            <div class="page">
                <?php if ($page > 1) { ?>
                <a href="index.php?page=<?= $page - 1 ?>">이전</a>
                <?php } else { ?>
                <span>이전</span>
                <?php } ?>
                <?php for ($i = $s_page; $i <= $e_page; $i++) { ?>
                <?php if ($i == $page) { ?>
                <strong><?= $i ?></strong>
                <?php } else { ?>
                <a href="index.php?page=<?= $i ?>"><?= $i ?></a>
                <?php } ?>
                <?php } ?>

                <?php if ($page < $total_page) { ?>
                <a href="index.php?page=<?= $page + 1 ?>">다음</a>
                <?php } else { ?>
                <span>다음</span>
                <?php } ?>
            </div>
    </main>
    <footer></footer>
</body>

</html>