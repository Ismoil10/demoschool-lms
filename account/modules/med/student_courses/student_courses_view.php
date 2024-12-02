<?php

if ($link_type=='single'){
if ($_SESSION['page_action_cc']=='list'){require 'student_courses_list.php';}
if ($_SESSION['page_action_cc']=='detail'){require 'student_courses_detail.php';}
if (in_array($_SESSION['page_action_cc'], ['detail_part', 'learn', 'practice'])){require 'student_courses_detail_part.php';}
if (in_array($_SESSION['page_action_cc'], ['section', 'test', 'info'])){require 'student_courses_section.php';}
}
?>
        
<?
if ($link_type=='multi'){
if ($_GET['page']=='student_courses' AND $_GET['page_action']=='list'){require 'student_courses_list.php';}
if ($_GET['page']=='student_courses' AND $_GET['page_action']=='detail'){require 'student_courses_detail.php';}
if ($_GET['page']=='student_courses' AND in_array($_GET['page_action'], ['detail_part', 'learn', 'practice'])){require 'student_courses_detail_part.php';}
if ($_GET['page']=='student_courses' AND in_array($_GET['page_action'], ['section', 'test', 'info'])){require 'student_courses_section.php';}
}
?>