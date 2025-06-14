<?php
if (session_status() === PHP_SESSION_NONE) {
   session_start();
}
include "../resource/db.php";

if (!isset($_SESSION['id'], $_SESSION['name'])) {
   echo "<script>alert('로그인이 필요합니다.'); location.href='../member/login/login.php';</script>";
   exit;
}

$id = $_SESSION['id'];
$name = $_SESSION['name'];
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$search_keyword = "%$keyword%";

// paging
$page_num = 10;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$list_num = isset($_GET['list_num']) ? max(10, (int) $_GET['list_num']) : 10;

// 전체 게시글 수
if ($keyword) {
   $count_stmt = $db->prepare("
   SELECT COUNT(*) AS total
   FROM board
   WHERE board_writer LIKE ? AND board_content LIKE ?");
   $count_stmt->bind_param("ss", $name, $search_keyword);
} else {
   $count_stmt = $db->prepare("
   SELECT COUNT(*) AS total
   FROM board
   WHERE board_writer LIKE ?");
   $count_stmt->bind_param("s", $name);
}
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
if ($keyword) {
   $stmt = $db->prepare("
    SELECT b.*,
    (SELECT COUNT(*) FROM comment WHERE board_id = b.board_id) AS comment_count
    FROM board b
    WHERE board_writer LIKE ? AND board_title LIKE ?
    ORDER BY board_id DESC
    LIMIT ?, ?
    ");
   $stmt->bind_param("ssii", $name, $search_keyword, $start, $list_num);
} else {
   $stmt = $db->prepare("
    SELECT b.*,
    (SELECT COUNT(*) FROM comment WHERE board_id = b.board_id) AS comment_count
    FROM board b
    WHERE board_writer LIKE ?
    ORDER BY board_id DESC
    LIMIT ?, ?
    ");
   $stmt->bind_param("sii", $name, $start, $list_num);
}
$stmt->execute();
$board_result = $stmt->get_result();
$posts = [];

while ($row = $board_result->fetch_assoc()) {
   $title = htmlspecialchars($row['board_title'], ENT_QUOTES, 'UTF-8');
   if (!empty($keyword)) {
      $safe_keyword = htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8');
      $title = preg_replace(
         '/' . preg_quote($safe_keyword, '/') . '/iu',
         '<span class="mark">$0</span>',
         $title
      );
   }

   $posts[] = [
      'board_id' => $row['board_id'],
      'board_title' => $title,
      'comment_count' => $row['comment_count'],
      'created_at' => date("Y.m.d", strtotime($row['created_at']))
   ];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>게시글</title>
   <link rel="stylesheet" href="../resource/css/base.css">
   <link rel="stylesheet" href="../resource/css/common.css">
   <link rel="stylesheet" href="../resource/css/component.css">
   <link rel="stylesheet" href="../resource/css/contents.css">
   <link rel="stylesheet" href="../resource/css/page/mypage.css">
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
                        <a href="javascript:;" class="btn_user_data"><?= $name ?>님</a>
                        <div class="user_data" style="display: none;">
                           <ul class="user_data_list">
                              <li><a href="index.php">마이페이지</a></li>
                              <li><a href="info.php">내 정보</a></li>
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
                                 <div class="mypage_search_wrap">
                                    <div class="search_box">
                                       <div class="search clear">
                                          <form method="GET">
                                             <div class="inner_search">
                                                <input type="text" class="in_keyword" placeholder="게시글 제목 검색"
                                                   name="keyword" value="<?= $keyword ?>">
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
                                       <span class="blind">정렬기준선택</span>
                                       <em class="option_more">▼</em>
                                    </div>
                                    <ul class="option_box">
                                       <li onclick="location.href='posting.php'">전체보기</li>
                                    </ul>
                                 </div>
                                 <span class="greybox"></span>
                              </div>
                              <ul class="content_listbox">
                                 <?php if (empty($posts)): ?>
                                    <p>작성한 글이 없습니다</p>
                                 <?php else: ?>
                                    <?php foreach ($posts as $post): ?>
                                       <li>
                                          <div class="content">
                                             <div class="board_linkbox">
                                                <a class="link" href="../board/view.php?id=<?= $post['board_id'] ?>"
                                                   target="_blank">
                                                   <div class="boardtitle">
                                                      <strong>
                                                         <?= $post['board_title'] ?>
                                                         <span class="comment_num">[<?= $post['comment_count'] ?>]</span>
                                                   </div>
                                                   <div class="datebox">
                                                      <span class="date">
                                                         <?= $post['created_at'] ?>
                                                      </span>
                                                   </div>
                                                </a>
                                                <button type="button" class="btn_delete btn_listdel">
                                                   <span>삭제</span>
                                                </button>
                                             </div>
                                          </div>
                                       </li>
                                    <?php endforeach; ?>
                                 <?php endif; ?>
                              </ul>
                              <div class="bottom_paging_box">
                                 <?php if ($now_block > 1): ?>
                                    <a href="posting.php?keyword=<?= urlencode($keyword) ?>&page=1">맨처음</a>
                                    <a
                                       href="posting.php?keyword=<?= urlencode($keyword) ?>&page=<?= $s_page - 1 ?>">이전블록</a>
                                 <?php endif; ?>

                                 <?php for ($i = $s_page; $i <= $e_page; $i++): ?>
                                    <?php if ($i == $page): ?>
                                       <em><?= $i ?></em>
                                    <?php else: ?>
                                       <a href="posting.php?keyword=<?= urlencode($keyword) ?>&page=<?= $i ?>"><?= $i ?></a>
                                    <?php endif; ?>
                                 <?php endfor; ?>

                                 <?php if ($now_block < $total_block): ?>
                                    <a
                                       href="posting.php?keyword=<?= urlencode($keyword) ?>&page=<?= $e_page + 1 ?>">다음블록</a>
                                    <a href="posting.php?keyword=<?= urlencode($keyword) ?>&page=<?= $total_page ?>">맨끝</a>
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
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script>
      $(function () {
         // 정렬기준선택 클릭
         $('.option_more').on('click', function (e) {
            var $option_box = $('.option_box');
            if ($option_box.css('display') === 'none') {
               $option_box.show();
            } else {
               $option_box.hide();
            }
         });

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