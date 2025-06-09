<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../db.php";
include "../function.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            throw new Exception('오류가 발생했습니다.');
        }

        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
            ?: throw new Exception('제목을 입력해주세요.');
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
            ?: throw new Exception('내용을 입력해주세요.');

        $title = htmlspecialchars(strip_tags($title), ENT_QUOTES, 'UTF-8');
        $content = htmlspecialchars(strip_tags($content), ENT_QUOTES, 'UTF-8');


        $user_id = $_SESSION['id'] ?? '';
        $user_name = $_SESSION['name'] ?? '';

        if (isset($_SESSION['id'])) {
            $board_pw = null;
            $ip = null;
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
            $user_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
                ?: throw new Exception('닉네임을 입력해주세요.');
            $user_pw = filter_input(INPUT_POST, 'pw', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
                ?: throw new Exception('비밀번호를 입력해주세요.');
            $captcha_input = filter_input(INPUT_POST, 'captcha', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
                ?: throw new Exception('자동입력 방지코드를 입력해주세요.');
            $correct_code = $_SESSION['captcha_keystring'] ?? '';

            if (strtolower($captcha_input) !== strtolower($correct_code)) {
                throw new Exception('자동입력 방지코드가 일치하지 않습니다.');
            }

            unset($_SESSION['captcha_keystring']);

            $user_name = htmlspecialchars(strip_tags($user_name), ENT_QUOTES, 'UTF-8');
            $board_pw = password_hash($user_pw, PASSWORD_DEFAULT);

            $stmt = $db->prepare("SELECT COUNT(*) FROM user WHERE user_name = ?");
            $stmt->bind_param("s", $user_name);
            $stmt->execute();
            $stmt->bind_result($user_count);
            $stmt->fetch();
            $stmt->close();

            if ($user_count == 0) {
                $stmt = $db->prepare("INSERT INTO user (user_id, user_name, user_pw) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $user_id, $user_name, $board_pw);
                $stmt->execute();
                $stmt->close();
            }
        }

        $stmt = $db->prepare("INSERT INTO board (board_title, board_content, board_writer, board_writer_id, board_pw, ip) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $title, $content, $user_name, $user_id, $board_pw, $ip);

        if (!$stmt->execute()) {
            throw new Exception('오류가 발생했습니다.');
        }

        header("Location: ../main/index.php");
        exit;
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        echo "<script>alert('$error_message'); history.back();</script>";
        exit;
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>글쓰기</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/component.css">
    <link rel="stylesheet" href="../css/contents.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="../css/summernote/summernote-lite.css">
    <script src="../js/summernote/summernote-lite.js"></script>
    <script src="../js/summernote/lang/summernote-ko-KR.js"></script>
</head>

<body>
    <div id="top" class="width1160">
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
        <main id="container">
            <section>
                <header>
                    <div class="page_head clear">
                        <div class="fl clear">
                            <h2>
                                <a href="../main/index.php">게시판</a>
                            </h2>
                        </div>
                        <div class="fr"></div>
                    </div>
                </header>
                <article>
                    <h2 class="blind"></h2>
                    <div class="issue_wrap"></div>
                </article>
                <article id="write_wrap" class="clear">
                    <h2 class="blind">게시판 글쓰기 영역</h2>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
                        <div class="clear">
                            <fieldset>
                                <legend class="blind">글쓰기 입력폼</legend>
                                <?php if (!isset($_SESSION['id'])): ?>
                                    <div class="input_box input_info">
                                        <input type="text" class="input_text" name="name" value="ㅇㅇ" placeholder="닉네임"
                                            maxlength="20">
                                    </div>
                                    <div class="input_box input_info">
                                        <input type="password" class="input_text" name="pw" value="1234" placeholder="비밀번호"
                                            maxlength="20">
                                    </div>
                                    <div class="kcaptcha_img">
                                        <img src="../captcha_image.php?<?= time() ?>" alt="KCAPTCHA"
                                            onclick="this.src='../captcha_image.php?' + new Date().getTime()"
                                            style="cursor:pointer;">
                                    </div>
                                    <div class="input_box input_info input_kcaptcha">
                                        <input type="text" class="input_text" name="captcha" placeholder="코드 입력">
                                    </div>
                                <?php endif; ?>
                                <div class="input_box input_write_title">
                                    <input type="text" class="input_text" name="title" placeholder="제목을 입력하세요"
                                        autocomplete="off" maxlength="400">
                                </div>
                            </fieldset>
                            <div class="write_infobox">
                                <p>※ 쉬운 비밀번호는 타인이 수정, 삭제가 쉽습니다.</p>
                                <p>※ 음란물, 차별, 비하, 혐오 및 초상권, 저작권 침해 게시물은 민, 형사상의 책임을 질 수 있습니다.
                                    <button type="button">[저작권법 안내]</button>
                                    <button type="button">[게시물 이용 안내]</button>
                                </p>
                            </div>
                        </div>
                        <textarea id="summernote" name="content"></textarea>
                        <script>
                            $(document).ready(function () {
                                $('#summernote').summernote({
                                    height: 400,
                                    minHeight: 400,
                                    maxHeight: null,
                                    focus: false,
                                    lang: 'ko-KR',
                                    placeholder: '내용을 입력하세요.',
                                    toolbar: [
                                        ['style', ['bold', 'italic', 'underline', 'clear']],
                                        ['font', ['fontname', 'fontsize']],
                                        ['color', ['color']],
                                        ['para', ['ul', 'ol', 'paragraph']],
                                        ['table', ['table']],
                                        ['insert', ['link', 'picture']],
                                        ['view', ['undo', 'redo']]
                                    ]
                                });
                            });
                        </script>
                        <div class="btn_box write fr">
                            <button type="button" class="btn btn_grey"
                                onclick="location.href='../main/index.php'">취소</button>
                            <button type="submit" class="btn btn_blue">등록</button>
                        </div>
                    </form>
                </article>
            </section>
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