<?

$sections = db::arr("SELECT * FROM student_modules WHERE course_id = '$_SESSION[item_id]' ORDER BY `order` ASC");

$course = db::arr_s("SELECT * FROM student_courses WHERE id = '$_SESSION[item_id]'");
?>
<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0"><?=$course['name']?></h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/account/main_page/list">Asosiy menyu</a>
                                </li>
                                <li class="breadcrumb-item active"><a href="/account/student_courses/list">Kurslar</a>
                                </li>
                                <li class="breadcrumb-item active"><a href="/account/student_courses/detail/<?=$course['id']?>"><?=$course['name']?></a>
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
                                <h4 class="card-title">Bo'limlar ro'yhati</h4>
                                <div class="dt-action-buttons text-right">
                                    <div class="dt-buttons d-inline-flex">
                                        <button class="dt-button create-new btn btn-primary mr-1" type="button" data-toggle="modal" onclick="add_module()">Yangi qo'shish</button>
                                        <button class="dt-button create-new btn btn-warning btn-icon" type="button" data-toggle="modal" data-target="#filter1"><i data-feather="filter"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-datatable">
                                <table class="d_tab dt-responsive table" id="customtable">
                                    <? //echo '<pre>'; print_r($_POST); echo '</pre>'; 
                                    ?>
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Tartib</th>
                                            <th>Tavsif</th>
                                            <th>Rasm</th>
                                            <th>Harakat</th>
                                        </tr>
                                    </thead>
                                    <? foreach ($sections as $v) : ?>
                                        <tr>
                                            <td></td>
                                            <td><?=$v['id']?></td>
                                            <td><a href="/account/student_courses/detail_part/<?=$v["id"]?>"><?=$v['title']?></a></td>
                                            <td><?=$v['order']?></td>
                                            <td class="custom-td" data-toggle="tooltip" data-placement="top"><?= $v['description']?></td>
                                            <td class="custom-img">
                                                <img src="<?="https://demoschool.senet.uz".$v['url'] ?>"  style="height: 60px;">
                                            </td>
                                            <td>
                                                <!--<div class="circle"></div>-->
                                                <button class="btn btn-sm btn-primary" onclick="editModule(<?= $v['id'] ?>)"><i data-feather="edit"></i></button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteModule(<?= $v['id'] ?>)"><i data-feather="trash"></i></button>
                                                <a href="/account/student_courses/detail_part/<?=$v["id"]?>" class="btn btn-sm btn-success"><i data-feather='chevron-right'></i></a>
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

const getTd = document.querySelectorAll(".custom-td");


getTd.forEach(e => {
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
    .custom-img {
        max-width: auto;
        max-height: auto;
    } 

    .select12{
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
<? require "modules/med/student_courses/student_courses_detail_modal.php"; ?>