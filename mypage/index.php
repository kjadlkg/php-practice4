<?php
session_start();
include "../resource/db.php";

if (!isset($_SESSION['id'], $_SESSION['name'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='../member/login/login.php';</script>";
    exit;
}

$id = $_SESSION['id'];
$name = $_SESSION['name'];

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
SELECT c.*, b.board_title
FROM comment c
JOIN board b ON c.board_id = b.board_id
WHERE c.comment_writer = ?
ORDER BY c.comment_id DESC
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
    <link rel="stylesheet" href="../resource/css/base.css">
    <link rel="stylesheet" href="../resource/css/common.css">
    <link rel="stylesheet" href="../resource/css/component.css">
    <link rel="stylesheet" href="../resource/css/contents.css">
    <link rel="stylesheet" href="../resource/css/page/mypage.css">
</head>

<body>
    <div class="innerbox">
        <div id="top" class="mypage_wrap width1160 home">
            <div class="headbox">
                <div class="head">
                    <a href="../main/index.php" class="main_page">
                        메인페이지 가기
                        <em class="icon_main_page">▶</em>
                    </a>
                    <div class="area_links clear">
                        <ul class="fl clear">
                            <li class="area_nick">
                                <a href="javascript:;" class="btn_user_data"><?= $name ?>님</a>
                                <div class="user_data" style="display: none;">
                                    <ul class="user_data_list">
                                        <li><a href="index.php">마이페이지</a></li>
                                        <li><a href="info.php">내 정보</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <a class="btn_top_logout" href="../member/login/logout.php">로그아웃</a>
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
                                <li class="home on"><a href="index.php">홈</a></li>
                                <li class="posting"><a href="posting.php">게시글</a></li>
                                <li class="comment"><a href="comment.php">댓글</a></li>
                            </ul>
                        </div>
                        <div class="wrap_right">
                            <section>
                                <!-- 게시글 -->
                                <section>
                                    <div class="mypage_content">
                                        <header>
                                            <div class="content_head clear">
                                                <h2 class="title" onclick="location.href='posting.php'">
                                                    게시글<span class="num">(<?= $post_count ?>)</span>
                                                </h2>
                                            </div>
                                        </header>
                                        <div class="content_box">
                                            <ul class="content_listbox">
                                                <?php if ($board_result->num_rows == 0): ?>
                                                    <br>
                                                    <p>작성한 글이 없습니다</p><br><br><br><br><br><br>
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
                                                                </div>
                                                            </div>
                                                        </li>
                                                    <?php endwhile; ?>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </section>
                                <!-- 댓글 -->
                                <section>
                                    <div class="mypage_content comments">
                                        <header>
                                            <div class="content_head clear">
                                                <h2 class="title" onclick="location.href='comment.php'">
                                                    댓글<span class="num">(<?= $comment_count ?>)</span>
                                                </h2>
                                            </div>
                                        </header>
                                        <div class="content_box">
                                            <ul class="content_listbox">
                                                <?php if ($comment_result->num_rows == 0): ?>
                                                    <br>
                                                    <p>작성한 댓글이 없습니다</p><br><br><br><br><br><br>
                                                <?php else: ?>
                                                    <?php while ($row = $comment_result->fetch_assoc()) { ?>
                                                        <li>
                                                            <div class="content">
                                                                <div class="board_linkbox">
                                                                    <a class="link"
                                                                        href="../board/view.php?id=<?= htmlspecialchars($row['board_id']) ?>"
                                                                        target="_blank">
                                                                        <p class="text">
                                                                            <?= htmlspecialchars($row['comment_content'], ENT_QUOTES, 'UTF-8'); ?>
                                                                        </p>
                                                                        <div class="datebox">
                                                                            <span><?= date("Y.m.d", strtotime($row['created_at'])) ?></span>
                                                                        </div>
                                                                        <div class="boardtitle">
                                                                            <strong>
                                                                                <?= htmlspecialchars($row['board_title'], ENT_QUOTES, 'UTF-8') ?>
                                                                            </strong>
                                                                        </div>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    <?php } ?>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </section>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(function () {
            $('.btn_user_data').on('click', function (e) {
                e.stopPropagation();
                var $btn = $(this);
                var $menu = $btn.siblings('.user_data');

                $btn.toggleClass('on');

                if ($btn.hasClass('on')) {
                    $menu.show();
                } else {
                    $menu.hide();
                }
            });

            // 외부 클릭 시 닫힘
            $(document).on('click', function () {
                $('.btn_user_data').removeClass('on');
                $('.user_data').hide();
            });

            // 내부 클릭 시 닫힘 방지
            $('.user_data').on('click', function (e) {
                e.stopPropagation();
            });
        });
    </script>
</body>

</html>