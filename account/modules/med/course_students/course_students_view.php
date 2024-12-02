
<?php

if ($link_type=='single'){
if ($_SESSION['page_action_cc']=='list'){require 'course_students_list.php';}
if ($_SESSION['page_action_cc']=='detail'){require 'course_students_detail.php';}
}
?>
        
<?
if ($link_type=='multi'){
if ($_GET['page']=='course_students' AND $_GET['page_action']=='list'){require 'course_students_list.php';}
if ($_GET['page']=='course_students' AND $_GET['page_action']=='detail'){require 'course_students_detail.php';}
}
?>