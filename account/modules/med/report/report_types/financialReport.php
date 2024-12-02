<?
$data = db::arr("SELECT 
  tl.ID, 
  tl.TRANSACTION_DATE, 
  tl.CREATED_BY, 
  sl.NAME, 
  tl.AMOUNT, 
  tl.TYPE, 
  tl.DESCRIPTION,
  gsu.NAME AS `USERNAME`, 
  gsu.SURNAME 
  FROM `transaction_list` AS tl 
  LEFT JOIN `student_list` AS sl ON tl.STUDENT_ID = sl.ID 
  LEFT JOIN `gl_sys_users` AS gsu ON gsu.ID = tl.CREATED_BY 
  WHERE tl.ACTION_TYPE = 'add' $sql_data_query
  AND tl.TRANSACTION_DATE BETWEEN '$from' AND '$to'");
$incomes_data = db::arr("SELECT TYPE, SUM(AMOUNT) as amount FROM `transaction_list` WHERE $sql_total_query `ACTION_TYPE` = 'add' AND TRANSACTION_DATE BETWEEN '$from' AND '$to' GROUP BY `TYPE`");
$outcomes_data = db::arr("SELECT TYPE, SUM(AMOUNT) as amount FROM `transaction_list` WHERE $sql_total_query `ACTION_TYPE`='expense' AND TRANSACTION_DATE BETWEEN '$from' AND '$to' GROUP BY `TYPE`");
$finance_total = db::arr_s("SELECT SUM(AMOUNT) AS amount FROM `transaction_list` WHERE $sql_total_query `ACTION_TYPE` = 'add' AND TRANSACTION_DATE BETWEEN '$from' AND '$to' ");
$taken_total = db::arr_s("SELECT SUM(AMOUNT) AS amount FROM `transaction_list` WHERE $sql_total_query `ACTION_TYPE`='expense' AND TRANSACTION_DATE BETWEEN '$from' AND '$to'");
?>

<div class="content-body">
  <div class="row">
    <div class=" col-sm-6 col-12">
      <div class="card">
        <div class="card-header">
          <div>
            <h4 class="font-weight-bolder mb-0">To'lovlar miqdori: <?= number_format($finance_total["amount"], 0, 2, " ") ?></h4>
            <p class="card-text"><?= date("d.m.Y", strtotime($from)) ?> ~ <?= date("d.m.Y", strtotime($to)) ?></p>
          </div>
          <div class="avatar bg-light-primary p-50 m-0">
            <div class="avatar-content">
              <i data-feather="inbox" class="font-medium-5"></i>
            </div>
          </div>
        </div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            <? foreach ($incomes_data as $income) : ?>
              <li class="list-group-item pl-0"><b><?= $payment_types[$income["TYPE"]] ?>: </b><?= number_format($income["amount"], 0, 2, " ") ?></li>
            <? endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
    <div class=" col-sm-6 col-12">
      <div class="card">
        <div class="card-header">
          <div>
            <h4 class="font-weight-bolder mb-0">Harajatlar miqdori: <?= number_format($taken_total["amount"], 0, 2, " ") ?> UZS</h4>
            <p class="card-text"><?= date("d.m.Y", strtotime($from)) ?> ~ <?= date("d.m.Y", strtotime($to)) ?></p>
          </div>
          <div class="avatar bg-light-success p-50 m-0">
            <div class="avatar-content">
              <i data-feather="stop-circle" class="font-medium-5"></i>
            </div>
          </div>
        </div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            <? foreach ($outcomes_data as $income) : ?>
              <li class="list-group-item pl-0"><b><?= $payment_types[$income["TYPE"]] ?>: </b><?= number_format($income["amount"], 0, 2, " ") ?></li>
            <? endforeach; ?>
          </ul>
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
            <table class="d_tab dt-responsive table" id="customtable">
              <thead>
                <tr>
                  <th></th>
                  <th>Sana</th>
                  <th>Talaba </th>
                  <th>Miqdori</th>
                  <th>Izoh</th>
                  <th>To'lov turi</th>
                  <th>Xodim</th>
                </tr>
              </thead>
              <tbody>
                <? foreach ($data as $v) : ?>
                  <? $total_income += $v["AMOUNT"]; ?>
                  <tr>
                    <td></td>
                    <td><?= date("d.m.Y", strtotime($v["TRANSACTION_DATE"])) ?></td>
                    <td><?= $v["NAME"] ?></td>
                    <td><?= number_format($v["AMOUNT"], 0, 2, " ") ?> UZS</td>
                    <td><?= $v["DESCRIPTION"] ?></td>
                    <td>
                      <div class="badge badge-info badge-pill badge-glow"><?= $payment_types[$v["TYPE"]] ?></div>
                    </td>
                    <td><?= $v["USERNAME"] . " " . $v["SURNAME"] ?></td>
                  </tr>
                <? endforeach; ?>
              </tbody>
              <tfoot>
                <td></td>
                <td></td>
                <td></td>
                <td>Umumiy: <?= number_format($total_income, 0, 2, " ") ?></td>
                <td></td>
                <td></td>
                <td></td>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>