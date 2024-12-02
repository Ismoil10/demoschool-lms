
<?php

if ($link_type=='single'){
if ($_SESSION['page_action_cc']=='list'){require 'group_chat_list.php';}
if ($_SESSION['page_action_cc']=='detail'){require 'group_chat_detail.php';}
}
?>
        
<?
if ($link_type=='multi'){
if ($_GET['page']=='group_chat' AND $_GET['page_action']=='list'){require 'group_chat_list.php';}
if ($_GET['page']=='group_chat' AND $_GET['page_action']=='detail'){require 'group_chat_detail.php';}
}
?>