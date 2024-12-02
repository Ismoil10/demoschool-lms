<?
if (isset($_POST['addCourse'])) {

    if ($_FILES["course_image"]["error"] != UPLOAD_ERR_NO_FILE) {
        $file = db::file_upload("course_image", "uploads");
}

$img = $file['url'];

$insert_module = db::query("INSERT INTO `student_courses` 
(`name`,  
`description`,
`url`) 
VALUES
('$_POST[course_name]',
'$_POST[course_text]',
'$img')");

    LocalRedirect("index.php");
}

if (isset($_POST['editCourse'])) {

    $select_url = db::arr_s("SELECT * FROM `student_courses` WHERE id='$_POST[course_id]'");

    if ($_FILES["edit_image"]["error"] != UPLOAD_ERR_NO_FILE) {
        $file = db::file_upload("edit_image", "uploads");
        $select_url['url'] = $file['url'];
    }

    $edit_img = $select_url['url'];

    $update_img = ",`url`='$edit_img'";

    $edit_course = db::query("UPDATE `student_courses` SET 
`name` = '$_POST[edit_name]',
`description` = '$_POST[edit_description]'
$update_img
WHERE `id` = '$_POST[course_id]'");

    LocalRedirect("index.php");
}

if (isset($_POST['deleteSubmit'])) {
    $select_section = db::arr("SELECT module.*,
    lesson.id AS lesson_id
    FROM student_lessons AS lesson
    LEFT JOIN student_modules AS module ON module.id = lesson.module_id
    WHERE module.course_id = '$_POST[courseID]'");

    foreach ($select_section as $v) {
        $delete_question = db::query("DELETE FROM `student_questions` WHERE `lesson_id` = '$v[lesson_id]'");
        $delete_module = db::query("DELETE FROM `student_lessons` WHERE `module_id` = '$v[id]'");
    }

    $delete_section = db::query("DELETE FROM `student_modules` WHERE `course_id` = '$_POST[courseID]'");

    $delete_course = db::query("DELETE FROM `student_courses` WHERE `id` = '$_POST[courseID]'");

    LocalRedirect("index.php");
}
?>

<!-- Add modal -->

<div class="modal fade text-left" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" id="addForm" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addModalLabel1">Yangi mavzu</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?
                    // echo '<pre>';
                    // print_r($select_section);
                    // echo '</pre>'; 
                    ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <div style="display: block;">
                                <div class="mb-1">
                                    <label class="label-form">Kurs nomi</label><br>
                                    <input type="text" class="form-control" name="course_name">
                                </div>
                                <div class="mb-3">
                                    <label class="label-form">Kurs tavsifi</label>
                                    <textarea name="course_text" cols="30" rows="10" class="form-control summernote" style="height: 80px;"></textarea>
                                </div>
                                <div class="custom-file mb-2">
                                    <input type="file" name="course_image" class="custom-file-input" onchange="loadFile(event)">
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
                                        console.log(event);
                                        reader.readAsDataURL(event.target.files[0]);
                                    };
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                    <button type="submit" form="addForm" name="addCourse" class="btn btn-primary">Qo'shish</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit -->

<div class="modal fade text-left" id="editCourse" tabindex="-1" role="dialog" aria-labelledby="addModalLabel1" aria-hidden="true">

</div>

<!-- Delete -->

<div class="modal fade text-left" id="deleteCourse" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
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
                    <input type="hidden" name="courseID">
                    <div class="row">
                        <div class="col-md-12 p-1 mt-1">
                            <h4>Ushbu kursni o'chirib tashlamoqchimisiz?</h4>
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

<style>
    .custom-circle {
        height: 2rem;
        width: 2rem;
        display: inline-block;
    }

    .center-image {
        margin-top: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>