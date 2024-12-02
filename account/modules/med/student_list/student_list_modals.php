<?php
$responsible = db::arr("SELECT `ID`, CONCAT(NAME,' ', SURNAME, ' ', PHONE) AS `NAME` FROM `gl_sys_users` WHERE `ROLE_ID` IN (2,3,4) AND ORG_ID IN ('3', '4', '5', '6','7','8') AND `STATUS` = '1'");
$student_cur_groups = db::arr("SELECT 
gl.ID, gl.NAME, 
sl.SPECIAL_PRICE, 
sl.ACTIVE, 
sl.TYPE,
sl.DAY,
DATE_FORMAT(sl.START_DATE, '%Y-%m-%d') AS `START_DATE`,
DATE_FORMAT(sl.END_DATE, '%Y-%m-%d') AS `END_DATE`, 
sl.LAPTOP 
FROM `group_list` gl LEFT JOIN `subscribe_list` sl ON sl.GROUP_ID = gl.ID WHERE sl.STUDENT_ID='$student_id' AND sl.ACTIVE=1 AND sl.TYPE <> 'free'");
$discountsList = db::arr("SELECT * FROM `discounts_list` WHERE `ACTIVE`=1 AND `DISCOUNT_TYPE`='discount'");
$packagesList = db::arr("SELECT * FROM `discounts_list` WHERE `ACTIVE`=1 AND `DISCOUNT_TYPE`='package'");
$page = $_GET["page"];
$add_form_data = db::arr_s("SELECT * FROM `form_list` WHERE `ORG_ID`='$org_id' AND `PAGE`='$page' AND `FORM_TYPE`='addModal' AND `ACTIVE`='1'");
$rows = json_decode($add_form_data["ROWS_JSON"], true);
$inputs = json_decode($add_form_data["INPUTS_JSON"], true);
$edit_form_data = db::arr_s("SELECT * FROM `form_list` WHERE `ORG_ID`='$org_id' AND `PAGE`='$page' AND `FORM_TYPE`='editModal' AND `ACTIVE`='1'");
$edit_rows = json_decode($edit_form_data["ROWS_JSON"], true);
$edit_inputs = json_decode($edit_form_data["INPUTS_JSON"], true);

$input_group_types = ["before" => "prepend", "after" => "append", "both" => ["prepend", "append"]];
?>

<div class="modal fade text-left" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel1">Qo'shish</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="addForm" class="p-0 m-0">
                    <div class="row">
                        <? foreach ($rows as $inputName => $className) : ?>
                            <?
                            if (!isset($inputs[$inputName])) continue;
                            $input = $inputs[$inputName];
                            ?>
                            <div class="<?= $className; ?>">
                                <div class="<?= isset($input["class"]) ? $input["class"] : "mb-3"; ?>">
                                    <? if (isset($input["label"])) : ?>
                                        <label><?= $input["label"] ?></label>
                                    <? endif; ?>

                                    <? if (!isset($input["input_group"]) and isset($input["input"])) : ?>

                                        <input <?= join(" ", array_map("generate_attributes", array_keys($input["input"]), array_values($input["input"]))) ?> />

                                    <? elseif (!isset($input["input_group"]) and isset($input["select"])) : ?>
                                        <select <?= join(" ", array_map("generate_attributes", array_keys($input["select"]), array_values($input["select"]))) ?>>
                                            <?= join(" ", array_map("generate_options", array_keys($input["select"]["option"]), array_values($input["select"]["option"]))) ?>
                                        </select>

                                    <? elseif (isset($input["input_group"]) and isset($input["input"])) : ?>
                                        <div class="input-group input-group-merge mb-2">
                                            <? if ($input["input_group"]["type"] = "text" and ($input["input_group"]["position"] == "before" or $input["input_group"]["position"] == "both")) : ?>
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><?= $input["input_group"]["text"][0] ?></span>
                                                </div>
                                            <? endif; ?>

                                            <input <?= join(" ", array_map("generate_attributes", array_keys($input["input"]), array_values($input["input"]))) ?> />

                                            <? if ($input["input_group"]["type"] = "text" and ($input["input_group"]["position"] == "after" or $input["input_group"]["position"] == "both")) : ?>
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><?= $input["input_group"]["text"][1] ?></span>
                                                </div>
                                            <? endif; ?>
                                        </div>


                                    <? elseif (isset($input["input_group"]) and isset($input["select"])) : ?>
                                        <div class="input-group input-group-merge mb-2">
                                            <? if ($input["input_group"]["type"] = "text" and ($input["input_group"]["position"] == "before" or $input["input_group"]["position"] == "both")) : ?>
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><?= $input["input_group"]["text"][0] ?></span>
                                                </div>
                                            <? endif; ?>
                                            <select <?= join(" ", array_map("generate_attributes", array_keys($input["select"]), array_values($input["select"]))) ?>>
                                                <?= join(" ", array_map("generate_options", array_keys($input["select"]["option"]), array_values($input["select"]["option"]))) ?>
                                            </select>
                                            <? if ($input["input_group"]["type"] = "text" and ($input["input_group"]["position"] == "after" or $input["input_group"]["position"] == "both")) : ?>
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><?= $input["input_group"]["text"][1] ?></span>
                                                </div>
                                            <? endif; ?>
                                        </div>
                                    <? endif; ?>
                                </div>
                            </div>
                        <? endforeach; ?>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                <button type="submit" class="btn btn-primary" form="addForm" name="addSubmit">Qo'shish</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel1">Tahrirlash</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form action="" method="post" id="editForm" class="p-0 m-0">
                    <div class="row">
                        <? foreach ($edit_rows as $inputName => $className) : ?>
                            <?
                            if (!isset($edit_inputs[$inputName])) continue;
                            $input = $edit_inputs[$inputName];
                            ?>
                            <div class="<?= $className; ?>">
                                <div class="<?= isset($input["class"]) ? $input["class"] : "mb-3"; ?>">
                                    <? if (isset($input["label"])) : ?>
                                        <label><?= $input["label"] ?></label>
                                    <? endif; ?>

                                    <? if (!isset($input["input_group"]) and isset($input["input"])) : ?>

                                        <input <?= join(" ", array_map("generate_attributes", array_keys($input["input"]), array_values($input["input"]))) ?> />

                                    <? elseif (!isset($input["input_group"]) and isset($input["select"])) : ?>
                                        <select <?= join(" ", array_map("generate_attributes", array_keys($input["select"]), array_values($input["select"]))) ?>>
                                            <?= join(" ", array_map("generate_options", array_keys($input["select"]["option"]), array_values($input["select"]["option"]))) ?>
                                        </select>

                                    <? elseif (isset($input["input_group"]) and isset($input["input"])) : ?>
                                        <div class="input-group input-group-merge mb-2">
                                            <? if ($input["input_group"]["type"] = "text" and ($input["input_group"]["position"] == "before" or $input["input_group"]["position"] == "both")) : ?>
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><?= $input["input_group"]["text"][0] ?></span>
                                                </div>
                                            <? endif; ?>

                                            <input <?= join(" ", array_map("generate_attributes", array_keys($input["input"]), array_values($input["input"]))) ?> />

                                            <? if ($input["input_group"]["type"] = "text" and ($input["input_group"]["position"] == "after" or $input["input_group"]["position"] == "both")) : ?>
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><?= $input["input_group"]["text"][1] ?></span>
                                                </div>
                                            <? endif; ?>
                                        </div>


                                    <? elseif (isset($input["input_group"]) and isset($input["select"])) : ?>
                                        <div class="input-group input-group-merge mb-2">
                                            <? if ($input["input_group"]["type"] = "text" and ($input["input_group"]["position"] == "before" or $input["input_group"]["position"] == "both")) : ?>
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><?= $input["input_group"]["text"][0] ?></span>
                                                </div>
                                            <? endif; ?>
                                            <select <?= join(" ", array_map("generate_attributes", array_keys($input["select"]), array_values($input["select"]))) ?>>
                                                <?= join(" ", array_map("generate_options", array_keys($input["select"]["option"]), array_values($input["select"]["option"]))) ?>
                                            </select>
                                            <? if ($input["input_group"]["type"] = "text" and ($input["input_group"]["position"] == "after" or $input["input_group"]["position"] == "both")) : ?>
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><?= $input["input_group"]["text"][1] ?></span>
                                                </div>
                                            <? endif; ?>
                                        </div>
                                    <? endif; ?>
                                </div>
                            </div>
                        <? endforeach; ?>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                <button type="submit" class="btn btn-primary" form="editForm" name="editSubmit">Saqlash</button>
            </div>
        </div>
    </div>
</div>

<!-- Task Student -->
<div class="modal fade text-left" id="taskModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel1">Yangi vazifa qo'shing</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="taskModalForm">
                    <div class="mb-3">
                        <label class="form-label">Mas'ul</label>
                        <select name="responsible[]" id="" class="select2 form-control" multiple>
                            <? foreach ($responsible as $v) : ?>
                                <option value="<?= $v["ID"]; ?>"><?= $v["NAME"]; ?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Muddat</label>
                        <input type="date" name="due_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Vazifa</label>
                        <textarea name="taskText" rows="5" class="form-control" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                <button type="submit" name="taskSubmit" form="taskModalForm" class="btn btn-primary">Tasdiqlash</button>
            </div>
        </div>
    </div>
</div>

<!-- Online Course -->
<div class="modal fade text-left" id="onlineCourseModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel1">Onlayn kursga qo'shish</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="onlineModalForm">
                    <div class="mb-3">
                        <label class="form-label">Talaba</label>
                        <input type="text" class="form-control" value="<?= $student['EMAIL'] ?>" name="student_username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Talaba paroli</label>
                        <input type="text" class="form-control" name="student_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kurs</label>
                        <select name="online_courses[]" id="" class="select2 form-control" multiple>
                            <? foreach (db::arr("SELECT * FROM student_courses") as $v) : ?>
                                <option value="<?= $v["id"]; ?>"><?= $v["name"]; ?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                <button type="submit" name="onlineSubmit" form="onlineModalForm" class="btn btn-primary">Tasdiqlash</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade text-left" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModal">O'chirish</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="deleteForm">
                    <div class="mb-2">
                        <label>Boshlanish Sana</label>
                        <input type="date" name="deletedDate" class="form-control" min="<?= date("Y-m-d", strtotime("-15 day")); ?>" required />
                    </div>
                    <div class="demo-inline-spacing mb-1">
                        <div class="custom-control custom-radio">
                            <input type="radio" id="customRadio1" name="classType" class="custom-control-input" value="demoClass" checked />
                            <label class="custom-control-label" for="customRadio1">Sinov Dars</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="customRadio2" name="classType" class="custom-control-input" value="mainClass" />
                            <label class="custom-control-label" for="customRadio2">Asosiy Dars</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <lable class="form-label">Sabablar</lable>
                        <select name="studentDeleteReason" id="reasonList" class="from-control hide-search" required>
                            <option value="Ustoz yoqmadi">Ustoz yoqmadi</option>
                            <option value="Kurs qimmat">Kurs qimmat</option>
                            <option value="Manzil uzoq">Manzil uzoq</option>
                            <option value="Darsga qiziqmadi">Darsga qiziqmadi</option>
                            <option value="Tel. o'chiq">Tel. o'chiq</option>
                            <option value="Dubl">Dubl</option>
                            <option value="Rus tilida kerak">Rus tilida kerak</option>
                            <option value="Vaqti yo'q">Vaqti yo'q</option>
                            <option value="Boshqa o'quv markaziga keti">Boshqa o'quv markaziga keti</option>
                            <option value="Darsga umuman kelmagan">Darsga umuman kelmagan</option>
                            <option value="Bitirdi">Bitirdi</option>
                            <option value="Sog`lig`i sababli">Sog`lig`i sababli</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Izoh</label>
                        <textarea name="studentDeleteText" class="form-control" required></textarea>
                    </div>
                    <input type="hidden" name="deleteId">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor Qilish</button>
                <button type="submit" class="btn btn-primary" form="deleteForm" name="deleteSubmit">O'chirish</button>
            </div>
        </div>
    </div>
</div>
<!-- Filter Modal -->
<div class="modal fade text-left" id="filter" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel1">Filter</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="filterForm">
                    <div class="mb-3">
                        <label>Filter turi</label>
                        <select name="filterType" class="form-control" id="student-filter-types">
                            <option selected disabled></option>
                            <option value="demoStudent" <? if ($_SESSION["studentFilterType"] == "demoStudent") echo "selected"; ?>>Sinov darsidagi talabalar</option>
                            <option value="newStudent" <? if ($_SESSION["studentFilterType"] == "newStudent") echo "selected"; ?>>Yangi talabalar</option>
                            <option value="freezedStudent" <? if ($_SESSION["studentFilterType"] == "freezedStudent") echo "selected"; ?>>Muzlatilingan talabalar</option>
                            <option value="debtors" <? if ($_SESSION["studentFilterType"] == "debtors") echo "selected"; ?>>Qarzdorlar</option>
                            <option value="activeStudent" <? if ($_SESSION["studentFilterType"] == "activeStudent") echo "selected"; ?>>Faol Talabalar</option>
                            <option value="activatedStudent" <? if ($_SESSION["studentFilterType"] == "activatedStudent") echo "selected"; ?>>Faollashgan talabalar</option>
                            <option value="restoredStudent" <? if ($_SESSION["studentFilterType"] == "restoredStudent") echo "selected"; ?>>Qayta tiklangan talabalar</option>
                            <option value="defrostedStudent" <? if ($_SESSION["studentFilterType"] == "defrostedStudent") echo "selected"; ?>>Eritilgan talabalar</option>
                            <option value="paymentTypeStudent" <? if ($_SESSION["studentFilterType"] == "paymentTypeStudent") echo "selected"; ?>>To'lovlar turi</option>
                            <option value="extraFilter" <? if ($_SESSION["studentFilterType"] == "extraFilter") echo "selected"; ?>>Qo'shimcha filtr</option>
                        </select>
                    </div>
                    <div class="mb-3 <? if (!in_array($_SESSION["studentFilterType"], ["paymentTypeStudent"])) echo "d-none"; ?>" id="student-filter-payment-types">
                        <label>To'lov Usullari</label>
                        <select name="paymentTypes[]" class="select2 form-control" multiple>
                            <? foreach (PAYMENT_TYPES as $paymentKey => $paymentText) : ?>
                                <option value="<?= $paymentKey ?>" <? if (in_array($paymentKey, $_SESSION["studentFilterPaymentType"])) echo "selected"; ?>><?= $paymentText ?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                    <? $date_range_filters = ["activatedStudent", "freezedStudent", "paymentTypeStudent", "restoredStudent", "defrostedStudent"]; ?>
                    <div class="<? if (!in_array($_SESSION["studentFilterType"], $date_range_filters)) echo "d-none"; ?> mb-3" id="student-filter-date-range">
                        <label>Sana</label>
                        <input type="text" id="fp-range" name="filterDate" class="form-control flatpickr-range flatpickr-input" value="<?= $_SESSION["studentFilterDate"] ?>" placeholder="YYYY-MM-DD to YYYY-MM-DD" readonly="readonly" />
                    </div>
                    <div class="mb-2 extra-filter <?= $_SESSION["studentFilterType"] === "extraFilter" ? "" : "d-none"; ?>" id="student-filter-mentor">
                        <label>Menter</label>
                        <select name="filter_teacher[]" id="student-filter-mentor-select" class="select2 form-control" multiple>
                            <option value=""></option>
                            <? foreach (db::arr("SELECT `ID`,`NAME` FROM `teacher_list` WHERE `ACTIVE`='1' AND `ORG_ID`='$org_id'") as $teacher_option) : ?>
                                <option value="<?= $teacher_option["ID"] ?>" <?= in_array($teacher_option["ID"], $_SESSION["studentExtraFilter"]["teacher"]) ? "selected" : null; ?>><?= $teacher_option["NAME"] ?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-2 extra-filter <?= $_SESSION["studentFilterType"] === "extraFilter" ? "" : "d-none"; ?>" id="student-filter-groups">
                        <label>Guruhlar</label>
                        <select name="filter_groups[]" id="student-filter-groups-select" class="select2 form-control" multiple>
                            <option value=""></option>
                            <? foreach (db::arr("SELECT `ID`,`NAME`,`TEACHER_ID` FROM `group_list` WHERE `STATUS`='active' AND `ORG_ID`='$org_id'") as $group_option) : ?>
                                <option value="<?= $group_option["ID"] ?>" data-teacher="<?= $group_option["TEACHER_ID"] ?>" <?= in_array($group_option["ID"], $_SESSION["studentExtraFilter"]["groups"]) ? "selected" : null; ?>><?= $group_option["NAME"] ?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                    <? //echo '<pre>'; print_r($_POST['studentCreated']); echo '</pre>'; ?>
                    <div class="mb-2 extra-filter <?= $_SESSION["studentFilterType"] === "extraFilter" ? "" : "d-none"; ?>" id="student-filter-create-date">
                        <label>Talaba Qo'shilgan sana</label>
                        <input type="text" id="fp-range" name="studentCreated" class="form-control flatpickr-range flatpickr-input" placeholder="YYYY-MM-DD to YYYY-MM-DD" value="<?= !empty($_SESSION["studentExtraFilter"]["studentCreated"]) ? $_SESSION["studentExtraFilter"]["studentCreated"] : null ?>" readonly="readonly" />
                    </div>
                    <div id="student-filter-subscribe-type" class="mb-2 extra-filter <?= $_SESSION["studentFilterType"] === "extraFilter" ? "" : "d-none"; ?>">
                        <label>To'vol turi</label>
                        <select name="withdraw_type" id="student-filter-subscription-withdraw-select" class="form-control">
                            <option value="all" <?= $_SESSION["studentExtraFilter"]["withdraw_type"] === "all" ? "selected" : null; ?>>Barchasi</option>
                            <option value="simple" <?= $_SESSION["studentExtraFilter"]["withdraw_type"] === "simple" ? "selected" : null; ?>>Davomat bo'yicha</option>
                            <option value="monthly" <?= $_SESSION["studentExtraFilter"]["withdraw_type"] === "monthly" ? "selected" : null; ?>>Oylik</option>
                            <option value="free" <?= $_SESSION["studentExtraFilter"]["withdraw_type"] === "free" ? "selected" : null; ?>>Bepul</option>
                        </select>
                    </div>
                    <div class="row mb-2 extra-filter <?= $_SESSION["studentFilterType"] === "extraFilter" ? "" : "d-none"; ?>" id="student-filter-balance-status">
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <label>Balansi summadan boshlab</label>
                            <input type="text" name="balance[from]" class="form-control formated-number-input" value="<?= $_SESSION["studentExtraFilter"]["balance"]["from"]; ?>">
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <label>Balansi summagacha</label>
                            <input type="text" name="balance[to]" class="form-control formated-number-input" value="<?= $_SESSION["studentExtraFilter"]["balance"]["to"]; ?>">
                        </div>
                    </div>
                    <div class="row mb-2 extra-filter <?= $_SESSION["studentFilterType"] === "extraFilter" ? "" : "d-none"; ?>" id="student-filter-price">
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <label>Individual narxdan boshlab</label>
                            <input type="text" name="special_price[from]" class="form-control formated-number-input" value="<?= $_SESSION["studentExtraFilter"]["special_price"]["from"]; ?>">
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <label>Individual narxgacha</label>
                            <input type="text" name="special_price[to]" class="form-control formated-number-input" value="<?= $_SESSION["studentExtraFilter"]["special_price"]["to"]; ?>">
                        </div>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="stopFilter" id="stop_filter">
                        <label class="custom-control-label" for="stop_filter">Filter O'chirish</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                <button type="submit" form="filterForm" name="filterSubmit" class="btn btn-primary">Tasdiqlash</button>
            </div>
        </div>
    </div>
</div>
<!-- Single Student Payment Modal -->
<div class="modal fade text-left" id="singleStudentPaymentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel1">Talaba to'lovi</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="singleStudentPaymentBox">
                <form action="" method="post" id="singleStudentPaymentForm">
                    <input type="hidden" name="student_id" value="<?= $id_encrypte ?>">
                    <div class="row">
                        <div class="col-8">
                            <div class="card">
                                <div class="card-header">
                                    <div class="info-box">
                                        <span class="h4"> Talaba: </span><span id="studentName"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Guruh</label>
                                <select name="subscribe_id" class="form-control">
                                    <? foreach ($subscribe as $sub_value) : ?>
                                        <option value="<?= $sub_value["ID"] ?>"><?= $group_list[$sub_value['GROUP_ID']]["NAME"]; ?></option>
                                    <? endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Miqdor</label>
                                <input type="text" name="paymentAmount" class="form-control formated-number-input" required>
                            </div>
                            <div id="dollar_content" style="display:none;">
                                <div class="mb-3">
                                    <label>Miqdor $</label>
                                    <input type="text" name="paymentAmount_usd" class="form-control formated-number-input">
                                </div>
                                <div class="mb-3">
                                    <label>Qaytim</label>
                                    <input type="text" name="paymentAmount_qaytim" class="form-control formated-number-input">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Sana</label>
                                <input type="date" class="form-control" name="paymentDate" required>
                            </div>
                            <div class="mb-1">
                                <label>Izoh</label>
                                <textarea name="descriptionText" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card">
                                <div class="card-header">
                                    <div class="info-box">
                                        <span class="h4"> Balans: </span><span id="studentBallance"></span> UZS
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="row card-header">
                                    <div class="col-12">
                                        <label class="form-label">To'lov Usuli</label>
                                    </div>
                                    <div class="col-12 p-0">
                                        <? if ($_SESSION["USER"]["ROLE_ID"] == "1") : ?>
                                            <div class="custom-control custom-radio pl-1">
                                                <input type="radio" name="paymentType" id="systemType" value="system" checked>
                                                <label for="systemType">Tizim</label>
                                            </div>
                                        <? endif; ?>
                                        <div class="custom-control custom-radio pl-1">
                                            <input type="radio" name="paymentType" id="cash1" value="naqd" <? if ($_SESSION["USER"]["ROLE_ID"] !== "1") echo "checked"; ?>>
                                            <label for="cash1">Naqd Pul <b>UZS</b></label>
                                        </div>
                                        <div class="custom-control custom-radio pl-1">
                                            <input type="radio" name="paymentType" id="chb_dollar" value="dollar">
                                            <label for="chb_dollar">Naqd Pul <b>USD</b></label>
                                        </div>
                                        <div class="custom-control custom-radio pl-1">
                                            <input type="radio" name="paymentType" id="cash2" value="kassa">
                                            <label for="cash2">Naqd Pul <b>KASSA</b></label>
                                        </div>
                                        <div class="custom-control custom-radio pl-1">
                                            <input type="radio" name="paymentType" id="card" value="plastik">
                                            <label for="card">Plastik Karta</label>
                                        </div>
                                        <div class="custom-control custom-radio pl-1">
                                            <input type="radio" name="paymentType" id="bank" value="bank_hisob">
                                            <label for="bank">Bank Hisobi</label>
                                        </div>
                                        <div class="custom-control custom-radio pl-1">
                                            <input type="radio" name="paymentType" id="terminal" value="terminal">
                                            <label for="terminal">Terminal</label>
                                        </div>
                                        <div class="custom-control custom-radio pl-1">
                                            <input type="radio" name="paymentType" id="payme" value="payme">
                                            <label for="payme">Payme</label>
                                        </div>
                                        <div class="custom-control custom-radio pl-1">
                                            <input type="radio" name="paymentType" id="click" value="click">
                                            <label for="click">Click</label>
                                        </div>
                                        <div class="custom-control custom-radio pl-1">
                                            <input type="radio" name="paymentType" id="uzum" value="uzum">
                                            <label for="uzum">Uzum</label>
                                        </div>
                                        <div class="custom-control custom-radio pl-1">
                                            <input type="radio" name="paymentType" id="cloudPayments" value="cloudPayments">
                                            <label for="cloudPayments">Cloud Payments</label>
                                        </div>
                                        <div class="custom-control custom-radio pl-1">
                                            <input type="radio" name="paymentType" id="uzumNasiya" value="uzumNasiya">
                                            <label for="uzumNasiya">Uzum Nasiya</label>
                                        </div>
                                        <div class="custom-control custom-radio pl-1">
                                            <input type="radio" name="paymentType" id="itBilim" value="itBilim">
                                            <label for="itBilim">IT Bilim</label>
                                        </div>
                                        <div class="custom-control custom-radio pl-1">
                                            <input type="radio" name="paymentType" id="anorBank" value="anor_bank">
                                            <label for="anorBank">Anor Bank</label>
                                        </div>
                                        <div class="custom-control custom-radio pl-1">
                                            <input type="radio" name="paymentType" id="zoodPay" value="zoodpay">
                                            <label for="zoodPay">Zood Pay</label>
                                        </div>
                                        <div class="custom-control custom-radio pl-1">
                                            <input type="radio" name="paymentType" id="express" value="express">
                                            <label for="express">Express</label>
                                        </div>
                                        <div class="custom-control custom-radio pl-1">
                                            <input type="radio" name="paymentType" id="vaucher" value="vaucher">
                                            <label for="vaucher">Vaucher</label>
                                        </div>
                                        <div class="custom-control custom-radio pl-1">
                                            <input type="radio" name="paymentType" id="paynet" value="paynet">
                                            <label for="paynet">Paynet</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                <button type="submit" form="singleStudentPaymentForm" name="singleStudentPaymentSubmit" class="btn btn-primary">Qo'shish</button>
            </div>
        </div>
    </div>
</div>
<!-- Reminder Student -->
<div class="modal fade text-left" id="reminderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel1">Yangi eslatma qo'shing</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="reminderForm">
                    <div class="mb-3">
                        <label class="form-label">Izoh</label>
                        <textarea name="reminderText" rows="5" class="form-control" require></textarea>
                        <input type="hidden" name="student_id" value="<?= $id_encrypte ?>">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                <button type="submit" form="reminderForm" name="reminderSubmit" class="btn btn-primary">Tasdiqlash</button>
            </div>
        </div>
    </div>
</div>
<!-- Coin Modal -->
<div class="modal fade text-left" id="coinModal" tabindex="-1" data-role-id="<?= $_SESSION["USER"]["ROLE_ID"] ?>" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel1">Coins</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger mt-1 alert-validation-msg d-none" role="alert">
                    <div class="alert-body"></div>
                </div>
                <form action="" method="post" id="coinForm">
                    <label class="fs-6 mb-0">Haraqat turi</label>
                    <div class="demo-inline-spacing mb-1">
                        <div class="custom-control custom-radio">
                            <input type="radio" id="add" name="actionType" value="add" class="custom-control-input" />
                            <label class="custom-control-label" for="add">Qo'shish</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="remove" name="actionType" value="remove" class="custom-control-input" />
                            <label class="custom-control-label" for="remove">Ayirish</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="label-form">Coin Qiymati</label>
                        <input type="number" step="0.5" min="0" name="coinAmount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="label-form">Izoh</label>
                        <textarea name="coinComment" cols="30" rows="5" class="form-control" required></textarea>
                    </div>
                    <input type="hidden" name="student_id" value="<?= $id_encrypte ?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                <button type="submit" form=coinForm name="coinSubmit" class="btn btn-primary">Tasdiqlash</button>
            </div>
        </div>
    </div>
</div>

<!-- Remove Transaction -->
<div class="modal fade text-left" id="removeTranModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel1">Balansdan Ayirish</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="removeTranForm">
                    <input type="hidden" name="student_id" value="<?= $id_encrypte ?>">
                    <div class="mb-3">
                        <label class="label-form">Harakat turi</label>
                        <select name="balanceType" class="form-control" id="balance-withdraw-type">
                            <option value="withdrow">Balansdan Ayirish</option>
                            <option value="refund">Refund</option>
                        </select>
                    </div>
                    <div class="mb-3" style="display: none;" id="refund-payment-types">
                        <label>To'lov Turi</label>
                        <select name="refund_payment_type" class="form-control" disabled>
                            <? foreach (PAYMENT_TYPES as $paymentKey => $paymentTitle) : ?>
                                <? if ($paymentKey == "refund") continue; ?>
                                <option value="<?= $paymentKey ?>"><?= $paymentTitle ?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="label-form">Qiymat</label>
                        <input type="text" name="removeTranAmount" min="0" class="form-control formated-number-input" required>
                    </div>
                    <div class="mb-3">
                        <label class="label-form">Sana</label>
                        <input type="date" name="removeTranDate" class="form-control" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                <button type="submit" form="removeTranForm" name="removeTranSubmit" class="btn btn-primary">Tasdiqlash</button>
            </div>
        </div>
    </div>
</div>

<!-- SMS Modal -->
<div class="modal fade text-left" id="smsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel1">Guruhga SMS yuboring</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="smsForm">
                    <div class="mb-3">
                        <label class="form-label">Xabarni kiriting</label>
                        <textarea name="message_text" class="form-control message-textarea" rows="5"></textarea>
                    </div>
                    <input type="hidden" name="student_id" value="<?= $id_encrypte ?>">
                </form>
                <div class="row">
                    <div class="col-12 message-templates">
                        <? foreach ($formated_templates as $v) : ?>
                            <div class="card shadow">
                                <div class="card-body message">
                                    <?= $v["TEXT"] ?>
                                </div>
                            </div>
                        <? endforeach ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                <button type="submit" form="smsForm" name="smsSubmit" class="btn btn-primary">SMS Yuborish</button>
            </div>
        </div>
    </div>
</div>
<!-- Confirm Modal -->
<div class="modal fade text-left" id="conformModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel1"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="resendForm"><input type="hidden" name="actionId"><input type="hidden" name="student_id" value="<?= $id_encrypte ?>"></form>
                <h4 class="question-text"></h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Yo'q</button>
                <button type="submit" form="resendForm" class="btn btn-primary conform-action">Ha</button>
            </div>
        </div>
    </div>
</div>
<!-- Add Price Modal -->
<div class="modal fade text-left" id="addPriceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel1">Yangi Narxi</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="addPriceForm">
                    <input type="hidden" name="student_id" value="<?= $id_encrypte ?>">
                    <div class="mb-2">
                        <label class="form-label">Guruhni tanlang</label>
                        <select name="group_select" class="form-control group-list-select" required>
                            <option selected disabled></option>
                            <? $active_date = date("Y-m-d");
                            foreach ($student_cur_groups as $group) : ?>
                                <? if ($group["ACTIVE"] == 1) {
                                    $active_date = $group["START_DATE"];
                                    $last_active_date = $group["END_DATE"] === "0000-00-00" ? NULL : $group["END_DATE"];
                                    $day =  sprintf("%02d", $group["DAY"]);
                                } ?>
                                <option value="<?= $group["ID"] ?>" data-type="<?= $group["TYPE"]; ?>" data-price="<?= $group["SPECIAL_PRICE"] ?>" data-laptop="<?= $group["LAPTOP"] == "1" ? "true" : "false"; ?>"><?= $group["NAME"] ?></option>
                            <? endforeach ?>
                        </select>
                    </div>
                    <? if ($student_cur_groups !== "empty") : ?>
                        <div class="mb-3 d-none" id="for-simple-subscription">
                            <label>Boshlanish Sanasi</label>
                            <? if ($_SESSION["user"]["role_id"] == "1" || $active_date > date("Y-m-d", strtotime("-15 days"))) : ?>
                                <input type="date" name="simple_start_date" class="form-control" min="<?= $active_date; ?>">
                            <? else : ?>
                                <input type="date" name="simple_start_date" class="form-control" min="<?= date("Y-m-d", strtotime("-15 days")); ?>">
                            <? endif; ?>
                        </div>
                        <div class="mb-3 d-none" id="for-monthly-subscription">
                            <label>Boshlanish Sanasi</label>
                            <? if ($_SESSION["user"]["role_id"] == "1") : ?>
                                <input type="date" name="monthly_start_date" class="form-control" min="<?= $active_date; ?>">
                            <? else : ?>
                                <select name="monthly_start_date" class="form-control">
                                    <option value=""></option>
                                    <?
                                    $two_months_before = date("Y-m-$day", strtotime("-2 month"));
                                    $two_months_after = date("Y-m-$day", strtotime("+3 month"));
                                    $start = $active_date < $two_months_before
                                        ? new DateTime($two_months_before)
                                        : new DateTime($active_date);
                                    $end = $two_months_after > $last_active_date && !is_null($last_active_date)
                                        ? new DateTime($last_active_date)
                                        : new DateTime($two_months_after);
                                    $end->modify("Next day");
                                    $interval = new DateInterval('P1M');
                                    $period   = new DatePeriod($start, $interval, $end);
                                    ?>
                                    <? foreach ($period as $time) : ?>
                                        <option value="<?= $time->format("Y-m-d") ?>"><?= $time->format("d.m.Y") ?></option>
                                    <? endforeach; ?>
                                </select>
                            <? endif; ?>
                        </div>
                    <? endif; ?>
                    <div id="discount-options" class="mb-2 d-flex align-items-center justify-content-between">
                        <div class="custom-control custom-radio">
                            <input type="radio" id="package-price" name="discount_option" class="custom-control-input" value="package-price" checked />
                            <label class="custom-control-label" for="package-price">Paketli narx</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="discount-price" name="discount_option" class="custom-control-input" value="discount-price" />
                            <label class="custom-control-label" for="discount-price">Chegirma</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="another-price" name="discount_option" class="custom-control-input" value="another-price" />
                            <label class="custom-control-label" for="another-price">Boshqa</label>
                        </div>
                    </div>
                    <div class="mb-2" id="package-price-option">
                        <label>Paket turi</label>
                        <select name="package_price" class="form-control" id="package-price-field" required>
                            <option value=""></option>
                            <? foreach ($packagesList as $packageOption): ?>
                                <option value="<?= $packageOption['ID'] ?>"><?= $packageOption['NAME'] ?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-2 d-none" id="discount-price-option">
                        <label>Chegirma turi</label>
                        <select name="discount_price" class="form-control" id="discount-price-field">
                            <option value=""></option>
                            <? foreach ($discountsList as $discountList): ?>
                                <option value="<?= $discountList['ID'] ?>"><?= $discountList['NAME'] ?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-2 d-none" id="another-price-option">
                        <label>Narxi</label>
                        <input type="number" name="special_price" min="0" class="form-control special-price-input" id="another-price-field" readonly>
                        <div class="d-flex justify-content-between mt-1">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="give-laptop" name="give_laptop" value="true" class="custom-control-input">
                                <label class="custom-control-label" for="give-laptop">Talabaga noutbuk berildi</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="not-give-laptop" name="give_laptop" value="false" class="custom-control-input">
                                <label class="custom-control-label" for="not-give-laptop">Talabaga noutbuk berilmadi</label>
                            </div>
                        </div>
                    </div>
                    <? if ($_SESSION["USER"]["ID"] == '1') : ?>
                        <div class="mb-1">
                            <label for="discount-reason">Chegirma sababi</label>
                            <select class="form-control" name="discount_reason" id="discount-reason" required>
                                <option value=""></option>
                                <? foreach (DISCOUNT_REASONS as $reasonKey => $reasonValue) : ?>
                                    <option value="<?= $reasonKey ?>"><?= $reasonValue ?></option>
                                <? endforeach; ?>
                                <option value="other">Boshqa</option>
                            </select>
                        </div>
                    <? endif; ?>
                    <div class="mb-3">
                        <label>Izoh</label>
                        <textarea name="comment" rows="5" class="form-control" id="add-price-comment" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                <button type="submit" form="addPriceForm" name="addPriceSubmit" class="btn btn-primary">Tasdiqlash</button>
            </div>
        </div>
    </div>
</div>
<!-- Print Payment Check Modal -->
<div class="modal fade text-left" id="checkPrint" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel1"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-body w-75 mx-auto">
                        <div class="profile-image-wrapper">
                            <div class="profile-image d-flex justify-content-center">
                                <img src="/uploads/logo.jpg" alt="Profile Picture" class="w-25 round mb-1">
                            </div>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item pl-0">
                                    <b>Tekshirish raqami:</b> <span id="transaction-id"></span>
                                </li>
                                <li class="list-group-item pl-0">
                                    <b>Talaba:</b> <span id="student-name"></span>
                                </li>
                                <li class="list-group-item pl-0">
                                    <b>Telefon:</b> <span id="phone"></span>
                                </li>
                                <li class="list-group-item pl-0">
                                    <b>Guruh:</b> <span id="group"></span>
                                </li>
                                <li class="list-group-item pl-0">
                                    <b>Kurs narxi:</b> <span id="price"></span>
                                </li>
                                <li class="list-group-item pl-0">
                                    <b>Filial:</b> <span id="filial"></span>
                                </li>
                                <li class="list-group-item pl-0">
                                    <b>O'qituvchi:</b> <span id="teacher-name"></span>
                                </li>
                                <li class="list-group-item pl-0">
                                    <b>To'lov miqdori:</b> <span id="payment-amount"></span>
                                </li>
                            </ul>
                            <form action="" method="post" id="checkPrintForm">
                                <input type="hidden" name="transaction_id">
                                <input type="hidden" name="student_name">
                                <input type="hidden" name="phone">
                                <input type="hidden" name="group">
                                <input type="hidden" name="price">
                                <input type="hidden" name="filial">
                                <input type="hidden" name="teacher_name">
                                <input type="hidden" name="payment_amount">
                                <input type="hidden" name="by_user">
                                <input type="hidden" name="date">
                                <input type="hidden" name="student_id" value="<?= $_GET['item_id'] ?>">
                                <input type="text" class="form-control" autocomplete="off" name="comment" placeholder="Izoh..." id="checkComment">
                            </form>
                            <small class="text-secondary mb-1">
                                <b>Xodim:</b> <span id="by-user"></span>
                            </small> <br>
                            <small class="text-secondary mb-1">
                                <b>Vaqt:</b> <span id="date"></span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" form="checkPrintForm" name="checkPrintSubmit" class="btn btn-primary">Chop etish</button>
            </div>
        </div>
    </div>
</div>
<!-- Edit Subscription Modal -->
<div class="modal fade text-left" id="editSubscriptionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel1">Tahrirlash</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="editSubscriptionForm">
                    <input type="hidden" name="edit_subscription_id">
                    <div class="mb-3">
                        <label>To'lov Turi</label>
                        <select name="subscription_type" class="form-control">
                            <option value="simple">Davomat Bo'yicha</option>
                            <option value="monthly">Oylik</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Boshlanish sana</label>
                        <input type="date" name="edit_start_date" class="form-control">
                    </div>
                    <? if ($_SESSION["USER"]["ROLE_ID"] === "1") : ?>
                        <div class="mb-3">
                            <label>To'lov yechilish sana</label>
                            <input type="number" name="edit_day" min="1" max="28" class="form-control">
                        </div>
                    <? endif; ?>
                    <div class="d-flex justify-content-between mb-1">
                        <div class="custom-control custom-radio">
                            <input type="radio" id="given-laptop" name="given_laptop" value="true" class="custom-control-input">
                            <label class="custom-control-label" for="given-laptop">Talabaga noutbuk berildi</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="not-given-laptop" name="given_laptop" value="false" class="custom-control-input">
                            <label class="custom-control-label" for="not-given-laptop">Talabaga noutbuk berilmadi</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                <button type="submit" class="btn btn-primary" form="editSubscriptionForm" name="editSubscriptionSubmit">Saqlash</button>
            </div>
        </div>
    </div>
</div>
<!-- Upload student file Modal -->
<div class="modal fade text-left" id="studentFileModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel1">Fayl yuklash</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" id="studentFileForm" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="student_id">
                    <div class="form-group">
                        <label for="studentFileUpload">Fayl yuklash</label>
                        <div class="custom-file">
                            <input type="file" name="student_file" class="custom-file-input" id="studentFileUpload" required />
                            <label class="custom-file-label" for="studentFileUpload">Faylni tanlang</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Izoh</label>
                        <textarea name="uploadComment" rows="3" class="form-control"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                <button type="submit" class="btn btn-primary" form="studentFileForm" name="studentFileSubmit">Saqlash</button>
            </div>
        </div>
    </div>
</div>

<?
$now = date("Y-m-d H:i:s");
$user_id = $_SESSION["user"]["id"];
$cur_org_id = intval($_SESSION["USER"]["ORG_ID"]);
if (isset($_POST['onlineSubmit'])) {

$id = $student['ID'];
$username = filter_input(INPUT_POST, "student_username", FILTER_SANITIZE_ADD_SLASHES);
$student_password = $_POST['student_password'];

$select_id = db::arr_s("SELECT * FROM student_students WHERE user_id = '$id'");

$cs = db::query("DELETE FROM `student_course_access` WHERE `user_id` = '$select_id[id]'");
$ss = db::query("DELETE FROM `student_module_access` WHERE `user_id` = '$select_id[id]'");
$md = db::query("DELETE FROM `student_lesson_access` WHERE `user_id` = '$select_id[id]'");
$st = db::query("DELETE FROM `student_students` WHERE `id` = '$select_id[id]'");

$insert = db::query("INSERT INTO `student_students` 
(`user_id`,
`username`,  
`password`) 
VALUES
('$id',
'$username',
'$student_password')");
    
$courses = $_POST['online_courses'];

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

header("Location: $student_id");
exit;
}

if (isset($_POST['taskSubmit'])) {
    $task_text  = filter_input(INPUT_POST, "taskText", FILTER_SANITIZE_ADD_SLASHES);
    $due = $_POST['due_date'];
    $arr = $_POST['responsible'];
    $members_id = json_encode($arr);
    $insert = db::query("INSERT INTO `list_tasks` (
    `ORG_ID`, 
    `CREATE_DATE`, 
    `CREATED_BY`, 
    `TYPE`,
    `TARGET_ID`,
    `TASK`,
    `DUE_DATE`,
    `ASSIGNED_MEMBERS`,
    `STATUS`
    ) VALUES (
    '$org_id',
    '$now',
    '$user_id',
    'student',
    '$student_id',
    '$task_text',
    '$due',
    '$members_id',
    'open'
    )");
    $insert_log = db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`) VALUES ('$user_id', '$now', 'list_tasks','open_student_task','$insert[ID]','1')");
    header("Location: $student_id");
    exit;
}

// Add Modal
if (isset($_POST["addSubmit"])) {
    $name  = filter_input(INPUT_POST, "name", FILTER_SANITIZE_ADD_SLASHES);
    $phone = filter_input(INPUT_POST, "phone", FILTER_SANITIZE_NUMBER_INT);
    $lang = filter_input(INPUT_POST, "language", FILTER_SANITIZE_SPECIAL_CHARS);
    $parent_name  = filter_input(INPUT_POST, "parent_name", FILTER_SANITIZE_ADD_SLASHES);
    $parent_phone = filter_input(INPUT_POST, "parent_phone", FILTER_SANITIZE_NUMBER_INT);
    $tg_username = filter_input(INPUT_POST, "tg_username", FILTER_SANITIZE_ADD_SLASHES);
    $address = filter_input(INPUT_POST, "address", FILTER_SANITIZE_SPECIAL_CHARS);
    $birthdate = date("Y-m-d", strtotime($_POST["birth_date"]));
    $test_resulte = filter_input(INPUT_POST, "test_result", FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $match =  '/^\+?(\d{3})?(\d{2})(\d{3})(\d{2})(\d{2})$/';
    if (preg_match($match,  $phone,  $matches)) {
        $phone = $matches[2] . ' ' . $matches[3] . ' ' . $matches[4] . " " . $matches[5];
    }
    if (preg_match($match, $parent_phone,  $parent_matches)) {
        $parent_phone = $parent_matches[2] . ' ' . $parent_matches[3] . ' ' . $parent_matches[4] . " " . $parent_matches[5];
    }
    $eshitgan_joyi = json_encode($_POST["came_from"]);
    $soho_id = isset($_POST["soho_id"]) && !empty($_POST["soho_id"]) ? "'{$_POST["soho_id"]}'" : "'NULL'";
    $validate_phone = db::arr_s("SELECT `ID` FROM `gl_sys_users` WHERE `PHONE`='$phone'");
    if ($validate_phone === "empty") {
        $insert = db::query("INSERT INTO `student_list` 
        (`ORG_ID`,`NAME`,`PHONE`,`TG_USERNAME`,`PARENT_PHONE`,`PARENT_NAME`,`CREATED_DATE`,`CREATED_BY`,`ACTIVE`,`ADDRESS`,`BIRTH_DATE`,`TEST_SCORE`,`ESHITGAN_JOYI`,`EMAIL`,`LANG`,`SOHO_ID`) VALUES 
        ('$org_id','$name','$phone','$tg_username','$parent_phone','$parent_name','$now','$user_id',1,'$address','$birthdate','$test_resulte','$eshitgan_joyi','$email','$lang', $soho_id)");
        $insert_log = db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`) VALUES ('$user_id', '$now', 'student_list','add_student','$insert[ID]','1')");

        $login = str_replace(" ", "", $phone);
        $login = str_replace("-", "", $login);
        $final_login = $login;
        $name_gl = str_replace("'", "`", $name);

        $insert_gl = db::query("INSERT INTO gl_sys_users (NAME, SURNAME, PHOTO_URL, LOGIN, PHONE, PASSWORD, ORG_ID, ROLE_ID, PODRAZD_ID, TEAM_ID, STATUS) VALUES 
        ('$name_gl', '', '/uploads/account.png', '$final_login', '$phone', '1234', '$org_id', '6', '0', '0', '1')");
    }
    if ($insert["stat"] == "success") {
        header("Location: /account/student_list/detail/$insert[ID]");
        exit;
    } else {
        $_SESSION["student_error"] = "Xato. Bu telefon raqami allaqachon maʼlumotlar bazasida.";
        LocalRedirect("index.php");
    }
    // echo '<script>alert("Error. This is phone number already in database.")</script>';
}

// Edit Modal
if (isset($_POST["editSubmit"])) {
    $name  = filter_input(INPUT_POST, "edit_name", FILTER_SANITIZE_ADD_SLASHES);
    $phone = filter_input(INPUT_POST, "edit_phone", FILTER_SANITIZE_NUMBER_INT);
    $lang = filter_input(INPUT_POST, "edit_language", FILTER_SANITIZE_ADD_SLASHES);
    $parent_name  = filter_input(INPUT_POST, "edit_parent_name", FILTER_SANITIZE_ADD_SLASHES);
    $parent_phone = filter_input(INPUT_POST, "edit_parent_phone", FILTER_SANITIZE_NUMBER_INT);
    $tg_username = filter_input(INPUT_POST, "edit_tg_username", FILTER_SANITIZE_ADD_SLASHES);

    $address = filter_input(INPUT_POST, "edit_address", FILTER_SANITIZE_ADD_SLASHES);
    $birthdate = date("Y-m-d", strtotime($_POST["edit_birth_date"]));
    $test_resulte = filter_input(INPUT_POST, "edit_test_result", FILTER_SANITIZE_ADD_SLASHES);
    $email = filter_input(INPUT_POST, "edit_email", FILTER_SANITIZE_EMAIL);
    $match =  '/^\+?(\d{3})?(\d{2})(\d{3})(\d{2})(\d{2})$/';
    $eshitgan_joyi = json_encode($_POST["edit_came_from"]);
    if (preg_match($match,  $phone,  $matches)) {
        $phone = $matches[2] . ' ' . $matches[3] . ' ' . $matches[4] . " " . $matches[5];
    }
    if (preg_match($match, $parent_phone,  $parent_matches)) {
        $parent_phone = $parent_matches[2] . ' ' . $parent_matches[3] . ' ' . $parent_matches[4] . " " . $parent_matches[5];
    }
    $soho_id = isset($_POST["edit_soho_id"]) ? "`SOHO_ID`='{$_POST["edit_soho_id"]}'," : "";
    $login = str_replace(" ", "", $phone);
    $login = str_replace("-", "", $login);
    $final_login = $login;
    $name_gl = str_replace("'", "`", $name);
    $checkPhone = db::arr_s("SELECT * FROM student_list WHERE `ID`='$_POST[editId]'");
    $update_gl = db::query("UPDATE gl_sys_users SET 
    `NAME`='$name_gl', `SURNAME`='',
    `PHOTO_URL`='/uploads/account.png', `LOGIN`='$final_login',
    `PHONE`='$phone' WHERE `PHONE`='$checkPhone[PHONE]'");

    $update = db::query("UPDATE `student_list` SET $soho_id
    `NAME`='$name', `PHONE`='$phone', `TG_USERNAME`='$tg_username',
    `PARENT_PHONE`='$parent_phone', `PARENT_NAME`='$parent_name',
    `ADDRESS`='$address', `BIRTH_DATE`='$birthdate',
    `TEST_SCORE`='$test_resulte', `ESHITGAN_JOYI`='$eshitgan_joyi',
    `EMAIL`='$email', `LANG`='$lang' WHERE ID='$_POST[editId]'");
    $insert_log = db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`) VALUES ('$user_id', '$now', 'student_list','edit_student','$_POST[editId]','1')");


    if (isset($_GET["item_id"])) {
        header("Location: $student_id");
        exit;
    } else {
        LocalRedirect("index.php");
    }
}

// Delete Modal
if (isset($_POST["deleteSubmit"])) {
    $student_id = filter_input(INPUT_POST, "deleteId", FILTER_SANITIZE_NUMBER_INT);
    $reason = filter_input(INPUT_POST, "studentDeleteReason", FILTER_SANITIZE_ADD_SLASHES);
    $comment = filter_input(INPUT_POST, "studentDeleteText", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $selected = date("Y-m-d", strtotime($_POST["deletedDate"]));
    $student_data = db::arr_s("SELECT `ID`, `CURRENT_BALANCE` FROM `student_list` WHERE `ID`='$student_id'");
    $monthly_sub = db::arr_s("SELECT * FROM `subscribe_list` WHERE `STUDENT_ID`='$student_id' AND `ACTIVE`='1' AND `TYPE`='monthly'");
    $simple_sub = db::arr_s("SELECT * FROM `subscribe_list` WHERE `STUDENT_ID`='$student_id' AND `ACTIVE`='1' AND `TYPE`='simple'");
    $attendance_data = db::arr("SELECT `ID`,`STUDENT_JSON` FROM `attendance_list` WHERE `ACTIVE`='1' AND `LESSON_DATE`>='$selected' AND (`STUDENT_JSON` LIKE '%\"$student_id\":1%' OR `STUDENT_JSON` LIKE '%\"$student_id\":0%')");
    foreach ($attendance_data as $attendance) {
        $attendance_arr = json_decode($attendance["STUDENT_JSON"], true);
        $attendance_arr[$student_id] = null;
        $attendance_json = json_encode($attendance_arr);
        db::query("UPDATE `attendance_list` SET `STUDENT_JSON`='$attendance_json' WHERE `ID`='$attendance[ID]'");
    }

    if ($simple_sub != "empty" and $attendance_data != "empty") {
        $get_transaction = db::arr_s("SELECT SUM(AMOUNT) `amount` FROM `transaction_list` WHERE `STUDENT_ID`='$student_id' AND `TRANSACTION_DATE` >= '$selected' AND `ACTION_TYPE`='taken'");
        $cur_balance = intval($student_data["CURRENT_BALANCE"]) + intval($get_transaction["amount"]);
        db::query("INSERT INTO `transaction_list` (`STUDENT_ID`,`SUBSCRIBE_ID`,`ORG_ID`,`CREATED_DATE`,`CREATED_BY`,`CHANGED_DATE`,`TRANSACTION_DATE`,`CHANGED_BY`,`ACTION_TYPE`,`TYPE`,`AMOUNT`,`DESCRIPTION`) VALUES 
        ('$student_data[ID]','$simple_sub[ID]','$cur_org_id',now(),'$user_id',now(),now(),'$user_id', 'retake','system','$get_transaction[amount]','Talaba o\'chirilganiga qayta hisoblash')");
    }
    if ($monthly_sub != "empty") {
        $sub_day = sprintf('%02d', $monthly_sub["DAY"]);
        $last_pay_date = date("d") >= $sub_day ? date("Y-m-" . $sub_day) : date("Y-m-" . $sub_day, strtotime("-1 month"));
        $next_pay_date = $sub_day > date("d") ? date("Y-m-" . $sub_day) : date("Y-m-" . $sub_day, strtotime("+1 month"));
        $pre_last_pay_date = date("Y-m-d", strtotime("-1 month " . $last_pay_date));


        $last = new DateTime($last_pay_date);
        $next = new DateTime($next_pay_date);
        $pre_last = new DateTime($pre_last_pay_date);
        $selected = new DateTime($_POST["deletedDate"]);

        $last_transaction = db::arr_s("SELECT * FROM `transaction_list` WHERE `STUDENT_ID`='$student_id' AND `ACTION_TYPE`='taken' AND `TYPE`='system' AND `CREATED_BY`= '0' AND `CREATED_DATE` BETWEEN '$last_pay_date' AND '$now' LIMIT 1");

        $days_range = $last->diff($selected)->format("%a");
        if ($selected->format("Y-m-d") < $last_pay_date) {

            $pre_last_transaction = db::arr_s("SELECT * FROM `transaction_list` 
            WHERE `STUDENT_ID`='$student_id' AND `ACTION_TYPE`='taken' AND `TYPE`='system' 
            AND `CREATED_DATE` BETWEEN '$pre_last_pay_date' AND '$last_pay_date' LIMIT 1");

            $total = $pre_last->diff($last)->format("%a");
            $day_cost = round(intval($pre_last_transaction["AMOUNT"]) / $total);
            $to_add = ($day_cost * $days_range) + intval($last_transaction["AMOUNT"]);
            $cur_balance = intval($student_data["CURRENT_BALANCE"]) + $to_add;
        } else {
            $total = $last->diff($next)->format("%a");
            $day_cost = round(intval($last_transaction["AMOUNT"]) / $total);
            $studied = $day_cost * $days_range;
            $to_add = intval($last_transaction["AMOUNT"]) - $studied;
            $cur_balance = intval($student_data["CURRENT_BALANCE"]) + $to_add;
        }

        db::query("INSERT INTO `transaction_list` (`STUDENT_ID`,`SUBSCRIBE_ID`,`ORG_ID`,`CREATED_DATE`,`CREATED_BY`,`CHANGED_DATE`,`TRANSACTION_DATE`,`CHANGED_BY`,`ACTION_TYPE`,`TYPE`,`AMOUNT`,`DESCRIPTION`) VALUES 
        ('$student_data[ID]','$monthly_sub[ID]','$cur_org_id',now(),'$user_id',now(),now(),'$user_id', 'retake','system','$to_add','Talaba o\'chirilganiga qayta hisoblash')");
    }

    $comment_json = json_encode(["classType" => htmlspecialchars($_POST["classType"]), "reason" => $reason, "comment" => trim($comment)]);
    $deactivate = db::query("UPDATE `student_list` SET ACTIVE='0', `CURRENT_BALANCE`='$cur_balance' WHERE ID='$student_id'");
    if ($deactivate['stat'] == 'success') {
        $checkForLead = db::arr_s("SELECT `ID`,`LEAD_ID`,`STUDENT_ID`, (SELECT `STATUS` FROM `lead_student_list` WHERE ID=LEAD_ID) AS `STATUS` FROM `leads_students_linker` WHERE `STUDENT_ID`='$student_id'");
        if ($checkForLead != 'empty' && $checkForLead['LEAD_ID'] != null) {
            db::query("UPDATE `lead_student_list` SET `ACTIVE`=0 WHERE `ID`='$checkForLead[LEAD_ID]'");
            $comment_log = json_encode([
                "lead_status" => $checkForLead["STATUS"],
                "reason" => $reason,
                "comment" => htmlspecialchars($comment),
                "link_id" => $checkForLead == "empty" ? null : $checkForLead["ID"],
                "student_id" => $checkForLead == "empty" ? null : $checkForLead["STUDENT_ID"]
            ]);
            $comment_log = str_replace("\\\'", "\'", $comment_log);
            db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`,`COMMENT`) VALUES ('$user_id', NOW() ,'lead_student_list','delete_lead','$checkForLead[LEAD_ID]', '1', '$comment_log')");
        }
    }
    $log_action = db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`,`COMMENT`) VALUES 
    ('$user_id', '$now', 'student_list', 'delete_student', '$student_id', '1','$comment_json')");
    $deactivate_subscriptions = db::query("UPDATE `subscribe_list` SET `ACTIVE`='0', `STATUS`='archive' WHERE STUDENT_ID='$student_id'");

    header("Location: /account/student_list/list");
    exit;
}

// Filter Modal
if (isset($_POST["filterSubmit"])) {
    if ($_POST["stopFilter"] == "on") {
        unset($_SESSION["studentFilterType"]);
        unset($_SESSION["studentFilterDate"]);
        unset($_SESSION["studentFilterPaymentType"]);
        unset($_SESSION["studentExtraFilter"]);
    } else {
        $_SESSION["studentFilterType"] = $_POST["filterType"];
        $_SESSION["studentFilterDate"] = $_POST["filterDate"];
        $_SESSION["studentFilterPaymentType"] = $_POST["paymentTypes"];
    }
    if ($_POST["filterType"] != "extraFilter" or $_POST["stopFilter"] == "on") {
        unset($_SESSION["studentExtraFilter"]);
    } else if ($_POST["filterType"] == "extraFilter") {
        $_SESSION["studentExtraFilter"]["groups"] = $_POST["filter_groups"];
        $_SESSION["studentExtraFilter"]["teacher"] = $_POST["filter_teacher"];
        $_SESSION["studentExtraFilter"]["studentCreated"] = $_POST["studentCreated"];
        $_SESSION["studentExtraFilter"]["withdraw_type"] = $_POST["withdraw_type"];
        $_SESSION["studentExtraFilter"]["balance"] = str_replace(" ", "", $_POST["balance"]);
        $_SESSION["studentExtraFilter"]["special_price"] = str_replace(" ", "", $_POST["special_price"]);
    }
    LocalRedirect("index.php");
}

// Reminder Modal
if (isset($_POST["reminderSubmit"])) {
    $student_id = openssl_decrypt($_POST["student_id"], 'AES-256-CBC', "StudentSecret", 0, substr(md5("StudentSecret"), 0, 16));
    $comment = filter_input(INPUT_POST, "reminderText", FILTER_SANITIZE_SPECIAL_CHARS);
    $update = db::query("INSERT INTO `note_list` (`CREATED_DATE`,`CREATED_BY`,`STUDENT_ID`,`TEXT`) VALUES ('$now','$user_id','$student_id','$comment')");
    $insert = db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`,`COMMENT`) VALUES ('$user_id', '$now', 'student_list', 'reminder_student','$student_id',1,'$comment')");
    header("Location: $student_id");
    exit;
}

