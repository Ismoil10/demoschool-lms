<? date_default_timezone_set("Asia/Tashkent");
//error_reporting(E_ALL & ~E_NOTICE);
ob_start();
session_start();
//ini_set('error_reporting', 1);
ini_set('display_errors', 0);
ini_set("pcre.backtrack_limit", "5000000");
ini_set('memory_limit', '-1');


$_SESSION['link_type'] = $link_type;


require_once 'action_class.php';
require_once 'mail.php';
require_once 'js_core.php';

$path = '/core/lib/';
$path_f = '/core/lib/footer';
$path_h = '/core/lib/header';

$nl_template_path = '/core/template_nl/';
$es_template_path = '/core/template_evosoft/';
$ms_template_path = '/core/template_ms/';
$zn_template_path = '/core/template_zn/';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


if ($_SERVER['HTTP_HOST'] != 'demoschool.senet.uz') {
    exit();
}

class db
{


    static function conn()
    {
        $conn = new mysqli('localhost', '<username>', '<password>', '<database>');
        $conn->set_charset("utf8");
        if ($conn->connect_error) {
            die('Connection faield:' . $conn->connect_error);
        } else {
            $rs = $conn;
        }
        return $rs;
    }

    static function query($sql)
    {
        $conn = db::conn();
        if ($conn->query($sql) === TRUE) {
            $rs['stat'] = 'success';
            $rs['ID'] = $conn->insert_id;
        } else {
            $rs = $conn->error;
        }
        return $rs;
    }

    static function arr($sql)
    {
        $conn = db::conn();
        $q = $conn->query($sql);
        if ($q === FALSE) {
            $rs = $conn->error;
        }
        if ($q->num_rows > 0) {
            while ($row = $q->fetch_assoc()) {
                $rs[] = $row;
            }
        }
        if ($q->num_rows == 0) {
            $rs = 'empty';
        }
        return $rs;
    }

    static function arr_s($sql)
    {
        $conn = db::conn();
        $q = $conn->query($sql);
        if ($q === FALSE) {
            $rs = $conn->error;
        }
        if ($q->num_rows > 0) {
            while ($row = $q->fetch_assoc()) {
                $rs = $row;
            }
        }
        if ($q->num_rows == 0) {
            $rs = 'empty';
        }
        return $rs;
    }

    static function arr_by_id($sql)
    {
        //$sql = db::prefix($sql);
        $conn = db::conn();
        $q = $conn->query($sql);
        if ($q === FALSE) {
            $rs = $conn->error;
        }
        if ($q->num_rows > 0) {
            while ($row = $q->fetch_assoc()) {
                $rs[$row['ID']] = $row;
            }
        }
        if ($q->num_rows == 0) {
            $rs = 'empty';
        }
        return $rs;
    }

    static function auth_log_manager()
    {
        $pass = '7416414';
        $login = 'superadmin';
        if ($_POST['login'] == $login and $_POST['pass'] == $pass) {
            $rs = 'success';
            $data = ['ID' => 'admin', 'NAME' => 'super', 'SURNAME' => 'admin', 'POSITION' => 'superadmin', 'PHOTO_LINK' => ''];
            $_SESSION['user_manager']['id'] = $data['ID'];
            $_SESSION['user_manager']['name'] = $data['NAME'];
            $_SESSION['user_manager']['surname'] = $data['SURNAME'];
            $_SESSION['user_manager']['pos'] = $data['POSITION'];
            $_SESSION['user_manager']['photo'] = $data['PHOTO_LINK'];
            LocalRedirect('index.php?page=main');
        }
        return $rs;
    }

