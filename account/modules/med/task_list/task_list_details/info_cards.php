<?
$all = db::arr_s("SELECT COUNT(*) `amount` FROM `list_tasks` WHERE `ASSIGNED_MEMBERS` LIKE '%\"$id\"%' AND `STATUS` IN ('open','inprogress')");
$completed = db::arr_s("SELECT COUNT(*) `amount` FROM `list_tasks` WHERE `ASSIGNED_MEMBERS` LIKE '%\"$id\"%' AND `STATUS`='closed'");

$by_me =  db::arr_s("SELECT COUNT(*) `amount` FROM `list_tasks` WHERE `CREATED_BY`='$id' AND `STATUS` IN ('open','inprogress')");
$by_me_com = db::arr_s("SELECT COUNT(*) `amount` FROM `list_tasks` WHERE `CREATED_BY`='$id' AND `STATUS`='closed'");

$today = db::arr_s("SELECT COUNT(*) `amount` FROM `list_tasks` WHERE `ASSIGNED_MEMBERS` LIKE '%\"$id\"%' AND `DUE_DATE` = '$now' AND `STATUS` IN ('open','inprogress')");
?>
<!-- Statistics card section -->
<section id="statistics-card">
  <!-- Stats Horizontal Card -->
  <? //echo '<pre>'; print_r($today); echo '</pre>'; ?>
  <div class="row">
  <div class="col-lg-2 col-sm-6 col-12">
      <a href="/account/task_list/list/to_me_today" class="text-secondary"><!-- /account/task_list/list/to_me_today -->
        <div class="card">
          <div class="card-header">
            <div>
              <h2 class="font-weight-bolder mb-0"><?=number_format($today["amount"], 0, "", " ") ?></h2>
              <p class="card-text mb-0">Menga tayinlangan</p>
              <small class="text-secondary">Bugungi vazifalar</small>
            </div>
            <div class="avatar bg-light-danger p-50 m-0">
              <div class="avatar-content">
                <i data-feather="calendar" class="font-medium-5"></i>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-lg-2 col-sm-6 col-12">
      <a href="/account/task_list/list/to_me" class="text-secondary">
        <div class="card">
          <div class="card-header">
            <div>
              <h2 class="font-weight-bolder mb-0"><?= number_format($all["amount"], 0, "", " ") ?></h2>
              <p class="card-text mb-0">Menga tayinlangan</p>
              <small class="text-secondary">Ochiq vazifalar</small>
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
    <div class="col-lg-2 col-sm-6 col-12">
      <a href="/account/task_list/list/to_me_comp" class="text-secondary">
        <div class="card">
          <div class="card-header">
            <div>
              <h2 class="font-weight-bolder mb-0"><?=number_format( $completed["amount"], 0, "", " ") ?></h2>
              <p class="card-text mb-0">Menga tayinlangan</p>
              <small class="text-secondary">Tugallanganlar</small>
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
    <div class="col-lg-2 col-sm-6 col-12">
      <a href="/account/task_list/list/by_me" class="text-secondary">
        <div class="card">
          <div class="card-header">
            <div>
              <h2 class="font-weight-bolder mb-0"><?=number_format( $by_me["amount"], 0, "", " ") ?></h2>
              <p class="card-text mb-0">Men tayinlagan</p>
              <small class="text-secondary">Ochiq vazifalar</small>
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
    <div class="col-lg-2 col-sm-6 col-12">
      <a href="/account/task_list/list/by_me_comp" class="text-secondary">
        <div class="card">
          <div class="card-header">
            <div>
              <h2 class="font-weight-bolder mb-0"><?= number_format($by_me_com["amount"], 0, "", " ") ?></h2>
              <p class="card-text mb-0">Men tayinlagan</p>
              <small class="text-secondary">Tugallanganlar</small>
            </div>
            <div class="avatar bg-light-success p-50 m-0">
              <div class="avatar-content">
                <i data-feather="award" class="font-medium-5"></i>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>
  </div>
</section>