if (isset($_POST["delete_note"])) {
    $id = filter_input(INPUT_POST, "delete_note", FILTER_SANITIZE_NUMBER_INT);
    $deactivate = db::query("UPDATE `note_list` SET `ACTIVE`='0' WHERE `ID`='$id'");
    $insert = db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`) VALUES ('$user_id', '$now', 'note_list', 'delete_reminder','$id',1)");
    header("Location: $student_id");
    exit;
}
// Payment Model
if (isset($_POST["singleStudentPaymentSubmit"])) {
    $student_id = openssl_decrypt($_POST["student_id"], 'AES-256-CBC', "StudentSecret", 0, substr(md5("StudentSecret"), 0, 16));
    $format_price = filter_input(INPUT_POST, "paymentAmount", FILTER_SANITIZE_NUMBER_INT);
    $usd_amount = filter_input(INPUT_POST, "paymentAmount_usd", FILTER_SANITIZE_NUMBER_INT);
    $change_amount = filter_input(INPUT_POST, "paymentAmount_qaytim", FILTER_SANITIZE_NUMBER_INT);
    $student = db::arr_s("SELECT `CURRENT_BALANCE` FROM `student_list` WHERE ID='$student_id'");
    $total_amount = $student["CURRENT_BALANCE"] + intval($format_price);
    $update_student_balance = db::query("UPDATE `student_list` SET `CURRENT_BALANCE`='$total_amount', `ACTIVE`='1' WHERE ID='$student_id'");
    $comment = filter_input(INPUT_POST, "descriptionText", FILTER_SANITIZE_SPECIAL_CHARS);

    $insert = db::query("INSERT INTO `transaction_list` (
    `STUDENT_ID`, 
    `SUBSCRIBE_ID`,
    `ORG_ID`,
    `TRANSACTION_DATE`,
    `CREATED_DATE`, 
    `CREATED_BY`, 
    `CHANGED_DATE`, 
    `CHANGED_BY`, 
    `ACTION_TYPE`,
    `TYPE`, 
    `AMOUNT`, 
    `DESCRIPTION`)
    VALUES (
    '$student_id', 
    '$_POST[subscribe_id]',
    '$cur_org_id',
    '$_POST[paymentDate]',
    '$now', 
    '$user_id', 
    '$now', 
    '$user_id',
    'add',  
    '$_POST[paymentType]', 
    '$format_price', 
    '$comment')
    ");

    $student_list = db::arr_s("SELECT * FROM `student_list` WHERE ID='$student_id'");
    // if dollar $insert['ID'] olib convertation_list ga qo`yamiz ;
    if ($_POST['paymentType'] == 'dollar') {
        $studentName = addslashes($student_list['NAME']);

        $ins_dollar = db::query("INSERT INTO `transaction_list` (
	`STUDENT_ID`, 
	`SUBSCRIBE_ID`, 
    `ORG_ID`,
	`CREATED_DATE`, 
	`CREATED_BY`, 
	`CHANGED_DATE`, 
	`TRANSACTION_DATE`, 
	`CHANGED_BY`, 
	`ACTION_TYPE`, 
	`TYPE`, 
	`AMOUNT`, 
	`DESCRIPTION`)
	VALUES (
	'0', 
	'0', 
    '$cur_org_id',
	now(), 
	'$user_id', 
	now(),
	'$_POST[paymentDate]', 
	'$user_id', 
	'add_balance', 
	'dollar', 
	'$usd_amount', 
	'dollar: #$student_list[ID] $studentName')");

        $ins_qaytim = db::query("INSERT INTO `transaction_list` (
	`STUDENT_ID`, 
	`SUBSCRIBE_ID`, 
    `ORG_ID`,
	`CREATED_DATE`, 
	`CREATED_BY`, 
	`CHANGED_DATE`, 
	`TRANSACTION_DATE`, 
	`CHANGED_BY`, 
	`ACTION_TYPE`, 
	`TYPE`, 
	`AMOUNT`, 
	`DESCRIPTION`)
	VALUES (
	'0', 
	'0',
    '$cur_org_id',
	now(), 
	'$user_id', 
	now(), 
	'$_POST[paymentDate]', 
	'$user_id', 
	'expense', 
	'naqd', 
	'$change_amount', 
	'qaytim: #$student_list[ID] $studentName');");
        if ($ins_qaytim["stat"] == "success") {
            $add_expense = db::query("INSERT INTO `expense_list` (`TRANSACTION_ID`,`CATEGORY`) VALUES ('$ins_qaytim[ID]','qaytim')");
        }
        $kurs = round(($change_amount + $_POST['paymentAmount']) / $usd_amount);

        $konvertatsiya_text =  "#" . $student_list['ID'] . ' ' . $studentName . ": $usd_amount $ berdi, $change_amount so`m qaytim oldi, $_POST[paymentAmount] balansiga qo`shildi ;  1 $ - $kurs";

        $ins_konvertatsiya = db::query("INSERT INTO `convertation_list` (
	`CREATED_DATE`, 
	`TR_ID`, 
	`TR_ID_USD`, 
	`TR_ID_QAYTIM`, 
	`DESC`, 
	`ACTIVE`)
    VALUES (
	now(), 
	'$insert[ID]', 
	'$ins_dollar[ID]', 
	'$ins_qaytim[ID]', 
	'$konvertatsiya_text',
	'1');");
    }

    $comment_log = json_encode(["student_payment" => $format_price, "transaction_id" => $insert["ID"]]);
    $insert_log = db::query("INSERT INTO `table_log` 
    (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`,`COMMENT`) VALUES 
    ('$user_id', '$now', 'student_list', 'add_payment','$student_id',1,'$comment_log')");

    if (empty($_POST["subscribe_id"])) {
        $search_unactive = db::arr_s("SELECT * FROM `subscribe_list` WHERE `STUDENT_ID`='$student_id' AND `STATUS`='demo' AND `ACTIVE`='1' AND `GROUP_ID` IN (SELECT ID FROM `group_list` WHERE `STATUS`='active')");
        $sub_data = db::arr_s("SELECT * FROM `subscribe_list` WHERE `STUDENT_ID`='$student_id' AND `ACTIVE`='1' AND `GROUP_ID` IN (SELECT ID FROM `group_list` WHERE `STATUS`='active')");
    } else {
        $subscribe_id = intval($_POST["subscribe_id"]);
        $search_unactive = db::arr_s("SELECT * FROM `subscribe_list` WHERE `ID`='{$subscribe_id}' AND `STATUS`='demo' AND `ACTIVE`='1'");
        $sub_data = db::arr_s("SELECT * FROM `subscribe_list` WHERE `ID`='{$subscribe_id}' AND `ACTIVE`='1'");
    }
    $lead_linker = db::arr_s("SELECT LEAD_ID, (SELECT `STATUS` FROM `lead_student_list` WHERE ID=LEAD_ID) AS STATUS  FROM `leads_students_linker` WHERE `STUDENT_ID`='$student_id'");

    $sum_transactions = db::arr_s("SELECT SUM(AMOUNT) AS `amount` FROM `transaction_list` WHERE `STUDENT_ID`='$student_id' AND `ACTION_TYPE`='add'");
    if ($search_unactive != "empty" and $sum_transactions["amount"] >= $search_unactive["SPECIAL_PRICE"]) {
        $update_scribe_status = db::query("UPDATE `subscribe_list` SET `STATUS`='active' WHERE `ID`='$search_unactive[ID]'");
        $log_activation = db::query("INSERT INTO `table_log` 
        (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`) VALUES 
        ('$user_id', '$now', 'subscribe_list', 'activate_student','$search_unactive[ID]',1)");
        if ($lead_linker["LEAD_ID"] != null && $lead_linker['STATUS'] != 'active') {
            db::query("UPDATE `lead_student_list` SET `STATUS`='active' WHERE `ID`= '$lead_linker[LEAD_ID]'");
            $change_comment = json_encode(["from" => $lead_linker["STATUS"], "to" => "active"]);
            db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`,`COMMENT`) VALUES 
            ('$user', NOW(), 'lead_student_list','lead_status_change','$lead_linker[LEAD_ID]','1','$change_comment')");
        }
    }

    // if($sub_data["TYPE"] == "monthly" and !empty($sub_data["END_DATE"]) and date("", strtotime($sub_data["END_DATE"])) > 2020 and $sub_data["END_DATE"] < date("Y-m-d")){
    //     $nextPaymentDate = sprintf("%02d", $sub_data["DAY"]) > date("d") 
    //     ? date("Y-m-{$sub_data["DAY"]}") 
    //     : date("Y-m-{$sub_data["DAY"]}", strtotime("next month"));
    // }
    // else if($sub_data["TYPE"] == "simple"){
    //     $nextPaymentDate = calculateNextPaymentDate($sub_data["SPECIAL_PRICE"], $student_list["CURRENT_BALANCE"]);
    // }

    // if(isValidDate($nextPaymentDate)){
    //     $messageText = "Next Payment Date: ".date_var($nextPaymentDate, "d.m.Y");
    //     $phone = str_replace([" ", "+", "(", ")", "-"],"",$student_list["PHONE"]);
    //     $post_body = createMessageUsers($messageText, ["998$phone"]);
    //     $sendMessage = boolval(sendMessageRequest($post_body));
    //     if ($sendMessage != false) {
    //         $log_sms = db::query("INSERT INTO `sms_log` (`CREATED_DATE`,`CREATED_BY`,`SEND_DATE`,`SEND_TO`,`PHONE`,`TEXT`,`TYPE`) VALUES ('$now','$user_id','$now','$student_id','998$phone','$messageText','notify_next_payment')");
    //     } else {
    //         $log_sms = db::query("INSERT INTO `sms_log` (`CREATED_DATE`,`CREATED_BY`,`SEND_TO`,`PHONE`,`TEXT`,`TYPE`,`ACTIVE`) VALUES ('$now','$user_id','$student_id','998$phone','$messageText','notify_next_payment','0')");
    //     }
    // }

    header("Location: $student_id");
    exit;
}


// Coin Modal
if (isset($_POST["coinSubmit"])) {

    $student_id = openssl_decrypt($_POST["student_id"], 'AES-256-CBC', "StudentSecret", 0, substr(md5("StudentSecret"), 0, 16));
    $curStudentData = db::arr_s("SELECT * FROM `student_list` WHERE `ID`='$student_id'");
    $coinAmount = intval($_POST["coinAmount"]);
    $action_type = $_POST["actionType"] === "add" ? "add_coins" :  "remove_coins";
    if ($action_type == "add_coins") {
        $cal_amount = $curStudentData["COINS"] + $coinAmount;
        $text = "Sizni tabriklaymiz 🎉\n\nSizga $coinAmount coin qo'shildi\n\nIzoh:<b>$_POST[coinComment]</b>\n\nBalansingiz: $cal_amount";
    } else {
        $cal_amount = $curStudentData["COINS"] - $coinAmount;
        $text = "😢 Kechirasiz, lekin sizga aytishim kerak\n\nSizdan $coinAmount coin olindi\n\nIzoh:<b>$_POST[coinComment]</b>\n\nBalansingiz: $cal_amount";
    }
    $input_comment = filter_input(INPUT_POST, "coinComment", FILTER_SANITIZE_SPECIAL_CHARS);
    $comment = json_encode([$input_comment, $coinAmount]);
    $min_before = date("Y-m-d H:i:s", strtotime("-1 min"));
    $get_coin_log = db::arr_s("SELECT * FROM `table_log` WHERE `ITEM_ID`='$student_id' AND `COMMENT`='$comment' AND `TABLE_NAME`='student_list' AND `ACTION`='$action_type' AND `LOG_DATE` BETWEEN '$min_before' AND '$now'");
    if ($get_coin_log == "empty") {
        $update = db::query("UPDATE `student_list` SET `COINS`='$cal_amount' WHERE `ID`='$student_id'");
        $chat_id = db::arr_s("SELECT * FROM `tg_users` WHERE `STUDENT_ID`='$student_id'");
    }
    if ($get_coin_log == "empty" and $update["stat"] == "success") {
        $data = ['chat_id' => $chat_id["CHAT_ID"], 'parse_mode' => 'HTML', 'disable_web_page_preview' => false, 'text' => $text];
        $send_message = file_get_contents("https://api.telegram.org/<token>/sendMessage?" . http_build_query($data));
        $insert_log = db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`,`COMMENT`) VALUES ('$user_id', '$now', 'student_list', '$action_type','$student_id',1,'$comment')");
    }
    header("Location: $student_id");
    exit;
}

