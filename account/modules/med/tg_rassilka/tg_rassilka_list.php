<!-- BEGIN: Content-->
<?

$select_rassilka = db::arr("SELECT * FROM `tg_rassilka`")

?>
<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Jo'natmalar</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/account/main_page/list">Asosiy menyu</a>
                                </li>
                                <li class="breadcrumb-item active">Jo'natmalar
                                </li>
                            </ol>
                        </div>
                        <? //echo '<pre>'; print_r($chat_id); echo '</pre>'; 
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <? //echo "<pre>"; print_r($_POST); echo "</pre>";  
        ?>
        <? //echo "<pre>"; print_r($_SESSION['sql_text']); echo "</pre>";  
        ?>

        <div class="content-body">
            <!-- Responsive Datatable -->
            <section id="responsive-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">Jo'natmalar ro'yhati</h4>
                                <div class="dt-action-buttons text-right">
                                    <div class="dt-buttons d-inline-flex">
                                        <button class="dt-button create-new btn btn-primary mr-1" type="button" data-toggle="modal" onclick="add_modal()">Yangi qo'shish</button>
                                        <button class="dt-button create-new btn btn-warning btn-icon" type="button" data-toggle="modal" data-target="#filter"><i data-feather="filter"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-datatable">
                                <table class="d_tab dt-responsive table" id="customtable">
                                    <thead>
                                        <? //echo '<pre>';
                                        //print_r($student_id);
                                        //echo '</pre>'; 
                                        ?>
                                        <tr>
                                            <th></th>
                                            <th>#</th>
                                            <th>Sana</th>
                                            <th>Tekst</th>
                                            <th>Rasm</th>
                                            <th>Harakat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <? foreach ($select_rassilka as $v) : ?>
                                            <? //$get_img = json_decode($v['FILE_URL'], true) ?>
                                            <tr>
                                                <td></td>
                                                <td><?= $v['ID']?></td>
                                                <td><?= $v['CREATED_DATE']?></td>
                                                <td><?= $v['TEXT']?></td>
                                                <td align="center" class="custom-img">
                                                    <img src="<?= $v['FILE_URL'] ?>" alt="" style="height: 60px;">
                                                </td>
                                                <td style="width: 100px;">
                                                <button class="btn btn-sm btn-success" onclick="view_modal('<?=$v['ID'];?>')" type="button"><i data-feather="eye"></i></button>
                                                <button class="btn btn-sm btn-danger" onclick="delete_modal('<?=$v['ID'];?>')" type="button"><i data-feather="trash-2"></i></button>
                                                </td>
                                            </tr>
                                        <? endforeach; ?>
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
    .custom-img {
        max-width: auto;
        max-height: auto;
    }
</style>

<? require "modules/med/tg_rassilka/tg_rassilka_js.php"; ?>
<? require "modules/med/tg_rassilka/tg_rassilka_modal.php"; ?>