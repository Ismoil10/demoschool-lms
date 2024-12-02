<?require $_SERVER["DOCUMENT_ROOT"].'/core/backend.php';?>
<?
if ($_FILES["avatar"]["error"] != UPLOAD_ERR_NO_FILE) {
    $image = db::file_upload("avatar", "uploads");
}

if ($image['sts'] == 'ok') {
    echo json_encode(['url' => $image['url']]);
}

if ($_FILES["file"]["error"] != UPLOAD_ERR_NO_FILE) {
    $files = db::file_upload("file", "uploads");
}

if ($files['sts'] == 'ok') {
    echo json_encode(['url' => $files['url']]);
}

//echo '<pre>'; print_r($_FILES); echo '</pre>';
?>
