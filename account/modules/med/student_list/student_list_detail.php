<!-- BEGIN: Content-->
<?
function days($days)
{
  $rs = '';
  if ($days == "1,2,3,4,5,6,7") {
    $rs = "Har kuni";
  } elseif ($days == "6,7") {
    $rs = "Dam kunlari";
  } elseif ($days == "1,3,5") {
    $rs = "Toq kunlar";
  } elseif ($days == "2,4,6") {
    $rs = "Juft kunlar";
  } else {
    $days_arr = explode(",", $days);
    $days_name = ["1" => "Du.", "2" => "Se.", "3" => "Cho.", "4" => "Pa.", "5" => "Ju.", "6" => "Sha.", "7" => "Ya."];
    foreach ($days_arr as $day) {
      $rs = $rs . ' ' . $days_name[$day];
    }
  }
  return $rs;
}
$student_id = filter_var($_GET["item_id"], FILTER_SANITIZE_NUMBER_INT);
$id_encrypte = openssl_encrypt($student_id, 'AES-256-CBC', "StudentSecret", 0, substr(md5("StudentSecret"), 0, 16));
$student = db::arr_s("SELECT * FROM `student_list` WHERE ID='$student_id'");
$org_id = $_SESSION["USER"]["ORG_ID"];
if ($org_id > 0) {
  $sql_org_id = " AND `ORG_ID`='$org_id'";
}
$subscribe = db::arr_by_id("SELECT * FROM `subscribe_list` WHERE STUDENT_ID='$student_id' AND ACTIVE=1");
$last_tran = db::arr_s("SELECT * FROM `transaction_list` WHERE STUDENT_ID='$student_id' AND ACTION_TYPE ='add' ORDER BY TRANSACTION_DATE DESC LIMIT 1");
$course_list = db::arr_by_id("SELECT * FROM `course_list` ");
$group_list = db::arr_by_id("SELECT * FROM `group_list`");
$teacher_list = db::arr_by_id("SELECT * FROM `teacher_list`");
$gl_sys_users = db::arr_by_id("SELECT `ID`,CONCAT(`NAME`,' ',`SURNAME`) FULL_NAME FROM `gl_sys_users`");
$transactions_history = db::arr("SELECT * FROM `transaction_list` WHERE STUDENT_ID='$student_id' AND ACTION_TYPE IN ('add','taken','subtract','retake') ORDER BY TRANSACTION_DATE DESC");
$history_list = db::arr("SELECT 
  DATE_FORMAT(CREATE_DATE, '%Y.%m.%d') AS start_date,
  DATE_FORMAT(LESSON_DATE, '%Y.%m.%d') as lesson_date,
  DATE_FORMAT(COMPLETION_DATE, '%Y.%m.%d') as end_date,
  CREATE_BY,
  COMPLETED_BY,
  COMMENT
  FROM `list_tasks` 
  WHERE `TARGET_ID` = '$student_id' 
  AND `TYPE` = 'student'
  AND `STATUS` = 'closed' $sql_org_id");

$sms_logs = db::arr("SELECT 
ID, 
CREATED_BY,
DATE_FORMAT(SEND_DATE, '%d.%m.%Y %H:%i') AS SDATE,
`TEXT`,
ACTIVE
FROM `sms_log` WHERE `SEND_TO`='$student_id' ORDER BY SEND_DATE DESC");
$formated_templates = [];
$months = ["01" => "Yanvar", "02" => "Fevral", "03" => "Mart", "04" => "Aprel", "05" => "May", "06" => "Iyun", "07" => "Iyul", "08" => "Avgust", "09" => "Sentyaber", "10" => "Oktyaber", "11" => "Noyabir", "12" => "Dekaber"];
foreach (db::arr("SELECT * FROM `sms_templates` WHERE ACTIVE=1") as $temp) {
  $temp_text = $temp["TEXT"];
  $pattern = array();
  $pattern["sname"] = "/(\[STUDENT_NAME\])/";
  $pattern["amount"] = "/(\[PAYMENT_AMOUNT\])/";
  $pattern["score"] = "/(\[TEST_SCORE\])/";
  $pattern["current_month"] = "/(\[CURMONTH\])/";
  if ($subscribe != "empty") {
    $pattern["time"] = "/(\[LESSON_TIME\])/";
    $pattern["day"] = "/(\[GROUP_DAYS\])/";
    $pattern["teacher_name"] = "/(\[TEACHER_NAME\])/";
  }
  $pattern["pname"] = "/(\[PARENT_NAME\])/";
  $replacement = array();
  $replacement["sname"] = $student["NAME"];
  $replacement["amount"] = number_format($last_tran["AMOUNT"]);
  $replacement["score"] = $student["TEST_SCORE"];
  $replacement["current_month"] = "Month";
  if ($subscribe != "empty") {
    $replacement["time"] = $group_list[end($subscribe)["GROUP_ID"]]["START_TIME"];
    $replacement["day"] = days($group_list[end($subscribe)["GROUP_ID"]]["DAYS"]);
    $replacement["teacher_name"] = $teacher_list[$group_list[end($subscribe)["GROUP_ID"]]["TEACHER_ID"]]["NAME"];
  }
  $replacement["pname"] = $student["PARENT_NAME"];
  $changed_text = preg_replace($pattern, $replacement, $temp_text);
  array_push($formated_templates, ["ID" => $temp["ID"], "TITLE" => $temp["TITLE"], "TEXT" => $changed_text]);
}
// $message_templates
?>

<?
$user_id = $_SESSION["USER"]["ID"];
$now = date("Y-m-d H:i:s");
if (isset($_POST['add_group']) and isset($group_list[$_POST['group_id']])) {
  $group = $group_list[$_POST['group_id']];
  $course = $course_list[$group["COURSE_ID"]];
  $give_laptop = $_POST["give_laptop"] == "true" ? 1 : 0;
  $start_date = $_POST["start_date"];
  if ($_POST["with_sub_length"] == "on" && is_numeric($_POST["sub_length"])) {
    $end_data = date("'Y-m-d 00:00:00'", strtotime("+$_POST[sub_length] month", strtotime($start_date)));
  } else {
    $end_data = 'NULL';
  }
  $day = date("d", strtotime($_POST["start_date"]));
  if ($day > 28) {
    $day = 28;
  }
  if ($_POST["with_special_price"] === "on") {
    $price = filter_input(INPUT_POST, "special_price", FILTER_SANITIZE_NUMBER_INT);
  } else {
    $price = $course["PRICE"];
  }
  if ($_POST["paymentType"] == "free") {
    $price = 0;
    $end_data = 'NULL';
  }
  // $check_subscription = db::arr_s("SELECT `ID` FROM `subscribe_list` WHERE `STUDENT_ID`='$student_id' AND `ACTIVE`='1'");
  // $check_transactions = db::arr_s("SELECT SUM(AMOUNT) as amount FROM `transaction_list` WHERE STUDENT_ID='$student_id' AND `SUBSCRIBE_ID`= 0 AND ACTION_TYPE='add'");
  $insert = db::query("INSERT INTO `subscribe_list` (
  `STUDENT_ID`, 
  `COURSE_ID`, 
  `GROUP_ID`, 
  `SPECIAL_PRICE`,
  `START_DATE`,
  `END_DATE`,
  `DAY`,
  `STATUS`, 
  `TYPE`,
  `LAPTOP`,
  `ACTIVE`)
  VALUES (
  '$student_id', 
  '$group[COURSE_ID]', 
  '$_POST[group_id]', 
  '$price', 
  '$start_date',
   $end_data,
  '$day',
  'demo',
  '$_POST[paymentType]', 
  '$give_laptop',
  '1')");
  if ($_POST["paymentType"] == "monthly") {
    $calBalance = intval($student["CURRENT_BALANCE"]) - $price;
    // update balance 
    db::query("UPDATE `student_list` SET `CURRENT_BALANCE`='$calBalance' WHERE `ID`='$student_id'");
    // insert transaction
    $des = date("Y-m-d") . "_" . $_POST["group_id"];
    db::query("INSERT INTO `transaction_list` (`STUDENT_ID`,`SUBSCRIBE_ID`,`CREATED_DATE`,`CREATED_BY`,`CHANGED_DATE`,`CHANGED_BY`,`TRANSACTION_DATE`,`ACTION_TYPE`,`TYPE`,`AMOUNT`,`DESCRIPTION`) VALUES 
    ('$student[ID]','$insert[ID]',now(),'$user_id',now(), '$user_id',now(),'taken','system','$price','$des')");
  }

  // if($check_subscription == "empty" AND $check_transactions != "empty"){
  //   db::query("UPDATE `transaction_list` SET `SUBSCRIBE_ID`='$insert[ID]' WHERE STUDENT_ID='$student_id' AND `SUBSCRIBE_ID`= 0 AND ACTION_TYPE='add'");
  //   if($check_transactions["amount"]>= $price){
  //     db::query("UPDATE `subscribe_list` SET `STATUS`='active' WHERE `ID`='$insert[ID]'");
  //   }
  // }
  $insert_log = db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`) VALUES ('$user_id','$now','subscribe_list','join_to_group','$insert[ID]', '1')");
  header('Location: /account/student_list/detail/' . $_GET["item_id"]);
  exit;
}
$reminders = db::arr("SELECT * FROM `note_list` WHERE `STUDENT_ID`='$student_id' AND `ACTIVE`='1' ORDER BY `ID` DESC");

$coin_logs = db::arr("SELECT * FROM `table_log` WHERE `ITEM_ID`='$student_id' AND `ACTION` IN ('add_coins','remove_coins') AND `ACTIVE`='1'");
$payment_types = PAYMENT_TYPES;
$coins_action_types = ["add_coins" => "Coin Qo'shildi", "remove_coins" => "Coin Oʻchirildi"];
$coins_type = ["attendance" => "Yo'qlamaga", "homework" => "Uy vazifaga"];
$subscribe_statuses = ["active" => "Aktiv", "demo" => "Sinov Darsi", "archive" => "Arxiv", "freezed" => "Muzlatilingan"];
$subscribe_type = ["simple" => "Davomat bo'yicha", "monthly" => "Oylik", "free" => "Tekin"];
$student_files = db::arr("SELECT * FROM `student_files_list` WHERE `STUDENT_ID`='$student[ID]' ORDER BY `ID` DESC");

$student_tasks = db::arr_by_id("SELECT lt.* FROM `table_log` tl 
LEFT JOIN `list_tasks` lt ON lt.ID = tl.ITEM_ID
WHERE lt.`TARGET_ID`='$student_id' AND tl.`ACTION` IN ('open_student_task','close_student_task')");

$student_logs = db::arr("(
  SELECT * FROM `table_log`
  WHERE ITEM_ID='$student_id' 
  AND `ACTION` IN ('add_student','edit_student','delete_student','reminder_student','add_payment',
  'subtract_from_balance','add_recalculation','remove_from_group','withdraw') 
  AND ACTIVE='1'
)
UNION 
(
  SELECT tbl.* FROM `table_log` tbl
  LEFT JOIN `subscribe_list` sbl ON sbl.ID = tbl.ITEM_ID
  WHERE sbl.`STUDENT_ID`='$student_id' AND tbl.`ACTION` IN (
  'freeze_student','subscription_deleted','edit_subscription','add_new_subscription','deactivate_old_subscription','activate_student','change_to_demo','defrosted','join_to_group'
) AND tbl.`ACTIVE`='1')
UNION 
(
  SELECT tl.* FROM `table_log` tl 
  INNER JOIN `student_files_list` sfl ON sfl.ID = tl.ITEM_ID
  WHERE sfl.`STUDENT_ID`='$student_id' AND tl.`ACTION` IN ('delete_student_file','upload_student_file')
)
UNION 
(
  SELECT tl.* FROM `table_log` tl 
  LEFT JOIN `list_tasks` lt ON lt.ID = tl.ITEM_ID
  WHERE lt.`TARGET_ID`='$student_id' AND tl.`ACTION` IN ('open_student_task','close_student_task')
)
ORDER BY `ID` DESC");

?>
<div class="app-content content ">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
          <div class="col-12">
            <h2 class="content-header-title float-left mb-0">Talabalar</h2>
            <div class="breadcrumb-wrapper">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/account/main_page/list">Asosiy menyu</a>
                </li>
                <li class="breadcrumb-item active">Talabalar
                </li>
                <li class="breadcrumb-item active">Batafsil
                </li>
              </ol>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="content-body">
      <!-- Responsive Datatable -->
      <section id="responsive-datatable">
        <div class="row">
          <? if ($student["ACTIVE"] == "0") : ?>
            <div class="col-12">
              <div class="alert alert-danger mt-1 alert-validation-msg" role="alert">
                <div class="alert-body">
                  <i data-feather="info"></i>
                  <span>Bu talaba <b>oʻchirilgan</b> va <b>arxivda</b></span>
                </div>
              </div>
            </div>
          <? elseif (isset($_SESSION["file_upload_error"]) or isset($_SESSION["phone_number_founded"])) : ?>
            <div class="col-12">
              <div class="alert alert-danger mt-1 alert-validation-msg" role="alert">
                <div class="alert-body">
                  <i data-feather="info"></i>
                  <?
                  echo $_SESSION["file_upload_error"] . "<br/>";
                  echo $_SESSION["phone_number_founded"];
                  unset($_SESSION["file_upload_error"]);
                  unset($_SESSION["phone_number_founded"]);
                  ?>
                </div>
              </div>
            </div>
          <? endif; ?>
          <div class="col-left mb-4 col-sm-8 col-xl-4 offset-sm-2 offset-lg-2 offset-xl-0 col-12">
            <div class="card mb-4">
              <div class="card-body sm">
                <div class="row">
                  <div class="col-md-10">
                    <div class="mb-1">
                      <h2 class="pr-5 mb-0"><?= $student['NAME'] ?></h2>
                      <span class="text-muted">(id: <?= $student['ID'] ?>)</span>
                    </div>
                    <div class="mb-2">
                      <ul class="list-group list-group-flush">
                        <li class="list-group-item pl-0"><b>Tel. Raqami: </b> <?= $student["PHONE"] ?></li>
                        <? if (!empty($student["EMAIL"])) : ?>
                          <li class="list-group-item pl-0"><b>Email: </b> <?= $student["EMAIL"] ?></li>
                        <? endif ?>
                        <? if (!empty($student["TG_USERNAME"])) : ?>
                          <li class="list-group-item pl-0"><b>Telegram username: </b> <a href="<?= preg_match("/^https:\/\/t.me\//", $student["TG_USERNAME"]) ? $student["TG_USERNAME"] : "https://t.me/" . $student["TG_USERNAME"]; ?>" target="_blank"><?= $student["TG_USERNAME"] ?></a></li>
                        <? endif ?>
                        <li class="list-group-item pl-0"><b>Tug'ilgan Sana: </b> <?= $student["BIRTH_DATE"] ?> (<?= date_diff(date_create($student["BIRTH_DATE"]), date_create('today'))->y ?> y.)</li>
                        <li class="list-group-item pl-0"><b>Ota-ona Ismi: </b> <?= $student["PARENT_NAME"] ?></li>
                        <?
                        $parents_phone = (json_decode($student["PARENT_PHONE"]) !== null) ? json_decode($student["PARENT_PHONE"], true) : $student["PARENT_PHONE"];
                        ?>
                        <?if(is_array($parents_phone) and count($parents_phone) > 0):?>
                          <?foreach($parents_phone as $parent_phone):?>
                          <li class="list-group-item pl-0"><b><?=PHONE_TYPES[$parent_phone["type"]]?> tel. Raqami: </b> <?= $parent_phone["phone"] ?> <span class="text-muted"><?= $parent_phone["name"] ?></span></li>
                          <?endforeach;?>                          
                        <?else:?>
                        <li class="list-group-item pl-0"><b>Ota-ona tel. Raqami: </b> <?= $student["PARENT_PHONE"] ?></li>
                        <?endif;?>
                        <li class="list-group-item pl-0"><b>Manzili: </b> <?= $student["ADDRESS"] ?></li>
                        <li class="list-group-item pl-0"><b>Coin'lar soni: </b> <?= $student["COINS"] ?></li>
                        <li class="list-group-item pl-0"><b>Balansi: </b> <?= number_format($student['CURRENT_BALANCE'], 0, ',', ' ') ?> so'm</li>

                        <li class="list-group-item pl-0"><b>Telegram Parol: </b> <?= $student["PASSWORD"] ?></li>
                      </ul>
                    </div>
                  </div>
                  <div class="col-md-2 d-flex justify-content-center">
                    <div class="position-absolute card-top-buttons d-flex flex-column">
                      <button title="Talabani tahrirlash" type="button" class="mb-1 btn btn-icon rounded-circle btn-primary" data-json="<?= htmlspecialchars(json_encode($student)) ?>" onclick="editModal(this)"><i data-feather="edit"></i></button>
                      <? if ($student["ACTIVE"] == "1") : ?>
                        <button title="O'chirish" type="button" class="mb-1 btn btn-icon rounded-circle btn-danger" onclick="deleteModal(<?= $student['ID'] ?>)"><i data-feather="trash"></i></button>
                      <? endif; ?>
                      <? $debt_status = $student["CURRENT_BALANCE"] < -50000 ? "'block'" : "'free'"; ?>
                      <button class="btn btn-warning btn-icon rounded-circle mb-1" onclick="coinModal(<?= $student['ID'] . ',' . $debt_status; ?>)"><i data-feather="compass"></i></button>
                      <button class="btn btn-icon btn-dark rounded-circle mb-1" onclick="removeTranModal(<?= $student['ID'] ?>)"><i data-feather="minus-circle"></i></button>
                      <button class="btn btn-icon btn-info rounded-circle mb-1" onclick="smsModal()"><i data-feather="send"></i></button>
                      <button type="button" class="btn btn-icon btn-secondary rounded-circle mb-1" onclick="studentFileFunction(<?= $student['ID'] ?>)"><i data-feather="upload"></i></button>
                    </div>
                  </div>
                </div>
                <hr>
                <div class="mb-1">
                  <span class="text-muted text-small">Talaba qo'shilgan sana:<b> <?= date_var($student['CREATED_DATE'], 'd.m.Y'); ?></b></span>
                </div>
                <div class="mt-1 d-flex justify-content-between">
                  <span>
                    <div class="mr-1">
                      <div role="group" class="btn-group">
                        <? if ($student["ACTIVE"] == "1") : ?>
                          <button type="button" class="mb-1 btn btn-sm  btn-primary waves-effect waves-float waves-light" data-toggle="modal" data-target="#add_to_group">
                            <i class="fa fa-plus"></i> &nbsp;Guruhga qo'shish </button>
                        <? endif; ?>
                      </div>
                    </div>
                  </span>
                  <span>
                    <div class="mr-1">
                      <div role="group" class="btn-group">
                        <? if ($student["ACTIVE"] == "1") : ?>
                          <button type="button" class="mb-1 btn btn-sm btn-info waves-effect waves-float waves-light" onclick="paymentModal('<?= $student['ID'] ?>')">
                            <i data-feather="dollar-sign"></i> &nbsp;To`lov </button>
                        <? endif; ?>
                      </div>
                    </div>
                  </span>
                </div>
              </div>
            </div>
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Eslatmalar</h4>
              </div>
              <? if ($reminders == "empty") : ?>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-12">
                      <button type="button" class="btn btn-icon rounded-circle btn-success" data-toggle="modal" data-target="#reminderModal"><i data-feather="plus"></i></button>
                    </div>
                  </div>
                </div>
              <? else : ?>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="card">
                        <div class="card-body p-0 pl-1 border-left-info ">
                          <div class="d-flex justify-content-start">
                            <span class="align-self-center"><?= $reminders[0]["TEXT"] ?></span>
                            <div class="btn-group-vertical ml-auto" role="group">
                              <form action="" method="post" class="m-0 p-0">
                                <button type="button" class="btn btn-icon btn-success" data-toggle="modal" data-target="#reminderModal"><i data-feather="plus-circle"></i></button>
                                <button type="submit" class="btn btn-icon btn-danger" name="delete_note" value="<?= $reminders[0]["ID"] ?>"><i data-feather="x-circle"></i></button>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <? endif ?>
            </div>

            <!-- vazifalar -->
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Vazifalar</h4>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12">
                    <? if ($student_tasks === "empty") : ?>
                      <button type="button" class="btn btn-icon rounded-circle btn-info" data-toggle="modal" data-target="#taskModal"><i data-feather="plus"></i></button>
                    <? else : ?>
                      <div class="card">
                        <div class="card-body p-0 pl-1 border-left-info ">
                          <div class="d-flex justify-content-start">
                            <span class="align-self-center"><?= $student_tasks[array_key_first($student_tasks)]["TASK"] ?></span>
                            <div class="btn-group-vertical ml-auto" role="group">
                              <form action="" method="post" class="m-0 p-0">
                                <button type="button" class="btn btn-icon btn-info" data-toggle="modal" data-target="#taskModal"><i data-feather="plus-circle"></i></button>
                                <?/*<button type="submit" class="btn btn-icon btn-danger" name="delete_note" value="<?=  $student_tasks[array_key_first($student_tasks)]["TASK"]  ?>"><i data-feather="x-circle"></i></button>*/ ?>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                    <? endif; ?>
                  </div>
                </div>
              </div>
            </div>

          </div>
          <div class="col-right col-lg-12 col-xl-8 col-12">
            <div class="card">
              <div class="card-body">
                <ul class="nav nav-tabs nav-justified" id="myTab2" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="home-tab-justified" data-toggle="tab" href="#home-just" role="tab" aria-controls="home-just" aria-selected="true">Guruhlar</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="profile-tab-justified" data-toggle="tab" href="#profile-just" role="tab" aria-controls="profile-just" aria-selected="false">Izohlar</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="price-history-tab-justified" data-toggle="tab" href="#price-history-just" role="tab" aria-controls="price-history-just" aria-selected="false">Narxlar tarixi</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="phone-calles-tab-justified" data-toggle="tab" href="#phone-calles-just" role="tab" aria-controls="phone-calles-just" aria-selected="false">Qo'ng'iroq tarixi</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="sms-messages-tab-justified" data-toggle="tab" href="#sms-messages-just" role="tab" aria-controls="sms-messages-just" aria-selected="false">SMS</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="coin-history-tab-justified" data-toggle="tab" href="#coin-history-just" role="tab" aria-controls="coin-history-just" aria-selected="false">Coin Tarixi</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="history-tab-justified" data-toggle="tab" href="#history-just" role="tab" aria-controls="history-just" aria-selected="false">Tarix</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="files-tab-justified" data-toggle="tab" href="#files-just" role="tab" aria-controls="files-just" aria-selected="false">Fayllar</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="online-tab-justified" data-toggle="tab" href="#online-just" role="tab" aria-controls="online-just" aria-selected="false">Onlayn kurs</a>
                  </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content pt-1">
                  <div class="tab-pane active" id="home-just" role="tabpanel" aria-labelledby="home-tab-justified">
                    <div class="row">
                      <? foreach ($subscribe as $v) : ?>
                        <? $group = $group_list[$v['GROUP_ID']];
                        if ($group["STATUS"] === "deleted") continue; ?>
                        <? $teacher_name = $teacher_list[$group['TEACHER_ID']]['NAME']; ?>
                        <div class="col-xl-6">
                          <div class="card mb-3">
                            <div class="card-body sm border border-primary rounded p-1">
                              <a href="/account/group_list/detail/<?= $group["ID"] ?>" class="">
                                <div class="align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero align-items-lg-center">
                                  <div class="w-40 w-sm-100">
                                    <span class="badge badge-light-primary"><?= $group["NAME"] ?></span>
                                    <br>
                                    <span><?= $course_list[$v['COURSE_ID']]['NAME'] ?></span>
                                    <br>
                                    <span><?= $teacher_name ?></span>
                                  </div>
                                  <div class="text-muted text-small"> <?= date_var($group['START_DATE'], 'd.m.Y') ?> — <br> <?= date_var($group['END_DATE'], 'd.m.Y') ?> <br> </div>
                                </div>
                              </a>
                              <hr>
                              <div class="d-flex justify-content-between">
                                <div class="subscribe-info">
                                  <div>
                                    <span class="text-muted">Holat: <?= $subscribe_statuses[$v["STATUS"]] ?></span> / <?= $subscribe_type[$v["TYPE"]] ?>
                                  </div>
                                  <div>
                                    <span class="text-muted">Talaba qo'shilgan sana:</span> <?= date_var($v['START_DATE'], 'd.m.Y') ?>
                                  </div>
                                  <div>
                                    <span class="text-muted">Bu talaba uchun narx:</span>
                                    <span> <?= number_format($v["SPECIAL_PRICE"], 0, ',', ' ') ?> so'm </span>
                                  </div>
                                  <? if ($v["TYPE"] === "monthly") : ?>
                                    <div>
                                      <span class="text-muted">Yechvolish sana:</span>
                                      <span><?= $v["DAY"] ?></span>
                                    </div>
                                  <? endif; ?>
                                  <? if ($v["TYPE"] === "monthly" and $v["END_DATE"] != "0000-00-00 00:00:00" and !is_null($v["END_DATE"])) : ?>
                                    <div>
                                      <span class="text-muted">Tugash sana:</span>
                                      <span><?= date_var($v["END_DATE"], "d.m.Y"); ?></span>
                                    </div>
                                  <? elseif ($v["TYPE"] === "monthly" and ($v["END_DATE"] == "0000-00-00 00:00:00" or empty($v["END_DATE"]))) : ?>
                                    <div>
                                      <span class="text-muted">Kurs tugash sana:</span>
                                      <span><?= date("d.m.Y", strtotime("+" . $course_list[$v['COURSE_ID']]['COURSE_DURATION'] . " months " . $v["START_DATE"])); ?></span>
                                    </div>
                                  <? endif; ?>
                                </div>
                                <div class="dropdown-items-wrapper">
                                  <i data-feather="more-vertical" id="dropdownMenuLink1" role="button" data-toggle="dropdown" aria-expanded="false"></i>
                                  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink1">
                                    <a class="dropdown-item" href="javascript:void(0)" data-id="<?= $v["ID"] ?>" data-type="<?= $v["TYPE"] ?>" data-start-date="<?= date("Y-m-d", strtotime($v["START_DATE"])) ?>" data-laptop-status="<?= $v["LAPTOP"] == "1" ? "true" : "false"; ?>" onclick="editSubscription(this)">
                                      <i data-feather="edit" class="mr-25"></i>
                                      <span class="align-middle">Tahrirlash</span>
                                    </a>
                                    <? if ($_SESSION["USER"]["ROLE_ID"] == "1") : ?>
                                      <a class="dropdown-item" href="javascript:void(0)" data-id="<?= $v["ID"] ?>" onclick="conformModal('<?= $v['ID'] ?>', 'removeGroup')">
                                        <i data-feather="trash" class="mr-25"></i>
                                        <span class="align-middle">Guruhdan O'chirish</span>
                                      </a>
                                    <? endif; ?>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      <? endforeach ?>
                    </div>
                    <h4 class="card-title">To`lovlar</h4>
                    <div class="table-responsive">
                      <table role="table" aria-busy="false" aria-colcount="5" class="table b-table table-striped" id="student-trans-table">
                        <thead role="rowgroup" class="">
                          <tr role="row" class="">
                            <th role="columnheader" scope="col" aria-colindex="1" class="">
                              <div>Sana</div>
                            </th>
                            <th role="columnheader" scope="col" aria-colindex="2" class="">
                              <div>Miqdor</div>
                            </th>
                            <th role="columnheader" scope="col" aria-colindex="3" class="">
                              <div>Izoh</div>
                            </th>
                            <th>
                              <div>Harakat</div>
                            </th>
                            <th role="columnheader" scope="col" aria-colindex="4" class="">
                              <div>Xodim</div>
                            </th>
                          </tr>
                        </thead>
                        <tbody role="rowgroup">
                          <? foreach ($transactions_history as $v) : ?>
                            <tr role="row" data-type="<?= $v["ACTION_TYPE"] ?>">
                              <td aria-colindex="1" role="cell">
                                <?= date_var($v['TRANSACTION_DATE'], 'd.m.Y') ?>
                              </td>
                              <td aria-colindex="2" role="cell">
                                <? if ($v['ACTION_TYPE'] == 'add' or $v['ACTION_TYPE'] == 'retake') : ?>
                                  <span class="text-success mb-0 text-wrap"><?= number_format($v['AMOUNT'], 0, ',', ' ') ?></span>
                                  <small class="text-dark">UZS</small>
                                <? endif ?>
                                <? if ($v['ACTION_TYPE'] == 'taken' or $v['ACTION_TYPE'] == 'subtract') : ?>
                                  <span class="text-danger mb-0 text-wrap">-<?= number_format($v['AMOUNT'], 0, ',', ' ') ?></span>
                                  <small class="text-dark">UZS</small>
                                <? endif ?>
                              </td>
                              <td aria-colindex="3" role="cell">
                                <? if ($v['ACTION_TYPE'] == 'add') : ?>
                                  <div class="btn-group">
                                    <span class="badge badge-info badge-glow"><?= $payment_types[$v['TYPE']] ?></span>
                                    <!--<span class="badge badge-info badge-glow" data-transaction="<?= htmlspecialchars(json_encode($v)) ?>" onclick="editTransaction(JSON.stringify(dataset.transaction))"><i data-feather="edit-2"></i></span>-->
                                    <span class="badge badge-info badge-glow" onclick="edit_tr(<?= $v['ID'] ?>,'<?= $id_encrypte ?>')"><i data-feather="edit-2"></i>
                                    </span>
                                  </div>
                                <? elseif ($v['ACTION_TYPE'] == 'retake') : ?>
                                  <div class="btn-group">
                                    <span class="badge badge-info badge-glow"><?= $payment_types[$v['TYPE']] ?></span>
                                  </div>
                                <? elseif ($v['ACTION_TYPE'] == 'taken') : ?>
                                  <span class="single-line-text" title="Darsga (<?= explode("_", $v["DESCRIPTION"])[0]; ?>)">
                                    Darsga (<?= explode("_", $v["DESCRIPTION"])[0]; ?>)
                                  </span>
                                <? elseif ($v['ACTION_TYPE'] == 'subtract') : ?>
                                  <div class="btn-group">
                                    <span class="badge badge-info badge-glow"><?= $payment_types[$v['TYPE']] ?></span>
                                    <span class="badge badge-info badge-glow" onclick="edit_tr(<?= $v['ID'] ?>,'<?= $id_encrypte ?>')"><i data-feather="edit-2"></i></span>
                                  </div>
                                <? endif ?>
                              </td>
                              <td>
                                <? if ($v["ACTION_TYPE"] == "taken" and $_SESSION["USER"]["ROLE_ID"] === "1") : ?>
                                  <button class="btn btn-icon btn-outline-danger rounded-circle" onclick="conformModal(<?= $v['ID'] ?>, 'removeTransaction')" title="(<?= explode("_", $v["DESCRIPTION"])[1]; ?>)"><i data-feather="trash"></i></button>
                                <? elseif ($v["ACTION_TYPE"] == "add") : ?>
                                  <button class="btn btn-outline-info btn-icon rounded-circle" data-date="<?= date("d.m.Y H:i", strtotime($v["CREATED_DATE"])) ?>" data-by-user="<?= $gl_sys_users[$v['CREATED_BY']]["FULL_NAME"] ?>" data-transaction="<?= $v["ID"] ?>" data-group="<?= $v["SUBSCRIBE_ID"] > 0 ? $group_list[$subscribe[$v["SUBSCRIBE_ID"]]["GROUP_ID"]]["NAME"] : $group_list[end($subscribe)["GROUP_ID"]]["NAME"]; ?>" data-price="<?= $v["SUBSCRIBE_ID"] > 0 ? $subscribe[$v["SUBSCRIBE_ID"]]["SPECIAL_PRICE"] : end($subscribe)["SPECIAL_PRICE"]; ?>" data-amount="<?= $v["AMOUNT"] ?>" data-student="<?= htmlspecialchars(json_encode([$student["NAME"], $student["PHONE"]])) ?>" data-teacher="<?= $teacher_name ?>" onclick="checkPrint(this)">
                                    <i data-feather="printer"></i>
                                  </button>
                                <? endif ?>
                              </td>
                              <td aria-colindex="4" role="cell">
                                <?= $gl_sys_users[$v['CREATED_BY']]["FULL_NAME"] ?>
                              </td>
                            </tr>
                          <? endforeach ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane" id="profile-just" role="tabpanel" aria-labelledby="profile-tab-justified">
                    <table class="table">
                      <thead>
                        <th>Izoh</th>
                        <th>Sana</th>
                        <th>Hodim</th>
                      </thead>
                      <tbody>
                        <? foreach ($reminders as $reminder) : ?>
                          <tr>
                            <td><?= $reminder["TEXT"] ?></td>
                            <td><?= date("d.m.Y (H:i)", strtotime($reminder["CREATED_DATE"])) ?></td>
                            <td><?= $gl_sys_users[$reminder["CREATED_BY"]]["FULL_NAME"] ?></td>
                          </tr>
                        <? endforeach ?>
                      </tbody>
                    </table>
                  </div>
                  <div class="tab-pane" id="price-history-just" role="tabpanel" aria-labelledby="price-history-tab-justified">
                    <div class="row">
                      <div class="col-12 d-flex justify-content-end pb-1">
                        <button class="btn btn-icon btn-outline-primary rounded-circle" onclick="addPriceModal()"><i data-feather="plus-circle"></i></button>
                      </div>
                      <div class="col-12">
                        <table class="table">
                          <thead>
                            <tr>
                              <th>Boshlanish</th>
                              <th>Tugash</th>
                              <th>Guruh</th>
                              <th>Narxi</th>
                            </tr>
                          </thead>
                          <tbody>
                            <? foreach (db::arr("SELECT * FROM `subscribe_list` WHERE STUDENT_ID='$student_id' ORDER BY ACTIVE DESC") as $value) : ?>
                              <tr>
                                <td><?= date("d.m.Y", strtotime($value["START_DATE"])) ?></td>
                                <? if ($value["END_DATE"] === "0000-00-00 00:00:00" and $value["STATUS"] === "active") : ?>
                                  <td>Aktiv</td>
                                <? elseif ($value["END_DATE"] === "0000-00-00 00:00:00" and $value["STATUS"] !== "active") : ?>
                                  <td><?= $subscribe_statuses[$value["STATUS"]] ?></td>
                                <? elseif ($value["END_DATE"] != "0000-00-00 00:00:00") : ?>
                                  <td><?= date("d.m.Y", strtotime($value["END_DATE"])); ?></td>
                                <? endif; ?>
                                <td><?= $group_list[$value["GROUP_ID"]]["NAME"] ?></td>
                                <td><?= number_format($value["SPECIAL_PRICE"], 0, "", " ") ?></td>
                              </tr>
                            <? endforeach ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane" id="phone-calles-just" role="tabpanel" aria-labelledby="phone-calles-tab-justified">
                    <div class="row">
                      <? foreach ($history_list as $v) : ?>
                        <div class="col-md-6 col-sm-12">
                          <div class="card">
                            <div class="card-body shadow-lg round">
                              <div class="card-title">Qo'ng'iroq</div>
                              <div class="card-text d-flex flex-column align-items-start">
                                <span><b>Qo'shilgan Sana:</b> <?= $v["start_date"] ?></span>
                                <span><b>Qo'shgan:</b> <?= $gl_sys_users[$v["CREATED_BY"]]["FULL_NAME"] ?></span>
                                <span><b>Dars Sanasi:</b> <?= $v["lesson_date"] ?></span>
                                <span><b>Tugatilgan Sana:</b> <?= $v["end_date"] ?></span>
                                <span><b>Tugatgan:</b> <?= $gl_sys_users[$v["COMPLETED_BY"]]["FULL_NAME"] ?></span>
                                <span><b>Izoh:</b> <?= $v["COMMENT"] ?></span>
                              </div>
                            </div>
                          </div>
                        </div>
                      <? endforeach ?>
                    </div>
                  </div>
                  <div class="tab-pane" id="sms-messages-just" role="tabpanel" aria-labelledby="sms-messages-tab-justified">
                    <div class="table-responsive">
                      <table class="table">
                        <thead>
                          <tr>
                            <th>Sana</th>
                            <th>Jonatgan Hodim</th>
                            <th>Matin</th>
                            <th>Haraqat</th>
                          </tr>
                        </thead>
                        <tbody>
                          <? foreach ($sms_logs as $sms_log) : ?>
                            <tr data-id="<?= $sms_log["ID"] ?>">
                              <? if ($sms_log["SDATE"] != "00.00.0000 00:00") : ?>
                                <td><?= $sms_log["SDATE"] ?></td>
                              <? else : ?>
                                <td><i data-feather="x-circle"></i></td>
                              <? endif ?>
                              <td><?= $gl_sys_users[$sms_log['CREATED_BY']]["FULL_NAME"] ?></td>
                              <td><span class="single-line-text" title="<?= $sms_log["TEXT"] ?>"><?= $sms_log["TEXT"] ?></span></td>
                              <? if ($sms_log["ACTIVE"] == 0) : ?>
                                <td><button class="btn btn-primary btn-icon rounded-circle" onclick="conformModal(<?= $sms_log['ID'] ?>,'resendSubmit')" title="Resend"><i data-feather='refresh-cw'></i></button></td>
                              <? else : ?>
                                <td><span class="badge badge-pill badge-success p-1"><i data-feather="check-circle"></i></span></td>
                              <? endif; ?>
                            </tr>
                          <? endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane" id="coin-history-just" role="tabpanel" aria-labelledby="coin-history">
                    <div class="row mx-auto">
                      <? foreach ($coin_logs as $log) : ?>
                        <? $comment = json_decode($log["COMMENT"], true); ?>
                        <div class="col-xl-4 col-sm-12">
                          <div class="card ">
                            <div class="card-body shadow border round">
                              <div class="row">
                                <div class="col-12">
                                  <h4 class="card-title mb-1"><?= $coins_action_types[$log["ACTION"]] ?></h4>
                                  <div class="font-small-2"><?= $gl_sys_users[$log["USER_ID"]]["FULL_NAME"] ?></div>
                                  <h5 class="mb-1">
                                    <? $last_item = end($comment);
                                    echo filter_var($last_item, FILTER_VALIDATE_INT);
                                    ?></h5>
                                  <small class="text-muted"><?= date("d.m.Y (H:i)", strtotime($log["LOG_DATE"])); ?></small>
                                  <div class="card-text text-muted font-small-2 position-relative overflow-hidden">
                                    <p class="single-line mb-0 text-primary">
                                      <?= isset($coins_type[$comment[0]]) ? $coins_type[$comment[0]] : $comment[0]; ?>
                                    </p>
                                    <? if (strlen($comment[0]) > 42) : ?>
                                      <a href="javascript:void()" class="read-more position-absolute badge badge-light-info" style="bottom: 0; right:0;">more</a>
                                    <? endif ?>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      <? endforeach; ?>
                    </div>
                  </div>
                  <div class="tab-pane" id="history-just" role="tabpanel" aria-labelledby="history-tab-justified">
                    <div class="row">
                      <? foreach ($student_logs as $action) : ?>
                        <div class="col-md-6 col-sm-12">
                          <div class="card mb-1">
                            <div class="card-body round shadow border">
                              <div class="card-title" title="<? //=$action["ACTION"];
                                                              ?>"><?= LOG_TYPES[$action["ACTION"]]; ?></div>
                              <div class="card-text d-flex flex-column align-items-start">
                                <span class="items-center"><i data-feather="calendar"></i> <?= date_var($action["LOG_DATE"], "d.m.Y (H:i)") ?></span>
                                <span class="items-center"><i data-feather="<?= $action["USER_ID"] !== "0" ? "user" : "monitor" ?>"></i> <?= $action["USER_ID"] !== "0" ? $gl_sys_users[$action["USER_ID"]]["FULL_NAME"] : "Tizim" ?></span>
                                <? if ($action["ACTION"] == "change_student_group") : ?>
                                  <? [$from_group, $to_group] = explode(" -> ", $action["COMMENT"]) ?>
                                  <span class="single-line" onclick="showDesc(this)"><i data-feather="git-branch"></i> <?= $group_list[$from_group]["NAME"] ?> dan <?= $group_list[$to_group]["NAME"] ?> ga </span>
                                <? elseif (in_array($action["ACTION"], ["freeze_student", "reminder_student"])) : ?>
                                  <span class="single-line text-secondary mt-1" onclick="showDesc(this)"><?= $action["COMMENT"] ?></span>
                                <? elseif ($action["ACTION"] == "withdraw") : ?>
                                  <span class="text-secondary mt-1">Guruh: <?= $group_list[explode("_", $action["COMMENT"])[1]]["NAME"]; ?></span>
                                <? elseif ($action["ACTION"] === "add_payment") : ?>
                                  <span class="text-secondary mt-1">To'lov: <?= number_format(json_decode($action["COMMENT"], true)["student_payment"], 0, "", " "); ?></span>
                                <? elseif ($action["ACTION"] === "open_student_task") : ?>
                                  <span><i data-feather="flag"></i> <?= date_var($student_tasks[$action["ITEM_ID"]]["DUE_DATE"], "d.m.Y (H:i)") ?></span>
                                  <span class="single-line text-secondary mt-1" onclick="showDesc(this)"><?= $student_tasks[$action["ITEM_ID"]]["TASK"] ?></span>
                                  <span class="single-line text-secondary mt-1" onclick="showDesc(this)"><i data-feather="users"></i>
                                    <? foreach (json_decode($student_tasks[$action["ITEM_ID"]]["ASSIGNED_MEMBERS"], true) as $userId) {
                                      echo $gl_sys_users[$userId]["FULL_NAME"] . "<br>";
                                    } ?>
                                  <? elseif ($action["ACTION"] === "close_student_task") : ?>
                                    <span><i data-feather="flag"></i> <?= date_var($student_tasks[$action["ITEM_ID"]]["COMPLETION_DATE"], "d.m.Y (H:i)") ?></span>
                                    <span class="single-line text-secondary mt-1" onclick="showDesc(this)"><?= $student_tasks[$action["ITEM_ID"]]["TASK"] ?></span>
                                    <span class="single-line text-secondary mt-1" onclick="showDesc(this)"><i data-feather="users"></i>
                                      <?= $gl_sys_users[$student_tasks[$action["ITEM_ID"]]["COMPLETED_BY"]]["FULL_NAME"] ?>
                                    </span>
                                  <? endif; ?>
                              </div>
                            </div>
                          </div>
                        </div>
                      <? endforeach; ?>
                    </div>
                  </div>
                  <div class="tab-pane" id="files-just" role="tabpanel" aria-labelledby="files-tab-justified">
                    <? if ($student_files == "empty") : ?>
                      <h3 class="text-center text-secondary">Maʼlumotlar topilmadi</h3>
                    <? endif; ?>
                    <div class="row">
                      <? foreach ($student_files as $file) : ?>
                        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                          <? if ($file["ACTIVE"] === "1") : ?>
                            <a href="<?= $file["PATH"]; ?>" target="_blank" class="card border round">
                              <div class="card-body">
                                <h6 class="text-primary single-line"><?= $file["NAME"]; ?></h6>
                                <small class="text-muted">Yuqlangan Sana: <?= date("d.m.Y", strtotime($file["CREATED_DATE"])); ?></small> <br>
                                <small class="text-muted">Hodim: <?= $gl_sys_users[$file["CREATED_BY"]]["FULL_NAME"] ?></small> <br>
                                <? if ($file["COMMENT"]) : ?>
                                  <p class="text-secondary mt-1 mb-0"><?= $file["COMMENT"]; ?></p>
                                <? endif; ?>
                              </div>
                            </a>
                            <span class="badge badge-pill badge-danger badge-glow badge-up waves-effect" data-toggle="tooltip" data-placement="top" data-original-title="Faylni o'chirish" style="cursor:pointer; top: -6px; right: 3px;" onclick="conformModal('<?= $file['ID'] ?>', 'deleteFile')"><i class="fa fa-times"></i></span>
                          <? else : ?>
                            <div class="card border round bg-danger bg-lighten-5">
                              <div class="card-body">
                                <h6 class="text-primary single-line"><?= $file["NAME"]; ?></h6>
                                <small class="text-muted">Yuqlangan Sana: <?= date("d.m.Y", strtotime($file["CREATED_DATE"])); ?></small> <br>
                                <small class="text-muted">Hodim: <?= $gl_sys_users[$file["CREATED_BY"]]["FULL_NAME"] ?></small> <br>
                                <? if ($file["COMMENT"]) : ?>
                                  <p class="text-secondary mt-1 mb-0"><?= $file["COMMENT"]; ?></p>
                                <? endif; ?>
                              </div>
                            </div>
                          <? endif; ?>
                        </div>
                      <? endforeach; ?>
                    </div>
                  </div>
                  <div class="tab-pane" id="online-just" role="tabpanel" aria-labelledby="online-tab-justified">
                    <div class="row">
                      <div class="col-12 d-flex justify-content-end pb-1">
                        <button type="button" onclick="addNewStudent()" class="btn btn-sm btn-outline-primary">
                          <!--<i data-feather="plus" class="mr-25"></i>-->
                          <span>Kursga qo'shish</span>
                        </button>
                      </div>
                      <?
                      $student_on = db::arr_s("SELECT * FROM student_students WHERE user_id = '$student[ID]'");

                      $event_log = db::arr("SELECT id, `action`, comment, NULL AS question_id, NULL AS correct, created_at, 'event' as entity_type
                      FROM student_event_log
                      WHERE user_id = '$student_on[id]'
                      UNION
                      SELECT id, NULL, NULL, question_id, correct, created_at, 'question' as entity_type
                      FROM student_question_log
                      WHERE student_id = '$student_on[id]'");

                      $student_action = db::arr_s("SELECT * FROM student_event_log WHERE user_id = '$student[ID]' ORDER BY id DESC LIMIT 1");

                      $course_id = db::arr("SELECT st.*,
                      access.course_id,
                      course.name
                      FROM student_students AS st
                      LEFT JOIN student_course_access AS access ON st.id = access.user_id
                      LEFT JOIN student_courses AS course ON course.id = access.course_id
                      WHERE st.user_id = '$student[ID]'");

                      ?>
                      <div class="col-md-12">
                        <? //echo '<pre>'; print_r($event_log); echo '</pre>'; 
                        ?>
                        <h4 class="card-title">Onlayn kurs</h4>
                        <table class="table">
                          <thead>
                            <th>Talaba</th>
                            <th>Kurs</th>
                          </thead>
                          <tbody>
                            <tr>
                              <td>
                                <? if ($student_on != 'empty') {
                                  echo $student_on['username'];
                                } ?>
                              </td>
                              <td>
                                <? foreach ($course_id as $cs) : ?>
                                  <div class="badge badge-glow badge-info"><?= $cs['name']; ?></div>
                                <? endforeach; ?>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- event card -->
            <div>
              <div class="tab-pane" id="online-just-event" style="display: none;" role="tabpanel" aria-labelledby="online-tab-justified">
                <div class="card" style="max-height: 400px; overflow-y: auto;">
                  <div class="card-body">
                    <div id="accordionWrapa1" role="tablist" aria-multiselectable="true">
                      <div class="card collapse-icon">
                        <div class="card-body">
                          <? function isJson($string)
                          {
                            json_decode($string);
                            return (json_last_error() == JSON_ERROR_NONE);
                          } ?>
                          <? foreach ($event_log as $v) : ?>
                            <? $plus = $v['entity_type'] . $v['id']; ?>
                            <?  $bin = bindec($v['correct']); ?>
                            <? $question_log = db::arr_s("SELECT * FROM student_questions WHERE id = '$v[question_id]' AND try_number IS NOT NULL"); ?>
                            <? if (isset($question_log['id']) or isset($v['action'])) : ?>
                              <div class="collapse-margin">
                                <div class="card">
                                  <div id="headingCollapse<?= $plus ?>" class="card-header" data-toggle="collapse" role="button" data-target="#collapse<?= $plus ?>" aria-expanded="false" aria-controls="collapse<?= $plus ?>">
                                    <div class="col-md-3">
                                      <span class="lead collapse-title"><? if (is_null($v['question_id'])) : ?><?= $v['created_at'] ?><? else : ?><?= $v['created_at'] ?><? endif; ?></span>
                                    </div>
                                    <div class="col-md-3">
                                      <span class="lead collapse-title"><?= $v['entity_type'] ?></span>
                                    </div>
                                    <div class="col-md-3">
                                      <span class="lead collapse-title"><? if (is_null($v['action'])) : ?><?= $v['question_id'] ?><? else : ?><?= $v['action'] ?><? endif; ?></span>
                                    </div>
                                    <div class="col-md-1">
                                      <? if (!is_null($v['question_id'])) : ?>
                                        <? if($bin != 0): ?>
                                      <div class="green-circle" style="background-color: #28C76F;"></div>
                                        <? else: ?>  
                                      <div class="green-circle" style="background-color: #EA5455;"></div>
                                        <? endif; ?>
                                      <? endif; ?>
                                    </div>
                                    <div class="col-md-2">
                                      <? if (!is_null($v['question_id'])) : ?>
                                        <td>
                                          <button type="button" onclick="deleteQuestion(<?= $v['question_id'] ?>)" class="btn btn-icon rounded-circle btn-outline-danger">
                                            <i data-feather="trash"></i>
                                          </button>
                                        </td>
                                      <? endif; ?>
                                    </div>
                                  </div>
                                  <div id="collapse<?= $plus ?>" role="tabpanel" aria-labelledby="headingCollapse<?= $plus ?>" class="collapse">
                                    <div class="card-body">
                                      <table class="table">
                                        <tbody>
                                          <? if (is_null($v['question_id'])) : ?>
                                            <? if (isJson($v['comment'])) : ?>
                                              <? $comm = json_decode($v['comment'], true); ?>
                                              <ul class="list-group list-group-flush">
                                                <? foreach ($comm as $key => $c) : ?>
                                                  <li class="list-group-item pl-0"><b><?= $key ?>: </b><?= $c ?></li>
                                                <? endforeach; ?>
                                              </ul>
                                            <? else : ?>
                                              <tr>
                                                <td>
                                                  <?= $v['comment'] ?>
                                                </td>
                                              </tr>
                                            <? endif; ?>
                                          <? else : ?>
                                            <tr>
                                              <td>
                                                <?= $question_log['question']; ?>
                                              </td>
                                            </tr>
                                          <? endif; ?>
                                        </tbody>
                                      </table>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            <? endif; ?>
                          <? endforeach; ?>
                          <style>
                            .green-circle {
                              width: 20px;
                              height: 20px;
                              border-radius: 50%;
                            }
                          </style>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>
<!-- Add Modal -->
<div class="modal fade text-left" id="add_to_group" tabindex="-1" role="dialog" aria-labelledby="addModalLabel1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="addModalLabel1">Guruhga qo`shish</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="" method="post" id="addForm" enctype="multipart/form-data">
          <input type="hidden" name="student_id" value="<?= $id_encrypte ?>">
          <div class="mb-3">
            <label class="label-form">Guruh</label>
            <select class="select2 form-control form-control-lg" name="group_id" required>
              <option selected disabled>Guruhni tanlang</option>
              <? foreach (db::arr("SELECT * FROM `group_list` WHERE STATUS IN ('active','waiting') $sql_org_id AND ID NOT IN (SELECT GROUP_ID FROM `subscribe_list` WHERE STUDENT_ID = '$student_id' AND ACTIVE=1)") as $v) : ?>
                <option value="<?= $v['ID'] ?>"><?= $v['NAME'] ?></option>
              <? endforeach ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">To'lov Turi</label>
            <select name="paymentType" class="form-control">
              <option value="simple">Davomat bo'yicha</option>
              <option value="monthly">Oylik</option>
              <option value="free">Bepul</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="label-from">Maxsus Narx</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <input type="checkbox" name="with_special_price" />
                </div>
              </div>
              <input type="text" name="special_price" autocomplete="off" class="form-control formated-number-input" readonly="readonly">
            </div>
          </div>
          <div class="mb-3">
            <label class="label-form">Boshlanish sanasi</label>
            <input type="date" class="form-control" name="start_date">
          </div>
          <div class="mb-2">
            <label>To'lov muddati (Oyda)</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <input type="checkbox" name="with_sub_length" />
                </div>
              </div>
              <input type="number" name="sub_length" autocomplete="off" class="form-control" readonly="readonly">
            </div>
          </div>
          <div class="d-flex justify-content-between">
            <div class="custom-control custom-radio">
              <input type="radio" id="no-laptop" name="give_laptop" value="true" class="custom-control-input">
              <label class="custom-control-label" for="no-laptop">Talabaga noutbuk berildi</label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" id="has-laptop" name="give_laptop" value="false" class="custom-control-input">
              <label class="custom-control-label" for="has-laptop">Talabaga noutbuk berilmadi</label>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
        <button type="submit" form="addForm" name="add_group" class="btn btn-primary">Qo'shish</button>
      </div>
    </div>
  </div>
</div>
<script>
  $(document).ready(function() {
    const tabIds = [
      "home-tab-justified",
      "profile-tab-justified",
      "price-history-tab-justified",
      "phone-calles-justified",
      "sms-messages-justified",
      "coin-history-justified",
      "history-tab-justified",
      "files-tab-justified",
      "online-tab-justified"
    ];

    const clickLog = $("#online-just-event");

    tabIds.forEach(function(tabId) {
      $("#" + tabId).on("click", function(e) {
        clickLog.css("display", tabId === "online-tab-justified" ? "block" : "none");
      });
    });
  });
</script>

<script>
  document.querySelector("input[name=with_special_price]").addEventListener("input", setReadOnly);
  document.querySelector("input[name=with_sub_length]").addEventListener("input", setReadOnly);

  function setReadOnly(e) {
    const inputName = e.target.name.replace(/^with\_/, "");
    const input = document.querySelector(`input[name=${inputName}]`);
    if (!e.target.checked) {
      input.setAttribute("readonly", "readonly");
      input.value = "";
    } else {
      input.removeAttribute("readonly");
    }
  }

  $(document).ready(function() {

    $('[name=paymentType]').change(function() {
      if (this.value == 'dollar') {
        $('#dollar_content').show();
        $("[name=paymentAmount_usd]").attr("required", true);
        $("[name=paymentAmount_qaytim]").attr("required", true);
      } else {
        $('[name=paymentAmount_usd]').removeAttr('required');
        $('[name=paymentAmount_qaytim]').removeAttr('required');
        $('#dollar_content').hide();
      }
    });


    edit_tr = function(tr_id, student_id) {
      fd = new FormData();
      fd.append('tr_id', tr_id);
      fd.append('student_id', student_id);
      js_ajax_post('student_list/edit_tr.php', fd).done(function(d) {
        $('#one_modal').html(d);
        $('#one_modal').modal('show');
      });
    }

  });
</script>

<div class="modal fade" id="one_modal" role="dialog" aria-labelledby="taskModalLabel"></div>



<?
require('modules/med/student_list/student_list_modals.php');
?>
<?
require('modules/med/student_list/student_list_js.php');
?>