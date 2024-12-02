<?
class action
{

	static function create_form($arr)
	{
		$form_start = '<form action=\"' . $arr['action'] . '\" method=\"POST\" id=\"' . $arr['id'] . '\">';
		foreach ($arr['data'] as $k => $v) {
			$form_input = $form_input . '<input type=\"hidden\" name=\"' . $k . '\" value=\"' . $v . '\">';
		}
		$form_end = '</form>';
		echo '$(document).ready(function(){
	$("[name=ppf_form]").html("' . htmlspecialchars($form_start . $form_input . $form_end) . '");});';
	}

	static function submit_form($arr)
	{
		echo '$(document).ready(function (){
	$( "#' . $arr['id'] . '" ).submit();});';
	}

	static function create_ajax($arr)
	{
		echo '$(document).ready(function(){
	formData= new FormData();';
		foreach ($arr['data'] as $k => $v) {
			echo 'formData.append("' . $k . '","' . $v . '");';
		}
		echo 'js_ajax_post("' . $arr['link'] . '",formData).done(function (data){
	' . $arr['success'] . '(data);});});';
	}

	static function modal_show($arr)
	{
		if ($arr['success'] == NULL) {
			$arr['success'] = '';
		}
		echo '$(document).ready(function(){
	$("#' . $arr['id'] . '").modal("show");
	' . $arr['success'] . '();})';
	}

	static function button($arr)
	{
		if ($arr['js'] != NULL) {
			echo $arr['js'] . '()';
		}
		if ($arr['modal'] != NULL) {
			action::modal_show($arr['modal']);
		}
		if ($arr['form'] != NULL) {
			action::create_form($arr['form']);
			action::submit_form($arr['form']);
		}
		if ($arr['ajax'] != NULL) {
			action::create_ajax($arr['ajax']);
		}
	}
}

class TelegramBot
{
	static function request($method_name, $data)
	{
		$token = "5917704072:AAHrzOHlfmMKrwFQgBHMZMbqxnKbmk9fj7c";
		$request_url = "https://api.telegram.org/bot{$token}/{$method_name}?" . http_build_query($data);
		return file_get_contents($request_url);
	}
	static function sendMessage($chat_id, $message, $replyMarkup)
	{
		$data = ['chat_id' => $chat_id, 'parse_mode' => 'HTML', 'disable_web_page_preview' => false, 'reply_markup' => $replyMarkup, 'text' => $message];
		return TelegramBot::request('sendMessage', $data);
	}
}
?>

<?
function dd($display, $forId = 1)
{
	if ($_SESSION["USER"]["ID"] == $forId) {
		echo '<pre class="bg-light-primary py-1 position-relative rounded">';
		echo '<h4 class="text-center text-primary" id="prompt-message">Prompt</h4>';
		echo '<span id="copy-text">';
		var_dump($display);
		echo '</span>';
		echo '<i class="fa fa-clone position-absolute" style="top:8px; right:8px; padding:5px 8px;" id="copy-clickboard" role="button"></i>';
		echo '</pre>';
		die;
	}
}
function dp($display, $forId = 1)
{
	if ($_SESSION["USER"]["ID"] == $forId) {
		echo '<pre class="bg-light-primary py-1 position-relative rounded">';
		echo '<h4 class="text-center text-primary" id="prompt-message">Prompt</h4>';
		echo '<span id="copy-text">';
		print_r($display);
		echo '</span>';
		echo '<i class="fa fa-clone position-absolute" style="top:8px; right:8px; padding:5px 8px;" id="copy-clickboard" role="button"></i>';
		echo '</pre>';
	}
}
function uploadFile($inputName, $directory)
{
	$targetDirectory = $directory;
	$originalFileName = basename($_FILES[$inputName]["name"]);
	$fileHash = md5_file($_FILES[$inputName]["tmp_name"]);
	$targetFile = $targetDirectory . $fileHash . "_" . $originalFileName;
	$zipTargetFile = $targetDirectory . $fileHash . "_" . basename($_FILES[$inputName]["name"], "." . strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION))) . ".zip";
	$uploadOk = 1;

	// Check if file is an image
	$imageFileType = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
	$allowedImageFormats = ["jpg", "jpeg", "png", "gif"];

	// Check if file size exceeds limit
	$sizeLimit = 8000000; // 8 MB

	if (!in_array($imageFileType, $allowedImageFormats) && $_FILES[$inputName]["size"] > $sizeLimit) {
		// Try to upload file
		if (move_uploaded_file($_FILES[$inputName]["tmp_name"], $targetFile)) {
			// Create a ZIP archive
			$zip = new ZipArchive();
			if ($zip->open($zipTargetFile, ZipArchive::CREATE) === TRUE) {
				$zip->addFile($targetFile, $originalFileName);
				$zip->close();
				unlink($targetFile); // Remove the original file after compressing
				echo "The file " . $originalFileName . " has been uploaded, and a compressed version has been created.";
			} else {
				echo "Error creating ZIP archive.";
			}
		} else {
			echo "Error uploading file.";
		}
	} else {
		// File is an image or within size limit, do not compress
		if (move_uploaded_file($_FILES[$inputName]["tmp_name"], $targetFile)) {
			return $targetFile;
		} else {
			return false;
		}
	}
}
function createMessageUsers($text, $numbers)
{
	$data = ["messages" => []];
	foreach ($numbers as $number) {
		$single_data = [
			"recipient" => $number,
			"message-id" => "code000000002",
			"sms" => [
				"originator" => "CoddyCamp",
				"content" => [
					"text" => stripslashes($text)
				]
			]
		];
		array_push($data["messages"], $single_data);
	}

	return json_encode($data);
}

