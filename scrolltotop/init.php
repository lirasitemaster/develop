<?php defined('isCMS') or die;

if (!empty($module -> settings -> classes)) {
	if (is_object($module -> settings -> classes)) {
		if (!empty($module -> settings -> classes -> base)) {
			$module -> var['classes'] = $module -> settings -> classes -> base;
		}
		if (!empty($module -> settings -> classes -> icon)) {
			$module -> var['icon'] = $module -> settings -> classes -> icon;
		}
		if (!empty($module -> settings -> classes -> text)) {
			$module -> var['text'] = $module -> settings -> classes -> text;
		}
	} elseif (is_string($module -> settings -> classes)) {
		$module -> var['classes'] = $module -> settings -> classes;
	}
}

if (empty($module -> var['classes'])) {
	$module -> var['classes'] = 'scroll-to-top';
	$module -> var['base'] = 'scroll-to-top';
} elseif (strpos($module -> var['classes'], ' ') === false) {
	$module -> var['base'] = $module -> var['classes'];
} else {
	$module -> var['classesarray'] = datasplit($module -> var['classes']);
	$module -> var['base'] = $module -> var['classesarray'][0];
	$module -> var['classes'] = objectToString($module -> var['classesarray']);
	unset($module -> var['classesarray']);
}

if (
	empty($module -> settings -> distance) ||
	!is_numeric($module -> settings -> distance)
) {
	$module -> settings = objectMerge($module -> settings, (object) ['distance' => '100'], 'replace');
}
if (
	empty($module -> settings -> speed) ||
	!is_numeric($module -> settings -> speed)
) {
	$module -> settings = objectMerge($module -> settings, (object) ['speed' => '500'], 'replace');
}

$module -> var['elements'] = ['a', 'button', 'div', 'p', 'span'];

if (
	empty($module -> settings -> element) ||
	!is_string($module -> settings -> element) ||
	(
		is_string($module -> settings -> element) &&
		!in_array($module -> settings -> element, $module -> var['elements'])
	)
) {
	$module -> settings = objectMerge($module -> settings, (object) ['element' => 'a'], 'replace');
}

?>