<!--PAGE ACTION CONTROL-->
<?
if ($link_type=='single'){
if ($_SESSION['page_action_cc']=='list'){require 'report_list.php';}
if ($_SESSION['page_action_cc']=='detail'){require 'report_detail.php';}

if ($_SESSION['page_action_cc']=='rsm_list'){require 'md_users_rsm_list.php';}
if ($_SESSION['page_action_cc']=='mpd_list'){require 'md_users_mpd_list.php';}
if ($_SESSION['page_action_cc']=='trm_list'){require 'md_users_trm_list.php';}
}

if ($link_type=='multi'){
if ($_GET['page']=='report' AND $_GET['page_action']=='list'){require 'report_list.php';}
if ($_GET['page']=='report' AND $_GET['page_action']=='detail'){require 'report_detail.php';}

//if ($_GET['page']=='md_users' AND $_GET['page_action']=='rsm_list'){require 'md_users_rsm_list.php';}
//if ($_GET['page']=='md_users' AND $_GET['page_action']=='trm_list'){require 'md_users_trm_list.php';}
//if ($_GET['page']=='md_users' AND $_GET['page_action']=='detail'){require 'md_users_detail.php';}
}
?>