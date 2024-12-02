<!-- BEGIN: Content-->
<?
$student_id = filter_var($_GET["item_id"], FILTER_SANITIZE_NUMBER_INT);
$id_encrypte = openssl_encrypt($student_id, 'AES-256-CBC', "StudentSecret", 0, substr(md5("StudentSecret"), 0, 16));
$user_id = $_SESSION["USER"]["ID"];
$now = date("Y-m-d H:i:s");
$org_id = $_SESSION["USER"]["ORG_ID"];

// homework start
$homeworks = db::arr("SELECT 
ht.CREATED_DATE,
ht.GROUP_ID,
ht.SUBJECT_ID,
hp.HOMEWORK_ID,
hp.STUDENT_ID,
hp.SCORE,
hp.SUBMIT_DATE,
hp.STATUS,
ll.NAME,
ll.COURSE_ID,
ll.HAS_HW
FROM `homework_process` hp
LEFT JOIN `homework_task` ht ON ht.ID = hp.HOMEWORK_ID
LEFT JOIN `lesson_list` ll ON ll.ID = ht.SUBJECT_ID 
WHERE hp.STUDENT_ID='$student_id' AND hp.STATUS IN ('completed','graded')");

$student = db::arr_s("SELECT * FROM `student_list` WHERE ID='$student_id'");

if ($org_id > 0) {
  $sql_org_id = " AND `ORG_ID`='$org_id'";
  // $where_org_id = "WHERE AND `ORG_ID`='$org_id'";
}
$subscribe = db::arr_by_id("SELECT * FROM `subscribe_list` WHERE STUDENT_ID='$student_id' AND ACTIVE=1");
$last_tran = db::arr_s("SELECT * FROM `transaction_list` WHERE STUDENT_ID='$student_id' AND ACTION_TYPE ='add' ORDER BY TRANSACTION_DATE DESC LIMIT 1");
$course_list = db::arr_by_id("SELECT * FROM `course_list` ");
$group_list = db::arr_by_id("SELECT * FROM `group_list`");
$teacher_list = db::arr_by_id("SELECT * FROM `teacher_list`");
$gl_sys_users = db::arr_by_id("SELECT `ID`,CONCAT(`NAME`,' ',`SURNAME`) AS FULL_NAME FROM `gl_sys_users`");

?>

<?
// $teachers = db::arr_by_id("SELECT * FROM `gy_sys_users` WHERE `ROLE_ID`='4' AND `ORG_ID`='$org_id'");
$reminders = db::arr("SELECT * FROM `note_list` WHERE `STUDENT_ID`='$student_id' AND `ACTIVE`='1' AND CREATED_BY IN (SELECT ID FROM `gl_sys_users` WHERE `ROLE_ID`='4' AND `ORG_ID`='$org_id') ORDER BY `ID` DESC");
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
      <section id="responsive-datatable">
        <div class="row">
          <div class="col-12 <?if(!isset($_SESSION["file_upload_error"])) echo "d-none";?>">
            <div class="alert alert-danger">
              <div class="alert-body"><?= $_SESSION["file_upload_error"]; unset($_SESSION["file_upload_error"]);?></div>
            </div>
          </div>
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
                        <li class="list-group-item pl-0"><b>Tug'ilgan Sana: </b> <?= $student["BIRTH_DATE"] ?> (<?= date_diff(date_create($student["BIRTH_DATE"]), date_create('today'))->y ?> y.)</li>
                        <li class="list-group-item pl-0"><b>Ota-ona Ismi: </b> <?= $student["PARENT_NAME"] ?></li>
                        <li class="list-group-item pl-0"><b>Ota-ona tel. Raqami: </b> <?= $student["PARENT_PHONE"] ?></li>
                        <li class="list-group-item pl-0"><b>Coin'lar soni: </b> <?= $student["COINS"] ?></li>
                        <li class="list-group-item pl-0"><b>Telegram parol: </b> <?= $gl_sys_users["PASSWORD"] ?></li>
                      </ul>
                    </div>
                  </div>
                  <div class="col-md-2 d-flex justify-content-center">
                    <div class="position-absolute card-top-buttons d-flex flex-column">
                      
                      <button class="btn btn-warning btn-icon rounded-circle mb-1" onclick="coinModal(<?= $student['ID'] ?>)"><i data-feather="compass"></i></button>
                    </div>
                  </div>
                </div>
                <hr>
                <div class="mb-1">
                  <span class="text-muted text-small">Talaba qo'shilgan sana:<b> <?= date_var($student['CREATED_DATE'], 'd.m.Y'); ?></b></span>
                </div>    
              </div>
            </div>
          </div>
          <div class="col-right col-lg-12 col-xl-8 col-12">
            <div class="card">
              <div class="card-body">
                <ul class="nav nav-tabs nav-justified" id="myTab2" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="homework-tab-justified" data-toggle="tab" href="#homework-just" role="tab" aria-controls="homework-just" aria-selected="true">Uyga vazifalar</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="profile-tab-justified" data-toggle="tab" href="#profile-just" role="tab" aria-controls="profile-just" aria-selected="false">Izohlar</a>
                  </li>                 
                </ul>
                <!-- Tab panes -->
                <div class="tab-content pt-1">
                  <div class="tab-pane active" id="homework-just" role="tabpanel" aria-labelledby="homework-tab-justified">
                    <div class="row">
                      <?if($homeworks === "empty"):?>
                        <div class="col-12">
                          <h4 class="text-primary text-center">Uy Vazifalari Topilmadi.</h4>
                        </div>
                      <?endif;?>
                      <? foreach ($homeworks as $homework) : ?>
                        <div class="col-xl-6">
                          <div class="card mb-3">
                            <div class="card-body shadow border round p-1">
                                <div class="align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero align-items-lg-center">
                                  <h4 class="text-muted">Mavzu: <span class="text-primary"><?= $homework['NAME'] ?></span></h4>
                                </div>
                              </a>                              
                              <div class="d-flex justify-content-between">
                                <div class="subscribe-info">
                                  <div>
                                    <span class="text-muted">Guruh: <b><?= $group_list[$homework["GROUP_ID"]]["NAME"]?></b></span>
                                  </div>
                                  <div>
                                    <span class="text-muted">Topshirgan sanasi: <b><?= $homework["SUBMIT_DATE"] !== "0000-00-00 00:00:00" ? date("d.m.Y (H:i)", strtotime($homework["SUBMIT_DATE"])) : "" ?></b></span> 
                                  </div>
                                  <div>
                                    <span class="text-muted">Bahosi:  <b><?= $homework['SCORE']?></b></span> 
                                  </div>
                                </div>
                              
                              </div>
                            </div>
                          </div>
                        </div>
                      <? endforeach ?>
                    </div>
                  </div>
                  <div class="tab-pane" id="profile-just" role="tabpanel" aria-labelledby="profile-tab-justified">
                  <div class="col-12 d-flex justify-content-end pb-1">
                      <button class="btn btn-icon btn-outline-primary rounded-circle" data-toggle="modal" data-target="#reminderModal"><i data-feather="plus-circle"></i></button>
                  </div>
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
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>

<?
require('modules/med/student_list/teacher_page/section_modals.php');
?>