// Edit Transaction Modal
if (isset($_POST["editTransactionSubmit"])) {
    $student_id = openssl_decrypt($_POST["student_id"], 'AES-256-CBC', "StudentSecret", 0, substr(md5("StudentSecret"), 0, 16));
    $student_data = db::arr_s("SELECT * FROM `student_list` WHERE `ID`='$student_id'");
    $last_amount = db::arr_s("SELECT `TYPE`, `AMOUNT`,`ACTION_TYPE` FROM `transaction_list` WHERE `ID`='$_POST[editTransactionId]'");
    $format_price = filter_input(INPUT_POST, "editPaymentAmount", FILTER_SANITIZE_NUMBER_INT);

    $amount_diff = $last_amount["AMOUNT"] - intval($format_price);
    $student_balance = $student_data["CURRENT_BALANCE"] - $amount_diff;
    if ($last_amount["ACTION_TYPE"] == "subtract") {
        $student_balance = $student_data["CURRENT_BALANCE"] + $amount_diff;
    }
    $payment_type = $_POST["editPaymentType"];
    $user_id = $_SESSION['user']['id'];

    if (empty($_POST["editPaymentType"])) {
        $payment_type = $last_amount["TYPE"];
    }

    $update_student_balance = db::query("UPDATE `student_list` SET `CURRENT_BALANCE`='$student_balance' WHERE `ID`='$student_id'");


    $update_transaction = db::query("UPDATE `transaction_list` SET `CHANGED_DATE`='$now', `CHANGED_BY`='$user_id', `TRANSACTION_DATE`='$_POST[editPaymentDate]',`AMOUNT`='$format_price', `TYPE`='$payment_type' WHERE `ID`='$_POST[editTransactionId]'");


    // Payment Type Dollar
    if ($payment_type == 'dollar') {

        $student_list = db::arr_s("SELECT * FROM `student_list` WHERE ID='$student_id'");
        $studentName = addslashes($student_list['NAME']);
        $convertation_list = db::arr_s("SELECT * FROM `convertation_list` WHERE TR_ID='$_POST[editTransactionId]'");
        if ($convertation_list == 'empty') {
            $ins_dollar = db::query("INSERT INTO `transaction_list` (
            `STUDENT_ID`, 
            `SUBSCRIBE_ID`, 
            `ORG_ID`,
            `CREATED_DATE`, 
            `CREATED_BY`, 
            `CHANGED_DATE`, 
            `TRANSACTION_DATE`, 
            `CHANGED_BY`, 
            `ACTION_TYPE`, 
            `TYPE`, 
            `AMOUNT`, 
            `DESCRIPTION`)
            VALUES (
            '0', 
            '0', 
            '$cur_org_id',
            now(), 
            '$user_id', 
            now(),
            '$_POST[editPaymentDate]', 
            '$user_id', 
            'add_balance', 
            'dollar', 
            '$_POST[edit_paymentAmount_usd]', 
            'dollar: #$student_list[ID]  $studentName')");

            $ins_qaytim = db::query("INSERT INTO `transaction_list` (
            `STUDENT_ID`, 
            `SUBSCRIBE_ID`, 
            `ORG_ID`,
            `CREATED_DATE`, 
            `CREATED_BY`, 
            `CHANGED_DATE`, 
            `TRANSACTION_DATE`, 
            `CHANGED_BY`, 
            `ACTION_TYPE`, 
            `TYPE`, 
            `AMOUNT`, 
            `DESCRIPTION`)
            VALUES (
            '0', 
            '0',
            '$cur_org_id',
            now(), 
            '$user_id', 
            now(), 
            '$_POST[editPaymentDate]', 
            '$user_id', 
            'expense', 
            'naqd', 
            '$_POST[edit_paymentAmount_qaytim]', 
            'qaytim: #$student_list[ID]  $studentName');");

            $kurs = round(($_POST['edit_paymentAmount_qaytim'] + $format_price) / $_POST['edit_paymentAmount_usd']);

            $konvertatsiya_text =  "#" . $student_list['ID'] . ' ' . $studentName . ": $_POST[edit_paymentAmount_usd] $ berdi, $_POST[edit_paymentAmount_qaytim] so`m qaytim oldi, $format_price balansiga qo`shildi ;  1 $ - $kurs";

            $ins_konvertatsiya = db::query("INSERT INTO `convertation_list` (
            `CREATED_DATE`, 
            `TR_ID`, 
            `TR_ID_USD`, 
            `TR_ID_QAYTIM`, 
            `DESC`, 
            `ACTIVE`)
            VALUES (
            now(), 
            '$_POST[editTransactionId]', 
            '$ins_dollar[ID]', 
            '$ins_qaytim[ID]', 
            '$konvertatsiya_text',
            '1');");
        } else {

            //dollar update
            $update_tr_usd = db::query("UPDATE `transaction_list` SET `CHANGED_DATE`='$now', `CHANGED_BY`='$user_id', `TRANSACTION_DATE`='$_POST[editPaymentDate]',`AMOUNT`='$_POST[edit_paymentAmount_usd]' WHERE `ID`='$convertation_list[TR_ID_USD]'");

            //qaytim_update
            $update_tr_qaytim = db::query("UPDATE `transaction_list` SET `CHANGED_DATE`='$now', `CHANGED_BY`='$user_id', `TRANSACTION_DATE`='$_POST[editPaymentDate]',`AMOUNT`='$_POST[edit_paymentAmount_qaytim]' WHERE `ID`='$convertation_list[TR_ID_QAYTIM]'");

            //convertation_list update	
            $kurs = round(($_POST['edit_paymentAmount_qaytim'] + $format_price) / $_POST['edit_paymentAmount_usd']);

            $konvertatsiya_text =  "#" . $student_list['ID'] . ' ' .  $studentName . ": $_POST[edit_paymentAmount_usd] $ berdi, $_POST[edit_paymentAmount_qaytim] so`m qaytim oldi, $format_price balansiga qo`shildi ;  1 $ - $kurs";

            $update_convertation_list = db::query("UPDATE `convertation_list` SET `CREATED_DATE`=now(),`DESC`='$konvertatsiya_text'  WHERE ID='$convertation_list[ID]'");
        }
    } else {

        $convertation_list = db::arr_s("SELECT * FROM `convertation_list` WHERE TR_ID='$_POST[editTransactionId]'");

        if ($convertation_list != 'empty') {
            $del_tr_usd = db::query("DELETE FROM `transaction_list` WHERE ID='$convertation_list[TR_ID_USD]'");
            $del_tr_qaytim = db::query("DELETE FROM `transaction_list` WHERE ID='$convertation_list[TR_ID_QAYTIM]'");
            $del_cl = db::query("DELETE FROM `convertation_list` WHERE ID='$convertation_list[ID]'");
        }
    }

    $insert_log = db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`) VALUES ('$user_id', '$now', 'transaction_list','edit_payment','$_POST[editTransactionId]','1')");
    if (empty($_POST["subscribe_id"])) {
        $search_demo = db::arr_s("SELECT * FROM `subscribe_list` WHERE `STUDENT_ID`='$student_id' AND `STATUS`='demo' AND `ACTIVE`='1'");
    } else {
        $subscribe_id = intval($_POST["subscribe_id"]);
        $search_demo = db::arr_s("SELECT * FROM `subscribe_list` WHERE `ID`='{$subscribe_id}' AND `STATUS`='demo' AND `ACTIVE`='1'");
    }
    $lead_linker = db::arr_s("SELECT *, (SELECT `STATUS` FROM `lead_student_list` WHERE ID=LEAD_ID) AS STATUS  FROM `leads_students_linker` WHERE `STUDENT_ID`='$student_id'");


    $total_transactions = db::arr_s("SELECT SUM(AMOUNT) amount FROM `transaction_list` WHERE `STUDENT_ID`='$student_id' AND `ACTION_TYPE`='add'");

    if ($search_demo["STATUS"] == "demo" and $total_transactions["amount"] >= $search_demo["SPECIAL_PRICE"]) {
        $update_scribe_status = db::query("UPDATE `subscribe_list` SET `STATUS`='active' WHERE `ID`='$search_demo[ID]'");
        $log_activation = db::query("INSERT INTO `table_log` 
        (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`) VALUES 
        ('$user_id', '$now', 'subscribe_list', 'activate_student','$search_demo[ID]',1)");

        if ($log_activation["stat"] == "success" and isset($lead_linker["LEAD_ID"]) && $lead_linker["STATUS"] != "active") {
            db::query("UPDATE `lead_student_list` SET `STATUS`='active' WHERE `ID`= '$lead_linker[LEAD_ID]'");
            $change_comment = json_encode(["from" => $lead_linker["STATUS"], "to" => "active"]);
            db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`,`COMMENT`) VALUES 
            ('$user', NOW(), 'lead_student_list','lead_status_change','$lead_linker[LEAD_ID]','1','$change_comment')");
        }
    } else if ($search_demo["STATUS"] == "active" and $total_transactions["amount"] < $search_demo["SPECIAL_PRICE"]) {
        $update_scribe_status = db::query("UPDATE `subscribe_list` SET `STATUS`='demo' WHERE `ID`='$search_demo[ID]'");
        $log_activation = db::query("INSERT INTO `table_log` 
        (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`) VALUES 
        ('$user_id', '$now', 'subscribe_list', 'change_to_demo','$search_demo[ID]',1)");
    }
    header("Location: $student_id");
    exit;
}

