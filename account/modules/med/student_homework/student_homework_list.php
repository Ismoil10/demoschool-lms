<?
$import_links = ["yuklangan", 'baholangan', 'rad_etilgan'];
if (in_array($_GET['item_id'], $import_links)) {

    $_SESSION['section'] = $_GET['item_id'];
    LocalRedirect('/account/student_homework/list');
}
$org_id = $_SESSION["USER"]["ORG_ID"];
/* Get Numbers Information  */
$user_phone = $_SESSION["USER"]["PHONE"];
$teacher_id = db::arr_s("SELECT ID FROM `teacher_list` WHERE `PHONE`='$user_phone'");

$homeworks = db::arr("SELECT 
sqp.id AS id,
sqp.status AS `status`,
DATE_FORMAT(sqp.created_at, '%Y-%m-%d') AS `date`,
ss.id AS student_id,
ss.username AS username,
sl.id AS lesson_id,
sl.title AS title
FROM student_question_practice sqp
RIGHT JOIN student_students AS ss ON ss.id = sqp.student_id
RIGHT JOIN student_questions AS sq ON sq.id = sqp.question_id
RIGHT JOIN student_lessons AS sl ON sl.id = sq.lesson_id
WHERE sqp.teacher_id = '$teacher_id[ID]'");

$upload_homeworks = db::arr_s("SELECT 
COUNT(sqp.id) AS amount 
FROM student_question_practice AS sqp
RIGHT JOIN student_students AS ss ON ss.id = sqp.student_id
WHERE sqp.status = 'uploaded' AND sqp.teacher_id = '$teacher_id[ID]'");

$approved_homeworks = db::arr_s("SELECT 
COUNT(sqp.id) AS amount 
FROM student_question_practice AS sqp
RIGHT JOIN student_students AS ss ON ss.id = sqp.student_id
WHERE sqp.status = 'approved' AND sqp.teacher_id = '$teacher_id[ID]'");

$rejected_homeworks = db::arr_s("SELECT 
COUNT(sqp.id) AS amount 
FROM student_question_practice AS sqp
RIGHT JOIN student_students AS ss ON ss.id = sqp.student_id
WHERE sqp.status = 'rejected' AND sqp.teacher_id = '$teacher_id[ID]'");

$statuses = ["uploaded" => "Yuklangan", "rejected" => "Rad etilgan", "graded" => "Baholangan"];
$statuses_background = ["uploaded" => "primary", "rejected" => "warning", "graded" => "success"];

$index = 1;
?>
<? if ($_SERVER["REMOTE_ADDR"] == "217.30.165.114"): ?>
    <!-- <pre><?= "SELECT `STUDENT_ID` AS ID
FROM `subscribe_list`
WHERE `ACTIVE`='1' AND GROUP_ID IN ($group_ids)" ?></pre> -->
<? endif; ?>
<!-- BEGIN: Content-->
<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                    <? //echo '<pre>'; print_r($homeworks); '</pre>'; ?>
                        <h2 class="content-header-title float-left mb-0">Uy Vazifalar</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/account/main_page/list">Asosiy Menyu</a></li>
                                <li class="breadcrumb-item active">Uy Vazifalar</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- Statistics card section -->
            <section id="statistics-card">
                <!-- Stats Horizontal Card -->
                <div class="row">
                    <div class="col-lg-4 col-sm-6 col-12">
                        <a href="/account/student_homework/list/yuklangan" class="text-secondary">
                            <div class="card">
                                <div class="card-header">
                                    <div>
                                        <h2 class="font-weight-bolder mb-0"><?= number_format($upload_homeworks["amount"], 0, "", " ") ?></h2>
                                        <p class="card-text">Yuklangan</p>
                                    </div>
                                    <div class="avatar bg-light-primary p-50 m-0">
                                        <div class="avatar-content">
                                            <i data-feather="package" class="font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-12">
                        <a href="/account/student_homework/list/baholangan" class="text-secondary">
                            <div class="card">
                                <div class="card-header">
                                    <div>
                                        <h2 class="font-weight-bolder mb-0"><?= $approved_homeworks["amount"] ?></h2>
                                        <p class="card-text">Baholangan</p>
                                    </div>
                                    <div class="avatar bg-light-info p-50 m-0">
                                        <div class="avatar-content">
                                            <i data-feather="loader" class="font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-12">
                        <a href="/account/student_homework/list/rad_etilgan" class="text-secondary">
                            <div class="card">
                                <div class="card-header">
                                    <div>
                                        <h2 class="font-weight-bolder mb-0"><?= $rejected_homeworks["amount"] ?></h2>
                                        <p class="card-text">Rad etilgan</p>
                                    </div>
                                    <div class="avatar bg-light-secondary p-50 m-0">
                                        <div class="avatar-content">
                                            <i data-feather="list" class="font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </section>
            <!--/ Statistics Card section-->
            <section id="responsive-datatable">
                <div class="row">
                    <div class="col-12">
                        <?
                        if($_SESSION['section'] == "yuklangan"){
                            require $_SERVER["DOCUMENT_ROOT"] . "/account/modules/med/student_homework/homework_type/uploaded_homeworks.php";
                        }else if($_SESSION['section'] == "baholangan"){
                            require $_SERVER["DOCUMENT_ROOT"] . "/account/modules/med/student_homework/homework_type/approved_homeworks.php";
                        }else if($_SESSION['section'] == "rad_etilgan"){
                            require $_SERVER["DOCUMENT_ROOT"] . "/account/modules/med/student_homework/homework_type/rejected_homeworks.php";
                        }else{
                            require $_SERVER["DOCUMENT_ROOT"] . "/account/modules/med/student_homework/homework_type/all_homeworks.php";
                        }
                        ?>
                    </div>
                </div>
                <!-- Hoverable rows end -->
            </section>
        </div>
    </div>
</div>
<!-- END: Content-->
<style>
    .single-line {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
<? require "modules/med/student_homework/homework_type/homework_js.php"; ?>
<? require "modules/med/student_homework/homework_type/homework_modal.php"; ?>

