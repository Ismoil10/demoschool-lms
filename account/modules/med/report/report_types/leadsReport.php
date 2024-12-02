<?
$filter_query = "";
if (isset($_SESSION["leads_filter"]["staff"])) {
  $staff_id = $_SESSION["leads_filter"]["staff"];
  $filter_query = $filter_query . "AND sl.`CREATED_BY`='$staff_id' ";
}
if (isset($_SESSION["leads_filter"]["eshitgan_joyi"])) {
  $eshitgan_arr = json_decode($_SESSION["leads_filter"]["eshitgan_joyi"], true);
  $filter_query = $filter_query . " AND (sl.`ESHITGAN_JOYI` LIKE '%\"$eshitgan_arr[select]\"%' AND sl.`ESHITGAN_JOYI` LIKE '%\"input\":\"$eshitgan_arr[input]\"%')";
}
$data = db::arr("SELECT 
  sl.ID,
  DATE_FORMAT(sl.CREATED_DATE, '%d.%m.%Y') AS CDATE,
  sl.NAME,
  sl.PHONE,
  sl.PARENT_PHONE,
  gsu.NAME AS `STUFF`,
  ol.NAME AS `filyal`,
  sl.ESHITGAN_JOYI,
  sl.CURRENT_BALANCE
  FROM `student_list` sl
  LEFT JOIN `gl_sys_users` gsu ON gsu.ID = sl.CREATED_BY 
  LEFT JOIN `org_list` ol ON ol.ID = sl.ORG_ID 
  WHERE CREATED_DATE BETWEEN '$from' AND '$to' $sql_data_query $filter_query");

?>
<div class="content-body">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div>
            <h4 class="font-weight-bolder mb-0">Davrdagi leadlar hisoboti: <?= date("d.m.Y (H:i)", strtotime($from)) ?> ~ <?= date("d.m.Y (H:i)", strtotime($to)); ?></h4>
          </div>
          <div class="group">
            <div class="avatar bg-light-primary p-50 m-0">
              <div class="avatar-content">
                <i data-feather="calendar" class="font-medium-5"></i>
              </div>
            </div>
            <button class="btn btn-outline-warning btn-icon rounded-circle p-1" data-toggle="modal" data-target="#leadsModal"><i data-feather="filter"></i></button>
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
                  <th>Sana </th>
                  <th>Ismi</th>
                  <th>Tel. raqam</th>
                  <th>Ota-ona tel. raqami</th>
                  <th>Mas'ul</th>
                  <th>Filial</th>
                  <th>Qayerdan Eshtgan</th>
                  <th>Balansi</th>
                </tr>
              </thead>
              <tbody>
                <? //var_dump($data);
                ?>
                <? foreach ($data as $v) : ?>
                  <? if (empty($v['ESHITGAN_JOYI'])) {
                    $eshitgan_joyi = "Ma'lumot topilmadi";
                  } else {
                    $eshitgan_joyi = json_decode($v['ESHITGAN_JOYI'])->select . ' ' . json_decode($v['ESHITGAN_JOYI'])->input;
                  } ?>
                  <tr>
                    <td></td>
                    <td><?= $v['CDATE'] ?></td>
                    <td><?= $v['NAME'] ?></td>
                    <td><?= $v['PHONE'] ?></td>
                    <td><?= $v['PARENT_PHONE'] ?></td>
                    <td><?= $v['STUFF'] ?></td>
                    <td><?= $v['filyal'] ?></td>
                    <td><?= ucfirst($eshitgan_joyi) ?></td>
                    <td><?= number_format($v['CURRENT_BALANCE'], 0, ',', ' ') ?> UZS</td>
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