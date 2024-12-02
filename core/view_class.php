<?

class view {
	
	
	static function CONTENT($sts,$arr) {
		
	if ($sts=='all'){
		
	$data = db::arr("SELECT * FROM content WHERE IBLOCK_ID='$arr[IBLOCK_ID]' ORDER BY SORT");	
	if ($data!='empty'){
	foreach ($data as $k=>$v){
	//$rs[$k] = json_decode($v['DATA_JSON'],TRUE);
	//$v['DATA_JSON'] = utf8_encode($v['DATA_JSON']);

	//$rs[$k] = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $v['DATA_JSON']), true );
	//$rs[$k] = json_decode(preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/u', '', $v['DATA_JSON']), true );
	$rs[$k] = json_decode(preg_replace('/[\x00-\x1F\x7F]/u', '', $v['DATA_JSON']), true );
	$rs[$k]['ID'] = $v['ID'];
	$rs[$k]['SORT'] = $v['SORT'];
	$rs[$k]['ACTIVE'] = $v['ACTIVE'];}	
	}else{$rs=[];}
	
	}	
	
	if ($sts=='all_active'){
		
	$data = db::arr("SELECT * FROM content WHERE IBLOCK_ID='$arr[IBLOCK_ID]' AND ACTIVE=1 ORDER BY SORT");	
	if ($data!='empty'){
	foreach ($data as $k=>$v){
	//$rs[$k] = json_decode($v['DATA_JSON'],TRUE);
	//$rs[$k] = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $v['DATA_JSON']), true );
	$rs[$k] = json_decode(preg_replace('/[\x00-\x1F\x7F]/u', '', $v['DATA_JSON']), true );
	
	//$string = preg_replace('/[\x00-\x1F\x7F]/u', '', $string);
	$rs[$k]['ID'] = $v['ID'];
	$rs[$k]['SORT'] = $v['SORT'];
	$rs[$k]['ACTIVE'] = $v['ACTIVE'];}	
	}else{$rs=[];}
	
	}
	
	if ($sts=='get_news_by_cat'){
	$key = $arr['KEY'];
	$data = db::arr("SELECT * FROM content WHERE DATA_JSON LIKE '%$key%' AND IBLOCK_ID='$arr[IBLOCK_ID]' AND ACTIVE=1 ORDER BY SORT");	
	if ($data!='empty'){
	foreach ($data as $k=>$v){
	$rs[$k] = json_decode($v['DATA_JSON'],TRUE);
	$var=explode('.',$rs[$k]['data']);
    $sum = $var[0]*$var[1]*$var[2];
	$rs[$k]['chislo'] = $var[0];
	$rs[$k]['mesyats'] = $var[1];
	$rs[$k]['arr_sort'] = $sum;
	$rs[$k]['ID'] = $v['ID'];
	$rs[$k]['SORT'] = $v['SORT'];
	$rs[$k]['ACTIVE'] = $v['ACTIVE'];}	
	}else{$rs=[];}	
	
	$keys = array_column($rs, 'arr_sort');
    array_multisort($keys, SORT_DESC, $rs);	
	}
	
	if ($sts=='single'){
		
	$data = db::arr("SELECT * FROM content WHERE ID='$arr[CONTENT_ID]' AND ACTIVE=1 ORDER BY SORT");	
	if ($data!='empty'){
	foreach ($data as $k=>$v){
	$rs = json_decode($v['DATA_JSON'],TRUE);
	$rs['ID'] = $v['ID'];
	$rs['SORT'] = $v['SORT'];
	$rs['ACTIVE'] = $v['ACTIVE'];}	
	}else{$rs=[];}
	
	}
	
		
		
	return $rs;}
	
	
	static function static_data($con_id){
	$data = db::arr_s("SELECT * FROM IBLOCK WHERE TYPE='STATIC' AND MAIN_BLOCK_ID='$con_id'");
    $content = db::arr_s("SELECT * FROM content WHERE IBLOCK_ID='$data[ID]'");
    foreach (json_decode($content['DATA_JSON'],TRUE) as $k=>$v){
    $rs[$v['name']] = $v['value'];}
    return $rs;}	
	
	static function section_data($con_id){
	$data = db::arr_s("SELECT * FROM IBLOCK WHERE TYPE='SECTION' AND MAIN_BLOCK_ID='$con_id'");
    $content = db::arr_s("SELECT * FROM content WHERE IBLOCK_ID='$data[ID]'");
    foreach (json_decode($content['DATA_JSON'],TRUE) as $k=>$v){
    $rs[$v['section_name']] = $v['active'];}
    return $rs;}

	
	
    static function A_USERS($sts, $arr){
    
	if ($sts == 'auth') {
    $rs = db::arr_s("SELECT * FROM A_USERS WHERE LOGIN='$_POST[login]' AND PASSWORD='$_POST[pass]' AND ACTIVE=1");}
    
	if ($sts == 'all') {
    $rs = db::arr("SELECT * FROM A_USERS");}

	if ($sts == 'active') {
    $rs = db::arr("SELECT * FROM A_USERS WHERE ACTIVE=1");}
        
	if ($sts == 'photo') {
    if ($_SESSION['user']['photo'] == NULL) {
	$rs = 'user.png';} else {
    $rs = $_SESSION['user']['photo'];}}
    
	if ($sts == 's') {
    $rs = db::arr_s("SELECT * FROM A_USERS WHERE ACTIVE=1 AND ID='$arr[ID]'");
    if ($rs['PHOTO_LINK'] == NULL) {
    $rs['PHOTO_LINK'] = 'user.png';}}
	
	if ($sts == 'oper'){
	$rs = db::arr("SELECT ID,NAME,SURNAME FROM A_USERS WHERE POSITION='operator'");}
	
    return $rs;}
	
	
	
	
}


?>