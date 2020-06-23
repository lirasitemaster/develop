<?php defined('isENGINE') or die;

if (!empty($sets['style'])) {
	
	$file = PATH_CUSTOM . 'modules' . DS . 'media' . DS . $module -> template . '_style.php';
	
	if ($module -> template === 'default' || !file_exists($file)) {
		$file = PATH_MODULES . $module -> name . DS . 'templates' . DS . 'default_style.php';
	}
	
	require $file;
	
	unset($file);
	
}

?>