<?
$now = date("Y-m-d H:i:s");

if (isset($_POST['addBanner'])) {

    if ($_FILES["image"]["error"] != UPLOAD_ERR_NO_FILE) {
        $file = db::file_upload("image", "uploads");
    }

    //$img = "https://demoschool.senet.uz/" . $file['url'];
    $img = $file['url'];
    $banner = $_POST['banner_title'];

    db::query("INSERT INTO `banner_section` (`title`, `image_url`) VALUES ('$banner', '$img')");

    LocalRedirect("index.php");
}

if (isset($_POST['editBanner'])) {
    $id = $_POST['bannId'];

    $select_url = db::arr_s("SELECT * FROM `banner_section` WHERE id='$id'");

    if ($_FILES["editImage"]["error"] != UPLOAD_ERR_NO_FILE) {
        $file = db::file_upload("editImage", "uploads");
        $select_url['url'] = $file['url'];
    }

    //$edit_img = "https://demoschool.senet.uz/" . $select_url['url'];
    $edit_img = $select_url['url'];

    $update_img = ",`image_url`='$edit_img'";

    $editBanner = $_POST['edit_title'];

    db::query("UPDATE `banner_section` SET `title`='$editBanner' $update_img WHERE `id`='$id'");

    LocalRedirect("index.php");
}

if (isset($_POST['deleteSubmit'])) {

    $delete = db::query("DELETE FROM `banner_section` WHERE `id` = '$_POST[bannerID]'");

    LocalRedirect("index.php");
}

?>

<!-- Add modal -->

<div class="modal fade text-left" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" id="addForm" enctype="multipart/form-data">
            <div class="modal-content">
                <? //echo '<pre>'; print_r($id); echo '</pre>'; 
                ?>
                <div class="modal-header">
                    <h4 class="modal-title" id="addModalLabel1">Banner</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-1">
                                <label class="label-form">Sarlavha</label><br>
                                <input type="text" class="form-control" name="banner_title">
                            </div>
                            <div class="custom-file mb-2">
                                <input type="file" name="image" class="custom-file-input" onchange="loadFile(event)">
                                <label class="custom-file-label" for="customFile">Rasimni tanlang</label>
                            </div>
                            <div class="center-image"><img id="output" width="60%" alt="" align="center"></div>
                            <script>
                                var loadFile = function(event) {
                                    var reader = new FileReader();
                                    reader.onload = function() {
                                        var output = document.getElementById('output');
                                        output.src = reader.result;
                                    };
                                    reader.readAsDataURL(event.target.files[0]);
                                };
                            </script>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                    <button type="submit" form="addForm" name="addBanner" class="btn btn-primary">Qo'shish</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit -->

<div class="modal fade text-left" id="editModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel1" aria-hidden="true">

</div>

<!-- Delete -->

<div class="modal fade text-left" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModal">O'chirish</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post" id="deleteForm">
                <div class="modal-body">
                    <input type="hidden" name="bannerID">
                    <div class="row">
                        <div class="col-md-12 p-1 mt-1">
                            <h4>Ushbu bannerni o'chirib tashlamoqchimisiz?</h4>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor Qilish</button>
                    <button type="submit" class="btn btn-primary" form="deleteForm" name="deleteSubmit">O'chirish</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .center-image {
        margin-top: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>