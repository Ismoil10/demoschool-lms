<?
$odd_days = createPlan($from, $to, $_SESSION["exception_date"])["odd"];
$even_days = createPlan($from, $to, $_SESSION["exception_date"])["even"];
$teacher_groups = [];
$teacher_list = db::arr("SELECT 
gl.ID `GID`,
gl.`NAME` `GROUP`,
tl.ID `TID`,
tl.NAME `TEACHER`
FROM `group_list` gl 
INNER JOIN `teacher_list` tl ON gl.TEACHER_ID = tl.ID
WHERE gl.STATUS='active' $sql_teacher");

foreach ($teacher_list as $value) {
  if (!isset($teacher_groups[$value["TID"]])) {
    $teacher_groups[$value["TID"]] = [];
    $teacher_amount_atten[$value["TID"]]["1,3,5"] = 0;
    $teacher_amount_atten[$value["TID"]]["2,4,6"] = 0;
  }
  array_push($teacher_groups[$value["TID"]], $value["GID"]);
}

foreach ($teacher_groups as $tid => $gids) {
  $group_ids = join(",", $gids);
  $att = db::arr("SELECT *, (SELECT `DAYS` FROM `group_list` WHERE ID=attendance_list.GROUP_ID ) AS `DAYS` FROM `attendance_list` WHERE `LESSON_DATE` BETWEEN '$from' AND '$to' AND GROUP_ID IN ($group_ids)");
  if ($att == "empty") continue;
  foreach ($att as $attendance) {
    $arr_atten = json_decode($attendance["STUDENT_JSON"], true);
    $valid_atten = array_filter($arr_atten, function ($var) {
      return is_numeric($var);
    });
    $join_ids = join(",", array_keys($valid_atten));
    $active_student = db::arr_s("SELECT COUNT(ID) `amount` FROM `subscribe_list` WHERE STUDENT_ID IN ($join_ids) AND ACTIVE=1 AND `STATUS`='active'");
    $teacher_amount_atten[$tid][$attendance["DAYS"]] += $active_student["amount"];
  }
}

$teacher_type = [
  "teacher" => "O'qituvchi",
  "ta" => "O'qituvchi yordamchisi"
];
?>
<div class="content-body">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div>
            <h4 class="font-weight-bolder mb-0">Davrdagi ish haqi: <?= date("d.m.Y (H:i)", strtotime($from)) ?> ~ <?= date("d.m.Y (H:i)", strtotime($to)); ?></h4>
          </div>
          <div class="avatar bg-light-primary p-50 m-0">
            <div class="avatar-content">
              <i data-feather="calendar" class="font-medium-5"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="content-body">
  <section id="responsive-datatable">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-datatable">
            <table class="d_tab dt-responsive table salaryReport" id="customtable">
              <thead>
                <tr>
                  <th></th>
                  <th>O`qituvchi </th>
                  <? if ($_SESSION["USER"]["ROLE_ID"] === "1") : ?>
                    <th>Davomat miqdori</th>
                  <? endif ?>
                  <th>Turi</th>
                  <th>Miqdori</th>
                </tr>
              </thead>
              <tbody>
                <? foreach (db::arr("SELECT * FROM `teacher_list` tl WHERE `ACTIVE`= 1 $sql_teacher") as $v) : ?>
                  <?
                  $for_ods = ($v["SALARY"] / $odd_days) * $teacher_amount_atten[$v["ID"]]["1,3,5"];
                  $for_evens = ($v["SALARY"] / $even_days) * $teacher_amount_atten[$v["ID"]]["2,4,6"];
                  $total = round($for_evens + $for_ods);
                  ?>
                  <tr>
                    <td></td>
                    <td><a href="javascript:void(0);" data-teacher="<?= $v["ID"] ?>" data-from="<?= $from ?>" data-to="<?= $to ?>" data-exception="<?= htmlspecialchars(json_encode($_SESSION["exception_date"])) ?>"><?= $v['NAME'] ?></a></td>
                    <? if ($_SESSION["USER"]["ROLE_ID"] === "1") : ?>
                      <td><?= $teacher_amount_atten[$v["ID"]]["1,3,5"] + $teacher_amount_atten[$v["ID"]]["2,4,6"] ?> ta</td>
                    <? endif ?>
                    <td><?= $teacher_type[$v["TYPE"]] ?></td>
                    <td><?= number_format($total, 0, ',', ' ') ?> UZS</td>
                  </tr>
                <? endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>