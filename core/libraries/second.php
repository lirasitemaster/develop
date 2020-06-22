<?php defined('isENGINE') or die;

global $libraries;

if (objectIs($libraries -> preload)) {
	foreach ($libraries -> preload as $i) {
		//echo PATH_LIBRARIES . str_replace('/', DS, $i) . '<br>';
		require_once PATH_LIBRARIES . str_replace('/', DS, $i);
	}
	unset($i);
}

unset(
	$libraries -> db,
	$libraries -> empty,
	$libraries -> preload,
	$libraries -> update
);

$libraries = $libraries -> list;

//print_r($libraries);

?>