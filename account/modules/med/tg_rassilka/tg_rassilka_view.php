<?

if ($link_type=='single'){
    if ($_GET['page']=='tg_rassilka' AND $_SESSION['page_action_cc']=='list'){require 'tg_rassilka_list.php';}
    
    //if ($_SESSION['page_action_cc']=='rsm_list'){require 'md_users_rsm_list.php';}
    //if ($_SESSION['page_action_cc']=='mpd_list'){require 'md_users_mpd_list.php';}
    //if ($_SESSION['page_action_cc']=='trm_list'){require 'md_users_trm_list.php';}
    //if ($_SESSION['page_action_cc']=='detail'){require 'md_users_detail.php';}
    }
    
    if ($link_type=='multi'){
    if ($_GET['page']=='tg_rassilka' AND $_GET['page_action']=='list'){require 'tg_rassilka_list.php';}
    
    //if ($_GET['page']=='teacher_list' AND $_GET['page_action']=='rsm_list'){require 'md_users_rsm_list.php';}
    //if ($_GET['page']=='teacher_list' AND $_GET['page_action']=='trm_list'){require 'md_users_trm_list.php';}
    //if ($_GET['page']=='teacher_list' AND $_GET['page_action']=='detail'){require 'md_users_detail.php';}
    }
?>