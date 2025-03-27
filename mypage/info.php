<?php
session_start();
include "../db.php";

if (!isset($_SESSION["id"])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='../member/login/login.php';</script>";
    exit;
}

$id = $_SESSION["id"];
$name = $_SESSION["name"];

$stmt = $db->prepare("SELECT user_email FROM user WHERE user_id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "<script>alert('유저 정보를 찾을 수 없습니다.'); location.href='../main/index.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>내 정보</title>
</head>

<body>
    <div>
        <h1>내 정보</h1>
        <a href="../member/forgot/index2.php">비밀번호 변경</a>
        <form method="post" action="change.php">
            <label for="name">이름</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required />
            <label for="id">아이디</label>
            <span id="id" name="id"><?php echo htmlspecialchars($id); ?></span>
            <label for="pw">비밀번호</label>
            <input type="password" id="pw" name="pw" placeholder="비밀번호 확인" required />
            <label for="email">이메일</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user["user_email"]); ?>"
                required />
            <input type="submit" value="정보 수정" />
        </form>
    </div>
</body>

</html>