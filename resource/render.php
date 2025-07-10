<li style="margin-left: 20px;">
   <div class="comment_info clear">
      <?php if ($child['is_deleted']): ?>
         <div class="comment_textbox btn_reply_write_all clear">
            <p class="comment_text">삭제된 댓글입니다.</p>
         </div>
      <?php else: ?>
         <div class="comment_nickbox">
            <span class="view_writer">
               <span class="nickname">
                  <em><?= $child['comment_writer'] ?></em>
                  <?php if ($child['user_ip']): ?>
                     <span class="ip">(<?= $child['user_ip'] ?>)</span>
                  <?php endif; ?>
               </span>
            </span>
         </div>
         <div class="comment_textbox btn_reply_write_all clear">
            <p class="comment_text btn_reply">
               <?= $child['comment_content'] ?>
            </p>
         </div>
         <div class="fr clear">
            <span class="date_time">
               <?= $child['created_at'] ?>
            </span>
            <div class="comment_delete">
               <form method="POST" action="../comment/delete.php" class="comment_delete_form"
                  onsubmit="return checkDeletePassword(this)">
                  <input type="hidden" name="board_id" value="<?= $child['board_id'] ?>">
                  <input type="hidden" name="comment_id" value="<?= $child['comment_id'] ?>">
                  <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
                  <?php if ($child['is_writer']): ?>
                     <button type="submit" class="btn_comment_delete">삭제</button>
                  <?php endif; ?>
                  <?php if (empty($child['comment_writer_id'])): ?>
                     <button type="button" class="btn_comment_delete"
                        data-comment_id="<?= $child['comment_id'] ?>">삭제</button>
                     <div class="comment_delpw_box">
                        <input type="password" class="comment_delpw" name="comment_pw" placeholder="비밀번호">
                        <button type="submit" class="btn_ok">확인</button>
                        <button type="button" class="btn_comment_pw_close">
                           <span class="blind">닫기</span>
                           <em>X</em>
                        </button>
                     </div>
                  <?php endif; ?>
               </form>
            </div>
         </div>
      </div>
      <!-- 대댓글 입력 폼 -->
      <div class="reply_write_box clear" style="display: none;">
         <form method="POST" action="../comment/comment.php" onsubmit="return checkCommentEmpty(this)">
            <div class="fl">
               <?php if ($is_login): ?>
                  <div class="user_info_input">
                     <input type="text" name="name" value="<?= $_SESSION['name'] ?>" maxlength="20">
                  </div>
               <?php else: ?>
                  <div class="user_info_input">
                     <input type="text" name="name" value="ㅇㅇ" placeholder="닉네임" maxlength="20">
                  </div>
                  <div class="user_info_input">
                     <input type="password" name="pw" value="1234" placeholder="비밀번호" maxlength="20">
                  </div>
                  <div class="user_info_input">
                     <input type="text" name="captcha" placeholder="코드입력">
                  </div>
                  <div class="kcaptcha_img">
                     <img src="../resource/captcha_image.php?<?= time() ?>" class="kcaptcha" alt="KCAPTCHA">
                  </div>
               <?php endif; ?>
               <input type="hidden" name="board_id" value="<?= $child['board_id'] ?>">
               <input type="hidden" name="parent_id" value="<?= $child['comment_id'] ?>">
               <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
            </div>
            <div class="comment_text_content">
               <div class="comment_write">
                  <textarea name="content" autocomplete="off" maxlength="400"></textarea>
               </div>
               <div class="comment_write_bottom">
                  <div class="fr">
                     <button type="submit" class="btn btn_blue small btn_reply">등록</button>
                  </div>
               </div>
            </div>
         </form>
      </div>
   <?php endif; ?>
</li>