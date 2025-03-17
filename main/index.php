<?php
session_start();
include "../db.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시판</title>
</head>

<body>
    <div>
        <?php if (!isset($_SESSION["id"])) { ?>
            <div>
                <a href="../member/login/login.php">로그인</a>
                <a href="../member/join/join.php">회원가입</a>
            </div>
        <?php } else { ?>
            <div>
                <p><a href="../mypage/index.php"><?php echo $_SESSION['name']; ?></a> 님</p>
                <a href="../mypage/index.php">마이페이지</a>
                <a href="../member/login/logout.php">로그아웃</a>
            </div>
        <?php } ?>
    </div>

    <div>
        <h1>게시판</h1>

        <button onclick="writePost()">글쓰기</button>

        <table>
            <tr>
                <th>번호</th>
                <th>제목</th>
                <th>작성자</th>
                <th>작성일</th>
                <th>조회수</th>
            </tr>
            <?php
            $list_num = 10;
            $page_num = 10;
            $num = query("SELECT COUNT(*) AS total FROM board")->fetch_assoc()['total'];

            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $total_page = ceil($num / $list_num);
            $total_block = ceil($total_page / $page_num);
            $now_block = ceil($page / $page_num);
            $s_page = ($now_block * $page_num) - ($page_num - 1);
            if ($s_page <= 0)
                $s_page = 1;
            $e_page = $now_block * $page_num;
            if ($total_page < $e_page)
                $e_page = $total_page;

            $start = ($page - 1) * $list_num;
            $sql = query("SELECT * FROM board ORDER BY board_id DESC LIMIT $start, $list_num");
            while ($row = $sql->fetch_array()) {
                echo '<tr>';
                echo '<td>' . $row['board_id'] . '</td>';
                echo '<td><a href="view.php?id=' . $row['board_id'] . '">' . $row['board_title'] . '</a><td>';
                $comment_sql = query("SELECT COUNT(*) FROM comment WHERE board_id = " . $row['board_id']);
                echo '<td>' . $row['board_writer'] . '</td>';
                echo '<td>' . $row['created_at'] . '</td>';
                echo '<td>' . $row['board_views'] . '</td>';
            }
            ?>
        </table>

        <div class="page">
            <?php
            if ($page <= 1) {
                echo '<span> 이전 </span>';
            } else {
                echo '<a href="index.php?page=' . ($page - 1) . '"> 이전 </a>';
            }

            for ($print_page = $s_page; $print_page <= $e_page; $print_page++) {
                if ($print_page == $page) {
                    echo '<strong>' . $print_page . '</strong>';
                } else {
                    echo '<a href="index.php?page=' . $print_page . '">' . $print_page . '</a>';
                }
            }

            if ($page >= $total_page) {
                echo '<span> 다음 </span>';
            } else {
                echo '<a href="index.php?page=' . ($page + 1) . '"> 다음 </a>';
            }
            ?>
        </div>

        <script>
            function writePost() {
                <?php if (!isset($_SESSION['id'])) { ?>
                    alert('로그인이 필요합니다.');
                    location.href = '../login/login.php';
                <?php } else { ?>
                    location.href = '../board//write.php';
                <?php } ?>
            }
        </script>
</body>

</html>