    static function auth_log()
    {

        $strings = array("+", " ", ")", "(");
        $login = str_replace($strings, "", $_POST['phone']);

        $data = db::arr_s("SELECT * FROM gl_sys_users WHERE LOGIN='$login' AND PASSWORD='$_POST[pass]' AND STATUS<>0");

        $data_block = db::arr_s("SELECT * FROM gl_sys_users WHERE LOGIN='$login' AND PASSWORD='$_POST[pass]'");

        if ($data_block['STATUS'] == '0') $rs = "notexist";

        if ($data != 'empty') {
            $user_access = db::arr_s("SELECT * FROM gl_sys_roles WHERE ID='$data[ROLE_ID]'");
            if ($user_access['ACCESS_JSON'] != 'full') {
                $access_array = json_decode($user_access['ACCESS_JSON'], TRUE);
            } else {
                $all_modules = db::arr("SELECT * FROM gl_sys_modules");
                foreach ($all_modules as $v) {
                    $access_array[$v['SYSTEM_NAME']] = 1;
                }
            }
            $data['DEFAULT_PAGE'] = $user_access['DEFAULT_PAGE'] . '&page_action=list';
            $rs = 'success';
            $_SESSION['USER'] = $data;
            $_SESSION['user']['id'] = $data['ID'];
            $_SESSION['user']['name'] = $data['NAME'];
            $_SESSION['user']['surname'] = $data['SURNAME'];
            $_SESSION['user']['login'] = $data['LOGIN'];
            $_SESSION['user']['role_id'] = $data['ROLE_ID'];
            $_SESSION['user']['photo'] = $data['PHOTO_URL'];
            $_SESSION['user']['podrazd_id'] = $data['PODRAZD_ID'];

            $_SESSION['user']['team_id'] = $data['TEAM_ID'];

            if ($data['ROLE_ID'] == '3') {
                $rsm_team_id = db::arr_s("SELECT ID FROM list_team WHERE MANAGER_ID='$data[ID]' AND STATUS<>0");
                $_SESSION['user']['team_id'] = $rsm_team_id['ID'];
            }

            if ($data['ROLE_ID'] == '6') {
                $lang = db::arr_s("SELECT LANG FROM student_list WHERE PHONE='$data[PHONE]'");
                $_SESSION['lang'] = $lang['LANG'];
            } else {
                $_SESSION['lang'] = 'uz';
            }

            $_SESSION['user']['status'] = $data['STATUS'];
            $_SESSION['user']['access'] = $access_array;
            $_SESSION['user']['shops'] = json_decode($data['USER_SHOPS']);
            $_SESSION['user']['menu'] = db::user_structure($data['ROLE_ID']);

            $_SESSION['oquvchi_id'] = $data['OQUVCHI_LIST_ID'];
            $_SESSION['tashkilot_id'] = $data['TASHKILOT_ID'];


            //if (db::count_item('mz_is_overed_xodim')!=''){$_SESSION['alert']='on';}


            if ($_SESSION['link_type'] == 'single') {
                LocalRedirect('index.php?page=' . $data['DEFAULT_PAGE']);
            }

            if ($_SESSION['link_type'] == 'multi') {

                $str = explode('&', $data['DEFAULT_PAGE'])[0];
                $_SESSION['page_cc'] = $str;
                $_SESSION['page_action_cc'] = 'list';


                LocalRedirect("index.php");
            }


        }
        return $rs;
    }


    function user_structure($role_id)
    {

        $user_access_data = db::arr_s("SELECT * FROM gl_sys_roles WHERE ID='$role_id'");
        $full_structure = db::arr("SELECT * FROM gl_sys_structure ORDER BY SORT");

        if ($user_access_data['ACCESS_JSON'] == 'full') {
            $rs = $full_structure;
        } else {
            foreach (json_decode($user_access_data['ACCESS_JSON']) as $k => $v) {
                $access_data_check[] = $k;
            }

            foreach ($full_structure as $k2 => $v2) {
                $data = json_decode($v2['DATA_JSON'], TRUE);
                if ($v2['TYPE'] == 'single') {
                    if (in_array($data['page_name'], $access_data_check)) {
                        $rs[] = $v2;
                    }
                }

                if ($v2['TYPE'] == 'group') {
                    $user_items = NULL;
                    foreach ($data['items'] as $v3) {
                        if (in_array($v3['page_name'], $access_data_check)) {
                            $user_items[] = $v3;
                        }
                    }
                    if ($user_items != NULL) {
                        $data['items'] = $user_items;
                        $v2['DATA_JSON'] = json_encode($data, JSON_UNESCAPED_UNICODE);
                        $rs[] = $v2;
                    }
                }
            }
        }

        return $rs;
    }

    static function file_upload($file_input_name, $folder_name)
    {
        $newFilename = $_SERVER['DOCUMENT_ROOT'] . '/' . $folder_name . '/';
        $uploadInfo = $_FILES[$file_input_name];
        $path_info = pathinfo($uploadInfo['name']);
        $ext = $path_info['extension'];
        //Перемещаем файл из временной папки в указанную
        $size = db::filesize_formatted($uploadInfo['size']);

        $q = db::query("INSERT INTO files (
	NAME, 
	FORMAT,
	SIZE) VALUES (
	'$uploadInfo[name]',
	'$ext',
	'$size');");
        $file_name = md5($q['ID']) . '.' . $ext;
        $url = '/' . $folder_name . '/' . $file_name;
        $upd = db::query("UPDATE files SET URL='$url' WHERE ID='$q[ID]'");

        if ($q['stat'] == 'success') {
            $newFilename = $newFilename . $file_name;
            if (!move_uploaded_file($uploadInfo['tmp_name'], $newFilename)) {
                db::query("DELETE FROM files WHERE ID='$q[ID]'");
                $rs = 'Не удалось осуществить сохранение файла';
            } else {
                $rs['url'] = $url;
                $rs['sts'] = 'ok';
            }
        }

        return $rs;
    }


