<?

if ($_GET['page'] == 'student_homework' and $_GET['page_action'] == 'list') {
	$_SESSION['page_cc'] = 'student_homework';

	if ($_SESSION['user']['role_id'] == '1' or $_SESSION['user']['role_id'] == '5' or $_SESSION['user']['role_id'] == '4') {
		$_SESSION['page_action_cc'] = 'list';
	}


	if ($link_type == 'single') {
		LocalRedirect('index.php');
	}
}

if ($_GET['page'] == 'student_homework' and $_GET['page_action'] == 'detail') {
	$_SESSION['page_cc'] = 'student_homework';
	$_SESSION['page_action_cc'] = 'detail';

	if ($link_type == 'single') {
		LocalRedirect('index.php');
	}
}
