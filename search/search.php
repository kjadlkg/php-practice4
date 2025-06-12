<?php
include "../db.php";
include "../function.php";

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search) {
    $stmt = $db->prepare("SELECT * FROM board WHERE board_title LIKE ? OR board_content LIKE ?");
    $search_text = "%$search%";
    $stmt->bind_param("ss", $search_text, $search_text);
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
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/component.css">
    <link rel="stylesheet" href="../css/contents.css">
</head>

<body>
    <div id="top" class="width1160 integrate_search_wrap">
        <header class="header typea">
            <div class="head">
                <h1 class="logo">
                    <a href="../main/index.php">메인페이지</a>
                </h1>
                <div class="search_wrap">
                    <h2 class="blind">메인 검색</h2>
                    <form method="GET">
                        <fieldset>
                            <legend class="blind">통합 검색</legend>
                            <div class="top_search clear">
                                <div class="inner_search">
                                    <input type="text" class="in_keyword" name="search" placeholder="제목+내용 검색"
                                        value="<?= $search ?>">
                                </div>
                                <button type="submit" class="btn_search">검색</button>
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
            <nav class="gnb clear">
                <h2 class="blind">GNB</h2>
                <ul class="gnb_list clear">

                </ul>
            </nav>
        </div>
        <main id="container" class="integrate_search">
            <div class="group clear">
                <section class="left_content">
                    <h2 class="blind">왼쪽 컨텐츠 영역</h2>
                </section>
                <section class="center_content">
                    <h2 class="blind">가운데 컨텐츠 영역</h2>
                    <div class="inner">
                        <div class="integrate_content search_result">
                            <div class="integrate_content_head clear">
                                <h3 class="fl">게시물</h3>
                                <div class="btn_sort_box fr">
                                    <button type="button" class="btn_sort new">최신순</button>
                                    <button type="button" class="btn_sort accuracy">정확도순</button>
                                </div>
                            </div>
                            <ul class="search_result_list">
                                <?php if ($result->num_rows == 0): ?>
                                    <p>관련 게시물이 없습니다</p>
                                <?php else: ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <?php
                                        $board_id = htmlspecialchars($row['board_id']);
                                        $created_at = htmlspecialchars($row['created_at']);
                                        $board_title = htmlspecialchars($row['board_title'], ENT_QUOTES, 'UTF-8');
                                        $board_content = htmlspecialchars($row['board_content'], ENT_QUOTES, 'UTF-8');

                                        $highlight_title = highlight_keywords($board_title, $search);
                                        $highlight_content = highlight_keywords($board_content, $search);
                                        ?>
                                        <li class="clear">
                                            <a href="../board/view.php?id=<?= $board_id ?>" target="_blank" class="title_text">
                                                <?= $highlight_title ?>
                                            </a>
                                            <p>
                                                <?= $highlight_content ?>
                                            </p>
                                            <span class="date_time"><?= $created_at ?></span>
                                        </li>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="intergrate_bottom_search">
                        <!-- 페이징 -->
                        <form method="GET">
                            <fieldset>
                                <legend class="blind">통합검색</legend>
                                <div class="bottom_search_wrap clear">
                                    <div class="bottom_search fl clear">
                                        <div class="inner_search">
                                            <input type="text" class="in_keyword" name="search" value="<?= $search ?>">
                                        </div>
                                        <button type="submit" class="btn_search">검색</button>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </section>
                <section class="right_content">
                    <h2 class="blind">오른쪽 컨텐츠 영역</h2>
                </section>
            </div>
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
    </div>
</body>

</html>