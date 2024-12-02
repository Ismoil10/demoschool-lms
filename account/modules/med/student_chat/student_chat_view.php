
<?php

if ($link_type=='single'){
if ($_SESSION['page_action_cc']=='list'){require 'student_chat_list.php';}
if ($_SESSION['page_action_cc']=='detail'){require 'student_chat_detail.php';}
}
?>
        
<?
if ($link_type=='multi'){
if ($_GET['page']=='student_chat' AND $_GET['page_action']=='list'){require 'student_chat_list.php';}
if ($_GET['page']=='student_chat' AND $_GET['page_action']=='detail'){require 'student_chat_detail.php';}
}
?>