// Remove Transaction Modal
if (isset($_POST["removeTranSubmit"])) {
    $student_id = openssl_decrypt($_POST["student_id"], 'AES-256-CBC', "StudentSecret", 0, substr(md5("StudentSecret"), 0, 16));
    $student_data = db::arr_s("SELECT * FROM `student_list` WHERE `ID`='$student_id'");
    $amount = filter_input(INPUT_POST, "removeTranAmount", FILTER_SANITIZE_NUMBER_INT);
    $payment_type = filter_input(INPUT_POST, "refund_payment_type", FILTER_SANITIZE_SPECIAL_CHARS);
    if ($_POST["balanceType"] === "withdrow") {
        $cal_total = $student_data["CURRENT_BALANCE"] - $amount;
        $update_student_balance = db::query("UPDATE `student_list` SET `CURRENT_BALANCE`='$cal_total' WHERE ID='$student_id'");
    }
    $transaction_type = $_POST["balanceType"] === "refund" ? "refund" : "stuff";
    $tran_date = $_POST["removeTranDate"];
    if ($_POST["balanceType"] === "refund") {
        db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`) VALUES ('$user_id', '$now', 'student_list','refund_student','$student_id','1')");
        $insert_trans = db::query("INSERT INTO `transaction_list`
        (`STUDENT_ID`,`TRANSACTION_DATE`,`ORG_ID`,`CREATED_DATE`,`CREATED_BY`,`CHANGED_DATE`,`CHANGED_BY`,`ACTION_TYPE`,`TYPE`,`AMOUNT`) VALUES 
        ('$student_id','$tran_date','$cur_org_id','$now','$user_id','$now','$user_id','expense','$payment_type','$amount')");
        db::query("INSERT INTO `expense_list` (`TRANSACTION_ID`, `CATEGORY`) VALUES ('$insert_trans[ID]','refund')");
    } else {
        db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`) VALUES ('$user_id', '$now', 'student_list','subtract_from_balance','$student_id','1')");
        db::query("INSERT INTO `transaction_list`
        (`STUDENT_ID`,`ORG_ID`,`TRANSACTION_DATE`,`CREATED_DATE`,`CREATED_BY`,`CHANGED_DATE`,`CHANGED_BY`,`ACTION_TYPE`,`TYPE`,`AMOUNT`) VALUES 
        ('$student_id','$cur_org_id','$tran_date','$now','$user_id','$now','$user_id','subtract','stuff','$amount')");
    }
    header("Location: $student_id");
    exit;
}

