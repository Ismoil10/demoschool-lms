<?
$student_list = db::arr("SELECT * FROM `student_list` WHERE ACTIVE=1 $sql_org_id ORDER BY ID DESC");
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
        <th>Harakat</th>
      </tr>
    </thead>
    <tbody>
      <? foreach ($student_list as $v) : ?>
        <tr class="<?= $group_list[$v["ID"]]["STATUS"] === "demo" && date("Y-m-d H:i:s", strtotime("+7 days {$group_list[$v["ID"]]["START_DATE"]}")) < date("Y-m-d H:i:s") ? "table-warning": "" ?>">
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
    </tfoot>
  </table>
</div>