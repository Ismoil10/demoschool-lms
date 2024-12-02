<?
if($_GET['page_action'] == "test"){

$data = "AND type IN ('selected-file', 'selected-drag')";
    
}elseif($_GET['page_action'] == "info"){

$data = "AND type = 'info'";

}else{
     
$data = "";    
}

$questions = db::arr("SELECT * FROM student_questions WHERE lesson_id = '$_SESSION[item_id]' $data ORDER BY `order_number`");

$test = db::arr_s("SELECT COUNT(id) AS amount FROM student_questions WHERE lesson_id = '$_SESSION[item_id]' AND type IN ('selected-file', 'selected-drag')");

$info = db::arr_s("SELECT COUNT(id) AS amount FROM student_questions WHERE lesson_id = '$_SESSION[item_id]' AND type = 'info'");

$lesson = db::arr_s("SELECT 
sl.id AS lesson_id,
sl.title AS lesson,
sm.id AS module_id,
sm.title AS module,
sc.id AS course_id,
sc.name AS course
FROM student_lessons AS sl
LEFT JOIN student_modules AS sm ON sm.id = sl.module_id
LEFT JOIN student_courses AS sc ON sc.id = sm.course_id
WHERE sl.id = '$_SESSION[item_id]'");
?>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        <? //echo '<pre>'; print_r($lesson); echo '</pre>'; ?>
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0"><?=$lesson['lesson']?></h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/account/main_page/list">Asosiy menyu</a>
                                </li>
                                <li class="breadcrumb-item active"><a href="/account/student_courses/list">Kurslar</a>
                                </li>
                                <li class="breadcrumb-item active"><a href="/account/student_courses/detail/<?=$lesson['course_id']?>"><?=$lesson['course']?></a>
                                </li>
                                <li class="breadcrumb-item active"><a href="/account/student_courses/detail_part/<?=$lesson['module_id']?>"><?=$lesson['module']?></a>
                                </li>
                                <li class="breadcrumb-item active"><a href="/account/student_courses/section/<?=$lesson['lesson_id']?>"><?=$lesson['lesson']?></a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
        <section id="responsive-datatable">
                <div class="row">
                    <div class="col-lg-6 col-sm-6 col-12">
                        <a href="/account/student_courses/test/<?= $lesson['lesson_id'] ?>" class="text-secondary">
                            <div class="card">
                                <div class="card-header">
                                    <div>
                                        <h2 class="font-weight-bolder mb-0"><?=$test['amount']?></h2>
                                        <p class="card-text mb-0">Testlar</p>
                                        <!--<small class="text-secondary">Learn</small>-->
                                    </div>
                                    <div class="avatar bg-light-primary p-50 m-0">
                                        <div class="avatar-content">
                                            <i data-feather="terminal" class="font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-12">
                        <a href="/account/student_courses/info/<?= $lesson['lesson_id'] ?>" class="text-secondary">
                            <div class="card">
                                <div class="card-header">
                                    <div>
                                        <h2 class="font-weight-bolder mb-0"><?=$info['amount']?></h2>
                                        <p class="card-text mb-0">Info</p>
                                        <!--<small class="text-secondary">Learn</small>-->
                                    </div>
                                    <div class="avatar bg-light-secondary p-50 m-0">
                                        <div class="avatar-content">
                                            <i data-feather="info" class="font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </section>
            <!-- Responsive Datatable -->
            <section id="responsive-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">Savollar ro'yhati</h4>
                                <div class="dt-action-buttons text-right">
                                    <div class="dt-buttons d-inline-flex">
                                        <button class="dt-button create-new btn btn-primary mr-1" type="button" data-toggle="modal" data-target="#addModalSection">Yangi savol</button>
                                        <button class="dt-button create-new btn btn-warning btn-icon" type="button" data-toggle="modal" data-target="#filter1"><i data-feather="filter"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-datatable">
                                <table class="d_tab dt-responsive table" id="customtable">
                                    <?
                                    //echo '<pre>';
                                    //print_r($_SESSION);
                                    //echo '</pre>';
                                    ?>
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>#</th>
                                            <th>Tartib</th>
                                            <th>Turi</th>
                                            <th>Savollar</th>
                                            <th>Video</th>
                                            <th>Harakat</th>
                                        </tr>
                                    </thead>
                                    <? foreach($questions as $v) : ?>
                                        <? $link = json_decode($v['other'], true); ?>
                                        <? $str = "/uploads";
                                        //echo '<pre>'; print_r($v['other']); echo '</pre>';
                                        ?>
                                        <tr>
                                            <td></td>
                                            <td><?= $v['id'] ?></td>
                                            <td><?= $v['order_number'] ?></td>
                                            <td><?= $v['type'] ?></td>
                                            <td class="c-question" data-toggle="tooltip" data-placement="top" data-trigger="click"><?=$v['question'];?></td>
                                            <td class="custom-link" data-toggle="tooltip" data-placement="top"><a href="<?=$link['video'];?>"><?=$link['video'];?></a></td>
                                            <td>
                                                <!--<div class="circle"></div>-->
                                                <button class="btn btn-sm btn-primary" onclick="editQuestion(<?= $v['id']?>)"><i data-feather="edit"></i></button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteQuestion(<?= $v['id'] ?>)"><i data-feather="trash"></i></button>
                                            </td>
                                        </tr>
                                    <? endforeach; ?>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>

const getTd = document.querySelectorAll(".custom-link");
getTd.forEach(e => {
const text = e.textContent;

const attribute = e.setAttribute("title", e.textContent);

});

const getQ = document.querySelectorAll(".c-question");
getQ.forEach(e => {
const text = e.textContent;
if(text.length > 50){

const attribute = e.setAttribute("title", e.textContent);
const content = text.substring(0, 50) + "...";
e.textContent = content;
//console.log(attribute);
}
});
</script>

<style>

    .custom-link{
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 20ch;
    }

    .select12 {
        padding-top: 10px;
        margin-left: 65%;
    }

    .sky-color {
        background-color: #cce6ff;
    }

    .progress-color {
        background-color: #ffffcc;
    }

    .circle {
        margin-top: 8px;
        margin-right: 20px;
        left: 12px;
        float: left;
        width: 20px;
        height: 20px;
        background-color: #87CEFA;
        border-radius: 50%
    }


</style>

<? require "modules/med/student_courses/student_courses_section_modal.php"; ?>
<? require "modules/med/student_courses/student_courses_js.php"; ?>