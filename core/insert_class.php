<?

class insert {
	
	static function CONTENT($arr){
    if (isset($arr['active_status'])) {$sts = 1;}else{$sts=0;}
	$sort = $arr['sort_status'];
	$block_id = $arr['block_id'];	
	$data = $arr;	
	unset($data['content_add_form']);
	unset($data['block_id']);
	unset($arr['active_status']);
	unset($arr['sort_status']);
	foreach ($data as $k=>$v) {if ($v!=NULL) {
	$new_data[$k]= htmlentities(trim($v),ENT_QUOTES);}}
//foreach ($_POST as $k=>$v){$_POST[$k]= str_replace("'","\'",$v);
		//$new_data[$k]= str_replace("'","\'",$v);}}	
	$data_json = json_encode($new_data,JSON_UNESCAPED_UNICODE);
	$rs = db::query("INSERT INTO content (IBLOCK_ID,DATA_JSON,ACTIVE,SORT) VALUES('$block_id','$data_json','$sts','$sort')");	
	return $rs;}	
	
	static function CONTENT_MULTI($arr){
	//header('Content-Type: application/json; charset=utf-8');	
	$block_id = $arr['block_id'];	
	$data = $arr;	
	foreach ($data as $k=>$v){	
    if (is_array($v)){
    foreach ($v as $k2=>$v2){$fields_json[$k][$k2] = htmlentities(trim($v2),ENT_QUOTES);}}}	
	$data_json = json_encode($fields_json,JSON_UNESCAPED_UNICODE);
	$rs = db::query("INSERT INTO content (IBLOCK_ID,DATA_JSON) VALUES('$block_id','$data_json')");	
	return $rs;}
	
	
	static function CREATE_MENU($con_id){
	$menu_data = db::arr("SELECT * FROM IBLOCK WHERE TYPE='MENU' AND MAIN_BLOCK_ID='$_SESSION[con_id]'");
	if ($menu_data=='empty'){	
	$arr = [  
    ['name'=>'name'     ,'type'=>'text'  ,'sort'=>'100','show_table'=>1],
    ['name'=>'page_name','type'=>'text'  ,'sort'=>'200','show_table'=>1],
    ['name'=>'url'      ,'type'=>'text'  ,'sort'=>'300','show_table'=>1]];
	$json = json_encode($arr);	
	$rs = db::query("INSERT INTO iblock (MAIN_BLOCK_ID, TYPE, NAME, `FIELDS_JSON`)
    VALUES ('$con_id', 'MENU', 'MENU', '$json')");}
	return $rs;}	
	
	static function CREATE_STATIC($con_id){
	$menu_data = db::arr("SELECT * FROM IBLOCK WHERE TYPE='STATIC' AND MAIN_BLOCK_ID='$_SESSION[con_id]'");
	if ($menu_data=='empty'){	
	//$arr = ['name'=>'name'     ,'type'=>'text'  ,'sort'=>'200','show_table'=>1];
	$arr = array();
	$json = json_encode($arr);	
	$rs = db::query("INSERT INTO iblock (MAIN_BLOCK_ID, TYPE, NAME, `FIELDS_JSON`)
    VALUES ('$con_id', 'STATIC', 'STATIC', '$json')");}
	return $rs;}

	static function CREATE_SECTION($con_id){
	$menu_data = db::arr("SELECT * FROM IBLOCK WHERE TYPE='SECTION' AND MAIN_BLOCK_ID='$_SESSION[con_id]'");
	if ($menu_data=='empty'){	
	//$arr = ['name'=>'name'     ,'type'=>'text'  ,'sort'=>'200','show_table'=>1];
	$arr = array();
	$json = json_encode($arr);	
	$rs = db::query("INSERT INTO iblock (MAIN_BLOCK_ID, TYPE, NAME, `FIELDS_JSON`)
    VALUES ('$con_id', 'SECTION', 'SECTION', '$json')");}
	return $rs;}
	
	
	static function USER_IBLOCK($data){		
    foreach ($data as $k=>$v){	
    if (is_array($v)){
    foreach ($v as $k2=>$v2){$fields_json[$k2][$k] = $v2;}}}
	if ($fields_json==NULL){
	$fields_json = [
	['name'=>'name'     ,'type'=>'text'  ,'sort'=>'100','show_table'=>1],     
    ['name'=>'active'   ,'type'=>'number','sort'=>'300','show_table'=>1]];}
	$fields_json = json_encode($fields_json,JSON_UNESCAPED_UNICODE);
	
	
	$rs = db::query("INSERT INTO iblock (
	MAIN_BLOCK_ID,
	TYPE, 
	NAME,
	FIELDS_JSON)
    VALUES (
	'$data[main_block_id]', 
	'USER_TABLE', 
	'$data[iblock_name]', 
	'$fields_json')");
	
	return $rs;}
	

	
}




?>