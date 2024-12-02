<?
require $_SERVER["DOCUMENT_ROOT"] . '/core/backend.php';
$org_id = isset($_SESSION["USER"]["ORG_ID"]) ? $_SESSION["USER"]["ORG_ID"] : 0;
$date = new DateTime("now");
$from = $_POST["from"];
$to = $_POST["to"];
$exception = isset($_POST["exception"]) ? json_decode($_POST["exception"], true) : [];
$days = ["1,3,5" => createPlan($from, $to, $exception)["odd"], "2,4,6" => createPlan($from, $to, $exception)["even"]];
$teacher = db::arr_s("SELECT ID, SALARY,`TYPE`,`NAME` FROM `teacher_list` WHERE ID='$_POST[teacher]'");

$search_by_role = $teacher["TYPE"] === "teacher" ? "TEACHER_ID" : "TA_ID";
$archive_students = db::arr_by_id("SELECT ID FROM `student_list` WHERE `ACTIVE`='0' AND `ORG_ID`='$org_id'");
$archive_students_arr = array_keys($archive_students);
$attendance_student = $calculate_attendance = [];

$attendance_list = db::arr("SELECT 
al.STUDENT_JSON,
al.LESSON_DATE,
gl.ID `gid`,
gl.DAYS
FROM `attendance_list` al 
LEFT JOIN `group_list` gl ON gl.ID = al.GROUP_ID
WHERE 
gl.STATUS='active' AND gl.`$search_by_role`= '$teacher[ID]' AND
al.`LESSON_DATE` BETWEEN '$from' AND '$to'");

foreach ($attendance_list as $value) {
  $attendance_arr = json_decode($value["STUDENT_JSON"], true);
  $attendance_filter = array_filter($attendance_arr, fn ($attend_status, $student) => is_numeric($attend_status) && !in_array($student, $archive_students_arr), ARRAY_FILTER_USE_BOTH);
  // $student_attendance_amount = array_map(fn($student_id, $status) =>[$student_id => $status], array_keys($attendance_filter), array_values($attendance_filter));
  foreach ($attendance_filter as $id => $status) {
    $attendance_student["$id"][$value["DAYS"]] += 1;
  }
}

$students_id = join(",", array_keys($attendance_student));
$student_index = 1;
$groups = db::arr_by_id("SELECT
stl.ID,
stl.NAME `student`,
gl.ID `gid`,
gl.NAME `group`,
sbl.STATUS,
sbl.ACTIVE,
gl.DAYS `days`
FROM `student_list` stl
LEFT JOIN `subscribe_list` sbl ON sbl.STUDENT_ID = stl.ID
LEFT JOIN `group_list` gl ON gl.ID = sbl.GROUP_ID
WHERE stl.ID IN ($students_id) AND sbl.STATUS='active' AND sbl.ACTIVE=1
ORDER BY stl.NAME ASC");
?>
<pre><?//print_r($archive_students_arr)?></pre>
<div class="content-body">
  <!-- Responsive Datatable -->
  <section id="responsive-datatable">
    <div class="row">
      <div class="col-12 d-none" id="alert-box">
        <div class="alert alert-danger mt-1 alert-validation-msg" role="alert">
          <div class="alert-body">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-info mr-50 align-middle">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="16" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <span id="alert-message"></span>
          </div>
        </div>
      </div>
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4><?= $teacher["NAME"] ?></h4><br>
            <div class="input-group w-25">
              <input type="number" class="form-control w-25" value="<?= $teacher["SALARY"] ?>" id="teacher-salary" aria-describedby="button-addon2">
              <div class="input-group-append" id="button-addon2">
                <button class="btn btn-outline-primary waves-effect" id="recalculate" type="button">Go</button>
              </div>
            </div>
            <span class="test-secondary"><?= date("d.m.Y (H:i)", strtotime($from)) ?> dan <?= date("d.m.Y (H:i)", strtotime($to)) ?> gacha</span>
          </div>
          <div class="card-datatable table-responsive">
            <table class="d_tab dt-responsive table" id="customtable">
              <thead>
                <tr>
                  <th></th>
                  <th>#</th>
                  <th>Guruh</th>
                  <th>Talaba</th>
                  <th>Davrdagi darslar</th>
                  <th>Belgilangan darslarning soni</th>
                  <th>So'm</th>
                  <? if ($_SESSION["USER"]["ROLE_ID"] == "1") : ?>
                    <th>Talaba statusi</th>
                  <? endif ?>
                </tr>
              </thead>
              <tbody>
                <? foreach ($attendance_student as $student => $arr_value) : ?>
                  <?
                  if ($groups[$student]["STATUS"] != "active" or $groups[$student]["ACTIVE"] != "1") continue;
                  $group_days = isset($arr_value["1,3,5"]) ? "1,3,5" : "2,4,6";
                  ?>
                  <tr>
                    <td></td>
                    <td><?= $student_index++; ?></td>
                    <td><?= $groups[$student]["group"] ?></td>
                    <td><?= $groups[$student]["student"] ?></td>
                    <td data-column="planed"><?= $days[$group_days] ?></td>
                    <? $total_atten += $arr_value[$group_days] ?>
                    <td data-column="actual"><?= $arr_value[$group_days] ?></td>
                    <? $total = ($teacher["SALARY"] / $days[$group_days]) * $arr_value[$group_days];
                    $final += $total; ?>
                    <td data-column="salary"><?= number_format($total, 0, "", " ") ?></td>
                    <? if ($_SESSION["USER"]["ROLE_ID"] == "1") : ?>
                      <td><?= $groups[$student]["STATUS"] ?></td>
                    <? endif ?>
                  </tr>
                <? endforeach; ?>
              </tbody>
              <tfoot>
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><?= $total_atten ?></td>
                  <td data-column="total"><b>Jami: </b><?= number_format($final, 0, "", " ") ?></td>
                  <? if ($_SESSION["USER"]["ROLE_ID"] == "1") : ?>
                    <td></td>
                  <? endif ?>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>