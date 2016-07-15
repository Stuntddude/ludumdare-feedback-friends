<?php

function util_sanitize($value) {
	// Only keep alpha-numeric chars
	// AND "+- " for search
	// TODO " "
	return preg_replace("/(([\w+\- ]*))/", '$0', $value);
}

function util_sanitize_query_param($key) {
	if (isset($_GET[$key])) {
		return util_sanitize($_GET[$key]);
	}
	else {
		return '';
	}
}

function util_check_picture_folder($event_id) {
	$folder_path = "data/$event_id";
	if (!file_exists($folder_path)) {
		mkdir($folder_path, 0770, true) or die("Failed to create $folder_path directory");
	}
}

function util_get_picture_path($event_id, $uid) {
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

?>