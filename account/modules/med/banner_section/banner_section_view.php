
<?php

if ($link_type=='single'){
if ($_SESSION['page_action_cc']=='list'){require 'banner_section_list.php';}
if ($_SESSION['page_action_cc']=='detail'){require 'banner_section_detail.php';}
}
?>
        
<?
if ($link_type=='multi'){
if ($_GET['page']=='banner_section' AND $_GET['page_action']=='list'){require 'banner_section_list.php';}
if ($_GET['page']=='banner_section' AND $_GET['page_action']=='detail'){require 'banner_section_detail.php';}
}
?>