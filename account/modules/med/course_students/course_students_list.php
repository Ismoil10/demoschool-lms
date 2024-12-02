<?
$course_id = db::arr("SELECT st.*,
access.course_id,
course.name
FROM student_students AS st
LEFT JOIN student_course_access AS access ON st.id = access.user_id
LEFT JOIN student_courses AS course ON course.id = access.course_id");

$students = db::arr("SELECT * FROM student_students");

?>
<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Kurs talabalari</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/account/main_page/list">Asosiy menyu</a>
                                </li>
                                <li class="breadcrumb-item active">Kurs talabalari
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <!-- Responsive Datatable -->
            <section id="responsive-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">Kurs talabalari ro'yhati</h4>
                                <div class="dt-action-buttons text-right">
                                    <div class="dt-buttons d-inline-flex">
                                        <button class="dt-button create-new btn btn-primary mr-1" type="button" data-toggle="modal" onclick="add_modal()">Yangi qo'shish</button>
                                        <button class="dt-button create-new btn btn-warning btn-icon" type="button" data-toggle="modal" data-target="#filter1"><i data-feather="filter"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-datatable">
                                <table class="d_tab dt-responsive table" id="customtable">
                                    <? 
                                    //echo '<pre>'; print_r($course_id); echo '</pre>'; 
                                    ?>
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>#</th>
                                            <th>Talaba</th>
                                            <th>Kurs</th>
                                            <th>Harakat</th>
                                        </tr>
                                    </thead>
                                    <? foreach ($students as $v) : ?>
                                        <tr>
                                            <td></td>
                                            <td><?= $v['id'] ?></td>
                                            <td><? if($v['user_id'] != null): ?><a href="/account/student_list/detail/<?=$v['user_id']?>" target="_blank"><?= $v['username'] ?></a><? else: ?><?= $v['username'] ?><? endif; ?></td>
                                            <td>
                                                <? foreach($course_id as $cs): ?>
                                                <div class="badge badge-glow badge-info"><? if($cs['id'] == $v['id']){ echo $cs['name']; }?></div>
                                                <? endforeach; ?>
                                            </td>
                                            <td>
                                                <!--<div class="circle"></div>-->
                                                <button class="btn btn-sm btn-primary" onclick="editStudent(<?= $v['id'] ?>)"><i data-feather="edit"></i></button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteStudent(<?= $v['id'] ?>)"><i data-feather="trash"></i></button>
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

<? require "modules/med/course_students/course_students_js.php"; ?>
<? require "modules/med/course_students/course_students_modal.php"; ?>