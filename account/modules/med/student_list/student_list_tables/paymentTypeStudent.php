<?
$from = date("Y-m-01");
$to = date("Y-m-t 23:59:59");
if (isset($_SESSION["studentFilterDate"])) {
  [$from, $to] = explode(" to ", $_SESSION["studentFilterDate"]);
  $to = !empty($to) ? $to : date("Y-m-d 23:59:59", strtotime($from));
}
$paymentTypes;
if (isset($_SESSION["studentFilterPaymentType"])) {
  $paymentTypes = join("','", $_SESSION["studentFilterPaymentType"]);
}

$student_list = db::arr("SELECT sl.*, tl.ID TRANSACTION_ID, tl.AMOUNT, tl.TRANSACTION_DATE, tl.TYPE FROM `student_list` sl
LEFT JOIN `transaction_list` tl ON tl.STUDENT_ID = sl.ID
WHERE tl.`TYPE` IN ('$paymentTypes') AND sl.`ACTIVE`='1' AND tl.TRANSACTION_DATE BETWEEN '$from' AND '$to' 
$sql_org_id");
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
        <th>To'lov sana</th>
        <th>To'langan narx</th>
        <th>To'lov turi</th>
        <th>Balans</th>
        <th>Harakat</th>
      </tr>
    </thead>
    <tbody>
      <? foreach ($student_list as $v) : ?>
        <tr>
          <td></td>
          <td><?= $stu_index++; ?></td>
          <td><a href="/account/student_list/detail/<?= $v['ID'] ?>" class="text-primary" target="_blank"><?= $v["NAME"] ?></a></td>
          <td><?= str_replace(" ", "", $v["PHONE"]);?></td>
          <td><?= $group_list[$v["ID"]]["NAME"] ?></td>
          <td><?= $group_list[$v["ID"]]["TEACHER_NAME"] ?></td>
          <? $total_amount += intval($v["AMOUNT"]) ?>
          <? $total_balance += intval($v["CURRENT_BALANCE"]) ?>
          <td><?= date_var($v['TRANSACTION_DATE'], 'd.m.Y'); ?></td>
          <td><?= number_format($v["AMOUNT"], 0, "", "") ?></td>
          <td><span class="badge badge-info badge-glow"><?= PAYMENT_TYPES[$v["TYPE"]] ?></span></td>
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
      <th>Umumiy: <?= number_format($total_amount, 0, "", " ") ?></th>
      <th></th>
      <th>Balans: <?= number_format($total_balance, 0, "", " ") ?></th>
      <th></th>

    </tfoot>
  </table>
</div>