<?

if ($_GET['page']=='banner_section' AND $_GET['page_action']=='list'){
	$_SESSION['page_action_cc'] = 'list';
	$_SESSION['page_cc'] = 'banner_section';
	
	if ($link_type=='single'){LocalRedirect('index.php');}
	
	}
	
	
if ($_GET['page']=='banner_section' AND $_GET['page_action']=='detail'){
	$_SESSION['page_cc'] = 'banner_section';
	$_SESSION['page_action_cc'] = 'detail';
	$_SESSION['item_id'] = $_GET['item_id'];
	
	if ($link_type=='single'){LocalRedirect('index.php');}
	
	}	
	
?>