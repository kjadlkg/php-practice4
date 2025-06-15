<?php
if (session_status() === PHP_SESSION_NONE) {
   session_start();
}
require_once("../../resource/db.php");

if (isset($_SESSION['id'])) {
   echo "<script>alert('이미 로그인 하셨습니다.');</script>";
   header("Location: ../../main/index.php");
}

// CSRF 토큰
if (!isset($_SESSION['csrf_token'])) {
   $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $id = isset($_POST['id']) ? trim($_POST['id']) : null;
   $pw = isset($_POST['pw']) ? $_POST['pw'] : null;
   $csrf_token = $_POST['csrf_token'] ?? '';

   // CSRF 토큰 검증
   if (!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] !== $csrf_token) {
      die("잘못된 접근입니다.");
   }

   if (empty($id) || empty($pw)) {
      $_SESSION['error'] = "아이디 또는 비밀번호를 입력해주세요.";
      header("Location: login.php");
      exit;
   }

   if (!$db) {
      $_SESSION['error'] = "오류가 발생했습니다.";
      header("Location: login.php");
      exit;
   }

   if (!($stmt = $db->prepare("SELECT user_name, user_id, user_pw FROM user WHERE user_id = ? AND is_deleted = 0"))) {
      $_SESSION['error'] = "오류가 발생했습니다.";
      header("Location: login.php");
      exit;
   }

   $stmt->bind_param("s", $id);

   if (!$stmt->execute()) {
      $_SESSION['error'] = "오류가 발생했습니다.";
      header("Location: login.php");
      exit;
   }

   $stmt->store_result();

   if ($stmt->num_rows === 1) {
      $stmt->bind_result($user_name, $user_id, $user_pw);
      $stmt->fetch();

      if (password_verify($pw, $user_pw)) {
         session_regenerate_id(true);
         $_SESSION["id"] = $user_id;
         $_SESSION["name"] = $user_name;

         if (!empty($_POST['idsave'])) {
            setcookie("saved_id", $id, time() + 60 * 60 * 24 * 3, "/", "", true, true);
         } else {
            setcookie("saved_id", "", time() - 3600, "/", "", true, true);
         }

         header("Location: ../../main/index.php");
         exit;
      }
   }
   $stmt->close();

   $_SESSION['error'] = "아이디 또는 비밀번호가 일치하지 않습니다.";
   header("Location: login.php");
   exit;
}
?>
<!DOCTYPE html>
<html lang="ko">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>로그인</title>
   <link rel="stylesheet" href="../../resource/css/base.css">
   <link rel="stylesheet" href="../../resource/css/common.css">
   <link rel="stylesheet" href="../../resource/css/component.css">
   <link rel="stylesheet" href="../../resource/css/contents.css">
   <link rel="stylesheet" href="../../resource/css/page/login.css">
</head>

<body>
   <div id="top" class="width868 login_wrap">
      <header class="header bg">
         <div class="head">
            <h1 class="logo">
               <a href="../../main/index.php">메인페이지</a>
               <a href="login.php">로그인</a>
            </h1>
         </div>
      </header>
      <main id="container">
         <div class="content login">
            <article>
               <h2 class="blind">로그인</h2>
               <section>
                  <h3 class="blind">로그인 정보 입력</h3>
                  <div class="login_page">
                     <div class="login_inputbox">
                        <div class="inner">
                           <?php if (isset($_SESSION['error'])): ?>
                              <p style="color: red;">
                                 <?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); ?>
                              </p>
                              <?php unset($_SESSION['error']); ?>
                           <?php endif; ?>

                           <form method="post" action="login.php">
                              <fieldset>
                                 <legend class="blind">로그인</legend>
                                 <div>
                                    <input type="text" class="int bg" name="id" placeholder="아이디" maxlength="20"
                                       value="<?= isset($_COOKIE['saved_id']) ? htmlspecialchars($_COOKIE['saved_id'], ENT_QUOTES, 'UTF-8') : '' ?>" />
                                    <input type="password" class="int bg" name="pw" placeholder="비밀번호" maxlength="40" />
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>" />
                                 </div>
                                 <button type="submit" class="btn btn_blue small btn_wfull">로그인</button>
                                 <div class="id_checkbox clear">
                                    <span class="checkbox">
                                       <input type="checkbox" name="idsave" id="idsave">
                                       <label for="idsave">아이디 저장</label>
                                    </span>
                                 </div>
                                 <div class="login_option">
                                    <a href="../join/join.php">회원가입</a>
                                    <a href="../forgot/index.php">비밀번호 찾기</a>
                                 </div>
                              </fieldset>
                           </form>
                        </div>
                     </div>
                  </div>
               </section>
            </article>
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