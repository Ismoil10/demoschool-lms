<?

if ($_GET['page']=='student_chat' AND $_GET['page_action']=='list'){
	$_SESSION['page_action_cc'] = 'list';
	$_SESSION['page_cc'] = 'student_chat';
	
	//role_id = 1,5 || role: "Администратор", "Генералный менеджер"
	if ($_SESSION['user']['role_id']=='1' OR $_SESSION['user']['role_id']=='5' OR $_SESSION['user']['role_id']=='4') {
	$_SESSION['page_action_cc'] = 'list'; }
	
	//role_id = 3 || role: "Региональный менеджер"
	if ($_SESSION['user']['role_id']=='3') {
	$_SESSION['page_action_cc'] = 'rsm_list'; }
	

	if ($link_type=='single'){LocalRedirect('index.php');}
	
	}
	
	
if ($_GET['page']=='student_chat' AND $_GET['page_action']=='detail'){
	$_SESSION['page_cc'] = 'student_chat';
	$_SESSION['page_action_cc'] = 'detail';
	$_SESSION['item_id'] = $_GET['item_id'];
	
	if ($link_type=='single'){LocalRedirect('index.php');}
	
	}	
	
?>