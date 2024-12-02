<?
if(isset($_POST['addStudent'])){

$insert = db::query("INSERT INTO `student_students` 
(`username`,  
`password`) 
VALUES
('$_POST[username]',
'$_POST[password]')");

$courses = $_POST['courses'];


foreach($courses as $key => $v){

$select_course = db::arr_s("SELECT 
sc.id AS course_id,
sm.id AS module_id,
sl.id AS lesson_id 
FROM student_courses AS sc
LEFT JOIN student_modules AS sm ON sm.course_id = sc.id
LEFT JOIN student_lessons AS sl ON sl.module_id = sm.id
WHERE sc.id = '$v' ORDER BY sm.order, sl.order ASC LIMIT 1");

$selectIds[$select_course['course_id']] = $select_course;

}

foreach($selectIds as $key => $v){

db::query("INSERT INTO `student_course_access`
(`course_id`,
`user_id`)
VALUES
('$key',
'$insert[ID]')");

db::query("INSERT INTO `student_module_access`
(`module_id`,
`user_id`)
VALUES
('$v[module_id]',
'$insert[ID]')");

db::query("INSERT INTO `student_lesson_access`
(`lesson_id`,
`user_id`)
VALUES
('$v[lesson_id]',
'$insert[ID]')");

}

LocalRedirect("index.php");
}

if(isset($_POST['editStudent'])){

$studentId = $_POST['student_id'];

$edit_student = db::query("UPDATE `student_students` SET 
`username` = '$_POST[edit_username]',
`password` = '$_POST[edit_password]'
WHERE `id` = '$studentId'");


$cs = db::query("DELETE FROM `student_course_access` WHERE `user_id` = '$studentId'");
$st = db::query("DELETE FROM `student_module_access` WHERE `user_id` = '$studentId'");
$md = db::query("DELETE FROM `student_lesson_access` WHERE `user_id` = '$studentId'");

$changedCourse = $_POST['edit_courses'];

foreach($changedCourse as $val){

$cml = db::arr_s("SELECT 
sc.id AS course_id,
sm.id AS module_id,
sl.id AS lesson_id 
FROM student_courses AS sc
LEFT JOIN student_modules AS sm ON sm.course_id = sc.id
LEFT JOIN student_lessons AS sl ON sl.module_id = sm.id
WHERE sc.id = '$val' ORDER BY sm.order, sl.order ASC LIMIT 1");
    
$editCml[$cml['course_id']] = $cml;

}

foreach($editCml as $key => $v){

db::query("INSERT INTO `student_course_access` 
(`course_id`, 
`user_id`) 
VALUES 
('$v[course_id]', 
'$studentId')");

db::query("INSERT INTO `student_module_access` 
(`module_id`, 
`user_id`) 
VALUES 
('$v[module_id]', 
'$studentId')");

db::query("INSERT INTO `student_lesson_access` 
(`lesson_id`, 
`user_id`) 
VALUES (
'$v[lesson_id]', 
'$studentId')");

}

LocalRedirect("index.php");
}

if (isset($_POST['deleteSubmit'])) {
    
    $delete_question_log = db::query("DELETE FROM `student_question_log` WHERE `student_id` = '$_POST[studentID]'");
    $delete_student = db::query("DELETE FROM `student_students` WHERE `id` = '$_POST[studentID]'");
    $delete_course = db::query("DELETE FROM `student_course_access` WHERE `user_id` = '$_POST[studentID]'");
    $delete_section = db::query("DELETE FROM `student_module_access` WHERE `user_id` = '$_POST[studentID]'");
    $delete_module = db::query("DELETE FROM `student_lesson_access` WHERE `user_id` = '$_POST[studentID]'");
    LocalRedirect("index.php");
}




$courses = db::arr("SELECT * FROM student_courses");

?>

<!-- Delete -->

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
                    <input type="hidden" name="studentID">
                    <div class="row">
                        <div class="col-md-12 p-1 mt-1">
                            <h4>Ushbu talabani o'chirib tashlamoqchimisiz?</h4>
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

<!-- Add modal -->
<div class="modal fade text-left" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" id="addForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addModalLabel1">Yangi talaba</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?
                    // foreach($module as $arr){
                    // foreach($arr as $v){
                    echo '<pre>';
                    print_r($selected);
                    echo '</pre>'; 
                    // echo '<pre>';
                    // print_r($edit_module);
                    // echo '</pre>';   
                //    }
                //    }
                    ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <div style="display: block;">
                                <div class="mb-1">
                                    <label class="label-form">Talaba</label><br>
                                    <input type="text" class="form-control" name="username">
                                </div>
                                <div class="mb-1">
                                    <label class="label-form">Talaba paroli</label><br>
                                    <input type="text" class="form-control" name="password">
                                </div>
                                <div class="mb-3">
                                    <label class="label-form">Kurs nomi</label>
                                    <select name="courses[]" id="" class="select2 form-control" multiple>
                                        <? foreach ($courses as $v) : ?>
                                            <option value="<?= $v["id"]; ?>"><?= $v["name"];?></option>
                                        <? endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                    <button type="submit" form="addForm" name="addStudent" class="btn btn-primary">Qo'shish</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit -->

<div class="modal fade text-left" id="editModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel1" aria-hidden="true">

</div>



<style>
    .custom-circle {
        height: 2rem;
        width: 2rem;
        display: inline-block;
    }
</style>