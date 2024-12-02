<?

class update {
	
	
	static function IBLOCK($data){		
    foreach ($data as $k=>$v){	
    if (is_array($v)){
    foreach ($v as $k2=>$v2){
	$v2 = str_replace('"', "", $v2);
    $v2 = str_replace("'", "", $v2);
	$fields_json[$k2][$k] = $v2;}}}
	$fields_json = json_encode($fields_json,JSON_UNESCAPED_UNICODE);	
	$rs = db::query("UPDATE iblock SET   
    NAME = '$data[iblock_name]',
    FIELDS_JSON = '$fields_json'
    WHERE ID = '$data[iblock_id]'");		
	return $rs;}

	static function CONTENT_MULTI($data){		
    foreach ($data as $k=>$v){	
    if (is_array($v)){
    foreach ($v as $k2=>$v2){$fields_json[$k2][$k] = htmlentities($v2,ENT_QUOTES);}}}
	$fields_json = json_encode($fields_json,JSON_UNESCAPED_UNICODE);	
	$rs = db::query("UPDATE content SET  
    DATA_JSON = '$fields_json'
    WHERE ID = '$data[content_id]'");		
	return $rs;}
	
	
	static function CONTENT($arr){
	if (isset($arr['active_status'])) {$sts = 1;}else{$sts=0;}
	$sort = $arr['sort_status'];
	$content_id = $arr['content_id'];	
	$data = $arr;
	unset($data['content_id']);
	unset($data['active_status']);
	unset($data['sort_status']);
	foreach ($data as $k=>$v) {if ($v!=NULL) {
	$new_data[$k]= htmlentities(trim($v),ENT_QUOTES);}}	
	$data_json = json_encode($new_data,JSON_UNESCAPED_UNICODE);
	$rs = db::query("UPDATE content SET ACTIVE='$sts',DATA_JSON = '$data_json',SORT='$sort' WHERE ID='$content_id'");	
	return $rs;}
	
	
}
