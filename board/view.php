<?php
session_start();
include "../db.php";
include "../function.php";

$board_id = $_GET['id'] ?? null;
$is_login = isset($_SESSION['id']);

if (!$board_id || !is_numeric(value: $board_id)) {
    echo "<script>alert('잘못된 접근입니다.'); location.href='../main/index.php';</script>";
    exit;
}

$board_id = (int) $board_id;

// 조회수 계산
$stmt = $db->prepare("UPDATE board SET board_views = board_views + 1 WHERE board_id = ?");
$stmt->bind_param("i", $board_id);
$stmt->execute();
$stmt->close();

// board 게시물
$stmt = $db->prepare(
    "SELECT b.*, u.user_id
        FROM board b
        JOIN user u
        ON b.board_writer = u.user_name
        WHERE b.board_id = ?"
);
$stmt->bind_param("i", $board_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_array(MYSQLI_ASSOC);
$stmt->close();

if (!$row) {
    echo "<script>alert('존재하지 않는 게시글입니다.'); location.href='../main/index.php';</script>";
    exit;
}

$user_ip = !empty($row['ip']) ? mask_ip($row['ip']) : '';
$board_writer = $row['board_writer'];
$board_pw = $row['board_pw'];
$board_views = $row['board_views'];
$recommend = $row['recommend_up'];
$created_at = date("Y.m.d H:m:s", strtotime($row['created_at']));
$board_title = htmlspecialchars($row['board_title'], ENT_QUOTES, 'UTF-8');
$board_content = htmlspecialchars($row['board_content'], ENT_QUOTES, 'UTF-8');
$is_writer = $is_login && $_SESSION['id'] == $row['user_id'];

// comment 댓글
$stmt = $db->prepare("
    SELECT c.*, u.user_name
    FROM comment c LEFT JOIN user u
    ON c.comment_writer = u.user_name
    WHERE c.board_id = ? ORDER BY c.created_at
    ");
$stmt->bind_param("i", $board_id);
$stmt->execute();
$comment_result = $stmt->get_result();
$comments = [];

while ($comment_row = $comment_result->fetch_assoc()) {
    $comment_date = strtotime($comment_row['created_at']);
    $now = time();

    if (date("Y", $comment_date) === date("Y", $now)) {
        $formatted_time = date("m.d H:m:s", $comment_date);
    } else {
        $formatted_time = date("y.m.d H:m:s", $comment_date);
    }

    $comments[] = [
        'comment_writer' => htmlspecialchars($comment_row['comment_writer'], ENT_QUOTES, 'UTF-8'),
        'comment_content' => nl2br(htmlspecialchars($comment_row['comment_content'], ENT_QUOTES, 'UTF-8')),
        'user_ip' => !empty($comment_row['ip']) ? mask_ip($comment_row['ip']) : '',
        'created_at' => $formatted_time,
    ];
}
$stmt->close();


// comment total 댓글 수 
$stmt = $db->prepare("SELECT COUNT(*) AS total FROM comment WHERE board_id = ?");
$stmt->bind_param("s", $board_id);
$stmt->execute();
$count_result = $stmt->get_result();
$count = $count_result->fetch_assoc()['total'];
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $board_title ?></title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/component.css">
    <link rel="stylesheet" href="../css/contents.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div id="top" class="width1160 view_wrap">
        <header class="header">
            <div class="head">
                <h1 class="logo">
                    <a href="../main/index.php">메인페이지</a>
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
            <div class="gnb clear">
                <h2 class="blind">GNB</h2>
            </div>
        </div>
        <div class="visit_bookmark">
            <div class="visit_history">

            </div>
        </div>
        <div class="main_inner_wrap">
            <main id="container" class="board_view clear">
                <section>
                    <header>
                        <div class="page_head clear">
                            <div class="fl clear">
                                <h2>
                                    <a href="../main/index.php">게시판</a>
                                </h2>
                            </div>
                            <div class="fr issuebox">
                            </div>
                        </div>
                    </header>
                    <article>
                        <h2 class="blind">게시판 이슈 박스</h2>
                        <div class="issue_wrap">
                            <div class="issuebox">
                            </div>
                        </div>
                    </article>
                    <article>
                        <h2 class="blind">본문 영역</h2>
                        <div class="view_content_wrap">
                            <header>
                                <div class="view_head clear">
                                    <h3 class="view_title">
                                        <span class="title_subject"><?= $board_title ?></span>
                                    </h3>
                                    <div class="view_writer">
                                        <div class="fl">
                                            <span class="nickname">
                                                <?= $board_writer ?>
                                                <?php if (!empty($user_ip)): ?>
                                                    (<?= $user_ip ?>)
                                                <?php endif; ?>
                                            </span>
                                            <span class="date"><?= $created_at ?></span>
                                        </div>
                                        <div class="fr">
                                            <span class="view">조회 <?= $board_views ?></span>
                                            <span class="recommend">추천 <?= $recommend ?></span>
                                            <span class="comment">
                                                <a href="#comment">댓글 <?= $count ?></a>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </header>
                            <div class="view_content">
                                <div class="inner clear">
                                    <div class="view_writing_box">
                                        <?= $board_content ?>
                                    </div>
                                </div>
                                <?php if (!$is_login): ?>
                                    <div class="recommend_kcaptcha">
                                        <div class="kcaptcha_img">
                                            <img src="../captcha_image.php?<?= time() ?>" class="kcaptcha" alt="KCAPTCHA">
                                        </div>
                                        <input type="text" id="captcha_input" name="captcha"
                                            class="recommend_kcaptcha_input" placeholder="코드입력">
                                    </div>
                                <?php endif; ?>
                                <div class="recommend_box">
                                    <h3 class="blind">추천</h3>
                                    <div class="innerbox">
                                        <div class="inner">
                                            <div class="up_num_box">
                                                <p class="up_num font_red" id="recom_up_count">
                                                    <?= $row['recommend_up'] ?>
                                                </p>
                                            </div>
                                            <button type="button" class="btn_recom_up"
                                                onclick="recommend(<?= $id ?>, 'up')">
                                                <span>추천</span>
                                            </button>
                                        </div>
                                        <div class="inner">
                                            <button type="button" class="btn_recom_down"
                                                onclick="recommend(<?= $id ?>, 'down')">
                                                <span>비추천</span>
                                            </button>
                                            <div class="down_num_box">
                                                <p id="recom_down_count"><?= (int) $row['recommend_down'] ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="recommend_bottom_box">
                                        <button type="button" class="btn_sns">공유</button>
                                        <button type="button" class="btn_report">신고</button>
                                    </div>
                                </div>
                                <div id="comment" class="attached_file_box">
                                    <strong>원본 첨부 파일<em class="font_red"></em></strong>
                                    <ul class="attached_file"></ul>
                                </div>
                            </div>
                            <div class="view_comment_wrap">
                                <h2 class="blind">댓글 영역</h2>
                                <div class="comment_wrap show">
                                    <div class="comment_count">
                                        <div class="fl">
                                            전체 댓글
                                            <em class="font_red"><?= $count ?></em>개
                                            <div class="comment_sort">
                                                <span class="radiobox">
                                                    <input type="radio" id="radio1" name="commentSort"
                                                        checked="checked">
                                                    <em>√</em>
                                                    <label for="radio1">등록순</label>
                                                </span>
                                                <span class="radiobox">
                                                    <input type="radio" id="radio2" name="commentSort">
                                                    <em>√</em>
                                                    <label for="radio2">최신순</label>
                                                </span>
                                                <span class="radiobox">
                                                    <input type="radio" id="radio3" name="commentSort">
                                                    <em>√</em>
                                                    <label for="radio3">답글순</label>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="fr">
                                            <a href="#container" class="containerGo opt">본문 보기</a>
                                            <button type="button" class="btn_comment_close opt">
                                                <span>댓글닫기</span>
                                                <em></em>
                                            </button>
                                            <button type="button" class="btn_comment_refresh opt">새로고침</button>
                                        </div>
                                    </div>
                                    <div class="comment_box">
                                        <ul class="comment_list">
                                            <?php foreach ($comments as $comment): ?>
                                                <li>
                                                    <div class="comment_info">
                                                        <div class="comment_nickbox">
                                                            <span class="view_writer">
                                                                <span class="nickname">
                                                                    <em><?= $comment['comment_writer'] ?></em>
                                                                </span>
                                                            </span>
                                                        </div>
                                                        <div class="comment_textbox btn_reply_write_all clear">
                                                            <p class="comment_text">
                                                                <?= $comment['comment_content'] ?>
                                                            </p>
                                                        </div>
                                                        <div class="fr clear">
                                                            <span class="date">
                                                                <?= $comment['created_at'] ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <div class="bottom_paging_box">
                                            <div class="comment_paging">
                                                <em></em>
                                            </div>
                                            <div class="comment_option">
                                                <a href="#container" class="containerGo opt">본문 보기</a>
                                                <button type="button" class="btn_comment_close opt">
                                                    <span>댓글닫기</span>
                                                    <em></em>
                                                </button>
                                                <button type="button" class="btn_comment_refresh opt">새로고침</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="comment_write_box clear">
                                    <div class="fl">
                                        <?php if ($is_login): ?>
                                            <div class="user_info_input">
                                                <input type="text" name="name" value="<?= $_SESSION['name'] ?>"
                                                    maxlength="20">
                                            </div>
                                        <?php else: ?>
                                            <div class="user_info_input">
                                                <input type="text" name="name" placeholder="닉네임" maxlength="20">
                                            </div>
                                            <div class="user_info_input">
                                                <input type="password" name="pw" placeholder="비밀번호" maxlength="20">
                                            </div>
                                            <div class="user_info_input">
                                                <input type="text" name="captcha" placeholder="코드입력">
                                            </div>
                                            <div class="kcaptcha_img">
                                                <img src="../captcha_image.php?<?= time() ?>" class="kcaptcha"
                                                    alt="KCAPTCHA">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="comment_text_content">
                                        <div class="comment_write">
                                            <textarea name="content" autocomplete="off" maxlength="400"></textarea>
                                        </div>
                                        <div class="comment_write_bottom">
                                            <div class="fr">
                                                <button type="button" class="btn btn_blue small">등록</button>
                                                <button type="button" class="btn btn_lightblue small">등록+추천</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="view_bottom_btnbox clear">
                            <div class="fl">
                                <button type="button" class="btn btn_blue"
                                    onclick="location.href='../main/index.php'">전체글</button>
                            </div>
                            <div class="fr">
                                <button type="button" class="btn btn_blue"
                                    onclick="location.href='write.php'">글쓰기</button>
                            </div>
                        </div>
                    </article>
                    <article>
                        <h2 class="blind">하단 게시판 리스트 영역</h2>
                    </article>
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
<script>
    function checkDeletePassword(form) {
        const password = form.querySelector('input[name="pw"]');
        if (!password || password.value.trim() === "") {
            alert("비밀번호를 입력하세요.");
            password.focus();
            return false;
        }
        return true;
    }

    function confirm_empty(form) {
        const isLogin = <?= isset($_SESSION['id']) ? 'true' : 'false' ?>;
        const username = form.querySelector('input[name = "name"]');
        const password = form.querySelector('input[name = "pw"]');
        const content = form.querySelector('textarea[name = "content"]');

        if (!isLogin) {
            if (username && username.value.trim() === "") {
                alert("닉네임을 입력하세요.");
                username.focus();
                return false;
            }

            if (password && password.value.trim() === "") {
                alert("비밀번호를 입력하세요.");
                password.focus();
                return false;
            }
        }

        if (content && content.value.trim() === "") {
            alert("내용을 입력하세요.");
            content.focus();
            return false;
        }

        return true;
    }

    function showPasswordForm(id) {
        document.getElementById('delete-box-' + id).style.display = 'inline-block';
    }

    function hidePasswordForm(id) {
        document.getElementById('delete-box-' + id).style.display = 'none';
    }

    const kcaptchaImg = document.getElementsByClassName("kcaptcha");

    kcaptchaImg.addEventListener('click', function () {
        this.src = '../captcha_image.php?' + new Date().getTime();
    });

    function recommend(boardId, type) {
        if (!['up', 'down'].includes(type)) return;

        const isLogin = <?= json_encode($is_login) ?>;
        let captcha = '';
        if (!isLogin) {
            const captchaInput = document.getElementById('captcha_input');
            if (!captchaInput || captchaInput.value.trim() === '') {
                alert('보안코드를 입력하세요.');
                captchaInput.focus();
                return;
            }
            captcha = encodeURIComponent(captchaInput.value.trim());
        }

        fetch('recommend.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'application/json'
            },
            body: 'board_id=' + boardId + '&type=' + type + '&captcha=' + captcha
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('서버 응답 오류: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    let upCount = document.getElementById('recom_up_count');
                    let downCount = document.getElementById('recom_down_count');
                    if (type === 'up') {
                        upCount.textContent = parseInt(upCount.textContent) + 1;
                    } else {
                        downCount.textContent = parseInt(downCount.textContent) + 1;
                    }
                    if (!isLogin) {
                        const captchaImg = document.querySelector('img[src^="../captcha_image.php"]');
                        if (captchaImg) {
                            captchaImg.src = '../captcha_image.php?' + new Date().getTime();
                        }
                        document.getElementById('captcha_input').value = '';
                    }
                } else {
                    alert(data.message || '오류가 발생했습니다.');
                }
            })
            .catch(error => {
                alert('요청 중 오류가 발생했습니다: ' + error.message);
            });

    }
</script>

</html>