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
$board_stmt = $db->prepare("
SELECT b.*,
(SELECT COUNT(*) FROM comment WHERE board_id = b.board_id) AS comment_count
FROM board b
ORDER BY b.board_id DESC
LIMIT ?, ?
");
$board_stmt->bind_param("ii", $start, $list_num);
$board_stmt->execute();
$board_result = $board_stmt->get_result();
$posts = [];

while ($row = $board_result->fetch_assoc()) {
    $created_at = strtotime($row['created_at']);
    $now = time();

    if (date("Y", $created_at) === date("Y", $now)) {
        if (date("Y-m-d", $created_at) === date("Y-m-d", $now)) {
            $formatted_time = date("H:i", $created_at);
        } else {
            $formatted_time = date("m.d", $created_at);
        }
    } else {
        $formatted_time = date("y.m.d", $created_at);
    }


    $posts[] = [
        'board_id' => $row['board_id'],
        'comment_count' => $row['comment_count'],
        'board_views' => $row['board_views'],
        'recommend_up' => $row['recommend_up'],
        'user_ip' => !empty($row['ip']) ? mask_ip($row['ip']) : '',
        'created_at' => $formatted_time,
        'board_title' => htmlspecialchars($row['board_title'], ENT_QUOTES, 'UTF-8'),
        'board_writer' => htmlspecialchars($row['board_writer'], ENT_QUOTES, 'UTF-8')
    ];
}
$board_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시판</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/component.css">
    <link rel="stylesheet" href="../css/contents.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/popup.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div id="top" class="main_wrap width1160">
        <header class="header typea">
            <div class="head">
                <h1 class="logo">
                    <a href="index.php">메인페이지</a>
                </h1>
                <div class="search_wrap">
                    <form method="GET" action="../search/search.php">
                        <h2 class="blind">메인 검색</h2>
                        <fieldset>
                            <legend class="blind">통합 검색</legend>
                            <div class="top_search clear">
                                <div class="inner_search">
                                    <input type="text" class="in_keyword" name="search" placeholder="제목+내용 검색">
                                </div>
                                <button type="submit" class="btn btn_search">검색</button>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div class="area_links clear">
                    <ul class="fl clear">
                        <?php if (!isset($_SESSION["id"])): ?>
                            <li><a href="../member/login/login.php">로그인</a></li>
                            <li><a href="../member/join/join.php">회원가입</a></li>
                        <?php else: ?>
                            <li class="area_nick"><a href="javascript:;" class="btn_user_data"><?= $_SESSION['name'] ?>님</a>
                            </li>
                            <li><a href="../mypage/index.php">마이페이지</a></li>
                            <li><a class="btn_top_logout" href="../member/login/logout.php">로그아웃</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </header>
        <div class="gnb_bar">
            <div class="gnb clear">
                <h2 class="blind">GNB</h2>
            </div>
        </div>
        <div class="visit_bookmark">
            <div class="visit_history">

            </div>
        </div>
        <div class="main_inner_wrap">
            <main id="container" class="list_wrap clear">
                <section class="left_content">
                    <header>
                        <div class="page_head clear">
                            <div class="fl clear">
                                <h2>게시판</h2>
                            </div>
                        </div>
                    </header>
                    <article>
                        <h2 class="blind">게시판 이슈 박스</h2>
                        <div class="issue_wrap">
                            <div class="issuebox">
                                <div class="issue_contentbox clear">
                                </div>
                            </div>
                        </div>
                    </article>
                    <article>
                        <h2 class="blind">게시판 리스트 영역</h2>
                        <div class="list_array_option clear">
                            <div class="array_tab left_box">
                                <button type="button" class="on">전체글</button>
                            </div>
                            <div class="center_box"></div>
                            <div class="right_box">
                                <div class="fr clear">
                                    <div class="select_box array_num">
                                        <select>
                                            <option value="30">30개</option>
                                            <option value="50">50개</option>
                                            <option value="100">100개</option>
                                        </select>
                                        <div class="select_area">
                                            <a href="#" onclick=""></a>
                                        </div>
                                        <ul>
                                            <li></li>
                                            <li></li>
                                            <li></li>
                                        </ul>
                                    </div>
                                    <div class="btn_box">
                                        <button type="button" class="btn_write text"
                                            onclick="location.href='../board/write.php'">글쓰기</button>
                                    </div>
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
                                    <?php foreach ($posts as $post): ?>
                                        <tr>
                                            <td class="board_num"><?= $post['board_id'] ?></td>
                                            <td class="board_title">
                                                <a href="../board/view.php?id=<?= $post['board_id'] ?>">
                                                    <em></em>
                                                    <?= $post['board_title'] ?>
                                                </a>
                                                <a class="comment_numbox"
                                                    href="../board/view.php?id=<?= $post['board_id'] ?>">
                                                    <span class="comment_num">[<?= $post['comment_count'] ?>]</span>
                                                </a>
                                            </td>
                                            <td class="board_writer">
                                                <?= $post['board_writer'] ?>
                                                <?php if (!empty($post['user_ip'])): ?>
                                                    (<?= $post['user_ip'] ?>)
                                                <?php endif; ?>
                                            </td>
                                            <td class="board_date"><?= $post['created_at'] ?></td>
                                            <td class="board_count"><?= $post['board_views'] ?></td>
                                            <td class="board_recommend"><?= $post['recommend_up'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="list_bottom_btnbox">
                            <div class="fl">
                                <button type="button" class="btn btn_blue">전체글</button>
                            </div>
                            <div class="fr">
                                <button type="button" class="btn btn_blue"
                                    onclick="location.href='../board/write.php'">글쓰기</button>
                            </div>
                        </div>
                        <div class="bottom_paging_wrap">
                            <div class="bottom_paging_box">
                                <?php if ($now_block > 1): ?>
                                    <a href="index.php?page=1">맨처음</a>
                                <?php else: ?>
                                <?php endif; ?>

                                <?php if ($now_block > 1): ?>
                                    <a href="index.php?page=<?= $s_page - 1 ?>">이전블록</a>
                                <?php else: ?>
                                <?php endif; ?>

                                <?php for ($i = $s_page; $i <= $e_page; $i++): ?>
                                    <?php if ($i == $page): ?>
                                        <em><?= $i ?></em>
                                    <?php else: ?>
                                        <a href="index.php?page=<?= $i ?>"><?= $i ?></a>
                                    <?php endif; ?>
                                <?php endfor; ?>

                                <?php if ($now_block < $total_block): ?>
                                    <a href="index.php?page=<?= $e_page + 1 ?>">다음블록</a>
                                <?php else: ?>
                                <?php endif; ?>

                                <?php if ($now_block < $total_block): ?>
                                    <a href="index.php?page=<?= $total_block ?>">맨끝</a>
                                <?php else: ?>
                                <?php endif; ?>
                            </div>
                            <div class="bottom_movebox">
                                <button type="button" class="btn_grey_roundbg btn_shortmove"
                                    onclick="$('.move_page_layer').show();">페이지 이동</button>
                            </div>
                            <div class="pop_wrap type3 move_page_layer" style="top: -121px; right: 0; display: none;">
                                <div class="pop_content shortmove">
                                    <div class="pop_head">
                                        <h3>페이지 이동</h3>
                                    </div>
                                    <div class="pop_inner page">
                                        <div class="hint_text">이동할 페이지 번호를 입력해주세요</div>
                                        <div class="moveset">
                                            <span class="title">페이지</span>
                                            <input type="text" name="move_page" value="<?= $page ?>">
                                            <span class="num total_page">
                                                <?= $total_page ?>
                                            </span>
                                            <button type="button" class="btn btn_blue small btn_move_page">이동</button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn_pop_layer_close"
                                    onclick="$('.move_page_layer').hide();">
                                    <span>레이어 닫기</span>
                                </button>
                            </div>
                        </div>
                    </article>
                </section>
                <section class="right_content">
                    <h2 class="blind">오른쪽 컨텐츠 영역</h2>
                    <div class="login_box">
                        <div class="user_info">
                            <strong onclick="location.href='../member/login/login.php'" style="cursor: pointer">로그인
                                해주세요</strong>
                        </div>
                        <div class="user_option">
                            <span>
                                <a href="javascript:;" onclick="alert('로그인이 필요합니다.');">마이페이지</a>
                            </span>
                            <span>
                                <a href="javascript:;" onclick="alert('로그인이 필요합니다.');">즐겨찾기</a>
                            </span>
                            <span>
                                <a>알림</a>
                            </span>
                        </div>
                    </div>
                </section>
            </main>
        </div>
        <footer class="footer">
            <div class="board_all">
                <div class="all_box">
                    <div class="all_list"></div>
                    <div class="all_bottom">
                        <span class="bottom_menu">
                            <a class="menu_link" href="#top">
                                <em></em>
                                맨위로
                            </a>
                        </span>
                    </div>
                </div>
            </div>
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
</body>

</html>