<?
$now = date("Y-m-d H:i:s");
$user = $_SESSION["user"]["id"];
$org_id = $_SESSION['USER']['ORG_ID'];

if (isset($_POST["taskCompleteSubmit"])) {
  db::query("UPDATE `list_tasks` SET `COMPLETED_BY`='$user', `COMMENT`='$_POST[comment]', `COMPLETION_DATE`='$now', `STATUS`='closed' WHERE ID='$_POST[taskCompleteID]'");
  LocalRedirect("index.php");
}

if (isset($_POST["taskProgressSubmit"])) {
  db::query("UPDATE `list_tasks` SET `COMPLETED_BY`='$user', `COMMENT`='$_POST[comment]', `STATUS`='inprogress' WHERE ID='$_POST[taskCompleteID]'");
  LocalRedirect("index.php");
}


if (isset($_POST['taskSubmit2'])) {

  $user_id = $_SESSION["user"]["id"];
  $task_text  = filter_input(INPUT_POST, "taskText", FILTER_SANITIZE_ADD_SLASHES);
  $due = $_POST['due_date'];

  $members_id = json_encode($_POST["responsible"]);

  if ($_POST['add_type'] == 'general') {
    $students = db::arr("SELECT * FROM student_list WHERE ACTIVE = '1' AND ORG_ID = '$org_id'");
  }

  if (isset($_POST['add_group'])) {
    $grid = $_POST['add_group'];
    $st = implode(",", $grid);
    $students = db::arr("SELECT 
    sub.*,
    sl.ID
    FROM subscribe_list AS sub
    LEFT JOIN student_list AS sl ON sl.ID = sub.STUDENT_ID
    WHERE sub.GROUP_ID IN ($st)");
  }

  if (isset($_POST['add_student'])) {
    $student = $_POST['add_student'];
    $cs = implode(",", $student);
    $students = db::arr("SELECT * FROM student_list WHERE ID IN ($cs) AND ORG_ID = '$org_id'");
  }


  foreach ($students as $v) {

    $target_id = $v['ID'];
    $type = $_POST['add_type'];

    $insert = db::query("INSERT INTO `list_tasks` (
    `ORG_ID`, 
    `CREATE_DATE`, 
    `CREATED_BY`, 
    `TYPE`,
    `TARGET_ID`,
    `TASK`,
    `DUE_DATE`,
    `ASSIGNED_MEMBERS`,
    `STATUS`
    ) VALUES (
    '$org_id',
    '$now',
    '$user_id',
    '$type',
    '$target_id',
    '$task_text',
    '$due',
    '$members_id',
    'open'
    )");
  }
  LocalRedirect("index.php");
}

$responsible = db::arr("SELECT `ID`, CONCAT(NAME,' ', SURNAME, ' ', PHONE) AS `NAME` FROM `gl_sys_users` WHERE `ROLE_ID` IN (1,2,3,4) AND ORG_ID IN ('1', '3', '4', '5') AND STATUS = '1'");

$group_list = db::arr("SELECT * FROM `group_list` WHERE STATUS = 'active'");

$student_list = db::arr("SELECT * FROM `student_list` WHERE ACTIVE = '1' AND ORG_ID = '$org_id'");


?>

<!-- Task Student -->
<div class="modal fade text-left" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel1">Yangi vazifa qo'shing</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="post" enctype="multipart/form-data">
        <div class="modal-body">
          <label class="form-label">Mas'ul</label>
          <div class="mb-3">
            <select name="responsible[]" id="" class="select2 form-control" multiple>
              <? foreach ($responsible as $v) : ?>
                <option value="<?= $v["ID"]; ?>"><?= $v["NAME"]; ?></option>
              <? endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Topshirish Muddati</label>
            <input type="date" name="due_date" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Turi</label>
            <select name="add_type" id="click_type" class="form-control">
              <option value="general">Umumiy</option>
              <option value="group">Guruh</option>
              <option value="student">Talaba</option>
            </select>
          </div>
          <div id="click_group" style="display: none;">
            <label class="form-label">Guruh</label>
            <div class="mb-3">
              <select name="add_group[]" class="select2 form-control" multiple>
                <? foreach ($group_list as $v) : ?>
                  <option value="<?= $v['ID'] ?>"><?= $v['NAME'] ?></option>
                <? endforeach; ?>
              </select>
            </div>
          </div>
          <div id="click_student" style="display: none;">
            <label class="form-label">Talaba</label>
            <div class="mb-3">
              <select name="add_student[]" class="select2 form-control" multiple>
                <? foreach ($student_list as $v) : ?>
                  <option value="<?= $v['ID'] ?>"><?= $v['NAME'] ?></option>
                <? endforeach; ?>
              </select>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Vazifa</label>
            <textarea name="taskText" rows="5" class="form-control" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
          <button type="submit" name="taskSubmit2" class="btn btn-primary">Tasdiqlash</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade text-left" id="taskCompleteModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="editModal">Tasdiqlash</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="post" id="taskCompleteForm">
        <div class="modal-body">
          <input type="hidden" name="taskCompleteID">
          <div class="row">
            <div class="col-md-12 mb-2">
              <label class="form-label">Izoh yozish</label>
              <textarea name="comment" class="form-control" rows="5"></textarea>
            </div>
            <div class="col-md-12 p-1 mt-1">
              <h5>Bu vazifani bajarilgan deb belgilashni xohlaysizmi?</h5>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger" name="taskProgressSubmit">Bajarish jarayonida</button>
          <button type="submit" class="btn btn-primary" form="taskCompleteForm" name="taskCompleteSubmit">Bajarildi</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Filter -->

<div class="modal fade text-left" id="filter1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel1">Filter</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="post" id="filterForm">
        <div class="modal-body">
          <div class="mb-3">
            <label>Filter turi</label>
            <select name="filterType1" class="select2 form-control">
              <option value="all" <? if ($_POST['filterType1'] == 'all') {
                                    echo 'selected';
                                  } ?>>All</option>
              <option value="open" <? if ($_POST['filterType1'] == 'open') {
                                      echo 'selected';
                                    } ?>>Open</option>
              <option value="inprogress" <? if ($_POST['filterType1'] == 'inprogress') {
                                            echo 'selected';
                                          } ?>>In progress</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
          <button type="submit" form="filterForm" name="filterSubmit1" class="btn btn-primary">Tasdiqlash</button>
        </div>
      </form>
    </div>
  </div>
</div>