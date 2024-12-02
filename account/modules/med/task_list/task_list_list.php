<?
$now = date("Y-m-d");

$id = $_SESSION["USER"]["ID"];
$org_id = $_SESSION["USER"]["ORG_ID"];
if (in_array($_GET["item_id"], ["to_me", "to_me_comp", "by_me", "by_me_comp", "to_me_today"])) {
    $_SESSION["task_list_table"] = $_GET["item_id"];
    LocalRedirect("index.php");
}
if ($org_id > 0) {
    $student_org_id = " AND sl.`ORG_ID`='$org_id'";
    $task_org_id = " AND lt.`ORG_ID`='$org_id'";
}

if ($_SESSION["task_list_table"] == 'to_me_comp') {

    $tasks = db::arr("SELECT lt.*,
    sl.NAME AS `STUDENT`,
    sl.PHONE AS `STUDENT_PHONE`
    FROM `list_tasks` lt
    LEFT JOIN `student_list` AS sl ON sl.ID = lt.TARGET_ID
    WHERE lt.STATUS ='closed' AND lt.`ASSIGNED_MEMBERS` LIKE '%\"$id\"%' $task_org_id");
} elseif ($_SESSION["task_list_table"] == 'by_me') {

    $tasks = db::arr("SELECT lt.*,
    sl.NAME AS `STUDENT`,
    sl.PHONE AS `STUDENT_PHONE`
    FROM `list_tasks` lt
    LEFT JOIN `student_list` AS sl ON sl.ID = lt.TARGET_ID
    WHERE lt.STATUS IN ('inprogress','open') AND lt.CREATED_BY = '$id' $task_org_id");
} elseif ($_SESSION["task_list_table"] == 'by_me_comp') {
    $tasks = db::arr("SELECT lt.*,
    sl.NAME AS `STUDENT`,
    sl.PHONE AS `STUDENT_PHONE`
    FROM `list_tasks` lt
    LEFT JOIN `student_list` AS sl ON sl.ID = lt.TARGET_ID
    WHERE lt.STATUS ='closed' AND lt.CREATED_BY = '$id' $task_org_id");
} elseif ($_SESSION["task_list_table"] == 'to_me_today') {
    $tasks = db::arr("SELECT lt.*,
    sl.NAME AS `STUDENT`,
    sl.PHONE AS `STUDENT_PHONE`
    FROM `list_tasks` lt
    LEFT JOIN `student_list` AS sl ON sl.ID = lt.TARGET_ID
    WHERE lt.STATUS IN ('open', 'inprogress') AND `DUE_DATE` = '$now' AND lt.`ASSIGNED_MEMBERS` LIKE '%\"$id\"%' $task_org_id");
} else {
    $tasks = db::arr("SELECT lt.*,
    sl.NAME AS `STUDENT`,
    sl.PHONE AS `STUDENT_PHONE`
    FROM `list_tasks` lt
    LEFT JOIN `student_list` AS sl ON sl.ID = lt.TARGET_ID
    WHERE lt.STATUS IN ('open', 'inprogress') AND lt.`ASSIGNED_MEMBERS` LIKE '%\"$id\"%' $task_org_id");
}
$status = ["open" => "bg-warning bg-lighten-5", "inprogress" => "bg-info bg-lighten-5", "closed" => "bg-success bg-lighten-5"];
$types = ["general" => "Umumiy", "group" => "Guruh", "student" => "Talaba"];
?>
<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Vazifalar</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/account/main_page/list">Asosiy menyu</a>
                                </li>
                                <li class="breadcrumb-item active">Vazifalar
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <!-- Info Cards -->
            <? require $_SERVER["DOCUMENT_ROOT"] . "/account/modules/med/task_list/task_list_details/info_cards.php"; ?>
            <!-- Responsive Datatable -->
            <section id="responsive-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">Vazifalar ro'yhati</h4>
                                <div class="dt-action-buttons text-right">
                                    <div class="dt-buttons d-inline-flex">
                                        <button class="dt-button create-new btn btn-primary mr-1" type="button" data-toggle="modal" data-target="#addModal">Yangi qo'shish</button>
                                        <!-- <button class="dt-button create-new btn btn-warning btn-icon" type="button" data-toggle="modal" data-target="#filter1"><i data-feather="filter"></i></button> -->
                                    </div>
                                </div>
                            </div>
                            <div class="card-datatable">
                                <? //echo '<pre>'; print_r($_SESSION); echo '</pre>'; ?>
                                <table class="d_tab dt-responsive table" id="customtable">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Boshlanish muddati</th>
                                            <th>Turi</th>
                                            <th>Talaba</th>
                                            <th>Tel. raqam</th>
                                            <th>Vazifa</th>
                                            <th>Topshirish muddati</th>
                                            <th>Tasdiqlash</th>
                                        </tr>
                                    </thead>
                                    <? foreach ($tasks as $task) : ?>
                                        <tr class="<?= $status[$task["STATUS"]] ?>">
                                            <td></td>
                                            <td><?= date("d.m.Y (H:i)", strtotime($task['CREATE_DATE'])) ?></td>
                                            <td><?= $types[$task["TYPE"]] ?></td>
                                            <td><?= $task["STUDENT"] ?></td>
                                            <td><?= $task["STUDENT_PHONE"] ?></td>
                                            <td><?= $task["TASK"] ?></td>
                                            <td><?= date("d.m.Y (H:i)", strtotime($task["DUE_DATE"])) ?></td>
                                            <td>
                                                <!--<div class="circle"></div>-->
                                                <button class="btn btn-icon btn-outline-success btn-right rounded-circle" onclick="taskComplete(<?= $task['ID'] ?>)"><i data-feather="check-circle"></i></button>
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
    <? require "modules/med/task_list/task_list_js.php"; ?>
    <? require "modules/med/task_list/task_list_modal.php"; ?>
</div>