
<!-- Reminder Student -->
<div class="modal fade text-left" id="reminderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel1">Yangi eslatma qo'shing</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="reminderForm">
                    <div class="mb-3">
                        <label class="form-label">Izoh</label>
                        <textarea name="reminderText" rows="5" class="form-control" require></textarea>
                        <input type="hidden" name="student_id" value="<?= $id_encrypte ?>">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                <button type="submit" form="reminderForm" name="reminderSubmit" class="btn btn-primary">Tasdiqlash</button>
            </div>
        </div>
    </div>
</div>
<!-- Coin Modal -->
<div class="modal fade text-left" id="coinModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel1">Coins</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="coinForm">
                    <label class="fs-6 mb-0">Haraqat turi</label>
                    <div class="demo-inline-spacing mb-1">
                        <div class="custom-control custom-radio">
                            <input type="radio" id="add" name="actionType" value="add" class="custom-control-input" checked />
                            <label class="custom-control-label" for="add">Qo'shish</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="remove" name="actionType" value="remove" class="custom-control-input" />
                            <label class="custom-control-label" for="remove">Ayirish</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="label-form">Coin Qiymati</label>
                        <input type="number" step="0.5" min="0" name="coinAmount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="label-form">Izoh</label>
                        <textarea name="coinComment" cols="30" rows="5" class="form-control" required></textarea>
                    </div>
                    <input type="hidden" name="student_id" value="<?= $id_encrypte ?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                <button type="submit" form=coinForm name="coinSubmit" class="btn btn-primary">Tasdiqlash</button>
            </div>
        </div>
    </div>
</div>
<script>
  function reminder(id) {
    $("[name=reminderUserId]").val(id);
    $("#reminderModal").modal("show");
  }
  function coinModal(id) {
    $("[name=coinStudentID]").val(id);
    $("#coinModal").modal("show");
  }
</script>
<?
// Reminder Modal
if (isset($_POST["reminderSubmit"])) {
  $student_id = openssl_decrypt($_POST["student_id"], 'AES-256-CBC', "StudentSecret", 0, substr(md5("StudentSecret"), 0, 16));
  $comment = filter_input(INPUT_POST, "reminderText", FILTER_SANITIZE_SPECIAL_CHARS);
  $update = db::query("INSERT INTO `note_list` (`CREATED_DATE`,`CREATED_BY`,`STUDENT_ID`,`TEXT`) VALUES ('$now','$user_id','$student_id','$comment')");
  $insert = db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`) VALUES ('$user_id', '$now', 'student_list', 'reminder_student','$student_id',1)");
  header("Location: /account/student_list/section/$student_id");
  exit;
}

if (isset($_POST["delete_note"])) {
  $id = filter_input(INPUT_POST, "delete_note", FILTER_SANITIZE_NUMBER_INT);
  $deactivate = db::query("UPDATE `note_list` SET `ACTIVE`='0' WHERE `ID`='$id'");
  header("Location: /account/student_list/section/$student_id");
  exit;
}

// Coin Modal
if (isset($_POST["coinSubmit"])) {
  $student_id = openssl_decrypt($_POST["student_id"], 'AES-256-CBC', "StudentSecret", 0, substr(md5("StudentSecret"), 0, 16));
  $amount = intval($_POST["coinAmount"]);
  $action_type = $_POST["actionType"] . "_coins";
  if ($action_type == "add_coins") {
      $cal_amount = $student["COINS"] + $amount;
      $text = "Sizni tabriklaymiz 🎉\n\nSizga $amount coin qo'shildi\n\nIzoh:<b>$_POST[coinComment]</b>\n\nBalansingiz: $cal_amount";
  } else {
      $cal_amount = $student["COINS"] - $amount;
      $text = "😢 Kechirasiz, lekin sizga aytishim kerak\n\nSizdan $amount coin olindi\n\nIzoh:<b>$_POST[coinComment]</b>\n\nBalansingiz: $cal_amount";
  }

  $input_comment = filter_input(INPUT_POST, "coinComment", FILTER_SANITIZE_SPECIAL_CHARS);
  $comment = json_encode([$input_comment, $amount]);
  $get_coin_log = db::arr_s("SELECT * FROM `table_log` WHERE `ITEM_ID`='$student_id' AND `COMMENT`='$comment' AND `TABLE_NAME`='student_list' AND `ACTION`='$action_type'");

  if ($get_coin_log == "empty") {
      $update = db::query("UPDATE `student_list` SET `COINS`='$cal_amount' WHERE `ID`='$student_id'");
      $chat_id = db::arr_s("SELECT * FROM `tg_users` WHERE `STUDENT_ID`='$student_id'");
  }

  if ($update["stat"] == "success") {
      $data = ['chat_id' => $chat_id["CHAT_ID"], 'parse_mode' => 'HTML', 'disable_web_page_preview' => false, 'text' => $text];
      $send_message = file_get_contents("https://api.telegram.org/bot5917704072:AAHrzOHlfmMKrwFQgBHMZMbqxnKbmk9fj7c/sendMessage?" . http_build_query($data));
      $insert_log = db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`,`COMMENT`) VALUES ('$user_id', '$now', 'student_list', '$action_type','$student_id',1,'$comment')");
  }
  header("Location: /account/student_list/section/$student_id");
  exit;
}

?>