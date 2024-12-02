<?

class del {
	
	static function CONTENT($arr){
	$rs = db::query("DELETE FROM content WHERE ID='$arr[content_id]'");
	return $rs;}

	
	
}
