<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<? require $_SERVER['DOCUMENT_ROOT'] . '/core/backend.php'; ?>

<?
$single = db::arr_s("SELECT * FROM student_questions 
WHERE id = '$_POST[item_id]' AND `type` IN ('info', 'video', 'selected-file', 'selected-drag', 'input-file')");

$info = db::arr_s("SELECT * FROM student_questions 
WHERE id = '$_POST[item_id]' AND `type` = 'info'");

$video = db::arr_s("SELECT * FROM student_questions 
WHERE id = '$_POST[item_id]' AND `type` = 'video'");

$file = db::arr_s("SELECT * FROM student_questions 
WHERE id = '$_POST[item_id]' AND `type` = 'selected-file'");

$drag = db::arr_s("SELECT * FROM student_questions 
WHERE id = '$_POST[item_id]' AND `type` = 'selected-drag'");

$input = db::arr_s("SELECT * FROM student_questions 
WHERE id = '$_POST[item_id]' AND `type` = 'input-file'");

$video_arr = json_decode($video['other'], true);

$file_arr = json_decode($file['other'], true);

$drag_arr = json_decode($drag['other'], true);

$input_arr = json_decode($input['other'], true);

$note = "Agar savolga javob kiritish (input) uchun joy tashlamoqchi bo'lsangiz, namunadan foydalaning: 'movie quote - \\\input \\\input \\\input'";


$folderPath = $_SERVER['DOCUMENT_ROOT']."/uploads";

function getFilesFromFolders($dir, $baseDir) {
    $filesArray = [];


    if (is_dir($dir)) {

        $dirContent = scandir($dir);
    
        foreach ($dirContent as $item) {

            if ($item === '.' || $item === '..') {
                continue;
            }
        
            $path = $dir . DIRECTORY_SEPARATOR . $item;
        
            if (is_dir($path)) {
                $filesArray = array_merge($filesArray, getFilesFromFolders($path, $baseDir));
            } else {

                $relativePath = str_replace($baseDir, '', $path);
                $filesArray[] = $relativePath;
            }
        }
    }

    return $filesArray;
}
$domain = $_SERVER['DOCUMENT_ROOT'];
$allFiles = getFilesFromFolders($folderPath, $domain);

?>
<div class="modal-dialog modal-lg" role="document">
    <form method="POST" id="editForm" enctype="multipart/form-data">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addModalLabel1">Tahrirlash</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?
                //echo '<pre>';
                //print_r($drag);
                //echo '</pre>';
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <input type="hidden" name="question_id" value="<?= $single['id'] ?>">
                        <div class="row">
                            <div class="col-lg-6 select mb-3">
                                <label for="standart-select">Turi</label>
                                <select class="form-control" name="edit_type" id="edit_selectID">
                                    <option value="0">None</option>
                                    <option value="info" <? if($single['type'] == "info"){ echo "selected";}?>>Informatsiya blogi</option>
                                    <option value="video" <? if($single['type'] == "video"){ echo "selected";}?>>Video yuklash</option>
                                    <option value="selected-file" <? if($single['type'] == "selected-file"){ echo "selected";}?>>Test yuklash</option>
                                    <option value="selected-drag" <? if($single['type'] == "selected-drag"){ echo "selected";}?>>Bo’sh qoldirilgan joyni to’ldirish</option>
                                    <option value="input-file" <? if($single['type'] == "input-file"){ echo "selected";}?>>Fayl yuklash</option>
                                </select>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="label-form">Tartib</label><br>
                                <input type="number" class="form-control" name="edit_order1" value="<?= $single['order_number'] ?>">
                            </div>
                        </div>
                        <div id="edit_click_info" style="display: none;">
                            <div class="mb-3">
                                <label class="label-form">Matn</label>
                                <textarea name="edit_text1" cols="30" rows="10" class="form-control edit-summernote" style="height: 80px;"><? if ($info != "empty") {
                                                                                                                                                echo $info['question'];
                                                                                                                                            } ?></textarea>
                            </div>
                        </div>
                        <div id="edit_click_video" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Sarlavha</label>
                                <input type="text" class="form-control" value="<?= $video_arr['title'] ?>" name="edit_video_title">
                            </div>
                            <div class="mb-3">
                                    <label for="standart-select">Fayl</label>
                                    <select class="form-control select2" name="editFileNames" id="getFiles">
                                        <option></option>
                                        <? foreach($allFiles as $v): ?>
                                            <? if($v != 'empty'): ?>
                                        <option value="<?=$v?>" <? if($v == $video['question']){ echo 'selected';}?>><?=$v?></option>
                                            <? endif; ?>
                                        <? endforeach; ?>
                                    </select>
                                </div>
                            <div class="mb-3">
                                <label class="form-label">Video</label>
                                <div class="mb-2">
                                    <input type="file" name="edit_video_file" id="videoEditInput" class="form-control" onchange="handleEditFile(this)" accept="video/*">
                                </div>
                                <div class="center-file" id="centerVideo">
                                    <video id="editVideoPlayer" width="320" height="240" controls>
                                        <source id="editOutput" src="<?= $video['question'] ?>" type="video/mp4">
                                    </video>
                                </div>
                            </div>
                            <div class="mb-3" id="link" style="display: block;">
                                <label class="form-label">Link</label>
                                <textarea name="edit_video_link" cols="30" rows="10" class="form-control" style="height: 80px;"><? if (strpos($video['question'], "school")) {
                                                                                                                                    echo $video['question'];
                                                                                                                                } else {
                                                                                                                                    echo "";
                                                                                                                                } ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Fayl</label>
                                <div class="custom-file mb-2">
                                    <input type="file" name="edit_pdf" class="form-control" onchange="editGetDoc(this)" accept="pdf/*">
                                </div>
                                <div id="centerDoc">
                                    <div id="loadDoc">
                                        <embed id="outputDoc" src="<?= $video_arr['file'] ?>" width="300" height="400" type="application/pdf">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="edit_click_file" style="display: none;">
                            <!--<div class="mb-3">
                                <label class="label-form">Try</label><br>
                                <input class="form-control edit-try-file" id="editTryF" type="number" value="<? if ($file != "empty") {
                                                                                                                    echo $file['try_number'];
                                                                                                                } ?>" name="edit_try_file">


                            </div>-->
                            <div class="mb-3">
                                <label class="label-form">Fayl nomi</label><br>
                                <input class="form-control" type="text" name="edit_file_name" value="<?= $file_arr['file_name'] ?>">
                            </div>
                            <div class="mb-3">
                                <label class="label-form">Savol</label>
                                <textarea name="edit_question1" cols="10" rows="10" class="form-control edit-summernote" style="max-height: 30px;"><? if ($file != "empty") {
                                                                                                                                                        echo $file['question'];
                                                                                                                                                    } ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="label-form">Fayl matni</label>
                                <textarea name="edit_file_text" id="editFileArea" placeholder="<?= $note ?>" cols="20" rows="10" class="form-control edit-summernote" style="max-height: 100px;"><?= $file_arr['file_text'] ?></textarea>
                                <div id="editFileAlert" class="alert alert-danger mt-1 alert-validation-msg" style="display: none;" role="alert">
                                    <div class="alert-body">
                                        <i data-feather="info" class="mr-50 align-middle"></i>
                                        <span><strong>Xatolik</strong>. Fayl matniga kamida 1ta javob uchun joy <strong>"\\input"</strong> kiritilishi shart.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="col-lg-12" id="listDayWorkHours">
                                    <table id="tableMovedDays1" class="table table-hover" style="font-size:14px">
                                        <thead style="color: #333;">
                                            <tr>
                                                <th>Tartib</th>
                                                <th>Javob nomi</th>
                                                <th>To'g'ri javob</th>
                                                <th>Harakat</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <? foreach ($file_arr['options'] as $v) : ?>
                                                <tr>
                                                    <td>
                                                        <input type="number" style="max-width: 50px;" name="edit_order_num[]" class="form-control" value="<?= $v['order'] ?>">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" name="edit_value_text[]" value="<?= $v['value'] ?>">
                                                    </td>
                                                    <td>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="edit_answer_point" <? if ($v['id'] == $file_arr['answer_id']) {
                                                                                                                                        echo 'checked';
                                                                                                                                    } ?> />
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteInputRow(this)"><i data-feather="trash"></i></button>
                                                    </td>
                                                </tr>
                                            <? endforeach; ?>
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="add_row2(this.value)" value="0"><i data-feather="plus"></i></button>
                                </div>
                            </div>
                        </div>
                        <div id="edit_click_drag" style="display: none;">
                            <!--<div class="mb-3">
                                <label class="label-form">Try</label><br>
                                <input class="form-control edit-try" id="editTryD" type="number" value="<? if ($drag != "empty") {
                                                                                                            echo $drag['try_number'];
                                                                                                        } ?>" name="edit_try_drag">
                            </div>-->
                            <div class="mb-3">
                                <label class="label-form">Fayl nomi</label><br>
                                <input class="form-control" type="text" value="<?= $drag_arr['file_name'] ?>" name="edit_file_name2">
                            </div>
                            <div class="mb-3">
                                <label class="label-form">Savol</label>
                                <textarea name="edit_question2" cols="10" rows="10" class="form-control edit-summernote" style="max-height: 30px;"><? if ($drag != "empty") {
                                                                                                                                                        echo $drag['question'];
                                                                                                                                                    } ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="label-form">Fayl matni</label>
                                <textarea name="edit_file_text2" id="editDragArea" placeholder="<?= $note ?>" cols="20" rows="10" class="form-control edit-summernote" style="max-height: 100px;"><?= $drag_arr['file_text'] ?></textarea>
                                <div id="editDragAlert" class="alert alert-danger mt-1 alert-validation-msg" style="display: none;" role="alert">
                                    <div class="alert-body">
                                        <i data-feather="info" class="mr-50 align-middle"></i>
                                        <span><strong>Xatolik</strong>. Fayl matniga kamida 1ta javob uchun joy <strong>"\\input"</strong> kiritilishi shart.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="label-form">Javob nomi</label><br>
                                <input class="form-control" type="text" name="edit_answer" value="<?= $drag_arr['answer'] ?>">
                            </div>
                            <div class="mb-3">
                                <div class="col-lg-12" id="listDayWorkHours">
                                    <table id="tableDays" class="table table-hover" style="font-size:14px">
                                        <thead style="color: #333;">
                                            <tr>
                                                <th>Tartib</th>
                                                <th>Javob nomi</th>
                                                <th>Harakat</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <? foreach ($drag_arr['options'] as $v) : ?>
                                                <tr>
                                                    <td>
                                                        <input type="number" style="max-width: 50px;" class="form-control" value="<?= $v['order'] ?>" name="edit_order_num2[]">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="<?= $v['value'] ?>" name="edit_value_text2[]">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteInputRow2(this)"><i data-feather="trash"></i></button>
                                                    </td>
                                                </tr>
                                            <? endforeach; ?>
                                        </tbody>
                                    </table>
                                    <button id="btnAddTableRow" type="button" class="btn btn-primary btn-sm" onclick="add_row(this.value)" value="0"><i data-feather="plus"></i></button>
                                </div>
                            </div>
                        </div>
                        <div id="edit_click_input" style="display: none;">
                            <!--<div class="mb-3">
                                <label class="label-form">Try</label><br>
                                <input class="form-control edit-try" id="editTryP" type="number" value="<? if ($input != "empty") {
                                                                                                            echo $input['try_number'];
                                                                                                        } ?>" name="edit_try_input">
                            </div>-->
                            <div class="mb-3">
                                <label class="label-form">Fayl nomi</label><br>
                                <input class="form-control" type="text" value="<?= $input_arr['file_name'] ?>" name="edit_input_name">
                            </div>
                            <div class="mb-3">
                                <label class="label-form">Savol</label>
                                <textarea name="edit_question3" cols="10" rows="10" class="form-control edit-summernote" style="max-height: 30px;"><? if ($input != "empty") {
                                                                                                                                                        echo $input['question'];
                                                                                                                                                    } ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="label-form">Fayl matni</label>
                                <textarea name="edit_file_text3" id="editInputArea" placeholder="<?= $note ?>" cols="20" rows="10" class="form-control edit-summernote" style="max-height: 100px;"><?= $input_arr['file_text'] ?></textarea>
                                <div id="editInputAlert" class="alert alert-danger mt-1 alert-validation-msg" style="display: none;" role="alert">
                                    <div class="alert-body">
                                        <i data-feather="info" class="mr-50 align-middle"></i>
                                        <span><strong>Xatolik</strong>. Fayl matniga kamida 1ta javob uchun joy <strong>"\\input"</strong> kiritilishi shart.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                <button type="button" form="editForm" name="editSubmit" class="editButton btn btn-primary">Saqlash</button>
            </div>
        </div>
    </form>
</div>
<style>
    .center-file {
        margin-top: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .c1control {
        margin-top: 20px;
    }

    .c1body {
        height: 120px;
    }

    .custom-title {
        padding: 20px;
    }
</style>
<script>
    var fileArea = document.getElementById('editFileArea');
    var dragArea = document.getElementById('editDragArea');
    var inputArea = document.getElementById('editInputArea');
    var editInfoButton = $(".editButton");
    var editFileButton = $(".editButton");
    var editDragButton = $(".editButton");
    var editInputButton = $(".editButton");
    var editDragAlert = $('#editDragAlert');
    var editFileAlert = $('#editFileAlert');
    var editInputAlert = $('#editInputAlert');

    editInfoInput = function() {
        editInfoButton.attr("type", "submit");
    }

    editFileInput = function() {
        // let sentence = fileArea.value;
        // let importantText = "\\\\input";
        // if (sentence.includes(importantText)) {
        //     editFileButton.attr("type", "submit");

        // } else {
        //     editFileButton.attr("type", "button");
        //     editFileAlert.show();
        // }
        editFileButton.attr("type", "submit");
    }

    editDragInput = function() {
        // let word = dragArea.value;
        // let importantText = "\\\\input";

        // if (word.includes(importantText)) {
        //     editDragButton.attr("type", "submit");
        // } else {
        //     editDragButton.attr("type", "button");
        //     editDragAlert.show();
        // }
        editDragButton.attr("type", "submit");
    }

    editInput = function() {
        /*let wd = inputArea.value;
        let impText = "\\\\input";

        if (wd.includes(impText)) {
            editInputButton.attr("type", "submit");
        } else {
            editInputButton.attr("type", "button");
            editInputAlert.show();
        }*/
        editInfoButton.attr("type", "submit");
    }
</script>
<!-- Summernote -->
<script>
    $(document).ready(function() {
        $("#tableMovedDays1 tbody input[type=radio]").on("input", onEditCheck);
        add_row2 = function() {
            document.querySelector('#tableMovedDays1 tbody').insertAdjacentHTML("beforeend", `<tr>
                <td>
                    <input type="number" style="max-width: 50px;" class="form-control" name="edit_order_num[]">
                </td>
                <td>
                    <input type="text" class="form-control" name="edit_value_text[]">
                </td>
                <td>
                    <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="edit_answer_point" />
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteInputRow(this)">${feather.icons['trash'].toSvg()}</button>
                </td>
            </tr>`);

            $("#tableMovedDays1 tbody input[type=radio]").off();
            $("#tableMovedDays1 tbody input[type=radio]").on("input", onEditCheck);
        }

        deleteInputRow = (elem) => {
            elem.parentNode.parentNode.parentNode.removeChild(elem.parentNode.parentNode);
            onEditCheck();
        }

        function onEditCheck() {
            document.querySelectorAll("#tableMovedDays1 tbody input[type=radio]").forEach((elem, index) => {
                elem.value = index + 1;
            });
        }

        add_row = function() {
            document.querySelector('#tableDays tbody').insertAdjacentHTML("beforeend", `<tr>
                <td>
                    <input type="number" style="max-width: 50px;" class="form-control" name="edit_order_num2[]">
                </td>
                <td>
                    <input type="text" class="form-control" name="edit_value_text2[]">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteInputRow(this)">${feather.icons['trash'].toSvg()}</button>
                </td>
            </tr>`);

            $("#tableDays tbody input[type=radio]").off();
        }

        deleteInputRow2 = (elem) => {
            elem.parentNode.parentNode.parentNode.removeChild(elem.parentNode.parentNode);
            onCheck();
        }
    });
</script>
<script>
    //const getLink = $("#link");
    //getLink.hide();

    function handleEditFile(input) {

        const file = input.files[0];
        //const label = input.nextElementSibling;

        const maxSize = 105 * 1024 * 1024;

        if (file.size > maxSize) {
            alert("Video hajmi 105mb dan ko'p bo'lmasligi kerak!");
            document.getElementById('videoEditInput').value = "";
            console.log(files[0]);
            return;
        } else {

            const reader = new FileReader();

            reader.onload = function(event) {
                const contents = event.target.result;
                console.log("File loaded:", contents);
            };
            //getLink.show();
        }

        const output = document.getElementById('editOutput');
        const videoPlayer = document.getElementById('editVideoPlayer');
        //const centerVideo = document.getElementById('centerVideo');

        if (file) {

            centerVideo.className = "center-file"
            videoPlayer.style.display = "block";
            //label.innerText = file.name;

            const videoReader = new FileReader();
            videoReader.onload = function(event) {

                output.src = event.target.result;
                videoPlayer.load();
            };
            videoReader.readAsDataURL(file);
            console.log(videoReader);
        }
    }

    function editGetDoc(input) {
        const file = input.files[0];
        const outputDoc = document.getElementById('outputDoc');
        const centerDoc = document.getElementById('centerDoc');
        const loadDoc = document.getElementById('loadDoc');

        if (file) {

            //centerDoc.className = "center-file"
            centerDoc.style.display = "flex";
            loadDoc.style.display = "block"

            const reader = new FileReader();
            reader.onload = function(event) {
                outputDoc.src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    $(document).ready(function() {

    function handleSelectedOption(selectedValue) {
        const sections = {
            info: $("#edit_click_info"),
            video: $("#edit_click_video"),
            file: $("#edit_click_file"),
            drag: $("#edit_click_drag"),
            input: $("#edit_click_input")
        };

        for (let section in sections) {
            sections[section].hide();
        }

        $("#editTryF, #editTryD, #editTryP").prop("required", false);

        const buttonConfig = {
            info: { section: sections.info, buttonId: "editInfoButton", onClick: "editInfoInput()" },
            video: { 
                section: sections.video, 
                buttonId: "editVideoButton", 
                onClick: "editVideoInput()", 
                extra: function() {
                    if ($('#outputDoc').attr('src') && $('#outputDoc').attr('src') !== "about:blank") {
                        $('#centerDoc').addClass("center-file");
                    } else {
                        $('#centerDoc').hide();
                    }
                }
            },
            "selected-file": { 
                section: sections.file, 
                buttonId: "editFileButton", 
                onClick: "editFileInput()", 
                requiredField: "#editTryF"
            },
            "selected-drag": { 
                section: sections.drag, 
                buttonId: "editDragButton", 
                onClick: "editDragInput()", 
                requiredField: "#editTryD"
            },
            "input-file": { 
                section: sections.input, 
                buttonId: "editInputButton", 
                onClick: "editInput()", 
                requiredField: "#editTryP"
            }
        };

        const currentConfig = buttonConfig[selectedValue];
        if (currentConfig) {
            currentConfig.section.show(); 
            $(".editButton").attr({
                id: currentConfig.buttonId,
                type: "submit",
                onclick: currentConfig.onClick
            });

            if (currentConfig.requiredField) {
                $(currentConfig.requiredField).prop("required", true);
            }

            if (currentConfig.extra) currentConfig.extra();
        }
    }

    const selectedValue = $("#edit_selectID").val();
    handleSelectedOption(selectedValue);

    $("#edit_selectID").on("change", function() {
        handleSelectedOption($(this).val());
    });
});


</script>