function createMessageLog($logs_id)
{
	$query_ids = join(",", $logs_id);
	$phone_nums = db::arr("SELECT 
	sms.TEXT,
	sl.PHONE
	FROM `sms_log` sms
	LEFT JOIN `student_list` sl ON sl.ID = sms.SEND_TO
	WHERE sms.ID IN ($query_ids)");
	$numbers = [];
	foreach ($phone_nums as $stud) {
		$student_phone = filter_var($stud["PHONE"], FILTER_SANITIZE_NUMBER_INT);
		array_push($phone_arr, "998$student_phone");
	}
	return createMessageUsers($phone_nums[0]["TEXT"], $numbers);
}

function sendMessageRequest($postdata)
{
	$opts = array(
		'http' =>
		array(
			'method'  => 'POST',
			'header'  => array(
				'Authorization: Basic ZGd0bGNhbXA6I3pnM0c1RGt+SyE3',
				'Content-Type: application/json'
			),
			'content' => $postdata
		)
	);
	$context  = stream_context_create($opts);
	$sendMessage = file_get_contents('http://91.204.239.44/broker-api/send', false, $context);
	return $sendMessage === "Request is received";
}
function createPlan($from, $to, $exceptions = [])
{
	$start = new DateTime($from);
	$end = new DateTime($to);
	$interval = new DateInterval('P1D');
	$dates   = new DatePeriod($start, $interval, $end);
	$odd_days = $even_days = [];

	foreach ($dates as $date) {
		if (in_array($date->format("N"), [2, 4, 6]) and !in_array($date->format("Y-m-d"), $exceptions)) {
			array_push($even_days, $date->format("Y-m-d"));
		} else if (in_array($date->format("N"), [1, 3, 5]) and !in_array($date->format("Y-m-d"), $exceptions)) {
			array_push($odd_days, $date->format("Y-m-d"));
		}
	}

	$odd_days = count($odd_days);
	$even_days = count($even_days);

	return ["odd" => $odd_days, "even" => $even_days];
}
function generate_attributes($key, $value)
{
	if (is_bool($value) && $value) {
		return "$key=\"true\"";
	} else if ((is_bool($value) && !$value) || is_array($value)) {
		return "";
	}
	return "$key=\"$value\"";
};
function generate_options($value, $text)
{
	$pattern = "/\[(.*?)\]/";
	if (preg_match_all($pattern, $value, $matches)) {
		$attributes = join(" ", $matches[1]);
		$value = preg_replace($pattern, "", $value);
		return "<option value=\"$value\" {$attributes}>{$text}</option>";
	}
	return "<option value=\"$value\"> $text </option>";
}

function generate_options_by_query($params)
{
	$data = db::arr($params["query"]);
	$options = [];
	if ($params["first_empty"]) {
		array_push($options, "<option value=\"\"></option>");
	}
	if ($params["key"] && trim($params["key"]) != "") {
		foreach ($data as $item) {
			$options[] = "<option value='{$item[$params["key"]]}'>{$item[$params["value"]]}</option>";
		}
	}

	return $options;
}

function diffInMonths($date1, $date2)
{
	if (!isValidDate($date1) || !isValidDate($date2)) {
		return;
	}
	$start =  new DateTime($date1, new DateTimeZone('UTC'));
	$end = new DateTime($date2, new DateTimeZone('UTC'));
	$interval = $start->diff($end);
	return $interval->y * 12 + $interval->m;
}
function isValidDate($dateString)
{
	$formats = ['Y-m-d H:i:s', 'Y/m/d H:i:s', 'Y.m.d H:i:s', 'Y-m-d', 'H:i:s'];
	foreach ($formats as $format) {
		if (DateTime::createFromFormat($format, $dateString) !== false) {
			return true; // Valid date and/or time found
		}
	}
	return false;
}

function onDev()
{
	ob_start();
}
function onDevEnd()
{
	$content = ob_get_clean();
	if ($_SESSION['USER']['ID'] == '1') {
		echo $content;
	}
}
require $_SERVER['DOCUMENT_ROOT'] . '/core/constants.php';
?>
