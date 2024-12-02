<? require $_SERVER['DOCUMENT_ROOT'] . '/core/backend.php'; ?>
<?

$select_lesson = db::arr("SELECT * FROM student_lessons WHERE ID = '$_POST[item_id]'");

?>

<div class="modal-dialog modal-lg" role="document">
        <form method="POST" id="editForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addModalLabel1">Mavzuni o'zgartirish</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?
                    //echo '<pre>';
                    //print_r($_POST);
                    //echo '</pre>'; 
                    ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <? foreach($select_lesson as $v):?>
                            <input type="hidden" value="<?=$v['id']?>" name="module_id">
                            <label for="standart-select">Turi</label>
                            <div class="select mb-3">
                                <select class="form-select" name="edit_type" style="width: 300px;">
                                    <option value="0">None</option>
                                    <option value="Learn" <? if($v['type'] == 'Learn'){ echo 'selected';}?>>Learn</option>
                                    <option value="Practice" <? if($v['type'] == 'Practice'){ echo 'selected';}?>>Practice</option>
                                </select>
                            </div>
                            <div style="display: block;">
                                <div class="mb-3">
                                    <label class="label-form">Tartib</label><br>
                                    <input name="module_order" type="number" class="form-control" value="<?=$v['order']?>">
                                </div>
                                <div class="mb-3">
                                    <label class="label-form">Mavzu</label>
                                    <textarea name="edit_title" cols="30" rows="10" class="form-control summernote" style="height: 80px;"><?=$v['title']?></textarea>
                                </div>
                            </div>
                            <? endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                    <button type="submit" form="editForm" name="editLesson" class="btn btn-primary">Saqlash</button>
                </div>
            </div>
        </form>
    </div>