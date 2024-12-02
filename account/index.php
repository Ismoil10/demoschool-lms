<?


$tmp_name = 'med';
$link_type = 'multi';
$kpi_type = 'user';
?>

<? require $_SERVER["DOCUMENT_ROOT"] . '/core/backend.php';
$_SESSION['tmp_name'] = $tmp_name;

?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/core/template_" . $tmp_name . "/header.php"); ?>

<? ini_set('display_errors', 0); ?>
<?
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";


?>

<? if (isset($_GET['page'])) {
	$_SESSION['current_page'] = $_SERVER['REQUEST_URI'];
	if ($_SESSION['back_url'] == '') {
		$_SESSION['back_url'] = $_SESSION['current_page'];
	}
} ?>
<!-- CONTROLLER -->
<? foreach ($_SESSION['user']['access'] as $k => $v) {
	if(file_exists('modules/' . $tmp_name . '/' . $k . '/' . $k . '_controller.php')){
		require_once 'modules/' . $tmp_name . '/' . $k . '/' . $k . '_controller.php';
	}
} ?>


<? if ($_GET['page'] == 'edit_profile' and $_SESSION['user']['id'] > 0) {
	$_SESSION['page_cc'] = 'edit_profile';
	//Localredirect('index.php');
} ?>

<!-- AUTH RULES -->
<? if ($_GET['logout'] == 'yes') {
	session_destroy();
	Localredirect('index.php');
}
if ($_SESSION['user']['id'] == NULL) {
	$_SESSION['page_cc'] = 'login';
} ?>

<!-- LAYOUT MANAGER -->

<? if ($_SESSION['back_url'] != $_SESSION['current_page']) {
	$_SESSION['back_url'] = $_SESSION['current_page'];
} ?>

<? foreach ($_POST as $k => $v) {
	$_POST[$k] = str_replace("'", "\'", $v);
} ?>

<? if ($_SESSION['page_cc'] == 'login') {
	require_once 'modules/' . $tmp_name . '/login/login_view.php';
}
if ($_SESSION['page_cc'] == 'edit_profile') {
	require_once 'modules/' . $tmp_name . '/edit_profile/edit_profile_view.php';
}
foreach ($_SESSION['user']['access'] as $k => $v) {
	if ($_SESSION['page_cc'] == $k AND file_exists('modules/' . $tmp_name . '/' . $k . '/' . $k . '_view.php')) {
		require_once 'modules/' . $tmp_name . '/' . $k . '/' . $k . '_view.php';
	}
} ?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/core/template_" . $tmp_name . "/footer.php");?> 