// SMS Modal
if (isset($_POST["smsSubmit"])) {
    $student_id = openssl_decrypt($_POST["student_id"], 'AES-256-CBC', "StudentSecret", 0, substr(md5("StudentSecret"), 0, 16));
    $student_data = db::arr_s("SELECT * FROM `student_list` WHERE `ID`='$student_id'");
    $phone = str_replace([" ", "+", "(", ")", "-"], "", $student_data["PHONE"]);
    $post_body = createMessageUsers($_POST["message_text"], ["998$phone"]);
    $sendMessage = boolval(sendMessageRequest($post_body));
    if ($sendMessage != false) {
        $log_sms = db::query("INSERT INTO `sms_log` (`CREATED_DATE`,`CREATED_BY`,`SEND_DATE`,`SEND_TO`,`PHONE`,`TEXT`) VALUES ('$now','$user_id','$now','$student_id','998$phone','$_POST[message_text]')");
    } else {
        $log_sms = db::query("INSERT INTO `sms_log` (`CREATED_DATE`,`CREATED_BY`,`SEND_TO`,`PHONE`,`TEXT`,`ACTIVE`) VALUES ('$now','$user_id','$student_id','998$phone','$_POST[message_text]','0')");
    }
    header("Location: $student_id");
    exit;
}

