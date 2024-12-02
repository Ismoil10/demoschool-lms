<!--PAGE ACTION CONTROL-->
<?
if ($link_type=='single'){
if ($_SESSION['page_action_cc']=='list'){require 'student_list_list.php';}
if ($_SESSION['page_action_cc']=='section'){require 'teacher_page/student_list_section.php';}

//if ($_SESSION['page_action_cc']=='rsm_list'){require 'md_users_rsm_list.php';}
//if ($_SESSION['page_action_cc']=='mpd_list'){require 'md_users_mpd_list.php';}
//if ($_SESSION['page_action_cc']=='trm_list'){require 'md_users_trm_list.php';}
}

if ($link_type=='multi'){
if ($_GET['page']=='student_list' AND $_GET['page_action']=='list' AND $_SESSION["user"]["role_id"] != "4"){require 'student_list_list.php';}
if ($_GET['page']=='student_list' AND $_GET['page_action']=='detail' AND $_SESSION["user"]["role_id"] != "4"){require 'student_list_detail.php';}
if ($_GET['page']=='student_list' AND $_GET['page_action']=='section'){require 'teacher_page/student_list_section.php';}
//if ($_GET['page']=='teacher_list' AND $_GET['page_action']=='rsm_list'){require 'md_users_rsm_list.php';}
//if ($_GET['page']=='teacher_list' AND $_GET['page_action']=='trm_list'){require 'md_users_trm_list.php';}
//if ($_GET['page']=='teacher_list' AND $_GET['page_action']=='detail'){require 'md_users_detail.php';}
}
?>