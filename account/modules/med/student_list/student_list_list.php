<!-- BEGIN: Content-->
<?
$org_id = $_SESSION["USER"]["ORG_ID"];
$org_list = db::arr_by_id("SELECT * FROM `org_list`");
if ($_SESSION['USER']['ORG_ID'] > 0) {
    $sql_org_id = "AND `ORG_ID` = '$org_id'";
}
$group_list = db::arr_by_id("SELECT 
    stl.ID,
    gl.NAME,
    sbl.SPECIAL_PRICE,
    sbl.TYPE,
    sbl.STATUS,
    sbl.START_DATE,
    tl.NAME AS TEACHER_NAME
    FROM `student_list` stl 
    LEFT JOIN `subscribe_list` sbl ON sbl.STUDENT_ID = stl.ID
    LEFT JOIN `group_list` gl ON gl.ID = sbl.GROUP_ID
    LEFT JOIN `teacher_list` tl ON tl.ID=gl.TEACHER_ID
    WHERE stl.ACTIVE=1 AND stl.ORG_ID='$org_id' AND sbl.ACTIVE=1");
$stu_index = 1;
?>
<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Talabalar</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/account/main_page/list">Asosiy menyu</a>
                                </li>
                                <li class="breadcrumb-item active">Talabalar
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
                    <div class="col-12 <? if (!isset($_SESSION["student_error"])) echo "d-none"; ?>">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="alert-body">
                                <?= $_SESSION["student_error"];
                                unset($_SESSION["student_error"]); ?>
                            </div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">Talabalar ro'yhati <? if (isset($_SESSION["studentFilterType"])) echo "<small>Filter: on</small>"; ?></h4>
                                <div class="dt-action-buttons text-right">
                                    <div class="dt-buttons d-inline-flex">
                                        <button class="dt-button create-new btn btn-primary mr-1" type="button" data-toggle="modal" data-target="#addModal">Yangi qo'shish</button>
                                        <button class="dt-button create-new btn btn-warning btn-icon" type="button" data-toggle="modal" data-target="#filter"><i data-feather="filter"></i></button>
                                    </div>
                                </div>
                            </div>
                            <?
                                /* Table goes here */
                                if(isset($_SESSION["studentFilterType"]) AND file_exists($_SERVER["DOCUMENT_ROOT"]."/account/modules/med/student_list/student_list_tables/$_SESSION[studentFilterType].php")){
                                    require $_SERVER["DOCUMENT_ROOT"]."/account/modules/med/student_list/student_list_tables/$_SESSION[studentFilterType].php";
                                }else{
                                    require $_SERVER["DOCUMENT_ROOT"]."/account/modules/med/student_list/student_list_tables/default_table.php";
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<?
require('modules/med/student_list/student_list_modals.php');
?>
<?
require('modules/med/student_list/student_list_js.php');
?>