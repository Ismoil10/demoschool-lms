<?
$from = date("Y-m-01 00:00:00");
$to = date("Y-m-t 23:59:59");

if (isset($_SESSION["reportRange"])) {
  $dateRange = explode(" to ", $_SESSION["reportRange"]);
  $from = $dateRange[0];
  $to = isset($dateRange[1]) ? date("Y-m-d 23:59:59", strtotime($dateRange[1])) : $from . " 23:59:59";
}

$reportTypes = ["financialReport" => "Moliyaviy hisobot", "debtorsReport" => "Qarzdorlar hisoboti", "salaryReport" => "Ish haqi hisoboti", "leadsReport" => "Leadlar Hisoboti", "thisMonthActivated" => "Ushbu Oy Faollashtirilgan"];

$org_id = $_SESSION["USER"]["ORG_ID"];
if ($org_id > 0) {
  $sql_total_query = " CREATED_BY IN (SELECT ID FROM `gl_sys_users` WHERE `ORG_ID`='$org_id') AND ";
  $sql_data_query = " AND sl.`ORG_ID`='$org_id'";
  // $sql_teacher = " AND `ORG_ID`='$org_id'";
  $sql_teacher = " AND tl.`ORG_ID`='$org_id'";
}

// Financial Report Query
if (isset($_SESSION["reportType"]) and $_SESSION["reportType"] == "financialReport") {

  $data = db::arr("SELECT 
  tl.ID, 
  tl.TRANSACTION_DATE, 
  tl.CREATED_BY, 
  sl.NAME, 
  tl.AMOUNT, 
  tl.TYPE, 
  tl.DESCRIPTION,
  gsu.NAME AS `USERNAME`, 
  gsu.SURNAME 
  FROM `transaction_list` AS tl 
  LEFT JOIN `student_list` AS sl ON tl.STUDENT_ID = sl.ID 
  LEFT JOIN `gl_sys_users` AS gsu ON gsu.ID = tl.CREATED_BY 
  WHERE tl.ACTION_TYPE = 'add' $sql_data_query
  AND tl.TRANSACTION_DATE BETWEEN '$from' AND '$to'");
  $incomes_data = db::arr("SELECT TYPE, SUM(AMOUNT) as amount FROM `transaction_list` WHERE $sql_total_query `ACTION_TYPE` = 'add' AND TRANSACTION_DATE BETWEEN '$from' AND '$to' GROUP BY `TYPE`");
  $outcomes_data = db::arr("SELECT TYPE, SUM(AMOUNT) as amount FROM `transaction_list` WHERE $sql_total_query `ACTION_TYPE`='expense' AND TRANSACTION_DATE BETWEEN '$from' AND '$to' GROUP BY `TYPE`");
  $finance_total = db::arr_s("SELECT SUM(AMOUNT) AS amount FROM `transaction_list` WHERE $sql_total_query `ACTION_TYPE` = 'add' AND TRANSACTION_DATE BETWEEN '$from' AND '$to' ");
  $taken_total = db::arr_s("SELECT SUM(AMOUNT) AS amount FROM `transaction_list` WHERE $sql_total_query `ACTION_TYPE`='expense' AND TRANSACTION_DATE BETWEEN '$from' AND '$to'");
}
// Debters Report Query
elseif (isset($_SESSION["reportType"]) and $_SESSION["reportType"] == "debtorsReport") {
  $price_level = "0";
  if (isset($_SESSION["debtors_filter"])) {
    $price_level = $_SESSION["debtors_filter"];
  }
  $data = db::arr("SELECT 
  sl.ID,
  sl.NAME, 
  sl.PHONE,
  sl.CURRENT_BALANCE,
  gl.NAME AS `GROUP`
  FROM `student_list` sl
  LEFT JOIN `subscribe_list` sub ON sub.STUDENT_ID = sl.ID
  LEFT JOIN `group_list` gl ON gl.ID = sub.GROUP_ID
  WHERE sl.CURRENT_BALANCE < $price_level AND sl.ACTIVE=1 $sql_data_query
  GROUP BY sl.ID");
  $total_debt = db::arr_s("SELECT SUM(CURRENT_BALANCE) AS amount FROM `student_list` WHERE CURRENT_BALANCE < -100");
}
// Salary Report Query
elseif (isset($_SESSION["reportType"]) and $_SESSION["reportType"] == "salaryReport") {
  $odd_days = createPlan($from, $to, $_SESSION["exception_date"])["odd"];
  $even_days = createPlan($from, $to, $_SESSION["exception_date"])["even"];
  $teacher_groups = [];
  $teacher_list = db::arr("SELECT 
  gl.ID `GID`,
  gl.`NAME` `GROUP`,
  tl.ID `TID`,
  tl.NAME `TEACHER`
  FROM `group_list` gl 
  INNER JOIN `teacher_list` tl ON gl.TEACHER_ID = tl.ID
  WHERE gl.STATUS='active' $sql_teacher");

  foreach ($teacher_list as $value) {
    if (!isset($teacher_groups[$value["TID"]])) {
      $teacher_groups[$value["TID"]] = [];
      $teacher_amount_atten[$value["TID"]]["1,3,5"] = 0;
      $teacher_amount_atten[$value["TID"]]["2,4,6"] = 0;
    }
    array_push($teacher_groups[$value["TID"]], $value["GID"]);
  }

  foreach ($teacher_groups as $tid => $gids) {
    $group_ids = join(",", $gids);
    $att = db::arr("SELECT *, (SELECT `DAYS` FROM `group_list` WHERE ID=attendance_list.GROUP_ID ) AS `DAYS` FROM `attendance_list` WHERE `LESSON_DATE` BETWEEN '$from' AND '$to' AND GROUP_ID IN ($group_ids)");
    if ($att == "empty") continue;
    foreach ($att as $attendance) {
      $arr_atten = json_decode($attendance["STUDENT_JSON"], true);
      $valid_atten = array_filter($arr_atten, function ($var) {
        return is_numeric($var);
      });
      $join_ids = join(",", array_keys($valid_atten));
      $active_student = db::arr_s("SELECT COUNT(ID) `amount` FROM `subscribe_list` WHERE STUDENT_ID IN ($join_ids) AND ACTIVE=1 AND `STATUS`='active'");
      $teacher_amount_atten[$tid][$attendance["DAYS"]] += $active_student["amount"];
    }
  }

  $teacher_type = [
    "teacher" => "O'qituvchi",
    "ta" => "O'qituvchi yordamchisi"
  ];
}
// Leads Report Query
elseif (isset($_SESSION["reportType"]) and $_SESSION["reportType"] == "leadsReport") {
  $filter_query = "";
  if (isset($_SESSION["leads_filter"]["staff"])) {
    $staff_id = $_SESSION["leads_filter"]["staff"];
    $filter_query = $filter_query . "AND sl.`CREATED_BY`='$staff_id' ";
  }
  if (isset($_SESSION["leads_filter"]["eshitgan_joyi"])) {
    $eshitgan_arr = json_decode($_SESSION["leads_filter"]["eshitgan_joyi"], true);
    $filter_query = $filter_query . " AND (sl.`ESHITGAN_JOYI` LIKE '%\"$eshitgan_arr[select]\"%' AND sl.`ESHITGAN_JOYI` LIKE '%\"input\":\"$eshitgan_arr[input]\"%')";
  }
  $data = db::arr("SELECT 
  sl.ID,
  DATE_FORMAT(sl.CREATED_DATE, '%d.%m.%Y') AS CDATE,
  sl.NAME,
  sl.PHONE,
  sl.PARENT_PHONE,
  gsu.NAME AS `STUFF`,
  ol.NAME AS `filyal`,
  sl.ESHITGAN_JOYI,
  sl.CURRENT_BALANCE
  FROM `student_list` sl
  LEFT JOIN `gl_sys_users` gsu ON gsu.ID = sl.CREATED_BY 
  LEFT JOIN `org_list` ol ON ol.ID = sl.ORG_ID 
  WHERE CREATED_DATE BETWEEN '$from' AND '$to' $sql_data_query $filter_query");
}
// Current Month Active
elseif (isset($_SESSION["reportType"]) and $_SESSION["reportType"] == "thisMonthActivated") {
  $data = db::arr("SELECT 
    sl.ID,
    DATE_FORMAT(tl.LOG_DATE, '%d.%m.%Y') AS LDATE,
    sl.NAME,
    sl.PHONE,
    sl.CURRENT_BALANCE
  FROM `table_log` tl
  INNER JOIN  `student_list` sl ON tl.ITEM_ID = sl.ID 
  WHERE sl.ACTIVE=1 AND tl.ACTION='activate_student' $sql_data_query
  AND tl.LOG_DATE BETWEEN '$from' AND '$to'");
}
$payment_types = [
  "naqd" => "Naqd pul UZS",
  "dollar" => "Naqd pul USD",
  "kassa" => "Naqd pul KASSA",
  "payme" => "Payme",
  "bank_hisob" => "Bank",
  "click" => "Click",
  "uzum" => "Uzum Bank",
  "apelsin" => "Apelsin",
  "plastik" => "Karta",
  "uzumNasiya" => "Uzum Nasiya",
  "itBilim" => "IT Bilim",
  "cloudPayments" => "Cloud Payments",
  "system" => "Tizim",
  "stuff" => "Hodim",
  "corp_card" => "Karparativ Karta",
  "terminal" => "Terminal"
];
$outcomes_types = [
  "naqd" => "Naqd pul UZS",
  "dollar" => "Naqd pul USD",
  "kassa" => "Kassa (Инкассация)",
  "taxi" => "Taxi",
]
?>
<div class="app-content content ">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>
  <div class="content-wrapper container-xxl p-0">
    <div class="content-header row">
      <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
          <div class="col-12">
            <h2 class="content-header-title float-left mb-0">Hisobot</h2>
            <div class="breadcrumb-wrapper">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/account/main_page">Asosiy menyu</a>
                </li>
                <li class="breadcrumb-item active">Hisobot ( detail )</li>
                <pre><? //print_r("SELECT TYPE, SUM(AMOUNT) as amount FROM `transaction_list` WHERE $sql_total_query `ACTION_TYPE`='expense' AND TRANSACTION_DATE BETWEEN '$from' AND '$to' GROUP BY `TYPE`")
                      ?></pre>
              </ol>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="content-body">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body d-flex justify-content-between">
              <h4 class="align-self-center"><?= isset($_SESSION["reportType"]) ? "Hisobot: " . $reportTypes[$_SESSION["reportType"]] : ""; ?></h4>
              <button class="btn btn-icon btn-outline-primary" data-toggle="modal" data-target="#report"><i data-feather="file-text"></i> Hisobotlar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?
    $file_path = $_SERVER["DOCUMENT_ROOT"] . "/account/modules/med/report/report_types/$_SESSION[reportType].php";
    if (isset($_SESSION["reportType"]) and file_exists($file_path)) :
      require $file_path;
    ?>
    <? else : ?>
      <div class="alert alert-danger mt-1 alert-validation-msg" role="alert">
        <div class="alert-body">
          <i data-feather="info"></i>
          <strong id="alert-message">Tanlangan Hisobot topilmadi.</strong>
        </div>
      </div>
    <? endif; ?>
    <? if (isset($_SESSION["reportType"]) and $_SESSION["reportType"] == "financialReport") : ?>
      <div class="content-body">
        <div class="row">
          <div class=" col-sm-6 col-12">
            <div class="card">
              <div class="card-header">
                <div>
                  <h4 class="font-weight-bolder mb-0">To'lovlar miqdori: <?= number_format($finance_total["amount"], 0, 2, " ") ?></h4>
                  <p class="card-text"><?= date("d.m.Y", strtotime($from)) ?> ~ <?= date("d.m.Y", strtotime($to)) ?></p>
                </div>
                <div class="avatar bg-light-primary p-50 m-0">
                  <div class="avatar-content">
                    <i data-feather="inbox" class="font-medium-5"></i>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <ul class="list-group list-group-flush">
                  <? foreach ($incomes_data as $income) : ?>
                    <li class="list-group-item pl-0"><b><?= $payment_types[$income["TYPE"]] ?>: </b><?= number_format($income["amount"], 0, 2, " ") ?></li>
                  <? endforeach; ?>
                </ul>
              </div>
            </div>
          </div>
          <div class=" col-sm-6 col-12">
            <div class="card">
              <div class="card-header">
                <div>
                  <h4 class="font-weight-bolder mb-0">Harajatlar miqdori: <?= number_format($taken_total["amount"], 0, 2, " ") ?> UZS</h4>
                  <p class="card-text"><?= date("d.m.Y", strtotime($from)) ?> ~ <?= date("d.m.Y", strtotime($to)) ?></p>
                </div>
                <div class="avatar bg-light-success p-50 m-0">
                  <div class="avatar-content">
                    <i data-feather="stop-circle" class="font-medium-5"></i>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <ul class="list-group list-group-flush">
                  <? foreach ($outcomes_data as $income) : ?>
                    <li class="list-group-item pl-0"><b><?= $payment_types[$income["TYPE"]] ?>: </b><?= number_format($income["amount"], 0, 2, " ") ?></li>
                  <? endforeach; ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    <? elseif (isset($_SESSION["reportType"]) and $_SESSION["reportType"] == "debtorsReport") : ?>
      <div class="content-body">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <div>
                  <h4 class="font-weight-bolder mb-0">Qarzdorlar: <?= date("d.m.Y (H:i)", strtotime($from)) ?> ~ <?= date("d.m.Y (H:i)", strtotime($to)); ?></h4>
                </div>
                <div class="group">
                  <div class="avatar bg-light-primary p-50 m-0">
                    <div class="avatar-content">
                      <i data-feather="calendar" class="font-medium-5"></i>
                    </div>
                  </div>
                  <button class="btn btn-outline-warning btn-icon rounded-circle p-1" data-toggle="modal" data-target="#debtorsModal"><i data-feather="filter"></i></button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <? elseif (isset($_SESSION["reportType"]) and $_SESSION["reportType"] == "salaryReport") : ?>
      <div class="content-body">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <div>
                  <h4 class="font-weight-bolder mb-0">Davrdagi ish haqi: <?= date("d.m.Y (H:i)", strtotime($from)) ?> ~ <?= date("d.m.Y (H:i)", strtotime($to)); ?></h4>
                </div>
                <div class="avatar bg-light-primary p-50 m-0">
                  <div class="avatar-content">
                    <i data-feather="calendar" class="font-medium-5"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <? elseif (isset($_SESSION["reportType"]) and $_SESSION["reportType"] == "leadsReport") : ?>
      <div class="content-body">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <div>
                  <h4 class="font-weight-bolder mb-0">Davrdagi leadlar hisoboti: <?= date("d.m.Y (H:i)", strtotime($from)) ?> ~ <?= date("d.m.Y (H:i)", strtotime($to)); ?></h4>
                </div>
                <div class="group">
                  <div class="avatar bg-light-primary p-50 m-0">
                    <div class="avatar-content">
                      <i data-feather="calendar" class="font-medium-5"></i>
                    </div>
                  </div>
                  <button class="btn btn-outline-warning btn-icon rounded-circle p-1" data-toggle="modal" data-target="#leadsModal"><i data-feather="filter"></i></button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <? endif ?>
    <div class="content-body <? if (!isset($_SESSION["reportType"])) echo "d-none" ?>">
      <!-- Responsive Datatable -->
      <section id="responsive-datatable">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <? if (isset($_SESSION["reportType"]) and $_SESSION["reportType"] == "financialReport") : ?>
                <div class="card-datatable">
                  <table class="d_tab dt-responsive table" id="customtable">
                    <thead>
                      <tr>
                        <th></th>
                        <th>Sana</th>
                        <th>Talaba </th>
                        <th>Miqdori</th>
                        <th>Izoh</th>
                        <th>To'lov turi</th>
                        <th>Xodim</th>
                      </tr>
                    </thead>
                    <tbody>
                      <? foreach ($data as $v) : ?>
                        <? $total_income += $v["AMOUNT"]; ?>
                        <tr>
                          <td></td>
                          <td><?= date("d.m.Y", strtotime($v["TRANSACTION_DATE"])) ?></td>
                          <td><?= $v["NAME"] ?></td>
                          <td><?= number_format($v["AMOUNT"], 0, 2, " ") ?> UZS</td>
                          <td><?= $v["DESCRIPTION"] ?></td>
                          <td>
                            <div class="badge badge-info badge-pill badge-glow"><?= $payment_types[$v["TYPE"]] ?></div>
                          </td>
                          <td><?= $v["USERNAME"] . " " . $v["SURNAME"] ?></td>
                        </tr>
                      <? endforeach; ?>
                    </tbody>
                    <tfoot>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td>Umumiy: <?= number_format($total_income, 0, 2, " ") ?></td>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tfoot>
                  </table>
                </div>
              <? elseif (isset($_SESSION["reportType"]) and $_SESSION["reportType"] == "debtorsReport") : ?>
                <div class="card-datatable">
                  <table class="d_tab dt-responsive table" id="customtable">
                    <thead>
                      <tr>
                        <th></th>
                        <th>Ims</th>
                        <th>Telefon </th>
                        <th>Qarz </th>
                        <th>Guruh</th>
                        <th>Holati</th>
                      </tr>
                    </thead>
                    <tbody>
                      <? foreach ($data as $v) : ?>
                        <tr>
                          <td></td>
                          <td><?= $v["NAME"] ?></td>
                          <td><?= $v["PHONE"] ?></td>
                          <td><?= number_format($v["CURRENT_BALANCE"], 0, 2, " ") ?> UZS</td>
                          <td>
                            <div class="badge badge-info badge-pill badge-glow"><?= $v["GROUP"] ?></div>
                          </td>
                          <td></td>
                        </tr>
                      <? endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <? elseif (isset($_SESSION["reportType"]) and $_SESSION["reportType"] == "salaryReport") : ?>
                <div class="card-datatable">
                  <table class="d_tab dt-responsive table salaryReport" id="customtable">
                    <thead>
                      <tr>
                        <th></th>
                        <th>O`qituvchi </th>
                        <? if ($_SESSION["USER"]["ROLE_ID"] === "1") : ?>
                          <th>Davomat miqdori</th>
                        <? endif ?>
                        <th>Turi</th>
                        <th>Miqdori</th>
                      </tr>
                    </thead>
                    <tbody>
                      <? foreach (db::arr("SELECT * FROM `teacher_list` tl WHERE `ACTIVE`= 1 $sql_teacher") as $v) : ?>
                        <?
                        $for_ods = ($v["SALARY"] / $odd_days) * $teacher_amount_atten[$v["ID"]]["1,3,5"];
                        $for_evens = ($v["SALARY"] / $even_days) * $teacher_amount_atten[$v["ID"]]["2,4,6"];
                        $total = round($for_evens + $for_ods);
                        ?>
                        <tr>
                          <td></td>
                          <td><a href="javascript:void(0);" data-teacher="<?= $v["ID"] ?>" data-from="<?= $from ?>" data-to="<?= $to ?>" data-exception="<?= htmlspecialchars(json_encode($_SESSION["exception_date"])) ?>"><?= $v['NAME'] ?></a></td>
                          <? if ($_SESSION["USER"]["ROLE_ID"] === "1") : ?>
                            <td><?= $teacher_amount_atten[$v["ID"]]["1,3,5"] + $teacher_amount_atten[$v["ID"]]["2,4,6"] ?> ta</td>
                          <? endif ?>
                          <td><?= $teacher_type[$v["TYPE"]] ?></td>
                          <td><?= number_format($total, 0, ',', ' ') ?> UZS</td>
                        </tr>
                      <? endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <? elseif (isset($_SESSION["reportType"]) and $_SESSION["reportType"] == "leadsReport") : ?>
                <div class="card-datatable">
                  <table class="d_tab dt-responsive table" id="customtable">
                    <thead>
                      <tr>
                        <th></th>
                        <th>Sana </th>
                        <th>Ismi</th>
                        <th>Tel. raqam</th>
                        <th>Ota-ona tel. raqami</th>
                        <th>Mas'ul</th>
                        <th>Filial</th>
                        <th>Qayerdan Eshtgan</th>
                        <th>Balansi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <? //var_dump($data);
                      ?>
                      <? foreach ($data as $v) : ?>
                        <? if (empty($v['ESHITGAN_JOYI'])) {
                          $eshitgan_joyi = "Ma'lumot topilmadi";
                        } else {
                          $eshitgan_joyi = json_decode($v['ESHITGAN_JOYI'])->select . ' ' . json_decode($v['ESHITGAN_JOYI'])->input;
                        } ?>
                        <tr>
                          <td></td>
                          <td><?= $v['CDATE'] ?></td>
                          <td><?= $v['NAME'] ?></td>
                          <td><?= $v['PHONE'] ?></td>
                          <td><?= $v['PARENT_PHONE'] ?></td>
                          <td><?= $v['STUFF'] ?></td>
                          <td><?= $v['filyal'] ?></td>
                          <td><?= ucfirst($eshitgan_joyi) ?></td>
                          <td><?= number_format($v['CURRENT_BALANCE'], 0, ',', ' ') ?> UZS</td>
                        </tr>
                      <? endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <? elseif (isset($_SESSION["reportType"]) and $_SESSION["reportType"] == "thisMonthActivated") : ?>
                <div class="card-datatable">
                  <table class="d_tab dt-responsive table" id="customtable">
                    <thead>
                      <tr>
                        <th></th>
                        <th>Sana </th>
                        <th>Ismi</th>
                        <th>Tel. raqam</th>
                        <th>Balansi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <? //var_dump($data);
                      ?>
                      <? foreach ($data as $v) : ?>

                        <tr>
                          <td></td>
                          <td><?= $v['LDATE'] ?></td>
                          <td><?= $v['NAME'] ?></td>
                          <td><?= $v['PHONE'] ?></td>
                          <td><?= number_format($v['CURRENT_BALANCE'], 0, ',', ' ') ?> UZS</td>
                        </tr>
                      <? endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <? endif ?>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>
<? require $_SERVER["DOCUMENT_ROOT"] . "/account/modules/med/report/report_modals.php"; ?>
<? require $_SERVER["DOCUMENT_ROOT"] . "/account/modules/med/report/report_js.php"; ?>