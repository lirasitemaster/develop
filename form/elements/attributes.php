<?php defined('isCMS') or die;

echo ' method="';
if (
	empty($module -> settings['get']) ||
	!empty($module -> settings['files'])
) {
	echo 'post';
} else {
	echo 'get';
}
echo '"';

if (!empty($module -> settings['files'])) {
	echo ' enctype="multipart/form-data"';
	// данный код может давать предупреждения антивируса, однако он является безопасным
}

echo ' action="' . $module -> var['base']['action'] . '"';

?>