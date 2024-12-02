<? require $_SERVER['DOCUMENT_ROOT'] . '/core/backend.php'; ?>

<?

$id = $_POST['homework_id'];


$homework = db::arr_s("SELECT * FROM student_question_practice WHERE id = '$id'");

?>

<div class="modal-dialog modal-lg" role="document">
    <form method="POST" id="editForm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel1">Rad etish</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?
            //    echo '<pre>';
            //    print_r($lesson_access);
            //    echo '</pre>'; 
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <input type="hidden" value="<?=$id?>" name="reject_id">
                        <div style="display: block;">
                            <div class="mb-3">
                                <label class="label-form">Baholash</label><br>
                                <input type="number" class="form-control" value="<?=$homework['score']?>" name="score">
                            </div>
                            <div class="mb-3">
                                <label class="label-form">Sabab</label><br>
                                <textarea name="reason" class="form-control"><?=$homework['reason']?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                <button type="submit" form="editForm" name="rejectSubmit" class="btn btn-primary">Saqlash</button>
            </div>
        </div>
    </form>
</div>