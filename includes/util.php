<?php

$start_time = microtime(true);
function util_time_elapsed() {
	global $start_time;
	return round(microtime(true) - $start_time, 3);
}

function util_is_admin() {
	return !LDFF_PRODUCTION || (php_sapi_name() == "cli" 
			|| (isset($_GET['p']) && $_GET['p'] == LDFF_ADMIN_PASSWORD));
}

function util_require_admin() {
	if (!util_is_admin()) {
		http_response_code(403);
		die('403 Unauthorized');
	}
}

function util_sanitize($value) {
	// Only keep alpha-numeric chars
	// AND "+- " for search
	return preg_replace("/(([\w+\- ]*))/", '$0', $value);
}

function util_sanitize_query_param($key) {
	if (isset($_GET[$key])) {
		return util_sanitize($_GET[$key]);
	} else {
		return '';
	}
}

// Also implemented in JS, see site-api.js > _formatType()
function util_format_type($value) {
	return ucfirst($value); // capitalize the first letter to get "Compo" or "Jam"
}

// Also implemented in JS, see site-api.js > _formatPlatforms()
function util_format_platforms($value) {
	static $PLATFORM_LABELS = array(
		'osx' => 'OSX',
		'html5' => 'HTML5',
		'vrgames' => 'VR',
		'htcvive' => 'Vive'
	);

	$result = '';
	$array = explode(' ', $value);
	foreach($array as $key => $platform) {
		if (isset($PLATFORM_LABELS[$platform])) {
			$result .= $PLATFORM_LABELS[$platform];
		}	else {
			$result .= ucfirst($platform);
		}
		$result .= ' ';
	}
	return $result;
}

function util_check_picture_folder($event_id) {
	$folder_path = __DIR__ . "/../data/$event_id";
	if (!file_exists($folder_path)) {
		mkdir($folder_path, 0770, true) or die("Failed to create $folder_path directory");
	}
}

function util_get_picture_file_path($event_id, $uid) {
	return __DIR__ . "/../data/$event_id/$uid.jpg";
}

function util_get_picture_url($event_id, $uid) {
	return "data/$event_id/$uid.jpg";
}

// turns [a,b,c,d,e] into [{k:[a,b,c]},{k:[d,e]}], useful for templates
function util_array_chuck_into_object($array, $size, $key) {
	$tmp = array_chunk($array, 5);
	$object = array();
	foreach ($tmp as $item) {
		$object[] = array($key => $item);
	}
	return $object;
}

function util_load_templates($names) {
	$out = [];
	foreach ($names as $name) {
		$code = file_get_contents(__DIR__.'/../templates/'.$name.'.html');
		array_push($out, array('name' => $name, 'code' => $code));
	}
	return $out;
}

function util_resize_image($originalFile, $targetFile, $newWidth) {
	$info = getimagesize($originalFile);
	$mime = $info['mime'];

	switch ($mime) {
		case 'image/jpeg':
		$image_create_func = 'imagecreatefromjpeg';
		break;

		case 'image/png':
		$image_create_func = 'imagecreatefrompng';
		break;

		case 'image/gif':
		$image_create_func = 'imagecreatefromgif';
		break;

		default: 
		return false;
	}

	$img = $image_create_func($originalFile);
	list($width, $height) = getimagesize($originalFile);

	$newHeight = ($height / $width) * $newWidth;
	$tmp = imagecreatetruecolor($newWidth, $newHeight);
	imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

	if (file_exists($targetFile)) {
		unlink($targetFile);
	}
	imagejpeg($tmp, $targetFile, 90);
	return true;
}

?>