    static function file_edit($file_input_name, $folder_name, $file_id)
    {
        $file_data = db::arr_s("SELECT * FROM FILES WHERE ID='$file_id'");
        unlink($_SERVER['DOCUMENT_ROOT'] . $file_data['URL']);


        $newFilename = $_SERVER['DOCUMENT_ROOT'] . '/' . $folder_name . '/';
        $uploadInfo = $_FILES[$file_input_name];
        $path_info = pathinfo($uploadInfo['name']);
        $ext = $path_info['extension'];
        //Перемещаем файл из временной папки в указанную
        $size = db::filesize_formatted($uploadInfo['size']);
        $file_name = md5($file_id) . '.' . $ext;
        $url = '/' . $folder_name . '/' . $file_name;
        $newFilename = $newFilename . $file_name;
        if (!move_uploaded_file($uploadInfo['tmp_name'], $newFilename)) {
            $rs = 'Не удалось осуществить сохранение файла';
        } else {
            $rs['upd'] = db::query("UPDATE files SET
	URL='$url',
	NAME = '$uploadInfo[name]', 
	FORMAT = '$ext',
	SIZE = '$size' WHERE ID='$file_id'");
            $rs['url'] = $url;
            $rs['sts'] = 'ok';
        }
        return $rs;
    }

    static function file_del($file_id, $file_url)
    {
        $file_data = db::arr_s("SELECT * FROM FILES WHERE ID='$file_id'");
        unlink($_SERVER['DOCUMENT_ROOT'] . $file_data['URL']);
        db::query("DELETE FROM files WHERE ID='$file_id'");
        return $rs;
    }


    static function filesize_formatted($size)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }

    static function count_item($page_name)
    {

        if ($page_name == 'mz_tasks') {
            $user_id = $_SESSION['user']['id'];
            $data = db::arr_s("SELECT COUNT(ID) as COUNT FROM mz_tasks WHERE ISPOLNITEL_ID='$user_id' AND STATUS='wait_doing'");
            if ($data != 'empty' and $data['COUNT'] != 0) {
                $rs = $data['COUNT'];
            }
        }

        if ($page_name == 'mz_is_overed_xodim') {
            $user_id = $_SESSION['user']['id'];
            $now = date('Y-m-d');
            $data = db::arr_s("SELECT COUNT(ID) as COUNT FROM mz_tasks WHERE ISPOLNITEL_ID='$user_id' AND STATUS='wait_doing' AND SROK<'$now'");
            if ($data != 'empty' and $data['COUNT'] != 0) {
                $rs = $data['COUNT'];
            }
        }

        return $rs;
    }


    static function read_sale_file($file_url)
    {

        require $_SERVER['DOCUMENT_ROOT'] . '/phpspreadsheet/vendor/autoload.php';


        $spreadsheet = new Spreadsheet();

        $inputFileName = $_SERVER['DOCUMENT_ROOT'] . $file_url;
        $inputFileType = 'Xlsx';
        //$inputFileName = 'Book1.xlsx';

        /**  Create a new Reader of the type defined in $inputFileType  **/
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
        /**  Advise the Reader that we only want to load cell data  **/
        $reader->setReadDataOnly(true);

        $worksheetData = $reader->listWorksheetInfo($inputFileName);

        foreach ($worksheetData as $worksheet) {

            $sheetName = $worksheet['worksheetName'];

            //echo "<h4>$sheetName</h4>";
            /**  Load $inputFileName to a Spreadsheet Object  **/
            $reader->setLoadSheetsOnly($sheetName);
            $spreadsheet = $reader->load($inputFileName);

            $worksheet = $spreadsheet->getActiveSheet();
            $arr = $worksheet->toArray();

            //echo "<pre>"; print_r($arr); echo "</pre>";
        }

        foreach ($arr as $k => $v) {
            if ($k != 0) {
                foreach ($v as $k2 => $v2) {
                    $data[$arr[0][$k2]] = $v2;
                }
                $main_arr[] = $data;
            }
        }

        foreach (db::arr("SELECT * FROM `list_dori_sales` WHERE STATUS='1'") as $v) {
            $list_dori_sales[$v['CODE']] = $v;
        }

        foreach ($main_arr as $v) {

            if (isset($list_dori_sales[$v['CODE']])) {
                $item['DORI_ID'] = $list_dori_sales[$v['CODE']]['ID'];
            } else {
                $item['DORI_ID'] = 0;
            }

            if ($v['XLS_DORI_NAME'] != NULL || $v['CODE'] != NULL) {
                $item['XLS_DORI_NAME'] = $v['XLS_DORI_NAME'];
                $item['PLAN'] = $v['PLAN'];
                $item['PLAN_SUMMA'] = $v['PLAN_SUMMA'];
                $item['FAKT'] = $v['FAKT'];
                $item['FAKT_SUMMA'] = $v['FAKT_SUMMA'];
                $item['SALE_KPI'] = $v['SALE_KPI'];
                $item['CODE'] = $v['CODE'];

                $rs['main_data'][] = $item;
                $rs['list_dori_sales'] = $list_dori_sales;
            }
        }


        return $rs;
    }

