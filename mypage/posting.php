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
SELECT b.*,
(SELECT COUNT(*) FROM comment WHERE board_id = b.board_id) AS comment_count
FROM board b
WHERE board_writer = ?
ORDER BY board_id DESC
");
$stmt->bind_param("s", $name);
$stmt->execute();
$board_result = $stmt->get_result();
$stmt->close();


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

$count_stmt = $db->prepare("SELECT COUNT(*) AS total FROM board WHERE board_writer = ?");
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
    <title>게시글</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/component.css">
    <link rel="stylesheet" href="../css/contents.css">
    <link rel="stylesheet" href="../css/page/mypage.css">
</head>

<body>
    <div class="innerbox">
        <div id="top" class="mypage_wrap width1160">
            <div class="headbox">
                <div class="head">
                    <a href="../main/index.php" class="main_page">
                        메인페이지 가기
                        <em class="icon_main_page">▶</em>
                    </a>
                    <div class="area_links clear">
                        <ul class="fl clear">
                            <li class="area_nick">
                                <a class="btn_user_data"><?= $name ?>님</a>
                                <div class="user_data">
                                    <ul class="user_data_list">
                                        <li><a>마이페이지</a></li>
                                        <li><a>내 정보</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <a class="btn_top_logout" href="../member/login/login.php">로그아웃</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="top_bar">
                <div id="top_bg" class="bar clear">
                </div>
            </div>
            <main id="container">
                <article>
                    <h2 class="blind">마이페이지 홈</h2>
                    <div class="content_wrap clear">
                        <div class="wrap_left">
                            <ul class="mypage_menu">
                                <li class="home"><a href="index.php">홈</a></li>
                                <li class="posting on"><a href="posting.php">게시글</a></li>
                                <li class="comment"><a href="comment.php">댓글</a></li>
                            </ul>
                        </div>
                        <div class="wrap_right">
                            <section>
                                <div class="mypage_content postings">
                                    <header>
                                        <div class="content_head clear">
                                            <div class="choice_wrap">
                                                <button type="button" class="on" onclick="location.href='posting.php'">
                                                    전체<span class="num">(<?= $total_rows ?>)</span>
                                                </button>
                                            </div>
                                            <div class="search_wrap">
                                                <div class="search_box">
                                                    <div class="search clear">
                                                        <form method="GET">
                                                            <div class="inner_search">
                                                                <input type="text" class="in_keyword"
                                                                    placeholder="게시글 제목 검색" name="keyword">
                                                            </div>
                                                            <button type="submit" class="btn_search">
                                                                <span>검색</span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </header>
                                    <div class="content_box">
                                        <div class="option_sort mypage">
                                            <div class="select_box select_arraybox ul_selectric">
                                                <div class="select_area">
                                                    전체보기
                                                    <span>정렬기준선택</span>
                                                    <em class=""></em>
                                                </div>
                                                <ul class="option_box">
                                                </ul>
                                                <input type="" name="filter">
                                            </div>
                                            <span class="greybox"></span>
                                        </div>
                                        <ul class="content_listbox">
                                            <?php if ($board_result->num_rows == 0): ?>
                                                <p>작성한 글이 없습니다</p>
                                            <?php else: ?>
                                                <?php while ($row = $board_result->fetch_assoc()): ?>
                                                    <li>
                                                        <div class="content">
                                                            <div class="board_linkbox">
                                                                <a class="link"
                                                                    href="../board/view.php?id=<?= htmlspecialchars($row['board_id']) ?>"
                                                                    target="_blank">
                                                                    <div class="boardtitle">
                                                                        <strong><?= htmlspecialchars($row['board_title'], ENT_QUOTES, 'UTF-8') ?></strong>
                                                                        <span
                                                                            class="comment_num">[<?= $row['comment_count'] ?>]</span>
                                                                    </div>
                                                                    <div class="datebox">
                                                                        <span class="date">
                                                                            <?= date("Y.m.d", strtotime($row['created_at'])) ?>
                                                                        </span>
                                                                    </div>
                                                                </a>
                                                                <button type="button" class="btn_delete btn_listdel">
                                                                    <span>삭제</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </li>
                                                <?php endwhile; ?>
                                            <?php endif; ?>
                                        </ul>
                                        <div class="bottom_paging_box">
                                            <?php if ($page > 1): ?>
                                                <a href="posting.php?page=<?= $page - 1 ?>">이전</a>
                                            <?php else: ?>
                                            <?php endif; ?>

                                            <?php for ($i = $s_page; $i <= $e_page; $i++): ?>
                                                <?php if ($i == $page): ?>
                                                    <em><?= $i ?></em>
                                                <?php else: ?>
                                                    <a href="posting.php?page=<?= $i ?>"><?= $i ?></a>
                                                <?php endif; ?>
                                            <?php endfor; ?>

                                            <?php if ($page < $total_page): ?>
                                                <a href="posting.php?page=<?= $page + 1 ?>">다음</a>
                                            <?php else: ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </section>
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
                        </div>
                    </div>
                </article>
            </main>
        </div>
    </div>
</body>

</html>