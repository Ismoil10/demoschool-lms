<?
$staff_list = db::arr("SELECT `ID`,`NAME`,`SURNAME` FROM `gl_sys_users` WHERE `STATUS`=1 $sql_teacher AND ROLE_ID IN (2,3)");
$came_from = db::arr("SELECT `ESHITGAN_JOYI`, COUNT(ID) AS amount FROM  `student_list` WHERE ESHITGAN_JOYI LIKE '%,\"input\"%' AND ESHITGAN_JOYI IS NOT NULL $sql_teacher GROUP BY ESHITGAN_JOYI");
?>
<div class="modal fade text-left" id="report" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel1">Basic Modal</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="" method="post" id="reportForm">
          <div class="mb-3">
            <label>Hisobotni tanlang</label>
            <select name="reportType" id="report-type" class="form-control">
              <? if ($_SESSION["USER"]["ROLE_ID"] == "3") : ?>
                <option value="financialReport" <?= $_SESSION["reportType"] == "financialReport" ? "selected" : ""; ?>>Moliyaviy hisobot</option>
              <? else : ?>
                <option value="leadsReport" <?= $_SESSION["reportType"] == "leadsReport" ? "selected" : ""; ?>>Lidlar hisoboti</option>
                <option value="thisMonthActivated" <?= $_SESSION["reportType"] == "thisMonthActivated" ? "selected" : ""; ?>>Ushbu oy faollashtirilgan</option>
                <option value="financialReport" <?= $_SESSION["reportType"] == "financialReport" ? "selected" : ""; ?>>Moliyaviy hisobot</option>
                <option value="debtorsReport" <?= $_SESSION["reportType"] == "debtorsReport" ? "selected" : ""; ?>>Qarzdorlar hisoboti</option>
                <option value="salaryReport" <?= $_SESSION["reportType"] == "salaryReport" ? "selected" : ""; ?>>Ish haqi hisoboti</option>
              <? endif; ?>
            </select>
          </div>
          <div class="mb-3">
            <label>Sana</label>
            <input type="text" id="fp-range" class="form-control flatpickr-range get-range" name="dateRange" placeholder="YYYY-MM-DD to YYYY-MM-DD" oninput="getRange(this)" required />
          </div>
          <div class="exception-dates d-none">
          </div>
          <div class="mb-3">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" name="stop_report" id="stop_report">
              <label class="custom-control-label" for="stop_report">Hisobotni o'chirish</label>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
        <button type="submit" form="reportForm" name="reportSubmit" class="btn btn-primary">Tasdiqlash</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade text-left" id="leadsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel1">Filter</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="" method="post" id="leadsForm">
          <div class="mb-3">
            <label>Mas'ul bo'yicha</label>
            <select name="by_staff" class="form-control">
              <option selected disabled></option>
              <? foreach ($staff_list as $staff) : ?>
                <option value="<?= $staff['ID'] ?>" <? if ($_SESSION["leads_filter"]["staff"] == $staff["ID"]) echo "selected"; ?>><?= $staff["NAME"] . " " . $staff["SURNAME"] ?></option>
              <? endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Eshitgan Joyi</label>
            <select name="eshitgan_joyi" class="form-control">
              <option value="empty" selected disabled></option>
              <? foreach ($came_from as $v) : ?>
                <? $arr = json_decode($v["ESHITGAN_JOYI"], true); ?>
                <option value="<?= htmlspecialchars($v["ESHITGAN_JOYI"]) ?>" <? if ($_SESSION["leads_filter"]["eshitgan_joyi"] == $v["ESHITGAN_JOYI"]) echo "selected"; ?>><?= ucfirst($arr["select"]) . " " . $arr["input"] ?></option>
              <? endforeach ?>
            </select>
          </div>
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="filter_on" id="filter_on">
            <label class="custom-control-label" for="filter_on">Filter'ni o'chirish</label>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
        <button type="submit" form="leadsForm" name="leadsSubmit" class="btn btn-primary">Tasdiqlash</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade text-left" id="debtorsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel1">Qarzdorlar Filteri</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="" method="post" id="debtorsForm">
          <div class="mb-1">
            <label for="">Narxi</label>
            <select name="price_select" class="form-control">
              <option selected disabled></option>
              <option value="79000" <? if ($_SESSION["debtors_filter"] == "79000") echo "selected"; ?>>
                &lt; 79.000</option>
              <option value="160000" <? if ($_SESSION["debtors_filter"] == "160000") echo "selected"; ?>>
                &lt; 160.000</option>
              <option value="250000" <? if ($_SESSION["debtors_filter"] == "250000") echo "selected"; ?>>
                &lt; 250.000</option>
              <option value="480000" <? if ($_SESSION["debtors_filter"] == "480000") echo "selected"; ?>>
                &lt; 480.000</option>
              <option value="950000" <? if ($_SESSION["debtors_filter"] == "950000") echo "selected"; ?>>
                &lt; 950.000</option>
              <option value="1900000" <? if ($_SESSION["debtors_filter"] == "1900000") echo "selected"; ?>>
                &lt; 1.900.000</option>
              <option value="2380000" <? if ($_SESSION["debtors_filter"] == "2380000") echo "selected"; ?>>
                &lt; 2.380.000</option>
            </select>
          </div>
          <div class="custom-control custom-checkbox">
            <input type="checkbox" name="debtors_filter_stop" class="custom-control-input" id="stop_filter">
            <label class="custom-control-label" for="stop_filter">Filter O'chirish</label>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
        <button type="submit" form="debtorsForm" name="debtorsSubmit" class="btn btn-primary">Tasdiqlash</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade text-left" id="singleTeacherModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel1">Ish haqi hisoboti</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body modal-report-body">
      </div>
    </div>
  </div>
</div>
<?
if (isset($_POST["reportSubmit"])) {
  if ($_POST["stop_report"] != "on") {
    $_SESSION["reportType"] = $_POST["reportType"];
    $_SESSION["reportRange"] = $_POST["dateRange"];
    if(count($_POST["exception_date"]) > 0){
      $_SESSION["exception_date"] = $_POST["exception_date"];
    }
  } else {
    unset($_SESSION["reportType"]);
    unset($_SESSION["reportRange"]);
    unset($_SESSION["exception_date"]);
  }
  LocalRedirect("index.php");
}

if (isset($_POST["leadsSubmit"])) {
  if ($_POST["filter_on"] != "on") {
    $_SESSION["leads_filter"]["staff"] = $_POST["by_staff"];
    $_SESSION["leads_filter"]["eshitgan_joyi"] = $_POST["eshitgan_joyi"];
  } else {
    unset($_SESSION["leads_filter"]);
  }
  LocalRedirect("index.php");
}

if (isset($_POST["debtorsSubmit"])) {
  if ($_POST["debtors_filter_stop"] != "on") {
    $_SESSION["debtors_filter"] = $_POST["price_select"];
  } else {
    unset($_SESSION["debtors_filter"]);
  }
  LocalRedirect("index.php");
}
?>