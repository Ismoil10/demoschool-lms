
<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Banner section</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/account/main_page/list">Asosiy menyu</a>
                                </li>
                                <li class="breadcrumb-item active">Banner section
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
                                <h4 class="card-title">Banner section list</h4>
                                <div class="dt-action-buttons text-right">
                                    <div class="dt-buttons d-inline-flex">
                                        <button class="dt-button create-new btn btn-primary mr-1" type="button" data-toggle="modal" onclick="add_modal()">Yangi qo'shish</button>
                                        <button class="dt-button create-new btn btn-warning btn-icon" type="button" data-toggle="modal" data-target="#filter1"><i data-feather="filter"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-datatable">
                                <table class="d_tab dt-responsive table" id="customtable">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>#</th>
                                            <th>Sarlavha</th>
                                            <th>Rasm</th>
                                            <th>Redirect</th>
                                            <th>Harakat</th>
                                        </tr>
                                    </thead>
                                    <? foreach (db::arr("SELECT * FROM banner_section") as $v) : ?>
                                        <tr>
                                            <td></td>
                                            <td><?=$v['id']?></td>
                                            <td><?=$v['title']?></td>
                                            <td class="custom-img"><img src="<?="https://demoschool.senet.uz".$v['image_url'] ?>"  style="height: 60px;"></td>
                                            <td><?=$v['redirect_url']?></td>
                                            <td>
                                                <!--<div class="circle"></div>-->
                                                <button class="btn btn-sm btn-primary" onclick="editBanner(<?= $v['id'] ?>)"><i data-feather="edit"></i></button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteBanner(<?= $v['id'] ?>)"><i data-feather="trash"></i></button>
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
    .custom-img {
        max-width: auto;
        max-height: auto;
    }    
</style>

<? require "modules/med/banner_section/banner_section_js.php"; ?>
<? require "modules/med/banner_section/banner_section_modal.php"; ?>