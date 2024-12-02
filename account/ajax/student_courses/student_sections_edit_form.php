<? require $_SERVER['DOCUMENT_ROOT'] . '/core/backend.php'; ?>
<?

$select_section = db::arr_s("SELECT * FROM student_modules WHERE id = '$_POST[item_id]'");

?>

<div class="modal-dialog modal-lg" role="document">
        <form method="POST" id="editForm" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addModalLabel1">Bo'limni o'zgartirish</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?
                    //echo '<pre>';
                    //print_r($_POST);
                    //echo '</pre>'; 
                    ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="hidden" value="<?=$select_section['id']?>" name="module_id">
                            <div style="display: block;">
                            <div class="mb-3">
                                    <label class="label-form">Tartib</label>
                                    <input type="number" class="form-control" name="editModuleOrder" value="<?=$select_section['order']?>">
                                </div>
                                <div class="mb-3">
                                    <label class="label-form">Bo'lim nomi</label><br>
                                    <input name="edit_title" type="text" class="form-control" value="<?=$select_section['title']?>">
                                </div>
                                <div class="mb-3">
                                    <label class="label-form">Tavsif</label>
                                    <textarea name="edit_description" cols="30" rows="10" class="form-control summernote" style="height: 80px;"><?=$select_section['description']?></textarea>
                                </div>
                                <div class="custom-file mb-2">
                                <input type="file" name="edit_image" class="custom-file-input" onchange="editFile(event)">
                                <label class="custom-file-label" for="customFile">Rasimni tanlang</label>
                            </div>
                            <div class="center-image"><img id="output1" width="60%" alt="" src="<?= $select_section['url'] ?>" align="center"></div>
                            <script>
                                var editFile = function(event) {
                                    var reader = new FileReader();
                                    reader.onload = function() {
                                        var output = document.getElementById('output1');
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
                    <button type="submit" form="editForm" name="editModule" class="btn btn-primary">Saqlash</button>
                </div>
            </div>
        </form>
    </div>

    <style>
    .center-image {
        margin-top: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>