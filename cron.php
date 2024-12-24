<?php
class db{
	static function conn()
	{
		$conn = new mysqli('localhost', '<username>', '<password>', '<database>');
		$conn->set_charset('utf8mb4_bin');
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
}
?>

<?php
	file_put_contents('file.txt', 'Ваш превосходный текст');
?>

<?php

$now = date("Y-m-d H:i:s");

foreach (db::arr("SELECT * FROM `message_log` WHERE `STATUS`=0") as $v) {

/* AGAR XABAR YUBORILGAN BOLSA STATUSNI 1 GA OZGARTIRISH KERAK*/


try
{ 

$rassilka = db::arr_s("SELECT * FROM `tg_rassilka` WHERE ID = '$v[RASSILKA_ID]'");

//$get_photo = json_decode($rassilka['FILE_URL'], true);

$token = "<telegram bot token>";
$api_url = "https://api.telegram.org/bot" . $token . "/sendPhoto?";
$file_url = "https://api.telegram.org/bot" . $token . "/sendDocument?";
$message_url = "https://api.telegram.org/bot" . $token . "/sendMessage?";

$text = urlencode($rassilka['TEXT']);

$format = ["jpg", "jpeg", "png", "webp", "gif"];
$mp = "mp4";

foreach($format as $img){
if(strpos($rassilka['FILE_URL'], $img) != false){

$send_message = $api_url."chat_id=".$v['CHAT_ID']."&photo=https://demoschool.senet.uz".$rassilka['FILE_URL']."&caption=".$text;

}
}
if(strpos($rassilka['FILE_URL'], $mp) == false){

$send_message = $file_url."chat_id=".$v['CHAT_ID']."&document=https://demoschool.senet.uz".$rassilka['FILE_URL']."&caption=".$text;
	
}
if($rassilka['FILE_URL'] == null){

$send_message = $message_url."chat_id=".$v['CHAT_ID']."&text=".$text;

}

$q = file_get_contents($send_message);

$get_id = json_decode($q, true);

$message_id = $get_id['result']['message_id'];	

db::query("UPDATE `message_log` 
SET SEND_DATE = '$now', 
MESSAGE_ID = '$message_id', 
STATUS = '1' 
WHERE ID = '$v[ID]'");

 
}
catch (\Exception $e) 
{
	 "bot Y send me the probable problem in my code....";
}
catch (Throwable $e)
{
	   "bot Y send me the probable problem in telegram such 
	   as blocking ,..";
}


}

foreach (db::arr("SELECT * FROM `message_log` WHERE `STATUS`=2") as $v) {

try
{ 

$token = "5917704072:AAHrzOHlfmMKrwFQgBHMZMbqxnKbmk9fj7c";
$api_url = "https://api.telegram.org/bot" . $token . "/deleteMessage?";

$send_message = $api_url."chat_id=".$v['CHAT_ID']."&message_id=".$v['MESSAGE_ID'];

$q = file_get_contents($send_message);

$arr = json_decode($q, true);
echo '<pre>'; print_r($arr); echo '</pre>';

db::query("DELETE FROM `message_log` WHERE ID = '$v[ID]'");

}
catch (\Exception $e) 
{
	 "bot Y send me the probable problem in my code....";
}
catch (Throwable $e)
{
	   "bot Y send me the probable problem in telegram such 
	   as blocking ,..";
}

}

?>
