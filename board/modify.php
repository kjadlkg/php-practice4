<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../resource/db.php";
include "../resource/function.php";

$is_login = isset($_SESSION['id']) && !empty($_SESSION['id']);
$loginUser = isset($_SESSION['id']) ?? '';

$board_id = $_GET['id'] ?? null;
$step = $_GET['step'] ?? null;
$valid_step = ['check', 'edit'];

if (!$board_id || !is_numeric(value: $board_id)) {
    echo "<script>alert('잘못된 접근입니다.'); location.href='../main/index.php';</script>";
    exit;
}

if (!in_array($step, $valid_step, true)) {
    echo "<script>alert('잘못된 접근입니다.'); location.href='../main/index.php';</script>";
    exit;
}

$board_id = (int) $board_id;

$stmt = $db->prepare("SELECT * FROM board WHERE board_id = ?");
$stmt->bind_param("i", $board_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_array(MYSQLI_ASSOC);
$stmt->close();

if (!$row) {
    echo "<script>alert('게시글이 존재하지 않습니다.'); location.href='../main/index.php';</script>";
    exit;
}

$boardPw = $row['board_pw'];
$boardWriter = $row['board_writer'];
$boardTitle = htmlspecialchars($row['board_title'], ENT_QUOTES, 'UTF-8');
$boardContent = htmlspecialchars($row['board_content'], ENT_QUOTES, 'UTF-8');

$skipPasswordCheck = $is_login && $loginUser === $boardWriter;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $step = $_POST['step'] ?? null;

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        echo "<script>alert('잘못된 접근입니다.'); location.href='../main/index.php';</script>";
        exit;
    }

    if ($step === 'check' && !$skipPasswordCheck) {
        try {
            $inputPw = filter_input(INPUT_POST, 'pw', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
                ?: throw new Exception('비밀번호를 입력해주세요.');

            if (!password_verify($inputPw, $boardPw)) {
                throw new Exception('비밀번호가 일치하지 않습니다. 다시 시도해주세요.');
            }

            $_SESSION['board_pw'][$board_id] = true;

            header("Location: modify.php?id={$board_id}&step=edit");
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            exit;

        } catch (Exception $e) {
            $error_message = $e->getMessage();
            echo "<script>alert('$error_message'); history.back();</script>";
            exit;
        }
    }

    if ($step === 'edit') {
        try {
            if ($skipPasswordCheck && empty($boardPw)) {
                if (!$is_login || $loginUser !== $boardWriter) {
                    throw new Exception('수정 권한이 없습니다.');
                }
            }

            $title = filter_input(INPUT_POST, 'title') ?: throw new Exception('제목을 입력해주세요.');
            $content = filter_input(INPUT_POST, 'content') ?: throw new Exception('내용을 입력해주세요.');

            $title = htmlspecialchars(strip_tags($_POST['title']), ENT_QUOTES, 'UTF-8');
            $content = htmlspecialchars(strip_tags($_POST['content']), ENT_QUOTES, 'UTF-8');

            $stmt = $db->prepare("UPDATE board SET board_title = ?, board_content = ? WHERE board_id = ?");
            $stmt->bind_param("ssi", $title, $content, $board_id);

            if (!$stmt->execute()) {
                throw new Exception('오류가 발생했습니다.');
            }

            header("Location: view.php?id=" . $board_id);
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
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $boardTitle ?></title>
    <link rel="stylesheet" href="../resource/css/base.css">
    <link rel="stylesheet" href="../resource/css/common.css">
    <link rel="stylesheet" href="../resource/css/component.css">
    <link rel="stylesheet" href="../resource/css/contents.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="../resource/css/summernote/summernote-lite.css">
    <script src="../resource/js/summernote/summernote-lite.js"></script>
    <script src="../resource/js/summernote/lang/summernote-ko-KR.js"></script>
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
                        <?php if (!isset($_SESSION['id'])): ?>
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
        <main id="container" class="clear">
            <section>
                <header>
                    <div class="page_head clear">
                        <div class="fl clear">
                            <h2>
                                <a href="../main/index.php">게시판</a>
                            </h2>
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
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
                    <?php if ($step === 'check'): ?>
                        <input type="hidden" name="step" value="check">
                        <article>
                            <div class="nonmember_wrap">
                                <div class="nonmember_content">
                                    <h3 class="blind">비회원 글 수정, 삭제</h3>
                                    <div class="inner">
                                        <b class="text">비밀번호를 입력하세요.</b>
                                        <input type="password" class="password" name="pw">
                                        <div class="btn_box">
                                            <button type="button" class="btn btn_grey small"
                                                onclick="history.back()">취소</button>
                                            <button type="submit" class="btn btn_blue small">확인</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php else: ?>
                        <input type="hidden" name="step" value="edit">
                        <article id="write_wrap" class="clear">
                            <legend class="blind">글쓰기 영역</legend>
                            <div class="clear">
                                <fieldset>
                                    <div class="input_box input_write_title">
                                        <input class="input_text" type="text" name="title" value="<?= $boardTitle ?>">
                                    </div>
                                </fieldset>
                                <div class="write_infobox">
                                    <?php if (!isset($_SESSION['id'])): ?>
                                        <p>※ 쉬운 비밀번호는 타인이 수정, 삭제가 쉽습니다.</p>
                                    <?php endif; ?>
                                    <p>※ 음란물, 차별, 비하, 혐오 및 초상권, 저작권 침해 게시물은 민, 형사상의 책임을 질 수 있습니다.
                                        <button type="button">[저작권법 안내]</button>
                                        <button type="button">[게시물 이용 안내]</button>
                                    </p>
                                </div>
                            </div>
                            <textarea id="summernote" name="content"><?= $boardContent ?></textarea>
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
                                    onclick="location.href='view.php?id=<?= $board_id ?>'">취소</button>
                                <button type="submit" class="btn btn_blue">수정</button>
                            </div>
                        </article>
                    <?php endif; ?>
                </form>
            </section>
        </main>
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