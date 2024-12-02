<?

if (isset($_POST['checkSubmit'])) {

$id = $_POST['check_id'];
$coin = $_POST['coin'];
$point = $_POST['point'];
$score = $_POST['score'];
$reason = filter_input(INPUT_POST, "reason", FILTER_SANITIZE_FULL_SPECIAL_CHARS);


db::query("UPDATE `student_question_practice` 
SET `awarded_coin` = '$coin',
`awarded_point` = '$point', 
`score` = '$score',
`reason` = '$reason', 
`status` = 'approved' 
WHERE `id` = '$id'");

$student = db::arr_s("SELECT * FROM student_question_practice WHERE id = '$id'");

$studs = db::arr_s("SELECT * FROM student_students WHERE id = '$student[student_id]'");

$addPoint = $studs['points'] + $point;

db::query("UPDATE `student_students` SET `points` = '$addPoint' WHERE `id` = '$student[student_id]'");

if($studs['user_id'] != NULL){

$currentCoin = db::arr_s("SELECT * FROM student_list WHERE ID = '$studs[user_id]'");

$addCoin = $currentCoin['COINS'] + $coin;

$award = db::query("UPDATE `student_list` SET `COINS` = '$addCoin' WHERE `ID` = '$studs[user_id]' AND `ACTIVE` = 1");

}



$question = db::arr_s("SELECT sca.course_id, sl.id AS lesson_id, sm.id AS module_id
FROM student_questions AS sq
LEFT JOIN student_lessons AS sl ON sl.id = sq.lesson_id
LEFT JOIN student_modules AS sm ON sm.id = sl.module_id
LEFT JOIN student_course_access AS sca ON sca.course_id = sm.course_id
WHERE sq.id = '{$student['question_id']}'");


db::query("UPDATE student_lesson_access SET `status` = 'completed' WHERE `lesson_id` = '{$question['lesson_id']}' AND `status` = 'checking' AND `user_id` = '{$student['student_id']}'");


$last_lesson = db::arr_s("SELECT sl.id AS lesson_id 
FROM student_module_access AS sma
LEFT JOIN student_lessons AS sl ON sl.module_id = sma.module_id
WHERE sma.user_id = '{$student['student_id']}' 
AND sma.module_id = '{$question['module_id']}' ORDER BY sl.order ASC");

$last_question = db::arr_s("SELECT 
sq.id AS question_id, 
sla.status
FROM student_lesson_access AS sla
LEFT JOIN student_questions AS sq ON sq.lesson_id = sla.lesson_id
WHERE sla.user_id = '{$student['student_id']}' 
AND sla.lesson_id = '{$question['lesson_id']}' 
AND sla.status = 'completed' 
ORDER BY sq.order_number ASC");

$courseLessons = db::arr("SELECT 
sl.id AS lesson_id, 
sl.order AS order_num, 
sm.order AS module_order, 
sm.id AS module_id 
FROM student_course_access AS sca
LEFT JOIN student_modules AS sm ON sm.course_id = sca.course_id
LEFT JOIN student_lessons AS sl ON sl.module_id = sm.id
WHERE sca.user_id = '{$student['student_id']}' 
AND sca.course_id = '{$question['course_id']}' 
ORDER BY sm.order, sl.order ASC");


$nextLessonId = null;
$nextModuleId = null;

if($question['lesson_id'] == $last_lesson['lesson_id'] and $last_question['status'] == 'completed'){


$result = [];

foreach($courseLessons as $item){

$module_id = $item['module_id'];
$lesson_id = $item['lesson_id'];

if(!isset($result[$module_id])){
    $result[$module_id] = [];
}

$result[$module_id][] = $lesson_id;

}


if (array_key_exists($question['module_id'], $result)) {
    $moduleIndex = $question['module_id'];
    
    $res = array_slice($result, array_search($moduleIndex, array_keys($result)) + 1, null, true);
}

$nextModuleId = key($res);

$moduleValues = current($res);
$nextLessonId = $moduleValues[0];

}

$emptyLesson = db::arr_s("SELECT id FROM student_questions AS sq
LEFT JOIN student_lesson_access AS sla ON sla.lesson_id = sq.lesson_id
WHERE sq.id = '$nextLessonId' AND sla.user_id = '{$student['student_id']}'");

$getLesson = db::arr_s("SELECT id FROM student_lessons WHERE id = '$nextLessonId'");

if ($question['lesson_id'] == $last_lesson['lesson_id'] && $last_lesson['lesson_id'] != 'empty') {
    if ($emptyLesson == 'empty') {
        $insertLesson = db::query("INSERT INTO `student_lesson_access` (`lesson_id`, `user_id`, `status`) VALUES ('{$getLesson['id']}', '{$student['student_id']}', 'opened')");
    } else {
        db::query("UPDATE `student_lesson_access` SET `status` = 'opened' WHERE `lesson_id` = '{$getLesson['id']}' AND `user_id` = '{$student['student_id']}' AND `status` = 'closed'");
    }
}

$emptyModule = db::arr_s("SELECT id FROM student_module_access AS sma
WHERE sma.module_id = '$nextModuleId' AND sma.user_id = '{$student['student_id']}'");

$getModule = db::arr_s("SELECT id FROM student_modules WHERE id = '$nextModuleId'");

if ($question['lesson_id'] == $last_lesson['lesson_id'] && $last_lesson['lesson_id'] != 'empty') {
    if ($emptyModule == 'empty') {
        db::query("INSERT INTO `student_module_access` (`module_id`, `user_id`) VALUES ('{$getModule['id']}', '{$student['student_id']}')");
    } else {
        db::query("UPDATE `student_lesson_access` SET `status` = 'opened' WHERE `lesson_id` = '{$getLesson['id']}' AND `user_id` = '{$student['student_id']}' AND `status` = 'closed'");
    }
}

LocalRedirect("index.php");
}

if (isset($_POST['rejectSubmit'])) {

$id = $_POST['check_id'];
$score = $_POST['score'];
$reason =  filter_input(INPUT_POST, "reason", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$reject = db::query("UPDATE `student_question_practice` SET 
`score` = '$score',
`reason` = '$reason',
`status` = 'rejected'
WHERE `id` = '$id'");

$student = db::arr_s("SELECT * FROM student_question_practice WHERE id = '$id'");

$question = db::arr_s("
SELECT sca.course_id, sl.id AS lesson_id, sm.id AS module_id
FROM student_questions AS sq
LEFT JOIN student_lessons AS sl ON sl.id = sq.lesson_id
LEFT JOIN student_modules AS sm ON sm.id = sl.module_id
LEFT JOIN student_course_access AS sca ON sca.course_id = sm.course_id
WHERE sq.id = '{$student['question_id']}'");

db::query("UPDATE `student_lesson_access` 
SET `status` = 'failed' 
WHERE `lesson_id` = '{$question['lesson_id']}' 
AND `status` = 'checking' 
AND `user_id` = '{$student['student_id']}'");

LocalRedirect("index.php");
}
?>


<div class="modal fade text-left" id="checkModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel1" aria-hidden="true">

</div>

<div class="modal fade text-left" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel1" aria-hidden="true">

</div>

<div class="modal fade text-left" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel1" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
    <form method="POST" id="editForm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel1">Baholash</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <? 
                    //  echo '<pre>';
                    //  print_r($res);
                    //  echo '</pre>';       
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                <button type="submit" form="editForm" name="checkSubmit" class="btn btn-primary">Saqlash</button>
            </div>
        </div>
    </form>
</div>
</div>

<?
// $question = db::arr_s("SELECT 
// sla.id 
// FROM student_question_practice AS sqp
// LEFT JOIN student_questions AS sq ON sq.id = sqp.question_id
// LEFT JOIN student_lesson_access AS sla.lesson_id = sq.lesson_id
// WHERE id = '$id'");

?>