<?require $_SERVER["DOCUMENT_ROOT"].'/core/backend.php';?>
<?if ($_POST['img_file']!=NULL){

$_FILES['img_file'] = $_POST['img_file'];

$q = db::file_upload($_POST['img_file'],'uploads');
if ($q['sts']=='ok'){echo $q['url'];}}


if ($_FILES["img_file"]["error"] != UPLOAD_ERR_NO_FILE) {
    $file = db::file_upload("img_file", "uploads");
}
$img_src = [
    "file" => $file["url"]
];

$image = json_encode($img_src);

$insert = db::query("INSERT INTO `student_questions` (`question`) VALUES ('$image')");
?>
