<?php
session_start();
include "../db.php";

if (!isset($_SESSION['id'])) {
    die("로그인이 필요합니다.");
}

$loginUser = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        die('잘못된 접근입니다.');
    }

    $id = (int) $_GET['id'];
    $stmt = $db->prepare("SELECT * FROM board WHERE board_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_array(MYSQLI_ASSOC);

    if (!$row) {
        die("해당 게시글을 찾을 수 없습니다.");
    }

    if ($row['board_writer'] !== $loginUser) {
        die("이 게시글을 수정할 권한이 없습니다.");
    }

    $userName = htmlspecialchars($row['board_writer'], ENT_QUOTES, 'UTF-8');
    $boardTitle = htmlspecialchars($row['board_title'], ENT_QUOTES, 'UTF-8');
    $boardContent = nl2br(htmlspecialchars($row['board_content']));

    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        die("잘못된 접근입니다.");
    }

    $id = (int) $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    if (empty($title) || empty($content)) {
        echo "<script>alert('제목과 내용을 입력해주세요.'); history.back();</script>";
        exit;
    }

    $title = htmlspecialchars(strip_tags($title), ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars(strip_tags($content), ENT_QUOTES, 'UTF-8');

    $stmt = $db->prepare("UPDATE board SET board_title = ?, board_content = ? WHERE board_id = ?");
    $stmt->bind_param("ssi", $title, $content, $id);

    if ($stmt->execute()) {
        header("Location: view.php?id=" . $id);
        exit;
    } else {
        echo "<script>alert('오류가 발생했습니다.'); history.back();</script>";
    }

    $stmt->close();

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $boardTitle; ?></title>
</head>

<body>
    <div>
        <h3>글 수정</h3>
        <form method="POST">
            <table>
                <tr>
                    <th>제목</th>
                    <td><input type="text" name="title" value="<?= $boardTitle; ?>" required></td>
                </tr>
                <tr>
                    <th>내용</th>
                    <td><textarea name="content" rows="5" cols="40" required><?= $boardContent; ?></textarea></td>
                </tr>
            </table>
            <input type="hidden" name="id" value="<?= $id; ?>">
            <button onclick="location.href='view.php?id=<?= $id; ?>'">취소</button>
            <button type="submit">수정</button>
        </form>
    </div>
</body>

</html>