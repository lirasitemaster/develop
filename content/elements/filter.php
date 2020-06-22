<?php defined('isCMS') or die;

if (
	thispage('is') === $module -> data['content']['parent'] &&
	$module -> data['content']['type'] !== 'alone'
) {
	
	//print_r($filter);
	//print_r($module -> settings['filter']);
	
	require $module -> elements . 'filter_form.php';
	
	if (!empty($module -> settings['filter']['options']['ajax'])) {
		//require $module -> elements . 'filter_ajax.php';
	}
	
}

?>