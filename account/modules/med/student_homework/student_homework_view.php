<!--PAGE ACTION CONTROL-->
<?
if ($link_type=='single'){
if ($_SESSION['page_action_cc']=='list'){require 'student_homework_list.php';}
//if ($_SESSION['page_action_cc']=='detail'){require 'student_homework_detail.php';}
//if ($_SESSION['page_action_cc']=='section'){require 'homework_section.php';}
}
?>

<?
if ($link_type=='multi'){
if ($_GET['page']=='student_homework' AND $_GET['page_action']=='list'){require 'student_homework_list.php';}
//if ($_GET['page']=='student_homework' AND $_GET['page_action']=='detail'){require 'student_homework_detail.php';}
//if ($_GET['page']=='student_homework' AND $_GET['page_action']=='section'){require 'homework_section.php';}
}
?>