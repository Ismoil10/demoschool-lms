<?
if (isset($_POST['addModule'])) {

$course_id = $_SESSION['item_id'];

if ($_FILES["module_image"]["error"] != UPLOAD_ERR_NO_FILE) {
    $file = db::file_upload("module_image", "uploads");
}

$img = $file['url'];

$moduleOrder = $_POST['module_order'];

$insert_section = db::query("INSERT INTO `student_modules` 
(`title`,  
`description`, 
`url`,
`course_id`,
`order`) 
VALUES
('$_POST[section_title]',
'$_POST[section_description]',
'$img',
'$course_id',
'$moduleOrder')");

    LocalRedirect("index.php");
}

if (isset($_POST['editModule'])) {

$select_url = db::arr_s("SELECT * FROM `student_modules` WHERE id='$_POST[module_id]'");

if ($_FILES["edit_image"]["error"] != UPLOAD_ERR_NO_FILE) {
    $file = db::file_upload("edit_image", "uploads");
    $select_url['url'] = $file['url'];
}

$edit_img = $select_url['url'];
$editOrder = $_POST['editModuleOrder'];

$update_img = ",`url`='$edit_img'";

$edit_section = db::query("UPDATE `student_modules` SET 
`title` = '$_POST[edit_title]',
`order` = '$editOrder',
`description` = '$_POST[edit_description]'
$update_img
WHERE `id` = '$_POST[module_id]'");
    
LocalRedirect("index.php");
}

if (isset($_POST['deleteSubmit'])) {

    $modules = db::arr("SELECT * FROM student_lessons WHERE module_id = '$_POST[moduleID]'");

    foreach ($modules as $v) {
        $delete_question = db::query("DELETE FROM `student_questions` WHERE `lesson_id` = '$v[id]'");
    }

    $delete_lesson = db::query("DELETE FROM `student_lessons` WHERE `module_id` = '$_POST[moduleID]'");
    $delete_module = db::query("DELETE FROM `student_modules` WHERE `id` = '$_POST[moduleID]'");


    LocalRedirect("index.php");
}
?>

<!-- Add modal -->

<div class="modal fade text-left" id="addModalModule" tabindex="-1" role="dialog" aria-labelledby="addModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" id="addForm" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addModalLabel1">Yangi bo'lim</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?
                    // echo '<pre>';
                    // print_r($insert_section);
                    // echo '</pre>'; 
                    ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <div style="display: block;">
                                <div class="mb-3">
                                    <label class="label-form">Tartib</label>
                                    <input type="number" class="form-control" name="module_order">
                                </div>
                                <div class="mb-3">
                                    <label class="label-form">Bo'lim nomi</label>
                                    <input type="text" class="form-control" name="section_title">
                                </div>
                                <div class="mb-3">
                                    <label class="label-form">Tavsif</label>
                                    <textarea name="section_description" cols="30" rows="10" class="form-control summernote" style="height: 80px;"></textarea>
                                </div>
                                <div class="custom-file mb-2">
                                    <input type="file" name="module_image" class="custom-file-input" onchange="loadFile(event)">
                                    <label class="custom-file-label" for="customFile">Rasimni tanlang</label>
                                </div>
                                <div class="center-image"><img id="output" width="60%" alt="" align="center"></div>
                                <script>
                                    var loadFile = function(event) {
                                        var reader = new FileReader();
                                        reader.onload = function() {
                                            var output = document.getElementById('output');
                                            output.src = reader.result;
                                        };
                                        reader.readAsDataURL(event.target.files[0]);
                                    };
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                    <button type="submit" form="addForm" name="addModule" class="btn btn-primary">Qo'shish</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit -->

<div class="modal fade text-left" id="editModule" tabindex="-1" role="dialog" aria-labelledby="addModalLabel1" aria-hidden="true">

</div>

<!-- Delete -->

<div class="modal fade text-left" id="deleteModule" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
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
                    <input type="hidden" name="moduleID">
                    <div class="row">
                        <div class="col-md-12 p-1 mt-1">
                            <h4>Ushbu bo'limni o'chirib tashlamoqchimisiz?</h4>
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