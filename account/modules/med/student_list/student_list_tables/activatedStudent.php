<?
$from = date("Y-m-01 00:00:00");
$to = date("Y-m-t 23:59:59");
if (!empty($_SESSION["studentFilterDate"])) {
  [$from, $to] = explode(" to ", $_SESSION["studentFilterDate"]);
  $to = empty($to) ? date("Y-m-d 23:59:59", strtotime($from)) : date("Y-m-d 23:59:59", strtotime($to));
}
$table_log = db::arr("SELECT tbl.USER_ID, tbl.LOG_DATE, tbl.ACTION, tbl.COMMENT, sbl.STUDENT_ID FROM `table_log` tbl
    LEFT JOIN `subscribe_list` sbl ON sbl.ID = tbl.ITEM_ID
    WHERE tbl.`ACTION` = 'activate_student'
    AND tbl.`LOG_DATE` BETWEEN '$from' AND '$to'
    AND sbl.STUDENT_ID IN (SELECT ID FROM `student_list` WHERE `ORG_ID`='$org_id')
    ORDER BY LOG_DATE ASC
  ");
$student_arr = array_map(fn ($log) => $log["STUDENT_ID"], $table_log);
$student_str = join("','", $student_arr);
$student_list = db::arr_by_id("SELECT * 
  FROM `student_list` 
  WHERE `ID` IN ('$student_str')
  $sql_org_id ORDER BY ID DESC");
$log_actions = ["defrosted" => "Eritilgan", "restore_student" => "Qayta tiklandi", "activate_student" => "Faollashtirilgan"];
?>
<div class="card-datatable">
  <table class="d_tab dt-responsive table" id="customtable">
    <thead>
      <tr>
        <th></th>
        <th>#</th>
        <th>Ism</th>
        <th>Telefon</th>
        <th>Guruh</th>
        <th>O'qituvchi</th>
        <th>Amal</th>
        <th>Amal sanasi</th>
        <th>Individual narx</th>
        <th>Balans</th>
        <th>Harakat</th>
      </tr>
    </thead>
    <tbody>
      <? foreach ($table_log as $log) : ?>
        <? $v = $student_list[$log["STUDENT_ID"]]; ?>
        <tr class="<?= $v["ACTIVE"] != '0' ?: "table-danger"; ?>">
          <td></td>
          <td><?= $stu_index++; ?></td>
          <td><a href="/account/student_list/detail/<?= $v['ID'] ?>" class="text-primary" target="_blank"><?= $v["NAME"] ?></a></td>
          <td><?= str_replace(" ", "", $v["PHONE"]); ?></td>
          <td><?= $group_list[$v["ID"]]["NAME"] ?></td>
          <td><?= $group_list[$v["ID"]]["TEACHER_NAME"] ?></td>
          <td>
            <p class="badge badge-info badge-glow"><?= $log_actions[$log["ACTION"]]; ?></p>
          </td>
          <td><?= date_var($log["LOG_DATE"], 'd.m.Y (H:i)'); ?></td>
          <? $total_price += intval($group_list[$v["ID"]]["SPECIAL_PRICE"]) ?>
          <? $total_balance += intval($v["CURRENT_BALANCE"]) ?>
          <td><?= $group_list[$v["ID"]]["SPECIAL_PRICE"] ?></td>
          <td><?= number_format($v["CURRENT_BALANCE"], 0, "", "") ?></td>
          <td>
            <div class="dropdown">
              <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                <i data-feather="more-vertical"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" data-json="<?= htmlspecialchars(json_encode($v)) ?>" onclick="editModal(this)" href="javascript:void(0);">
                  <i data-feather="edit-2" class="mr-50"></i>
                  <span>Tahrirlash</span>
                </a>
                <a class="dropdown-item" onclick="deleteModal(<?= $v['ID'] ?>)" href="javascript:void(0);">
                  <i data-feather="trash-2" class="mr-50"></i>
                  <span>Oʻchirish</span>
                </a>
              </div>
            </div>
          </td>
        </tr>
      <? endforeach ?>
    </tbody>
    <tfoot>
      <th></th>
      <th>#<?= $stu_index - 1; ?></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th>Narx: <?= number_format($total_price, 0, "", " ") ?></th>
      <th>Balans: <?= number_format($total_balance, 0, "", " ") ?></th>
      <th></th>
    </tfoot>
  </table>
</div>