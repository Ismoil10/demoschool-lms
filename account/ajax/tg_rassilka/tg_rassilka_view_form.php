<? require $_SERVER['DOCUMENT_ROOT'] . '/core/backend.php'; ?>
<?

$users = db::arr("SELECT ms_log.*, 
students.NAME AS STUDENT
FROM message_log AS ms_log
LEFT JOIN tg_users AS users ON users.CHAT_ID = ms_log.CHAT_ID
LEFT JOIN student_list AS students ON students.ID = users.STUDENT_ID
WHERE ms_log.RASSILKA_ID='$_POST[item_id]'");

$rassilka = db::arr_s("SELECT * FROM tg_rassilka WHERE ID = '$_POST[item_id]'");
?>

<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <h4 class="modal-title" id="addModalLabel1">Jo'natma</h4>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <form action="" method="post" id="addForm" enctype="multipart/form-data">
      <div class="modal-body">
        <div class="row">
          <? //echo '<pre>'; print_r($users); echo '</pre>'; 
          ?>
          <div class="col-sm-6 col-md-6 pc-bottom">
            <? $get_img = json_decode($rassilka['FILE_URL'], true); ?>
            <nav class="cs-img">
              <img class="img-fl" src="<?= $get_img['file'] ?>" alt="Product Thumnail">
            </nav>
          </div>
          <div class="col-sm-6 col-md-6 pc-bottom">
            <nav class="rectangle" style="max-height: 375px; overflow-y: auto;">
              <p>
                <span>
                  <b><?=$rassilka['TEXT'];?></b>
                </span>
              </p>
            </nav>
          </div>
          <div class="col-sm-12 col-md-12" style="max-height: 375px; overflow-y: auto;">
            <div class="card-datatable">
              <table class="d_tab dt-responsive table" id="customtable">
                <thead>
                  <tr>
                    <th></th>
                    <th>#</th>
                    <th>Yaratilgan sana</th>
                    <th>Yuborilgan sana</th>
                    <th>Kimga</th>
                  </tr>
                </thead>
                <tbody>
                  <? foreach ($users as $v) : ?>
                    <tr>
                      <td></td>
                      <td><?= $v['ID'] ?></td>
                      <td><?= $v['CREATE_DATE'] ?></td>
                      <td><?= $v['SEND_DATE'] ?></td>
                      <td><?= $v['STUDENT'] ?></td>
                    </tr>
                  <? endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
      </div>
    </form>
  </div>
</div>

<!--  
  </div>
  <div class="col-sm-6 col-md-6" style="height: 550px; overflow-y: auto;"> 
-->

<style>
  .rectangle{
  border: 2px; 
  padding: 10px;
  width: 360px;
  height: 375px;
  background-color: #F3F2F7;
  border-radius: 8px;
  margin-top: 20px;
  font-size: 16px;
}

  .img-fl {
    margin-top: 20px;
    max-height: 375px;
    border-radius: 8px;
  }

  .pc-bottom{
    padding-bottom: 16px;
  }
  .td-custom {
    margin-left: 50px;
  }
</style>