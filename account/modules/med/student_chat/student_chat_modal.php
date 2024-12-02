<?

$user = db::arr("SELECT * FROM student_students");


?>

<div class="modal fade text-left" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addModalLabel1">Qo'shish</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" id="addForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?
                            //echo '<pre>'; print_r($user); echo '</pre>'; 
                            ?>
                            <label for="standart-select">Talabalar</label>
                            <div class="select mb-2">
                                <select class="select2 form-select" id="relatedId" name="student_id" style="width: 200px;">
                                    <? foreach ($user as $v) : ?>
                                        <option value="<?= $v['id'] ?>"><?= $v['username'] ?></option>
                                    <? endforeach; ?>
                                </select>
                            </div>
                            <div class="custom-file mb-2" id="imgUp">
                                <input type="file" id="getAvatar" name="avatar" class="custom-file-input" onchange="loadImage(event)">
                                <label class="custom-file-label" for="customFile">Rasimni tanlang</label>
                            </div>
                            <div class="center-image"><img id="output1" width="60%" alt="" align="center"></div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                <button onclick="createUser(event)" class="btn btn-primary">Qo'shish</button>
            </div>
        </div>
    </div>
</div>
<style>
    .center-image {
        border-radius: 50%;
        overflow: hidden;
        margin-top: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .center-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>
<script>
    var loadImage = async function(event) {
        const selectPhoto = document.getElementById("getAvatar");
        const photo = selectPhoto.files[0];

        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('output1');
            output.src = reader.result;

        };

        reader.readAsDataURL(photo);
        //reader.readAsDataURL(event.target.files[0]);
    };





</script>