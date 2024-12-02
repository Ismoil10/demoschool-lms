<?
$data = db::arr("SELECT 
  sl.ID,
  DATE_FORMAT(tl.LOG_DATE, '%d.%m.%Y') AS LDATE,
  sl.NAME,
  sl.PHONE,
  sl.CURRENT_BALANCE
FROM `table_log` tl
INNER JOIN  `student_list` sl ON tl.ITEM_ID = sl.ID 
WHERE sl.ACTIVE=1 AND tl.ACTION='activate_student' $sql_data_query
AND tl.LOG_DATE BETWEEN '$from' AND '$to'");
?>
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
                  <th>Balansi</th>
                </tr>
              </thead>
              <tbody>
                <? foreach ($data as $v) : ?>
                  <tr>
                    <td></td>
                    <td><?= $v['LDATE'] ?></td>
                    <td><?= $v['NAME'] ?></td>
                    <td><?= $v['PHONE'] ?></td>
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