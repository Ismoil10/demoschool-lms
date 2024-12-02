<?

if($_GET['page_action'] == "learn"){

$data = "AND type = 'Learn'";

}elseif($_GET['page_action'] == "practice"){

$data = "AND type = 'Practice'";

}else{
 
$data = "";    
}

$lessons = db::arr("SELECT * FROM student_lessons WHERE module_id = '$_SESSION[item_id]' $data ORDER BY `order`");

$learn = db::arr_s("SELECT COUNT(ID) AS amount FROM student_lessons WHERE module_id = '$_SESSION[item_id]' AND type = 'Learn'");

$practic = db::arr_s("SELECT COUNT(ID) AS amount FROM student_lessons WHERE module_id = '$_SESSION[item_id]' AND type = 'Practice'");

$section = db::arr_s("SELECT 
sm.id AS module_id,
sm.title AS module,
sc.id AS course_id,
sc.name AS course
FROM student_modules AS sm
LEFT JOIN student_courses AS sc ON sc.id = sm.course_id
WHERE sm.id = '$_SESSION[item_id]'");
?>
<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0"><?= $section['module'] ?></h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/account/main_page/list">Asosiy menyu</a>
                                </li>
                                <li class="breadcrumb-item active"><a href="/account/student_courses/list">Kurslar</a>
                                </li>
                                <li class="breadcrumb-item active"><a href="/account/student_courses/detail/<?= $section['course_id'] ?>"><?= $section['course'] ?></a>
                                </li>
                                <li class="breadcrumb-item active"><a href="/account/student_courses/detail_part/<?= $section['module_id'] ?>"><?= $section['module'] ?></a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <? //echo '<pre>'; print_r($_GET['page_action']); echo '</pre>'; 
            ?>
            <section class="statistics-card">
                <div class="row">
                    <div class="col-lg-4 col-sm-6 col-12">
                        <a href="/account/student_courses/learn/<?= $section['module_id'] ?>" class="text-secondary">
                            <div class="card">
                                <div class="card-header">
                                    <div>
                                        <h2 class="font-weight-bolder mb-0"><?=$learn['amount']?></h2>
                                        <p class="card-text mb-0">Turi</p>
                                        <small class="text-secondary">Learn</small>
                                    </div>
                                    <div class="avatar bg-light-success p-50 m-0">
                                        <div class="avatar-content">
                                            <i data-feather="book-open" class="font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-12">
                        <a href="/account/student_courses/practice/<?= $section['module_id']?>" class="text-secondary">
                            <div class="card">
                                <div class="card-header">
                                    <div>
                                        <h2 class="font-weight-bolder mb-0"><?=$practic['amount']?></h2>
                                        <p class="card-text mb-0">Turi</p>
                                        <small class="text-secondary">Practice</small>
                                    </div>
                                    <div class="avatar bg-light-primary p-50 m-0">
                                        <div class="avatar-content">
                                            <i data-feather="code" class="font-medium-5"></i>
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
                                <h4 class="card-title">Mavzular ro'yhati</h4>
                                <div class="dt-action-buttons text-right">
                                    <div class="dt-buttons d-inline-flex">
                                        <button class="dt-button create-new btn btn-primary mr-1" type="button" data-toggle="modal" onclick="add_lesson()">Yangi qo'shish</button>
                                        <button class="dt-button create-new btn btn-warning btn-icon" type="button" data-toggle="modal" data-target="#filter1"><i data-feather="filter"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-datatable">
                                <table class="d_tab dt-responsive table" id="customtable">
                                    <? //echo '<pre>'; print_r($_POST); echo '</pre>'; ?>
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>#</th>
                                            <th>Tartib</th>
                                            <th>Mavzu</th>
                                            <th>Turi</th>
                                            <th>Harakat</th>
                                        </tr>
                                    </thead>
                                    <? foreach ($lessons as $v) : ?>
                                        <tr>
                                            <td></td>
                                            <td><?= $v['id'] ?></td>
                                            <td><?= $v['order'] ?></td>
                                            <td><a href="/account/student_courses/section/<?= $v["id"] ?>"><?= $v['title'] ?></a></td>
                                            <td><?= $v['type'] ?></td>
                                            <td>
                                                <!--<div class="circle"></div>-->
                                                <button class="btn btn-sm btn-primary" onclick="editLesson(<?= $v['id'] ?>)"><i data-feather="edit"></i></button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteLesson(<?= $v['id'] ?>)"><i data-feather="trash"></i></button>
                                                <a href="/account/student_courses/section/<?= $v["id"] ?>" class="btn btn-sm btn-success"><i data-feather='chevron-right'></i></a>
                                            </td>
                                        </tr>
                                    <? endforeach ?>
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


</script>

<style>
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

<? require "modules/med/student_courses/student_courses_js.php"; ?>
<? require "modules/med/student_courses/student_courses_detail_part_modal.php"; ?>