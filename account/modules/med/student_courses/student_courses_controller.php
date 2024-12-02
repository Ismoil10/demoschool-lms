<?

if ($_GET['page'] == 'student_courses' and $_GET['page_action'] == 'list') {
	$_SESSION['page_action_cc'] = 'list';
	$_SESSION['page_cc'] = 'student_courses';

	//role_id = 1,5 || role: "Администратор", "Генералный менеджер"
	if ($_SESSION['user']['role_id'] == '1' or $_SESSION['user']['role_id'] == '5' or $_SESSION['user']['role_id'] == '4') {
		$_SESSION['page_action_cc'] = 'list';
	}

	//role_id = 3 || role: "Региональный менеджер"
	if ($_SESSION['user']['role_id'] == '3') {
		$_SESSION['page_action_cc'] = 'rsm_list';
	}

	/*
	if ($_SESSION['user']['role_id']=='4') {
	$_SESSION['page_action_cc'] = 'mpd_list'; }
	*/
	if ($link_type == 'single') {
		LocalRedirect('index.php');
	}
}

if ($_GET['page'] == 'student_courses' and $_GET['page_action'] == 'detail') {
	$_SESSION['page_cc'] = 'student_courses';
	$_SESSION['page_action_cc'] = 'detail';
	$_SESSION['item_id'] = $_GET['item_id'];

	if ($link_type == 'single') {
		LocalRedirect('index.php');
	}
}

if ($_GET['page'] == 'student_courses' and in_array($_GET['page_action'], ['detail_part', 'learn', 'practice'])) {
	$_SESSION['page_cc'] = 'student_courses';
	
	if($_GET['page_action'] == 'detail_part'){

	$_SESSION['page_action_cc'] = 'detail_part';

	}elseif($_GET['page_action'] == 'learn'){

	$_SESSION['page_action_cc'] = 'learn';
	
	}elseif($_SESSION['page_action_cc'] = 'practice'){
		
	$_SESSION['page_action_cc'] = 'practice';
	}

	$_SESSION['item_id'] = $_GET['item_id'];

	if ($link_type == 'single') {
		LocalRedirect('index.php');
	}
}
if ($_GET['page'] == 'student_courses' and in_array($_GET['page_action'], ['section', 'test', 'info'])) {
	
	$_SESSION['page_cc'] = 'student_courses';
	
	if($_SESSION['page_action_cc'] = 'section'){
		
		$_SESSION['page_action_cc'] = 'section';
	
	}elseif($_SESSION['page_action_cc'] = 'test'){
		
		$_SESSION['page_action_cc'] = 'test';

	}elseif($_SESSION['page_action_cc'] = 'info'){
		
		$_SESSION['page_action_cc'] = 'info';
	};

	$_SESSION['item_id'] = $_GET['item_id'];

	if ($link_type == 'single') {
		LocalRedirect('index.php');
	}
}
