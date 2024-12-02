<? require $_SERVER['DOCUMENT_ROOT'] . '/core/backend.php'; ?>
<?
$banner_id = $_POST['banner_id'];

$banner = db::arr_s("SELECT * FROM banner_section WHERE id = '$banner_id'");

?>

<div class="modal-dialog modal-lg" role="document">
    <form method="POST" id="editForm" enctype="multipart/form-data">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel1">Tahrirlash</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?
                // echo '<pre>';
                // print_r($banner_id);
                // echo '</pre>'; 
                ?>
                <div class="row">
                    <div class="col-lg-12">
                    <input type="hidden" value="<?=$banner['id']?>" name="bannId">
                        <div class="mb-1">
                            <label class="label-form">Sarlavha</label><br>
                            <input type="text" class="form-control" value="<?=$banner['title']?>" name="edit_title">
                        </div>
                        <div class="custom-file mb-2">
                            <input type="file" name="editImage" class="custom-file-input" onchange="editFile(event)">
                            <label class="custom-file-label" for="customFile">Rasimni tanlang</label>
                        </div>
                        <div class="center-image"><img id="output1" width="60%" alt="" src="<?=$banner['image_url']?>" align="center"></div>
                        <script>
                            var editFile = function(event) {
                                var reader = new FileReader();
                                reader.onload = function() {
                                    var output = document.getElementById('output1');
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
                <button type="submit" form="editForm" name="editBanner" class="btn btn-primary">Saqlash</button>
            </div>
        </div>
    </form>
</div>

<style>
    .center-image {
        margin-top: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>