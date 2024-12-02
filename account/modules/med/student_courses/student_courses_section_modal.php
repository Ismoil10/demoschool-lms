<?
if (isset($_POST['addSubmit'])) {

    $data = [
        'order' => $_POST['order_num'],
        'text' => $_POST['value_text'],
    ];

    $arr = [];

    foreach ($data['order'] as $k => $v) {
        if ($v !== '') {

            $arr[] = array(
                'order' => $v,
                'value' => $data['text'][$k]
            );
        }
    }

    foreach ($arr as $id => $option) {
        $options[$id] = ["id" => $id + 1, "order" => $option['order'], "value" => $option["value"]];
    }

    foreach ($options as $v) {
        if ($_POST['answer_point'] == $v['id']) {
            $id = $v['id'];
            $answer = $v['value'];
            $success = "Lesson solved. Good job!";
        }
    }


    $file_text = [
        'options' => $options,
        'answer_id' => $id,
        'hint' => $answer,
        'file_name' => $_POST['file_name'],
        'file_text' => filter_input(INPUT_POST, "file_text", FILTER_SANITIZE_ADD_SLASHES),
        'success_message' => $success
    ];

    $json = json_encode($file_text);

    //Selected-drag

    $drag_data = [
        'order' => $_POST['order_num2'],
        'text' => $_POST['value_text2'],
    ];

    $array = [];

    foreach ($drag_data['order'] as $k => $v) {
        if ($v !== '') {

            $array[] = array(
                'order' => $v,
                'value' => $drag_data['text'][$k]
            );
        }
    }

    foreach ($array as $id => $option) {
        $drag[$id] = ["id" => $id + 1, "order" => $option['order'], "value" => $option["value"]];
    }

    $drag_text = [
        'file_name' => $_POST['file_name2'],
        'file_text' => filter_input(INPUT_POST, "drag_text", FILTER_SANITIZE_ADD_SLASHES),
        'answer' => $_POST['answer_name'],
        'options' => $drag
    ];

    $json2 = json_encode($drag_text);

    //Input text

    $input_text = [
        'file_name' => $_POST['file_name3'],
        'file_text' => filter_input(INPUT_POST, "input_text", FILTER_SANITIZE_ADD_SLASHES)
    ];

    $json3 = json_encode($input_text);

    $type = $_POST['type_n'];
    $order = $_POST['order_number'];
    $tryF = $_POST['file_try_number'];
    $tryD = $_POST['drag_try_number'];
    $tryP = $_POST['input_try_number'];
    $module_id = $_SESSION['item_id'];
    //$fileNames = $_POST['fileNames'];

    if ($_FILES["video_file"]["error"] != UPLOAD_ERR_NO_FILE) {
        $file = db::file_upload("video_file", "uploads");
    }elseif($_POST['fileNames'] != null and $_FILES["video_file"]["error"] == UPLOAD_ERR_NO_FILE){
        $file = ['url' => $_POST['fileNames']];
    }

    if ($_FILES["pdf_file"]["error"] != UPLOAD_ERR_NO_FILE) {
        $pdf = db::file_upload("pdf_file", "uploads");
    }

    $school = "https://demoschool.senet.uz";

    $video_title = filter_input(INPUT_POST, "video_title", FILTER_SANITIZE_ADD_SLASHES);

    if ($pdf['url'] != null) {

        $video_src = [
            "title" => $video_title,
            "video" => $school . $file['url'],
            "file" => $school . $pdf['url']
        ];
    } else {
        $video_src = [
            "title" => $video_title,
            "video" => $school . $file['url']
        ];
    }

    if ($pdf['url'] != null) {
        $url = [
            "title" => $video_title,
            "video" => $_POST['video_link'],
            "file" => $school . $pdf['url']
        ];
    } else {
        $url = [
            "title" => $video_title,
            "video" => $_POST['video_link']
        ];
    }

    $text = $_POST['text1'];
    $question_text = $_POST['question_text'];
    $question_text2 = $_POST['question_text2'];
    $question_text3 = $_POST['question_text3'];

    $sentence = "\\\input";

if ($order != null and $text != "") {

        $info = db::query("INSERT INTO `student_questions`
(`type`,
`order_number`,  
`question`,
`lesson_id`)
VALUES
('$type',
'$order',
'$text',
'$module_id')");
}

if ($order != null and ($_POST['fileNames'] != null or $_FILES["video_file"]["error"] != UPLOAD_ERR_NO_FILE or $_POST['video_link'] != null)) {

if ($_POST['fileNames'] != "" or $_POST['video_link'] != "" or ($_FILES["video_file"]["error"] != UPLOAD_ERR_NO_FILE and $_POST['video_link'] != "")) {
    $vid = $file['url'];
    $link = json_encode($video_src);
}else{
    $vid = $_POST['video_link'];
    $link = json_encode($url);
}

$video = db::query("INSERT INTO `student_questions`
(`type`,
`order_number`,  
`question`,
`lesson_id`,
`other`)
VALUES
('$type',
'$order',
'$vid',
'$module_id',
'$link')");
    }
//strpos($_POST['file_text'], $sentence) !== false ____ OLD -> and $_POST['file_text'] !== ""
if ($order != null and $question_text != "") {

$selected_file = db::query("INSERT INTO `student_questions` 
(`type`, 
`order_number`, 
`try_number`,
`question`, 
`lesson_id`,
`other`) 
VALUES
('$type',
'$order',
'$tryF',
'$question_text',
'$module_id',
'$json')");
    }


//strpos($_POST['drag_text'], $sentence) !== false

    if ($order != null and $question_text2 != "" and $_POST['drag_text'] !== "") {

        $selected_drag = db::query("INSERT INTO `student_questions` 
(`type`, 
`order_number`, 
`try_number`,
`question`, 
`lesson_id`,
`other`) 
VALUES
('$type',
'$order',
'$tryD',
'$question_text2',
'$module_id',
'$json2')");
    }

    if ($order != null and $question_text3 != "") {

        $selected_input = db::query("INSERT INTO `student_questions` 
(`type`, 
`order_number`, 
`try_number`,
`question`, 
`lesson_id`,
`other`) 
VALUES
('$type',
'$order',
'$tryP',
'$question_text3',
'$module_id',
'$json3')");
    }
    LocalRedirect("index.php");
}


if (isset($_POST['editSubmit'])) {
    $edit_data = [
        'order' => $_POST['edit_order_num'],
        'text' => $_POST['edit_value_text']
    ];

    $arr = [];

    foreach ($edit_data['order'] as $k => $v) {
        if ($v !== '') {

            $arr[] = array(
                'order' => $v,
                'value' => $edit_data['text'][$k]
            );
        }
    }

    foreach ($arr as $id => $option) {
        $options[$id] = ["id" => $id + 1, "order" => $option['order'], "value" => $option["value"]];
    }

    foreach ($options as $v) {
        if ($_POST['edit_answer_point'] == $v['id']) {
            $id = $v['id'];
            $edit_answer = $v['value'];
            $edit_success = "Lesson solved. Good job!";
        }
    }

    //Selected-drag

    $edit_drag_data = [
        'order' => $_POST['edit_order_num2'],
        'text' => $_POST['edit_value_text2'],
    ];

    $array = [];

    foreach ($edit_drag_data['order'] as $k => $v) {
        if ($v !== '') {

            $array[] = array(
                'order' => $v,
                'value' => $edit_drag_data['text'][$k]
            );
        }
    }

    foreach ($array as $id => $option) {
        $drag[$id] = ["id" => $id + 1, "order" => $option['order'], "value" => $option["value"]];
    }


    $edit_order = $_POST['edit_order1'];
    $editTryF = $_POST['edit_try_file'];
    $editTryD = $_POST['edit_try_drag'];
    $editTryP = $_POST['edit_try_input'];
    $edit_type = $_POST['edit_type'];
    $edit_file = $_POST['edit_file_name'];
    $edit_file_text = filter_input(INPUT_POST, "edit_file_text", FILTER_SANITIZE_ADD_SLASHES);
    $edit_file2 = $_POST['edit_file_name2'];
    $edit_file_text2 = filter_input(INPUT_POST, "edit_file_text2", FILTER_SANITIZE_ADD_SLASHES);
    $edit_input = $_POST['edit_input_name'];
    $edit_file_text3 = filter_input(INPUT_POST, "edit_file_text3", FILTER_SANITIZE_ADD_SLASHES);
    $edit_answer_text = filter_input(INPUT_POST, "edit_answer", FILTER_SANITIZE_ADD_SLASHES);

    $edit_text1 = $_POST['edit_text1'];
    $edit_question1 = $_POST['edit_question1'];
    $edit_question2 = $_POST['edit_question2'];
    $edit_question3 = $_POST['edit_question3'];

    $edit2 = [
        'options' => $options,
        'answer_id' => $id,
        'hint' => $edit_answer,
        'file_name' => $edit_file,
        'file_text' => $edit_file_text,
        'success_message' => $edit_success
    ];
    $edit_json = json_encode($edit2);


    $edit_drag_text = [
        'file_name' => $edit_file2,
        'file_text' => $edit_file_text2,
        'answer' => $edit_answer_text,
        'options' => $drag
    ];
    $edit_json2 = json_encode($edit_drag_text);

    $edit_input_text = [
        'file_name' => $edit_input,
        'file_text' => $edit_file_text3,
    ];
    $edit_json3 = json_encode($edit_input_text);

    $select_f = db::arr_s("SELECT * FROM `student_questions` WHERE `id` = '$_POST[question_id]'");
    $file_data = json_decode($select_f['other'], true);


if ($_FILES["edit_video_file"]["error"] !== UPLOAD_ERR_NO_FILE) {
    $editFile = db::file_upload("edit_video_file", "uploads");
    $upload = $editFile['url'];
} elseif ($_POST['editFileNames'] != "" and $_FILES["edit_video_file"]["error"] == UPLOAD_ERR_NO_FILE) {
    $upload = $_POST['editFileNames'];
} else {
    $upload = $select_f['question'];
}

if ($_FILES["edit_pdf"]["error"] !== UPLOAD_ERR_NO_FILE) {
    $editPdf = db::file_upload("edit_pdf", "uploads");
    $uploadPdf = $editPdf['url'];
} else {
    $uploadPdf = $file_data['file'];
}

$videoSource = json_decode($select_f['other'], true);
$title = addslashes($_POST['edit_video_title']);

$school = "https://demoschool.senet.uz";

if(($_FILES["edit_video_file"]["error"] !== UPLOAD_ERR_NO_FILE or $_POST['editFileNames'] != "" or $_POST['edit_video_link'] != "") and $_FILES["edit_pdf"]["error"] == UPLOAD_ERR_NO_FILE){
    if($_POST['edit_video_link'] != ""){
    $update_url = [
        "title" => $title,
        "video" => $_POST['edit_video_link'],
        "file" => $file_data['file']
    ];
    }else{
    $update_src = [
        "title" => $title,
        "video" => $school.$upload,
        "file" => $file_data['file']
    ];
}
}

if($_FILES["edit_pdf"]["error"] !== UPLOAD_ERR_NO_FILE and ($_FILES["edit_video_file"]["error"] == UPLOAD_ERR_NO_FILE or $_POST['editFileNames'] == "" or $_POST['edit_video_link'] == "")){
    $update_src = [
        "title" => $title,
        "video" => $school.$select_f['question'],
        "file" => $school.$uploadPdf
    ];
}

if($_FILES["edit_pdf"]["error"] !== UPLOAD_ERR_NO_FILE and ($_FILES["edit_video_file"]["error"] !== UPLOAD_ERR_NO_FILE or $_POST['editFileNames'] != "" or $_POST['edit_video_link'] != "")){
    if($_POST['edit_video_link'] != null){
        $update_src = [
            "title" => $title,
            "video" => $_POST['edit_video_link'],
            "file" => $school.$uploadPdf
        ];
        }else{
        $update_src = [
            "title" => $title,
            "video" => $school.$upload,
            "file" => $school.$uploadPdf
        ];
    }
}


    //Info
if ($edit_order != null and $edit_text1 != null) {

$update_info = db::query("UPDATE `student_questions` SET
`type` = '$edit_type',
`order_number` = '$edit_order',
`question` = '$edit_text1'
WHERE `id` = '$_POST[question_id]'");
}
    //Video


if ($edit_order != null and ($_POST['edit_video_link'] != "" or $_FILES["edit_video_file"]["error"] != UPLOAD_ERR_NO_FILE or $select_f['question'] != null or $_POST['editFileNames'] != null)) {

if ($_POST['edit_video_link'] == "" or $_FILES["edit_video_file"]["error"] != UPLOAD_ERR_NO_FILE or $_POST['editFileNames'] != "") {
    $updateVid = ", `question` = '$upload',";
    $link = json_encode($update_src);
} elseif ($_POST['edit_video_link'] != "" or $_FILES["edit_video_file"]["error"] != UPLOAD_ERR_NO_FILE) {
    $updateVid = ", `question` = '$_POST[edit_video_link]',";
    $link = json_encode($update_url);
} else {
    $updateVid = ", `question` = '$select_f[question]',";
    $link = json_encode($update_src);
}

$update_video = db::query("UPDATE `student_questions` SET
`type` = '$edit_type',
`order_number` = '$edit_order'
$updateVid
`other` = '$link'
WHERE `id` = '$_POST[question_id]'");
    }

    //File

if ($edit_order != null and $edit_question1 != null) {

$update_file = db::query("UPDATE `student_questions` SET
`type` = '$edit_type',
`order_number` = '$edit_order',
`try_number` = '$editTryF',
`question` = '$edit_question1',
`other` = '$edit_json'
WHERE `id` = '$_POST[question_id]'");
    }

    //Drag

    if ($edit_order != null and $edit_question2 != null and $edit_file_text2 !== "") {

        $update_file = db::query("UPDATE `student_questions` SET
`type` = '$edit_type',
`order_number` = '$edit_order',
`try_number` = '$editTryD',
`question` = '$edit_question2',
`other` = '$edit_json2'
WHERE `id` = '$_POST[question_id]'");
    }

    // Input file

    if ($edit_order != null and $edit_question3 != null) {

        $update_file = db::query("UPDATE `student_questions` SET
`type` = '$edit_type',
`order_number` = '$edit_order',
`try_number` = '$editTryP',
`question` = '$edit_question3',
`other` = '$edit_json3'
WHERE `id` = '$_POST[question_id]'");
    }

    LocalRedirect("index.php");
}

if (isset($_POST['deleteSubmit'])) {

    $delete_id = $_POST['deleteID'];

    db::query("DELETE FROM `student_question_log` WHERE `question_id` = '$delete_id'");

    db::query("DELETE FROM `student_questions` WHERE `id` = '$delete_id'");

    LocalRedirect("index.php");
}

$note = "Agar savolga javob kiritish (input) uchun joy tashlamoqchi bo'lsangiz, namunadan foydalaning: 'movie quote - \\\input \\\input \\\input'";

//End Question

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
<div class="modal fade text-left" id="addModalSection" tabindex="-1" role="dialog" aria-labelledby="addModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" id="addForm" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addModalLabel1">Yangi savol</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?
                    // echo "<pre>";
                    // print_r($delete);
                    // echo "</pre>";
                    ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-6 select mb-3">
                                    <label for="standart-select">Turi</label>
                                    <select class="form-control" name="type_n" id="selectID">
                                        <option value="0">None</option>
                                        <option value="info">Informatsiya blogi</option>
                                        <option value="video">Video yuklash</option>
                                        <option value="selected-file">Test yuklash</option>
                                        <option value="selected-drag">Bo’sh qoldirilgan joyni to’ldirish</option>
                                        <option value="input-file">Fayl yuklash</option>
                                    </select>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="label-form">Tartib</label><br>
                                    <input type="number" class="form-control" name="order_number" required>
                                </div>
                            </div>
                            <div id="click_info" style="display: none;">
                                <div class="mb-3">
                                    <label class="label-form">Matn</label>
                                    <textarea name="text1" id="infoText" cols="30" rows="10" class="form-control summernote" style="height: 80px;"></textarea>
                                </div>
                            </div>
                            <div id="click_video" style="display: none;">                               
                                <div class="mb-3">
                                    <label class="form-label">Sarlavha</label>
                                    <input type="text" class="form-control" name="video_title">
                                </div>
                                <div class="mb-3">
                                    <label for="standart-select">Fayl</label>
                                    <select class="form-control select2" name="fileNames" id="getFiles">
                                        <option></option>
                                        <? foreach($allFiles as $v): ?>
                                            <? if($v != 'empty'): ?>
                                        <option value="<?=$v?>"><?=$v?></option>
                                            <? endif; ?>
                                        <? endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Video</label>
                                    <div class="custom-file mb-2">
                                        <input type="file" name="video_file" id="videoInput" class="custom-file-input" onchange="handleFile(this)" accept="video/*">
                                        <label class="custom-file-label" for="customFile">Videoni tanlang</label>
                                    </div>
                                    <div id="centerVideo">
                                        <input type="hidden" name="videoId">
                                        <video id="videoPlayer" style="display: none;" width="320" height="240" controls>
                                            <source id="output" type="video/mp4">
                                        </video>
                                    </div>
                                </div>
                                <div class="mb-3" id="link" style="display: block;">
                                    <label class="form-label">Link</label>
                                    <textarea name="video_link" cols="30" rows="10" class="form-control" style="height: 80px;"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Fayl</label>
                                    <div class="custom-file mb-2">
                                        <input type="file" name="pdf_file" id="videoInput" onchange="getFile(this)" class="custom-file-input" accept="pdf/*">
                                        <label class="custom-file-label" for="customFile">Faylni tanlang</label>
                                    </div>
                                    <div id="centerFile">
                                        <div id="loadFile" style="display: none;">
                                            <embed id="outputPdf" width="300" height="400" type="application/pdf">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                $(document).ready(function() {
                                     file_content = function() {
                                        const content = $('#file_library').html();
                                        $('#file_content').html(content);
                                    }
                                
                                });

                                const getLink = $("#link");
                                //getLink.hide();

                                function handleFile(input) {
                                    const file = input.files[0];
                                    const label = input.nextElementSibling;
                                    const maxSize = 105 * 1024 * 1024;

                                    if (file.size > maxSize) {
                                        alert("Video hajmi 105mb dan ko'p bo'lmasligi kerak!");
                                        document.getElementById('videoInput').value = "";
                                        console.log(files[0]);
                                        return;
                                    } else {

                                        const reader = new FileReader();

                                        reader.onload = function(event) {
                                            const contents = event.target.result;
                                            console.log("File loaded:", contents);
                                        };
                                    }

                                    const output = document.getElementById('output');
                                    const videoPlayer = document.getElementById('videoPlayer');
                                    const centerVideo = document.getElementById('centerVideo');

                                    if (file) {

                                        centerVideo.className = "center-file";
                                        videoPlayer.style.display = "block";
                                        label.innerText = file.name;

                                        const videoReader = new FileReader();
                                        videoReader.onload = function(event) {

                                            output.src = event.target.result;
                                            videoPlayer.load();
                                            console.log(event.target.result);
                                        };
                                        videoReader.readAsDataURL(file);
                                    }
                                }

                                function getFile(input) {
                                    const file = input.files[0];
                                    const output = document.getElementById('outputPdf');
                                    const centerDoc = document.getElementById('centerFile');
                                    const loadDoc = document.getElementById('loadFile');

                                    if (file) {
                                        centerDoc.className = "center-file"
                                        loadDoc.style.display = "block"

                                        const reader = new FileReader();
                                        reader.onload = function(event) {
                                            output.src = event.target.result;
                                        };
                                        reader.readAsDataURL(file);
                                        
                                    }
                                }

                                
                            </script>
                            <div id="click_file" style="display: none;">
                                <!--<div class="mb-3">
                                    <label class="label-form">Try</label><br>
                                    <input class="form-control try-file" id="tryF" type="number" name="file_try_number">
                                </div>-->
                                <div class="mb-3">
                                    <label class="label-form">Fayl nomi</label><br>
                                    <input class="form-control" type="text" name="file_name">
                                </div>
                                <div class="mb-3">
                                    <label class="label-form">Savol</label>
                                    <textarea name="question_text" cols="10" rows="10" class="form-control summernote" style="max-height: 30px;"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="label-form">Fayl matni</label>
                                    <textarea id="textArea" name="file_text" placeholder="<?= $note ?>" cols="30" rows="10" class="form-control" style="max-height: 100px;"></textarea>
                                    <div id="error-alert" class="alert alert-danger mt-1 alert-validation-msg" style="display: none;" role="alert">
                                        <div class="alert-body">
                                            <i data-feather="info" class="mr-50 align-middle"></i>
                                            <span><strong>Xatolik</strong>. Fayl matniga kamida 1ta javob uchun joy <strong>"\\input"</strong> kiritilishi shart.</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="col-lg-12" id="listDayWorkHours">
                                        <table id="tableMovedDays" class="table table-hover" style="font-size:14px">
                                            <thead style="color: #333;">
                                                <tr>
                                                    <th>Tartib</th>
                                                    <th>Javob nomi</th>
                                                    <th>To'g'ri javob</th>
                                                    <th>Harakat</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                        <button id="btnAddTableRow" type="button" class="btn btn-primary btn-sm" onclick="add_table_row(this.value)" value="0"><i data-feather="plus"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div id="click_drag" style="display: none;">
                                <!--<div class="mb-3">
                                    <label class="label-form">Try</label><br>
                                    <input class="form-control try-file" id="tryD" type="number" name="drag_try_number">
                                </div>-->
                                <div class="mb-3">
                                    <label class="label-form">Fayl nomi</label><br>
                                    <input class="form-control" type="text" name="file_name2">
                                </div>
                                <div class="mb-3">
                                    <label class="label-form">Savol</label>
                                    <textarea name="question_text2" cols="10" rows="10" class="form-control summernote" style="max-height: 30px;"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="label-form">Fayl matni</label>
                                    <textarea name="drag_text" id="dragArea" placeholder="<?= $note ?>" cols="30" rows="10" class="form-control" style="max-height: 100px;"></textarea>
                                    <div id="drag-alert" class="alert alert-danger mt-1 alert-validation-msg" style="display: none;" role="alert">
                                        <div class="alert-body">
                                            <i data-feather="info" class="mr-50 align-middle"></i>
                                            <span><strong>Xatolik</strong>. Fayl matniga kamida 1ta javob uchun joy <strong>"\\input"</strong> kiritilishi shart.</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="label-form">Javob nomi</label><br>
                                    <input class="form-control" type="text" name="answer_name">
                                </div>
                                <div class="mb-3">
                                    <div class="col-lg-12" id="listDayWorkHours">
                                        <table id="tableMovedDays2" class="table table-hover" style="font-size:14px">
                                            <thead style="color: #333;">
                                                <tr>
                                                    <th>Tartib</th>
                                                    <th>Javoblar nomi</th>
                                                    <th>Harakat</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                        <button id="btnAddTableRow" type="button" class="btn btn-primary btn-sm" onclick="add_answer_row(this.value)" value="0"><i data-feather="plus"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div id="input_file" style="display: none;">
                                <!--<div class="mb-3">
                                    <label class="label-form">Try</label><br>
                                    <input class="form-control try-file" id="tryP" type="number" name="input_try_number">
                                </div>-->
                                <div class="mb-3">
                                    <label class="label-form">Fayl nomi</label><br>
                                    <input class="form-control" type="text" name="file_name3">
                                </div>
                                <div class="mb-3">
                                    <label class="label-form">Savol</label>
                                    <textarea name="question_text3" cols="10" rows="10" class="form-control summernote" style="max-height: 30px;"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="label-form">Fayl matni</label>
                                    <textarea name="input_text" id="inputArea" cols="30" rows="10" class="form-control" style="max-height: 100px;"></textarea>
                                    <div id="input-alert" class="alert alert-danger mt-1 alert-validation-msg" style="display: none;" role="alert">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Bekor qilish</button>
                    <button type="button" id="addButton" form="addForm" name="addSubmit" class="addButton btn btn-primary">Qo'shish</button>
                </div>
            </div>
        </form>
    </div>
</div>
<style>
    .center-file {
        margin-top: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .video-list {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        width: 760px;
        padding: 0;
        margin: 0;
    }

    .rectangle {
        border: 2px;
        padding: 10px;
        border-radius: 20px;
    }

    .overlay {
        position: block;
        bottom: 0;
        background: rgb(0, 0, 0);
        background: rgba(0, 0, 0, 0.5);
        /* Black see-through */
        color: #f1f1f1;
        width: 300px;
        transition: .5s ease;
        opacity: 0;
        color: white;
        font-size: 10px;
        padding: 5px;
        text-align: center;
        max-height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .video-container:hover .overlay {
        opacity: 1;
    }

    .videoSource {

        width: auto;
        height: auto;
        max-height: 110px;
        max-width: 150px;
        margin: 5px;
    }

    .circle {
        position: block;
        top: 8px;
        right: 8px;
        height: 15px;
        width: 15px;
        border: 1.5px solid #fff;
        border-radius: 7.5px;
        background: rgba(0, 0, 0, 0);
    }

    .selected .overlay .circle {
        border: 0px;
        background: #00BCD4;

    }

    .selected .overlay {
        opacity: 1;
    }
</style>
<script>
    const fileInput = document.getElementById('textArea');
    const dragInput = document.getElementById('dragArea');
    const inputFile = document.getElementById('inputArea');
    const infoButton = $('.addButton');
    const fileButton = $('.addButton');
    const dragButton = $('.addButton');
    const inputButton = $('.addButton');
    const fileAlert = $('#error-alert');
    const dragAlert = $('#drag-alert');
    const inputAlert = $('#input-alert');

    checkInfoInput = function() {
        infoButton.attr('type', 'submit');
    }

    checkFileInput = function() {
        // let sentence = fileInput.value;
        // let importantText = "\\\\input";

        // if (sentence.includes(importantText)) {
        //     fileButton.attr('type', 'submit');
        // } else {
        //     fileButton.attr('type', 'button');
        //     fileAlert.show();
        // }
        fileButton.attr('type', 'submit');
    }

    checkDragInput = function() {
        // let word = dragInput.value;
        // let importantText = "\\\\input";

        // if (word.includes(importantText)) {
        //     dragButton.attr('type', 'submit');
        // } else {
        //     dragButton.attr('type', 'button');
        //     dragAlert.show();
        // }
        dragButton.attr('type', 'submit');
    }

    checkInput = function() {

        inputButton.attr('type', 'submit');

    }
</script>

<!-- Edit Modal -->

<div class="modal fade text-left" id="editModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel1" aria-hidden="true">

</div>

<!-- DELETE MODAL -->

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
                    <input type="hidden" name="deleteID">
                    <div class="row">
                        <div class="col-md-12 p-1 mt-1">
                            <h4>Ushbu savolni o'chirib tashlamoqchimisiz?</h4>
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
    $(document).ready(function() {

        add_table_row = function() {
            document.querySelector('#tableMovedDays tbody').insertAdjacentHTML("beforeend", `<tr>
                <td>
                    <input type="number" style="max-width: 50px;" class="form-control" name="order_num[]">
                </td>
                <td>
                    <input type="text" class="form-control" name="value_text[]">
                </td>
                <td>
                    <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="answer_point" />
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteInputRow(this)">${feather.icons['trash'].toSvg()}</button>
                </td>
            </tr>`);

            $("#tableMovedDays tbody input[type=radio]").off();
            $("#tableMovedDays tbody input[type=radio]").on("input", onCheck)
        }

        deleteInputRow = (elem) => {
            elem.parentNode.parentNode.parentNode.removeChild(elem.parentNode.parentNode);
            onCheck();
        }
        onCheck = () => {
            document.querySelectorAll("#tableMovedDays tbody input[type=radio]").forEach((elem, index) => {
                elem.value = index + 1;
            });
        }

        add_answer_row = function() {
            document.querySelector('#tableMovedDays2 tbody').insertAdjacentHTML("beforeend", `<tr>
                <td>
                    <input type="number" style="max-width: 50px;" class="form-control" name="order_num2[]">
                </td>
                <td>
                    <input type="text" class="form-control" name="value_text2[]">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteInputRow(this)">${feather.icons['trash'].toSvg()}</button>
                </td>
            </tr>`);

            $("#tableMovedDays2 tbody input[type=radio]").off();
        }

        deleteInputRow = (elem) => {
            elem.parentNode.parentNode.parentNode.removeChild(elem.parentNode.parentNode);
            onCheck();
        }
    });


    $('.summernote').summernote({
        toolbar: [
            ['style', ['style']],
            ['fontsize', ['fontsize']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['insert', ['hr']],
            ['table', ['table']],
            ['insert', ['link', 'picture']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        height: 200,
        minHeight: null,
        maxHeight: 200,
        focus: true,
        onImageUpload: function(files, editor, welEditable) {
            sendFile(files[0], editor, welEditable);
        }
    });
</script>