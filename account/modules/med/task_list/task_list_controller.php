<?

if ($_GET['page'] == 'task_list' and $_GET['page_action'] == 'list') {
	$_SESSION['page_cc'] = 'task_list';

	if ($_SESSION['user']['role_id'] !== '6') {
		$_SESSION['page_action_cc'] = 'list';
	}


	if ($link_type == 'single') {
		LocalRedirect('index.php');
	}
}

if ($_GET['page'] == 'task_list' and $_GET['page_action'] == 'detail') {
	$_SESSION['page_cc'] = 'task_list';
	$_SESSION['page_action_cc'] = 'detail';

	if ($link_type == 'single') {
		LocalRedirect('index.php');
	}
}
?>