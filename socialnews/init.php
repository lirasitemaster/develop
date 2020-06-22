<?php defined('isENGINE') or die;

$module -> data = [];

require $module -> path . 'process' . DS . 'state.php';

if (!empty($module -> settings['api']) && file_exists($module -> path . 'process' . DS . $module -> settings['api'] . '.php')) {
	require $module -> path . 'process' . DS . $module -> settings['api'] . '.php';
}

if (!empty($module -> settings['reverse'])) {
	$module -> data = array_reverse($module -> data);
}

?>