// Resend Modal
if (isset($_POST["resendSubmit"])) {
    $sms_log_id = filter_input(INPUT_POST, "actionId", FILTER_SANITIZE_NUMBER_INT);
    $sendMessage = sendSmsMessage(null, $sms_log_id);
    if ($sendMessage != false) {
        $update_log = db::query("UPDATE `sms_log` SET `SEND_DATE`='$now', `ACTIVE`='1' WHERE `ID`='$sms_log_id'");
    }
    header("Location: $student_id");
    exit;
}

// Add New Price Modal
if (isset($_POST["addPriceSubmit"])) {
    $student_id = openssl_decrypt($_POST['student_id'], 'AES-256-CBC', 'StudentSecret', 0, substr(md5('StudentSecret'), 0, 16));
    $subscription_comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_SPECIAL_CHARS);
    $discount_reason = $_POST['discount_reason'] != 'other' ? $_POST['discount_reason'] : NULL;
    $laptop_status = $_POST['give_laptop'] == 'true' ? 1 : 0;
    $old_sub_end_date = $selected_date = !empty($_POST['simple_start_date']) ? date('Y-m-d', strtotime($_POST['simple_start_date'])) : date('Y-m-d', strtotime($_POST['monthly_start_date']));
    $newSubscriptionEndDate = 'NULL';
    
    if ($_POST['discount_option'] === 'another-price') {
        $specialPrice = (int)preg_replace('/[^0-9]/', '', $_POST['special_price']);
    } else {
        $fiedsMapping = [
            'package-price' => 'package_price',
            'discount-price' => 'discount_price'
        ];
        $discount_option = $_POST['discount_option'];
        $discount_option_id = $_POST[$fiedsMapping[$discount_option]];
        $discount_data = db::arr_s("SELECT `ID`,`PRICE`,`PERIOD`,`PERIOD_TYPE`,`DISCOUNT_TYPE` FROM `discounts_list` WHERE `ID`=$discount_option_id AND `ACTIVE`=1");
        $specialPrice = $discount_data['DISCOUNT_TYPE'] == 'package'
        ? round($discount_data['PRICE'] / $discount_data['PERIOD'])
        : $discount_data['PRICE'];
        $newSubscriptionEndDate = date_var('+' . $discount_data['PERIOD'] . ' ' . $discount_data['PERIOD_TYPE'] . ' ' . $selected_date, 'Y-m-d 23:59:59');
        $newSubscriptionEndDate = '\'' . $newSubscriptionEndDate . '\'';
    }

    $sub_data = db::arr_s("SELECT * FROM `subscribe_list` WHERE `GROUP_ID`='$_POST[group_select]' AND `STUDENT_ID`='$student_id' AND `ACTIVE`=1");
    $sub_day = sprintf('%02d', $sub_data['DAY']);
    if (date('d') >= $sub_day) {
        $next_pay_day = date('Y-m-' . $sub_day, strtotime('+1 month'));
        $last_pay_day = date('Y-m-' . $sub_day);
    } else {
        $next_pay_day = date('Y-m-' . $sub_day);
        $last_pay_day = date('Y-m-' . $sub_day, strtotime('-1 month'));
    }

    // For Attendance by Subscription
    if ($sub_data['TYPE'] == 'simple') {

        $transactions_data = db::arr_s("SELECT COUNT(ID) AS `times`, `AMOUNT` 
        FROM `transaction_list`  
        WHERE `STUDENT_ID`='$student_id' AND `ACTION_TYPE`='taken' AND `TYPE`='system' AND `DESCRIPTION` 
      BETWEEN CONCAT('$selected_date', '_','$_POST[group_select]') AND CONCAT(CURDATE(), '_','$_POST[group_select]')");

        $old_lesson_price = round(intval($sub_data['SPECIAL_PRICE']) / 12) * intval($transactions_data['times']);
        $new_lesson_price = round($specialPrice / 12) * intval($transactions_data['times']);
        $cal_difference = $old_lesson_price - $new_lesson_price;
    }
    // For Monthly Subscription after next payment date
    else if ($sub_data['TYPE'] == 'monthly' && $selected_date >= $next_pay_day) {
        $cal_difference = 0;
        $old_sub_end_date = date('Y-m-d', strtotime('-1 day ' . $selected_date));
    }
    // For Monthly Subscription after last payment date

    else if ($sub_data['TYPE'] == 'monthly' && $selected_date < $next_pay_day && $selected_date >= $last_pay_day) {
        // We should take last payed transaction find the 
        $last = new DateTime($last_pay_day);
        $next = new DateTime($next_pay_day);
        $selected = new DateTime($selected_date);
        $recharge_days = $selected->diff($next)->format('%a');
        $total_days = $last->diff($next)->format('%a');
        // Finding price for one day
        $old_day_payment = (intval($sub_data['SPECIAL_PRICE']) / $total_days);
        $new_day_payment = ($specialPrice / $total_days);
        // Total amount of studied cost
        $cal_difference = ($old_day_payment - $new_day_payment) * $recharge_days;
    }

    // For Monthly Subscription before last payment date

    else if ($sub_data['TYPE'] == 'monthly' && $selected_date < $next_pay_day && $selected_date < $last_pay_day) {
        $selected_day = date('d', strtotime($selected_date));
        $transaction_data = db::arr_s("SELECT COUNT(*) `amount`, `AMOUNT` AS `price` FROM `transaction_list` WHERE `STUDENT_ID`='$student_id' AND `ACTION_TYPE`='taken' AND `DESCRIPTION` BETWEEN '$selected_date" . '_' . "$_POST[group_select]' AND CONCAT(CURDATE(), '_','$_POST[group_select]')");
        if ($selected_day == $sub_day) {
            $selected = new DateTime($selected_date);
            $last = new DateTime($last_pay_day);
            $planned_months = $selected->diff($last)->format('%m') + 1;

            $old_cost_total = intval($transaction_data['price']) * intval($transaction_data['amount']);
            $new_cost_total = $specialPrice * intval($transaction_data['amount']);

            if ($planned_months > $transaction_data['amount']) {
                // Charging for missed dates
                $missed_amount = intval($planned_months) - intval($transaction_data['amount']);
                $new_cost_total += $specialPrice * $missed_amount;
            }

            $cal_difference = $old_cost_total - $new_cost_total;
        } else {
            $withdraw_start_date = $sub_day > $selected_day
                ? date('Y-m-' . $sub_day, strtotime('-1 month ' . $selected_date))
                : date('Y-m-' . $sub_day, strtotime($selected_date));
            $withdraw_end_date = date('Y-m-d', strtotime('+1 month ' . $withdraw_start_date));
            $start_with = new DateTime($withdraw_start_date);
            $end_with = new DateTime($withdraw_end_date);
            $selected = new DateTime($selected_date);
            $last = new DateTime($last_pay_day);

            $planned_months = $end_with->diff($last)->format('%m') + 1;

            $total_days = $start_with->diff($end_with)->format('%a');
            $recharge_days =  $selected->diff($end_with)->format('%a');

            $old_day_cost = (intval($sub_data['SPECIAL_PRICE']) / $total_days);
            $new_day_cost = ($specialPrice / $total_days);
            $selected_date_recharge_amount = ($old_day_cost - $new_day_cost) * $recharge_days;

            $old_months_cost = intval($transaction_data['price']) * intval($transaction_data['amount']);
            $new_months_cost = $specialPrice * intval($transaction_data['amount']);
            if ($planned_months > $transaction_data['amount']) {
                // Charging for missed dates
                $missed_amount = intval($planned_months) - intval($transaction_data['amount']);
                $new_months_cost += $specialPrice * $missed_amount;
            }
            $months_recharge_amount = $old_months_cost - $new_months_cost;

            $cal_difference = $selected_date_recharge_amount + $months_recharge_amount;
        }
    }
    $old_subscribe_active = $sub_data['TYPE'] == 'monthly' && $selected_date >= $next_pay_day ? 1 : 0;
    $old_subscribe_status = $sub_data['TYPE'] == 'monthly' && $selected_date >= $next_pay_day ? 'active' : 'archive';
    // Deactivate Student
    db::query("UPDATE `subscribe_list` SET `END_DATE`='$old_sub_end_date', `ACTIVE`='$old_subscribe_active', `STATUS`='$old_subscribe_status', `COMMENT`='$subscription_comment',`PROMO_REASON`='$discount_reason' WHERE `ID`='$sub_data[ID]'");

    // Add to student balance calculated difference
    $cur_ballance = intval($student['CURRENT_BALANCE']) + $cal_difference;
    $update_balance = db::query("UPDATE `student_list` SET `CURRENT_BALANCE`='$cur_ballance' WHERE `ID`='$student_id'");

    // Insert new tranaction if calculated difference is not zero
    if ($cal_difference != 0) {
        $comment = 'PRICE_CHANGE from ' . $sub_data['SPECIAL_PRICE'] . ' to ' . $_POST['special_price'];
        $insert_transaction = db::query("INSERT INTO `transaction_list` 
        (`STUDENT_ID`, `SUBSCRIBE_ID`,`ORG_ID`,`TRANSACTION_DATE`,`CREATED_DATE`,`CREATED_BY`,`CHANGED_DATE`,`CHANGED_BY`,`ACTION_TYPE`,`TYPE`,`AMOUNT`,`DESCRIPTION`) VALUES 
        ('$student_id','$sub_data[ID]','$cur_org_id','$now','$now','$user_id','$now','$user_id','retake','system','$cal_difference','$comment')");
    }

    // Set new subscription status
    $subscribe_status = $sub_data['STATUS'];
    $sum_transactions = db::arr_s("SELECT SUM(`AMOUNT`) AS `TOTAL_AMOUNT` FROM `transaction_list` WHERE ACTION_TYPE='add' AND STUDENT_ID='$student_id'");
    $getLinkedLead = db::arr_s("SELECT `LEAD_ID`, (SELECT `STATUS` FROM `lead_student_list` WHERE `ID`=`LEAD_ID`) FROM `leads_students_linker` WHERE `STUDENT_ID` = '$student_id'");
    if ($sub_data["STATUS"] == 'demo' && $sum_transactions['TOTAL_AMOUNT'] >= $specialPrice) {
        $subscribe_status = 'active';
    }
    if ($subscribe_status == 'active' && $getLinkedLead['STATUS'] != 'active') {
        db::query("UPDATE `lead_student_list` SET `STATUS`='active' WHERE `ID`='$getLinkedLead[LEAD_ID]'");
        $change_comment = json_encode(["from" => $getLinkedLead['STATUS'], "to" => "active"]);
        db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`,`COMMENT`) VALUES ('$user_id', NOW(), 'lead_student_list','lead_status_change','$getLinkedLead[LEAD_ID]','1','$change_comment')");
    }
    if ($sub_data['TYPE'] == 'monthly' && $selected_date >= $next_pay_day) {
        $subscribe_status = 'planned';
    }

    // Set new withdraw date
    $sub_data['DAY'] = date('d', strtotime($selected_date)) > 28 ? 28 : date("d", strtotime($selected_date));

    // Insert new subscription 
    $insert_subscription = db::query("INSERT INTO `subscribe_list` 
      (`STUDENT_ID`,`SPECIAL_PRICE`,`COURSE_ID`,`GROUP_ID`,`START_DATE`,`END_DATE`,`ACTIVE`,`TYPE`,`DAY`,`LAPTOP`,`STATUS`) VALUES
      ('$student_id','$specialPrice','$sub_data[COURSE_ID]','$sub_data[GROUP_ID]','$selected_date',$newSubscriptionEndDate,'1','$sub_data[TYPE]', '$sub_data[DAY]','$laptop_status','$subscribe_status')");



    $insert_log = db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`) VALUES 
    ('$user_id', '$now', 'subscribe_list','add_new_price','$student_id','1'),
    ('$user_id', '$now', 'subscribe_list','add_new_subscription','$insert_subscription[ID]','1'),
    ('$user_id', '$now', 'subscribe_list','deactivate_old_subscription','$sub_data[ID]','1'),
    ('$user_id', '$now', 'student_list','add_recalculation','$student_id','1')");
    header("Location: $student_id");
    exit;
}

// removeTransaction
if (isset($_POST["removeTransactionSubmit"])) {
    $id = filter_input(INPUT_POST, "actionId", FILTER_SANITIZE_NUMBER_INT);
    $student_id = openssl_decrypt($_POST["student_id"], 'AES-256-CBC', "StudentSecret", 0, substr(md5("StudentSecret"), 0, 16));
    $transaction = db::arr_s("SELECT * FROM `transaction_list` WHERE ID='$id'");
    [$date, $group_id] = explode("_", $transaction["DESCRIPTION"]);
    $sub_data = db::arr_s("SELECT `ID`, `TYPE` FROM `subscribe_list` WHERE `STUDENT_ID` = '$student_id' AND `GROUP_ID`='$group_id' AND `ACTIVE`='1'");
    if ($sub_data["TYPE"] == "simple") {
        $attendance_data = db::arr_s("SELECT * FROM `attendance_list` WHERE GROUP_ID='$group_id' AND LESSON_DATE='$date'");
        $attendance_arr = json_decode($attendance_data["STUDENT_JSON"], true);
        if (isset($attendance_arr[$student_id])) $attendance_arr[$student_id] = null;
        $attendance_json = json_encode($attendance_arr);
        $update_attendance = db::query("UPDATE `attendance_list` SET STUDENT_JSON='$attendance_json' WHERE ID='$attendance_data[ID]'");
    }

    $cal_amount = $student["CURRENT_BALANCE"] + $transaction["AMOUNT"];
    $log_comment = json_encode([
        "transaction_created_by" => $transaction["CREATED_BY"],
        "amount" => $transaction["AMOUNT"],
        "type" => $transaction["TYPE"],
        "action_type" => $transaction["ACTION_TYPE"]
    ]);
    $update_ballance = db::query("UPDATE `student_list` SET `CURRENT_BALANCE` = '$cal_amount' WHERE ID='$student_id'");
    $delete_transaction = db::query("DELETE FROM `transaction_list` WHERE ID='$id'");
    $insert_log = db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`,`COMMENT`) VALUES ('$user_id', '$now', 'student_list','subtract_from_balance','$student_id','1',''),('$user_id','$now', 'transaction_list', 'delete_transaction','$id','1','$log_comment')");
    header("Location: $student_id");
    exit;
}
if (isset($_POST["checkPrintSubmit"])) {
    $params = ["data" => base64_encode(json_encode($_POST))];

    header("Location: http://localhost/printer/?" . http_build_query($params));
    exit;
}

if (isset($_POST["editSubscriptionSubmit"])) {
    $id = filter_input(INPUT_POST, "edit_subscription_id", FILTER_SANITIZE_NUMBER_INT);
    $sub_data = db::arr_s("SELECT * FROM `subscribe_list` WHERE `ID`='$id'");
    $laptop_status = $_POST["given_laptop"] == "true" ? 1 : 0;
    $type = !empty($_POST["subscription_type"]) ? filter_input(INPUT_POST, "subscription_type", FILTER_SANITIZE_SPECIAL_CHARS) : $sub_data["TYPE"];
    $start_date = !empty($_POST["edit_start_date"]) ? $_POST["edit_start_date"] : $sub_data["START_DATE"];

    if (date("d", strtotime($_POST["edit_start_date"])) > 28) {
        $day = 28;
    } else {
        $day = !empty($_POST["edit_start_date"]) ? date("d", strtotime($_POST["edit_start_date"])) : $sub_data["DAY"];
    }
    if (!empty(trim($_POST["edit_day"])) and $_POST["edit_day"] > 0 and $_POST["edit_day"] < 29 and $_SESSION["USER"]["ROLE_ID"] == '1') {
        $day_query = ", `DAY`='$_POST[edit_day]'";
    }
    // update subscription 
    db::query("UPDATE `subscribe_list` SET `LAPTOP`='$laptop_status', `TYPE`='$type', `START_DATE`='$start_date' $day_query WHERE `ID`='$id'");
    // Insert Log
    db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`) VALUES ('$user_id', '$now', 'subscribe_list','edit_subscription','$id','1')");
    header("Location: $student_id");
    exit;
}
if (isset($_POST["studentFileSubmit"])) {
    if ($_FILES["student_file"]["error"] === UPLOAD_ERR_OK) {
        $file = db::file_upload("student_file", "uploads/students");
        $file_org_name = filter_var($_FILES["student_file"]["name"], FILTER_SANITIZE_ADD_SLASHES);
        $std_id = filter_input(INPUT_POST, "student_id", FILTER_SANITIZE_NUMBER_INT);
        $comment = !empty($_POST["uploadComment"]) ? "'" . filter_input(INPUT_POST, "uploadComment", FILTER_SANITIZE_SPECIAL_CHARS) . "'" : NULL;
        if ($file["url"]) {
            $insert = db::query("INSERT INTO `student_files_list` (`CREATED_DATE`,`CREATED_BY`,`STUDENT_ID`,`NAME`,`PATH`, `COMMENT`) VALUES (now(),'$user_id','$std_id','$file_org_name','$file[url]', $comment)");
            // Insert Log
            db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`) VALUES ('$user_id', '$now', 'student_files_list','upload_student_file','$insert[ID]','1')");
        }

        $search_for_file = db::arr_s("SELECT * FROM `files` WHERE `NAME` LIKE '%$file_org_name%'");
        if (!isset($file["url"]) and file_exists($_SERVER["DOCUMENT_ROOT"] . "$search_for_file[URL]")) {

            $insert = db::query("INSERT INTO `student_files_list` (`CREATED_DATE`,`CREATED_BY`,`STUDENT_ID`,`NAME`,`PATH`, `COMMENT`) VALUES (now(),'$user_id','$std_id','$file_org_name','$file[url]', $comment)");
            // Insert Log
            db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`) VALUES ('$user_id', '$now', 'student_files_list','upload_student_file','$insert[ID]','1')");
        }
    }
    if ($_FILES["student_file"]["error"] !== UPLOAD_ERR_OK or !$file["url"]) {
        $_SESSION["file_upload_error"] = "Faylni yuklash xatosi: Nimadir xato ketdi";
    }
    header("Location: $student_id");
    exit;
}
// Remove Group
if (isset($_POST["removeGroupSubmit"])) {
    $action_id = filter_input(INPUT_POST, "actionId", FILTER_SANITIZE_NUMBER_INT);
    db::query("UPDATE `subscribe_list` SET `ACTIVE`='0', `STATUS`='archive' WHERE `ID`='$action_id' ");
    // Insert Log
    db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`) VALUES ('$user_id', '$now', 'subscribe_list','subscription_deleted','$action_id','1')");
    header("Location: $student_id");
    exit;
}
if (isset($_POST["deleteFileSubmit"])) {
    $file_id = filter_input(INPUT_POST, "actionId", FILTER_SANITIZE_NUMBER_INT);
    $student_id = openssl_decrypt($_POST["student_id"], 'AES-256-CBC', "StudentSecret", 0, substr(md5("StudentSecret"), 0, 16));
    $getFileData = db::arr_s("SELECT * FROM `student_files_list` WHERE `ID`='$file_id'");
    if (file_exists($_SERVER["DOCUMENT_ROOT"] . $getFileData["PATH"])) {
        unlink($_SERVER["DOCUMENT_ROOT"] . $getFileData["PATH"]);
    }
    db::query("UPDATE `student_files_list` SET `ACTIVE`='0' WHERE `ID`='$file_id'");
    db::query("DELETE FROM `files` WHERE `URL`='$getFileData[PATH]'");
    db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`) VALUES ('$user_id', '$now', 'student_files_list','delete_student_file','$file_id','1')");
    header("Location: $student_id");
    exit;
}

