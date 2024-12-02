<? require $_SERVER['DOCUMENT_ROOT'] . '/core/backend.php'; ?>
<?

$select_course = db::arr_s("SELECT * FROM student_courses WHERE ID = '$_POST[item_id]'");

?>

<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="addModalLabel1">Kursni o'zgartirish</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form method="POST" id="editForm" enctype="multipart/form-data">
            <div class="modal-body">
                <?
                //echo '<pre>';
                //print_r($select_course);
                //echo '</pre>'; 
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div style="display: block;">
                            <input type="hidden" value="<?= $select_course['id'] ?>" name="course_id">
                            <div class="mb-3">
                                <label class="label-form">Kurs nomi</label>
                                <input type="text" class="form-control" value="<?= $select_course['name'] ?>" name="edit_name">
                            </div>
                            <div class="mb-3">
                                <label class="label-form">Tavsif</label>
                                <textarea name="edit_description" cols="30" rows="10" class="form-control summernote" style="height: 80px;"><?= $select_course['description'] ?></textarea>
                            </div>
                            <div class="custom-file mb-2">
                                <input type="file" name="edit_image" class="custom-file-input" onchange="editFile(event)">
                                <label class="custom-file-label" for="customFile">Rasimni tanlang</label>
                            </div>
                            <div class="center-image"><img id="output1" width="60%" alt="" src="<?= $select_course['url'] ?>" align="center"></div>
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
                <button type="submit" form="editForm" name="editCourse" class="btn btn-primary">Saqlash</button>
            </div>
        </form>
    </div>
</div>

<style>
.center-image {
    margin-top: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>