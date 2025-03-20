<?php
session_start();
include "../db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['id'])) {
        echo "<script>alert('로그인이 필요합니다.'); history.back();</script>";
        exit;
    }

    $title = $_POST['title'];
    $content = $_POST['content'];

    if (empty($title) || empty($content)) {
        echo "<script>alert('제목과 내용을 입력해주세요.'); history.back();</script>";
        exit;
    }

    $title = htmlspecialchars(strip_tags($title), ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars(strip_tags($content), ENT_QUOTES, 'UTF-8');

    $user_id = $_SESSION['id'];

    $stmt = $db->prepare("INSERT INTO board(board_title, board_content, board_writer) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $content, $user_id);

    if ($stmt->execute()) {
        header("Location: ../main/index.php");
        exit;
    } else {
        echo "<script>alert('글 작성 중 오류가 발생했습니다.'); history.back();</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>글쓰기</title>
</head>

<body>
    <div>
        <h1>글 작성</h1>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" />
            <table>
                <tr>
                    <th>제목</th>
                    <td><input type="text" name="title" placeholder="제목을 입력하세요" required /></td>
                </tr>
                <tr>
                    <th>내용</th>
                    <td><textarea name="content" rows="5" cols="40" placeholder="내용을 입력하세요" required></textarea></td>
                </tr>
            </table>
            <div>
                <button onclick="location.href='../main/index.php'">취소</button>
                <input type="submit" value="작성" />
            </div>
        </form>
    </div>
</body>

</html>