if (isset($_POST["closeTaskSubmit"])) {
    $comment = filter_input(INPUT_POST, "comment", FILTER_SANITIZE_SPECIAL_CHARS);
    $id = filter_input(INPUT_POST, "actionId", FILTER_SANITIZE_NUMBER_INT);
    db::query("UPDATE `list_tasks` SET `COMPLETED_BY`='$user_id', `COMMENT`='$comment', `COMPLETION_DATE`='$now', `STATUS`='closed' WHERE ID='$id'");
    // Insert log
    db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`COMMENT`,`ACTIVE`) VALUES ('$user_id', '$now', 'list_tasks','close_student_task','$id','$comment','1')");
    header("Location: $student_id");
    exit;
}

if (isset($_POST["activateSubscribeSubmit"])) {
    // $by_check = isset($_POST["activate_by_check"]) and $_POST["activate_by_check"] == "by_check";
    $only_lead = isset($_POST['activate_only_lead']) and $_POST['activate_only_lead'] === 'lead_only';
    $action_id = intval($_POST["actionId"]);
    $student_id = openssl_decrypt($_POST["student_id"], 'AES-256-CBC', "StudentSecret", 0, substr(md5("StudentSecret"), 0, 16));
    $search_unactive = db::arr_s("SELECT `ID`, `STATUS`,`SPECIAL_PRICE` FROM `subscribe_list` WHERE `ID`='{$action_id}' AND `ACTIVE`='1'");

    // if ($by_check) {
    $lead_linker = db::arr_s("SELECT LEAD_ID, (SELECT `STATUS` FROM `lead_student_list` WHERE ID=LEAD_ID) AS `STATUS`  FROM `leads_students_linker` WHERE `STUDENT_ID`='$student_id'");
    $sum_transactions = db::arr_s("SELECT SUM(AMOUNT) AS `amount` FROM `transaction_list` WHERE `STUDENT_ID`='$student_id' AND `ACTION_TYPE`='add'");

    if ($search_unactive != "empty" and $sum_transactions["amount"] >= $search_unactive["SPECIAL_PRICE"]) {
        if (!$only_lead && $search_unactive['STATUS'] != 'active') {
            $update_scribe_status = db::query("UPDATE `subscribe_list` SET `STATUS`='active' WHERE `ID`='$search_unactive[ID]'");
            $log_activation = db::query("INSERT INTO `table_log` 
                (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`) VALUES 
                ('$user_id', '$now', 'subscribe_list', 'activate_student','$search_unactive[ID]',1)");
        }
        if ($lead_linker["LEAD_ID"] != null && $lead_linker['STATUS'] != 'active' && $sum_transactions["amount"] >= $search_unactive["SPECIAL_PRICE"]) {
            db::query("UPDATE `lead_student_list` SET `STATUS`='active' WHERE `ID`= '$lead_linker[LEAD_ID]'");
            $change_comment = json_encode(["from" => $lead_linker["STATUS"], "to" => "active"]);
            db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`,`COMMENT`) VALUES 
                ('$user', NOW(), 'lead_student_list','lead_status_change','$lead_linker[LEAD_ID]','1','$change_comment')");
        }
    }
    // } else {
    //     if(!$only_lead){
    //         $update_scribe_status = db::query("UPDATE `subscribe_list` SET `STATUS`='active' WHERE `ID`='$search_unactive[ID]'");
    //         $log_activation = db::query("INSERT INTO `table_log` 
    //         (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`) VALUES 
    //         ('$user_id', '$now', 'subscribe_list', 'activate_student','$search_unactive[ID]',1)");

    //     }
    //     $lead_linker = db::arr_s("SELECT *, (SELECT `STATUS` FROM `lead_student_list` WHERE ID=LEAD_ID) AS STATUS  FROM `leads_students_linker` WHERE `STUDENT_ID`='$student_id'");
    //     if ($lead_linker["LEAD_ID"] != null && $lead_linker['STATUS'] != 'active') {
    //         db::query("UPDATE `lead_student_list` SET `STATUS`='active' WHERE `ID`= '$lead_linker[LEAD_ID]'");
    //         $change_comment = json_encode(["from" => $lead_linker["STATUS"], "to" => "active"]);
    //         db::query("INSERT INTO `table_log` (`USER_ID`,`LOG_DATE`,`TABLE_NAME`,`ACTION`,`ITEM_ID`,`ACTIVE`,`COMMENT`) VALUES 
    //         ('$user', NOW(), 'lead_student_list','lead_status_change','$lead_linker[LEAD_ID]','1','$change_comment')");
    //     }
    // }
    header("Location: $student_id");
    exit;
}
?>
