<?
$now = date("Y-m-d H:i:s");

if (isset($_POST['addSubmit'])){

$group_id = $_POST['group_id'];


if ($_FILES["image"]["error"] != UPLOAD_ERR_NO_FILE or ($_FILES["image"]["error"] == 0 and $_FILES["file_name"]["error"] == 0)) {
    $file = db::file_upload("image", "uploads");
}
elseif ($_FILES["file_name"]["error"] != UPLOAD_ERR_NO_FILE) {
    $file = db::file_upload("file_name", "uploads");
}



$img_src = [
    "file" => $file["url"]
];

$image = json_encode($img_src);

$insert = db::query("INSERT INTO `tg_rassilka` (`CREATED_DATE`, `TEXT`, `FILE_URL`) VALUES ('$now', '$_POST[rassilka]', '$file[url]')");

//$_SESSION['sql_text'] = "INSERT INTO `tg_rassilka` (`CREATED_DATE`, `TEXT`, `FILE_URL`) VALUES ('$now', '$_POST[rassilka]', '$image')";

if($group_id === '0'){

$student_id = db::arr("SELECT user.*
FROM tg_users AS user
LEFT JOIN subscribe_list AS sub ON sub.STUDENT_ID = user.STUDENT_ID WHERE user.CHAT_ID = '1586146743'");

}else{

$student_id = db::arr("SELECT tg_user.*
FROM tg_users AS tg_user
LEFT JOIN subscribe_list AS sub ON sub.STUDENT_ID = tg_user.STUDENT_ID
WHERE sub.GROUP_ID = '$group_id'");

}

foreach($student_id as $v){

$user_id = $v['ID'];
$chat_id = $v['CHAT_ID'];
$rassilka = "rassilka";
$last_id = $insert['ID'];

db::query("INSERT INTO `message_log` (
`CREATE_DATE`,
`TYPE`,
`RASSILKA_ID`,  
`USER_ID`, 
`CHAT_ID`
) VALUES (
'$now',
'$rassilka',
'$last_id',
'$user_id',
'$chat_id'
)");
}
    //6184134321:AAEmEs19KumiA6oDikus4Upk_dVGJXt8j_c

LocalRedirect("index.php");
}

if(isset($_POST['deleteSubmit'])){

$delete = db::query("DELETE FROM `tg_rassilka` WHERE `ID` = '$_POST[deleteID]'");

$update = db::query("UPDATE `message_log` SET `STATUS` = '2' WHERE `RASSILKA_ID` = '$_POST[deleteID]'");

LocalRedirect("index.php");

}

$select_group = db::arr("SELECT * FROM group_list WHERE STATUS = 'active'");


?>

<div class="modal fade text-left" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addModalLabel1">Qo'shish</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post" id="addForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?  
//                            echo '<pre>'; print_r($_FILES); echo '</pre>'; ?>
                            <? //echo '<pre>'; print_r($group_id); echo '</pre>'; ?>
                            <?php
/*$send_message = "https://api.telegram.org/bot6184134321:AAEmEs19KumiA6oDikus4Upk_dVGJXt8j_c/sendPhoto?chat_id=1586146743&photo=https://demoschool.senet.uz/uploads/2723d092b63885e0d7c260cc007e8b9d.jpg&caption=dfdefwef";

$q[] = file_get_contents($send_message);

$get_id = json_decode($q['0'], true);

foreach($get_id as $v){

echo "<pre>"; print_r($v['message_id']); echo "</pre>";
}*/
?>
                            <label for="standart-select">Guruh</label>
                            <div class="select mb-2">
                                <select class="select2 form-select" name="group_id" style="width: 200px;">
                                    <option value="0">All</option>
                                    <? foreach ($select_group as $v) : ?>
                                        <option value="<?= $v['ID'] ?>"><?= $v['NAME'] ?></option>
                                    <? endforeach; ?>
                                </select>
                            </div>
                            <label for="standart-select">Fayl yoki rasmni tanlang</label>
                            <div class="select mb-2">
                                <select class="form-control" id="fileId">
                                    <option value="0">none</option>
                                    <option value="img">Rasm</option>
                                    <option value="file">Fayl</option>
                                </select>
                            </div>
                            <div class="custom-file mb-2" id="fileUp" style="display: none;">
                                <input type="file" name="file_name" class="custom-file-input" accept="file/*">
                                <label class="custom-file-label" for="customFile">Faylni tanlang</label>
                            </div>
                            <div class="custom-file mb-2" id="imgUp" style="display: none;">
                                <input type="file" name="image" class="custom-file-input"  onchange="loadFile_2(event)">
                                <label class="custom-file-label" for="customFile">Rasimni tanlang</label>
                            </div>
                            <div class="center-image"><img id="output1" width="60%" alt="" align="center"></div>
                            <script>
                                var loadFile_2 = function(event) {
                                    var reader = new FileReader();
                                    reader.onload = function() {
                                        var output = document.getElementById('output1');
                                        output.src = reader.result;
                                    };
                                    reader.readAsDataURL(event.target.files[0]);
                                };
                            </script>
                            <div class="mb-3">
                                <label class="label-form">Tekst</label>
                                <textarea name="rassilka" cols="30" rows="10" class="form-control" style="height: 80px;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                    <button type="submit" form="addForm" name="addSubmit" class="btn btn-primary">Qo'shish</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- VIEW MODAL -->

<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel">

</div>

<!-- DELETE MODAL -->

<div class="modal fade text-left" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="editModal">O'chirish</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="post" id="deleteForm">
      <div class="modal-body">
          <input type="hidden" name="deleteID">
        <div class="row">
          <div class="col-md-12 p-1 mt-1">
            <h4>Ushbu jo'natmani o'chirib tashlamoqchimisiz?</h4>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor Qilish</button>
        <button type="submit" class="btn btn-primary" form="deleteForm" name="deleteSubmit">O'chirish</button>
      </div>
      </form>
    </div>
  </div>
</div>

<script>

$("#fileId").on("change", function(e){

getFile = $(".modal-dialog #fileUp");
getImg = $(".modal-dialog #imgUp");

getFile.hide();
getImg.hide();

switch(e.target.value){

case "img":
    getImg.show();
    break;
case "file":
    getFile.show();
    break;
}

});

</script>

<style>
    .center-image {
        margin-top: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>