    static function read_sale_file_medpred($file_url)
    {

        require $_SERVER['DOCUMENT_ROOT'] . '/phpspreadsheet/vendor/autoload.php';


        $spreadsheet = new Spreadsheet();

        $inputFileName = $_SERVER['DOCUMENT_ROOT'] . $file_url;
        $inputFileType = 'Xlsx';
        //$inputFileName = 'Book1.xlsx';

        /**  Create a new Reader of the type defined in $inputFileType  **/
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
        /**  Advise the Reader that we only want to load cell data  **/
        $reader->setReadDataOnly(true);

        $worksheetData = $reader->listWorksheetInfo($inputFileName);

        foreach ($worksheetData as $worksheet) {

            $sheetName = $worksheet['worksheetName'];

            //echo "<h4>$sheetName</h4>";
            /**  Load $inputFileName to a Spreadsheet Object  **/
            $reader->setLoadSheetsOnly($sheetName);
            $spreadsheet = $reader->load($inputFileName);

            $worksheet = $spreadsheet->getActiveSheet();
            $arr = $worksheet->toArray();

            //echo "<pre>"; print_r($arr); echo "</pre>";
        }

        foreach ($arr as $k => $v) {
            if ($k != 0) {
                foreach ($v as $k2 => $v2) {
                    $data[$arr[0][$k2]] = $v2;
                }
                $main_arr[] = $data;
            }
        }

        foreach (db::arr("SELECT * FROM `list_dori_sales` WHERE STATUS='1'") as $v) {
            $list_dori_sales[$v['CODE']] = $v;
        }

        foreach ($main_arr as $v) {

            if (isset($list_dori_sales[$v['CODE']])) {
                $item['DORI_ID'] = $list_dori_sales[$v['CODE']]['ID'];
            } else {
                $item['DORI_ID'] = 0;
            }

            if ($v['XLS_DORI_NAME'] != NULL || $v['CODE'] != NULL) {
                $item['XLS_DORI_NAME'] = $v['XLS_DORI_NAME'];
                $item['PLAN'] = $v['PLAN'];
                $item['PLAN_SUMMA'] = $v['PLAN_SUMMA'];
                $item['FAKT_LICH'] = $v['FAKT_LICH'];
                $item['FAKT_OPT'] = $v['FAKT_OPT'];
                $item['FAKT'] = $v['FAKT'];
                $item['FAKT_SUMMA'] = $v['FAKT_SUMMA'];
                $item['SALE_KPI'] = $v['SALE_KPI'];
                $item['CODE'] = $v['CODE'];

                $rs['main_data'][] = $item;
                $rs['list_dori_sales'] = $list_dori_sales;
            }
        }


        return $rs;
    }

    static function module_link($link, $type)
    {
        $rs = $link;
        if ($type == 'multi') {
            $str = explode('&', $rs);
            $page = explode('page=', $str[0])[1];
            $page_action = explode('page_action=', $str[1])[1];
            $rs = "/account/$page/$page_action";
        }
        return $rs;
    }


}

?>
<? //echo "<pre>"; print_r($_SESSION); echo "</pre>";  ?>


