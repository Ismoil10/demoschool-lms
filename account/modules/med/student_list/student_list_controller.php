<?

if ($_GET['page'] == 'student_list' and $_GET['page_action'] == 'list') {
	$_SESSION['page_cc'] = 'student_list';
	if ($_SESSION['user']['role_id'] !== '4') {
		$_SESSION['page_action_cc'] = 'list';
	}


	if ($link_type == 'single') {
		LocalRedirect('index.php');
	}
}

if ($_GET['page'] == 'student_list' and $_GET['page_action'] == 'detail') {
	$_SESSION['page_cc'] = 'student_list';
	if ($_SESSION['user']['role_id'] !== '4') {
		$_SESSION['page_action_cc'] = 'detail';
	}

	if ($link_type == 'single') {
		LocalRedirect('index.php');
	}
}
if ($_GET['page'] == 'student_list' and $_GET['page_action'] == 'section') {
	if (in_array($_SESSION["USER"]["ROLE_ID"], ["4", "1"])) {
		$_SESSION['page_cc'] = 'student_list';
		$_SESSION['page_action_cc'] = 'section';
	}
	if ($link_type == 'single') {
		LocalRedirect('index.php');
	}
}
if ($_GET['page'] == 'student_list' and $_GET['page_action'] == 'debtors') {
	$_SESSION['page_cc'] = 'student_list';
	$_SESSION["studentFilterType"] = "debtors";
	if ($_SESSION['user']['role_id'] !== '4') {
		LocalRedirect('/account/student_list/list');
	}
}
if ($_GET['page'] == 'student_list' and $_GET['page_action'] == 'activated_students') {
	$_SESSION['page_cc'] = 'student_list';
	$_SESSION['studentFilterType'] = 'activatedStudent';
	$_SESSION["studentFilterDate"] = date("Y-m-01") . " to " . date("Y-m-t");
	if ($_SESSION['user']['role_id'] !== '4') {
		LocalRedirect('/account/student_list/list');
	}
}
if ($_GET['page'] == 'student_list' and $_GET['page_action'] == 'active') {
	$_SESSION['page_cc'] = 'student_list';
	$_SESSION['studentFilterType'] = 'activeStudent';
	if ($_SESSION['user']['role_id'] !== '4') {
		LocalRedirect('/account/student_list/list');
	}
}
if ($_GET['page'] == 'student_list' and $_GET['page_action'] == 'demo_students') {
	$_SESSION['page_cc'] = 'student_list';
	$_SESSION["studentFilterType"] = "demoStudent";
	if ($_SESSION['user']['role_id'] !== '4') {
		LocalRedirect('/account/student_list/list');
	}
}
if ($_GET['page'] == 'student_list' and $_GET['page_action'] == 'freezedStudent') {
	$_SESSION['page_cc'] = 'student_list';
	$_SESSION["studentFilterType"] = "freezedStudent";
	$_SESSION["studentFilterDate"] = date("Y-m-01") . " to " . date("Y-m-t");
	if ($_SESSION['user']['role_id'] !== '4') {
		LocalRedirect('/account/student_list/list');
	}
}
