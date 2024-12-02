<? require $_SERVER['DOCUMENT_ROOT'] . '/core/backend.php'; ?>
<? 


$course_id = db::arr("SELECT * FROM student_course_access  WHERE user_id = '$_POST[student_id]'");

$student = db::arr_s("SELECT * FROM student_students WHERE id = '$_POST[student_id]'");

$edit_courses = db::arr("SELECT * FROM student_courses");
?>

<div class="modal-dialog modal-lg" role="document">
    <form method="POST" id="editForm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel1">Tahrirlash</h4>
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
                        <input type="hidden" value="<?=$student['id']?>" name="student_id">
                        <div style="display: block;">
                            <div class="mb-1">
                                <label class="label-form">Talaba</label><br>
                                <input type="text" class="form-control" value="<?=$student['username']?>" name="edit_username">
                            </div>
                            <div class="mb-1">
                                <label class="label-form">Talaba paroli</label><br>
                                <input type="text" class="form-control" value="<?=$student['password']?>" name="edit_password">
                            </div>
                            <div class="mb-3">
                                <label class="label-form">Kurs nomi</label>
                                <select name="edit_courses[]" class="form-control select2" multiple>
                                    <? foreach ($edit_courses as $v) : ?>
                                        <option value="<?=$v["id"]?>"<? foreach($course_id as $id){ if($v['id'] == $id['course_id']){ echo "selected"; }}?>><?= $v["name"]; ?></option>
                                    <? endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                <button type="submit" form="editForm" name="editStudent" class="btn btn-primary">Saqlash</button>
            </div>
        </div>
    </form>
</div>
<script src="/core/template_med/app-assets/js/scripts/forms/form-select2.js"></script>
<script src="/core/template_med/app-assets/vendors/js/pickers/flatpickr/pickdate.js"></script>