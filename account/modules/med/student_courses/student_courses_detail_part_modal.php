<?
if (isset($_POST['addLesson'])) {

    $section_id = $_SESSION['item_id'];

$insert_module = db::query("INSERT INTO `student_lessons` 
(`title`, 
`module_id`, 
`type`, 
`order`) 
VALUES
('$_POST[module_text]',
'$section_id',
'$_POST[mtype_id]',
'$_POST[module_order]')");

LocalRedirect("index.php");
}

if (isset($_POST['editLesson'])) {

$edit_module = db::query("UPDATE `student_lessons` SET 
`title` = '$_POST[edit_title]',
`type` = '$_POST[edit_type]',
`order` = '$_POST[module_order]'
WHERE `id` = '$_POST[module_id]'");
LocalRedirect("index.php");

}

if (isset($_POST['deleteSubmit'])) {

$delete_question = db::query("DELETE FROM `student_questions` WHERE `lesson_id` = '$_POST[lessonID]'");

$delete_module = db::query("DELETE FROM `student_lessons` WHERE `id` = '$_POST[lessonID]'");

LocalRedirect("index.php");
}
?>

<!-- Add modal -->

<div class="modal fade text-left" id="addLesson" tabindex="-1" role="dialog" aria-labelledby="addModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" id="addForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addModalLabel1">Yangi mavzu</h4>
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
                            <label for="standart-select">Turi</label>
                            <div class="select mb-2">
                                <select class="select form-control" name="mtype_id" style="width: 300px;">
                                    <option value="0">None</option>
                                    <option value="Learn">Learn</option>
                                    <option value="Practice">Practice</option>
                                </select>
                            </div>
                            <div style="display: block;">
                                <div class="mb-1">
                                    <label class="label-form">Tartib</label><br>
                                    <input type="number" class="form-control" name="module_order">
                                </div>
                                <div class="mb-3">
                                    <label class="label-form">Mavzu</label>
                                    <textarea name="module_text" cols="30" rows="10" class="form-control summernote" style="height: 80px;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                    <button type="submit" form="addForm" name="addLesson" class="btn btn-primary">Qo'shish</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit -->

<div class="modal fade text-left" id="editLesson" tabindex="-1" role="dialog" aria-labelledby="addModalLabel1" aria-hidden="true">

</div>

<!-- Delete -->

<div class="modal fade text-left" id="deleteLesson" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
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
                    <input type="hidden" name="lessonID">
                    <div class="row">
                        <div class="col-md-12 p-1 mt-1">
                            <h4>Ushbu mavzuni o'chirib tashlamoqchimisiz?</h4>
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