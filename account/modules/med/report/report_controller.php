<?

if ($_GET['page']=='report' AND $_GET['page_action']=='list'){
	$_SESSION['page_cc'] = 'report';
	
	if ($_SESSION['user']['role_id'] !='4') {
    $_SESSION['page_action_cc'] = 'list'; 
  }
    
    // if ($_SESSION['user']['role_id']=='3') {
    // $_SESSION['page_action_cc'] = 'rsm_list';
    // LocalRedirect('/account/report/rsm_list');

    // }
    
    // if ($_SESSION['user']['role_id']=='4') {
    // $_SESSION['page_action_cc'] = 'mpd_list';
    // LocalRedirect('/account/report/mpd_list');
    // }
    
    // if ($_SESSION['user']['role_id']=='7') {
    // $_SESSION['page_action_cc'] = 'trm_list'; 
    // LocalRedirect('/account/report/trm_list');
    // }
    
    if ($link_type=='single'){
      LocalRedirect('index.php');
    }
}
	
	if ($_GET['page']=='report' AND $_GET['page_action']=='detail'){
	$_SESSION['page_cc'] = 'report';
	$_SESSION['page_action_cc'] = 'detail';
	
	if ($link_type=='single'){LocalRedirect('index.php');}
	
	}
