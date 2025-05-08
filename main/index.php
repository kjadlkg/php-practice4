<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
include "../db.php";
include "../function.php";


// paging
$list_num = 10;
$page_num = 10;

$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

// 전체 게시글 수
$count_stmt = $db->prepare("SELECT COUNT(*) AS total FROM board");
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_rows = $count_result->fetch_assoc()['total'];
$count_stmt->close();

$total_page = max(1, ceil($total_rows / $list_num));

if ($page > $total_page)
    $page = $total_page;

$start = max(0, ($page - 1) * $list_num);

$total_block = ceil($total_page / $page_num);
$now_block = ceil($page / $page_num);

$s_page = max(1, ($now_block - 1) * $page_num + 1);
$e_page = min($total_page, $s_page + $page_num - 1);

// 게시글
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
    <link rel="stylesheet" href="../css/contents.css">
    <link rel="stylesheet" href="../css/page/main.css">
</head>

<body>
    <header>
        <div class="main_head">
            <div>
                <ul class="fl clear">
                    <?php if (!isset($_SESSION["id"])) { ?>
                        <li><a href="../member/login/login.php">로그인</a></li>
                        <li><a href="../member/join/join.php">회원가입</a></li>
                    <?php } else { ?>
                        <li><a
                                href="../mypage/index.php"><?= htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8') ?></a>
                            님
                        </li>
                        <li><a href="../mypage/index.php">마이페이지</a></li>
                        <li><a href="../member/login/logout.php">로그아웃</a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </header>
    <main>
        <div>
            <form method="GET" action="../search/search.php">
                <input type="text" class="main_search" name="search" placeholder="제목+내용 검색">
                <button type="submit" class="btn btn_blue">검색</button>
            </form>
        </div>
        <article>
            <div class="page_head clear">
                <div>
                    <h1>게시판</h1>
                </div>
            </div>

            <div class="list_array_option clear">
                <div class="right_box">
                    <div style="display:block">
                        <button type="button" class="btn btn_blue"
                            onclick="location.href='../board/write.php'">글쓰기</button>
                    </div>
                </div>
            </div>

            <div class="board_list_wrap">
                <table class="board_list">
                    <caption>게시판 리스트</caption>
                    <colgroup>
                        <col style="width: 7%">
                        <col>
                        <col style="width: 18%">
                        <col style="width: 6%">
                        <col style="width: 6%">
                        <col style="width: 6%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th scope="col">번호</th>
                            <th scope="col">제목</th>
                            <th scope="col">작성자</th>
                            <th scope="col">작성일</th>
                            <th scope="col">조회수</th>
                            <th scope="col">추천</th>
                        </tr>
                    </thead>
                    <tbody>
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
                                <td><?= $row['recommend_up'] ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="paging_wrap">
                <div class="paging_box">
                    <?php if ($now_block > 1): ?>
                        <a href="index.php?page=<?= $s_page - 1 ?>">이전블록</a>
                    <?php else: ?>
                    <?php endif; ?>

                    <?php if ($page > 1): ?>
                        <a href="index.php?page=<?= $page - 1 ?>">이전</a>
                    <?php else: ?>
                    <?php endif; ?>

                    <?php for ($i = $s_page; $i <= $e_page; $i++): ?>
                        <?php if ($i == $page): ?>
                            <em><?= $i ?></em>
                        <?php else: ?>
                            <a href="index.php?page=<?= $i ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_page): ?>
                        <a href="index.php?page=<?= $page + 1 ?>">다음</a>
                    <?php else: ?>
                    <?php endif; ?>

                    <?php if ($now_block < $total_block): ?>
                        <a href="index.php?page=<?= $e_page + 1 ?>">다음블록</a>
                    <?php else: ?>
                    <?php endif; ?>
                </div>
            </div>
        </article>
    </main>
    <footer class="footer">
        <div class="info_policy">
            <a href="">회사소개</a>
            <a href="">제휴안내</a>
            <a href="">광고안내</a>
            <a href="">이용약관</a>
            <a href="">개인정보처리방침</a>
            <a href="">청소년보호정책</a>
        </div>
        <div class="copyright">Copyright ⓒ</div>
    </footer>
</body>

</html>