<?
function copy_infoblock($inf_name, $inf_id, $main_block_id)
{
    $inf = db::arr_s("SELECT * FROM iblock WHERE ID='$inf_id'");
    $q['ins'] = db::query("INSERT INTO iblock(
`MAIN_BLOCK_ID`,
`TYPE`,`NAME`,
`FIELDS_JSON`) VALUES (
'$main_block_id','$inf[TYPE]','$inf_name','$inf[FIELDS_JSON]')");
    if ($q['ins']['stat'] == 'success') {
        $iblock_id = $q['ins']['ID'];
        $content = db::arr("SELECT * FROM content WHERE IBLOCK_ID='$inf_id'");
        if ($content != 'empty') {
            foreach ($content as $v) {
                $rs['ins'][] = db::query("INSERT INTO  content (
`ACTIVE`,`SORT`,`IBLOCK_ID`,`DATA_JSON`) VALUES ('$v[ACTIVE]','$v[SORT]','$iblock_id','$v[DATA_JSON]')");
            }
        }
    }
    return $rs;
}

?>

<? function arr_define($var)
{
    if (isset($var)) {
        $rs = $var;
    } else {
        $rs = [];
    }
    return $rs;
} ?>
<? function var_define($var)
{
    if (isset($var)) {
        $rs = $var;
    } else {
        $rs = [];
    }
    return $rs;
} ?>
<? function is_empty($arr)
{
    if ($arr = 'empty') {
        $rs = $arr;
    } else {
        $rs = [];
    }
    return $rs;
} ?>

<? function LocalRedirect($url)
{

    //header("Location: ".$url);exit;

    if ($_SESSION['link_type'] == 'single') {
        header("Location: " . $url);
        exit;
    }
    if ($_SESSION['link_type'] == 'multi') {

        if (isset($_SESSION['item_id'])) {
            $item_url = '/' . $_SESSION['item_id'];
        };

        $url = "/account/$_SESSION[page_cc]/$_SESSION[page_action_cc]$item_url";
        header("Location: " . $url);
        exit;
    }

}

?>

<? function lr_otchet($url)
{
    header("Location: " . $url);
    exit;
} ?>

<? function is_nullable($v)
{
    if ($v != NULL || $v != "") {
        $rs = $v;
    } else {
        $rs = NULL;
    }
    return $rs;
} ?>

<? function ch($v)
{
    $rs = htmlspecialchars($v, ENT_QUOTES);
    return $rs;
} ?>

<? function chd($v)
{
    $rs = htmlspecialchars_decode($v, ENT_QUOTES);
    return $rs;
} ?>

<? function quatation($v)
{
    $rs = str_replace('"', "&dble;", str_replace("'", "&sngl;", $v));
    return $rs;
} ?>

<? function date_var($v, $f)
{
    if ($v != NULL) {
        if ($f == NULL) {
            $d = new DateTime($v);
            $v = $d->format('Y-m-d');
            $rs = $v . ' ' . date('H:i:s');
        } else {
            $d = new DateTime($v);
            $rs = $d->format($f);
        }
    } else {
        $rs = NULL;
    }
    return $rs;
} ?>

<? /*
	function date_var($v,$f){if($v!=NULL){if($f==NULL){
	$v = implode('.',$v);
	$v_format = $v[1].'.'.$v[0].'.'.$v[2];
	$d = new DateTime($v_format);$v = $d->format('Y-m-d');$rs = $v;
	return $rs;
	} else {$d = new DateTime($v);$rs = $d->format($f);}}else{$rs=NULL;}return $rs;}
   */
?>

<? function str_r($s)
{
    $s = str_replace('"', '&#34;', $s);
    $s = str_replace("'", "/'", $s);
    return $s;
} ?>

<? function debug($var, $p_k)
{
    echo "<b>RESULT:</b><pre>";
    print_r($var);
    echo "</pre>";
    if ($_POST != NULL) {
        post($p_k);
    }
} ?>

<? function post($k)
{
    if ($k == NULL) {
        echo "<b>$" . "_POST</b>:<pre>";
        print_r($_POST);
        echo "</pre>";
    } else {
        echo "<pre>";
        print_r($_POST[$k]);
        echo "</pre>";
    }
} ?>

<? function session($k)
{
    if ($k == NULL) {
        echo "<pre>";
        print_r($_SESSION);
        echo "</pre>";
    } else {
        echo "<pre>";
        print_r($_SESSION[$k]);
        echo "</pre>";
    }
} ?>

<?
function post_query($url, $arr)
{

    $url = 'https://' . $url;
    $data = $arr;

// use key 'http' even if you send the request to https://...
    $options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context = stream_context_create($options);
    $rs = file_get_contents($url, false, $context);
    if ($rs === FALSE) {
        $rs = 'no';
    }

    return $rs;
}

?>
