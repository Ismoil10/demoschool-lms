<?

[$from, $to] = strlen($_SESSION["studentExtraFilter"]["studentCreated"]) > 0
  ? explode(" to ", $_SESSION["studentExtraFilter"]["studentCreated"])
  : [];
$to = strlen($_SESSION["studentExtraFilter"]["studentCreated"]) > 0 && strlen($to) > 0 ? date("Y-m-d 23:59:59", strtotime($to)) : date("Y-m-d 23:59:59", strtotime($from));
$group_id_str = join(",", $_SESSION["studentExtraFilter"]["groups"]);
$teacher_id_str = join(",", $_SESSION["studentExtraFilter"]["teacher"]);

if (!empty($_SESSION["studentExtraFilter"]["teacher"]) and !empty($_SESSION["studentExtraFilter"]["groups"])) {
  $group_in = "sbl.GROUP_ID IN (SELECT `ID` FROM `group_list` WHERE `TEACHER_ID` IN ($teacher_id_str) AND `ID` IN ($group_id_str))";
} else if (!empty($_SESSION["studentExtraFilter"]["teacher"])) {
  $group_in = "sbl.GROUP_ID IN (SELECT `ID` FROM `group_list` WHERE `TEACHER_ID` IN ($teacher_id_str))";
} else if (!empty($_SESSION["studentExtraFilter"]["groups"])) {
  $group_in = "sbl.GROUP_ID IN (SELECT `ID` FROM `group_list` WHERE `ID` IN ($group_id_str))";
}


if (is_numeric($_SESSION["studentExtraFilter"]["balance"]["from"]) and is_numeric($_SESSION["studentExtraFilter"]["balance"]["to"])) {
  $student_balance = "stl.CURRENT_BALANCE >= '{$_SESSION['studentExtraFilter']['balance']['from']}' AND stl.CURRENT_BALANCE <= '{$_SESSION['studentExtraFilter']['balance']['to']}' ";
} else if (is_numeric($_SESSION["studentExtraFilter"]["balance"]["from"])) {
  $student_balance = "stl.CURRENT_BALANCE >= '{$_SESSION['studentExtraFilter']['balance']['from']}' ";
} else if (is_numeric($_SESSION["studentExtraFilter"]["balance"]["to"])) {
  $student_balance = "stl.CURRENT_BALANCE <= '{$_SESSION['studentExtraFilter']['balance']['to']}' ";
}
if (is_numeric($_SESSION["studentExtraFilter"]["special_price"]["from"]) and is_numeric($_SESSION["studentExtraFilter"]["special_price"]["to"])) {
  $special_price = "sbl.`SPECIAL_PRICE` >= '{$_SESSION['studentExtraFilter']['special_price']['from']}' AND sbl.`SPECIAL_PRICE` <= '{$_SESSION['studentExtraFilter']['special_price']['to']}' ";
} else if (is_numeric($_SESSION["studentExtraFilter"]["special_price"]["from"])) {
  $special_price = "sbl.`SPECIAL_PRICE` >= '{$_SESSION['studentExtraFilter']['special_price']['from']}' ";
} else if (is_numeric($_SESSION["studentExtraFilter"]["special_price"]["to"])) {
  $special_price = "sbl.`SPECIAL_PRICE` <= '{$_SESSION['studentExtraFilter']['special_price']['to']}' ";
}
$create_date = !empty($_SESSION["studentExtraFilter"]["studentCreated"]) ? "stl.`CREATED_DATE` BETWEEN '$from' AND '$to' " : null;
$subscribe_type = $_SESSION["studentExtraFilter"]["withdraw_type"] !== "all" ? "sbl.`TYPE` = '{$_SESSION['studentExtraFilter']['withdraw_type']}'" : null;
$params_arr = array_filter(["sbl.`STATUS`<>'archive'","stl.`ORG_ID`='{$_SESSION['USER']['ORG_ID']}'", "stl.`ACTIVE`='1'", $student_balance, $special_price, $create_date, $group_in, $subscribe_type], fn ($value) => !is_null($value));
$join_query_params = join(" AND ", $params_arr);

$withdraw_types = ["simple" => "Davomat bo'yicha", "monthly" => "Oylik", "free" => "Bepul"];
$student_list = db::arr("SELECT stl.*, sbl.TYPE
FROM `student_list` stl
LEFT JOIN `subscribe_list` sbl ON sbl.STUDENT_ID = stl.ID
WHERE
$join_query_params 
ORDER BY stl.`ID` DESC");
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
        <th>Qo'shilgan sana</th>
        <th>Individual narx</th>
        <th>Balans</th>
        <th>To'lov turi</th>
        <th>Harakat</th>
      </tr>
    </thead>
    <tbody>
      <? foreach ($student_list as $v) : ?>
        <tr>
          <td></td>
          <td><?= $stu_index++; ?></td>
          <td><a href="/account/student_list/detail/<?= $v['ID'] ?>" class="text-primary" target="_blank"><?= $v["NAME"] ?></a></td>
          <td><?= str_replace(" ", "", $v["PHONE"]); ?></td>
          <td><?= $group_list[$v["ID"]]["NAME"] ?></td>
          <td><?= $group_list[$v["ID"]]["TEACHER_NAME"] ?></td>
          <td><?= date_var($v['CREATED_DATE'], 'd.m.Y'); ?></td>
          <? $total_price += intval($group_list[$v["ID"]]["SPECIAL_PRICE"]) ?>
          <? $total_balance += intval($v["CURRENT_BALANCE"]) ?>
          <td><?= $group_list[$v["ID"]]["SPECIAL_PRICE"] ?></td>
          <td><?= number_format($v["CURRENT_BALANCE"], 0, 2, "") ?></td>
          <td>
            <span class="badge badge-glow badge-info"><?= $withdraw_types[$v["TYPE"]] ?></span>
          </td>
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
      <th>Narx: <?= number_format($total_price, 0, "", " ") ?></th>
      <th>Balans: <?= number_format($total_balance, 0, "", " ") ?></th>
      <th></th>
      <th></th>
    </tfoot>
  </table>
</div>