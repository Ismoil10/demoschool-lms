<?
$price_level = "0";
if (isset($_SESSION["debtors_filter"])) {
  $price_level = $_SESSION["debtors_filter"];
}
$data = db::arr("SELECT 
  sl.ID,
  sl.NAME, 
  sl.PHONE,
  sl.CURRENT_BALANCE,
  gl.NAME AS `GROUP`
  FROM `student_list` sl
  LEFT JOIN `subscribe_list` sub ON sub.STUDENT_ID = sl.ID
  LEFT JOIN `group_list` gl ON gl.ID = sub.GROUP_ID
  WHERE sl.CURRENT_BALANCE < $price_level AND sl.ACTIVE=1 $sql_data_query
  GROUP BY sl.ID");
$total_debt = db::arr_s("SELECT SUM(CURRENT_BALANCE) AS amount FROM `student_list` WHERE CURRENT_BALANCE < -100");

?>
<div class="content-body">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div>
            <h4 class="font-weight-bolder mb-0">Qarzdorlar: <?= date("d.m.Y (H:i)", strtotime($from)) ?> ~ <?= date("d.m.Y (H:i)", strtotime($to)); ?></h4>
          </div>
          <div class="group">
            <div class="avatar bg-light-primary p-50 m-0">
              <div class="avatar-content">
                <i data-feather="calendar" class="font-medium-5"></i>
              </div>
            </div>
            <button class="btn btn-outline-warning btn-icon rounded-circle p-1" data-toggle="modal" data-target="#debtorsModal"><i data-feather="filter"></i></button>
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
            <table class="d_tab dt-responsive table" id="customtable">
              <thead>
                <tr>
                  <th></th>
                  <th>Ims</th>
                  <th>Telefon </th>
                  <th>Qarz </th>
                  <th>Guruh</th>
                  <th>Holati</th>
                </tr>
              </thead>
              <tbody>
                <? foreach ($data as $v) : ?>
                  <tr>
                    <td></td>
                    <td><?= $v["NAME"] ?></td>
                    <td><?= $v["PHONE"] ?></td>
                    <td><?= number_format($v["CURRENT_BALANCE"], 0, 2, " ") ?> UZS</td>
                    <td>
                      <div class="badge badge-info badge-pill badge-glow"><?= $v["GROUP"] ?></div>
                    </td>
                    <td></td>
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