<?php defined('isCMS') or die;

if (empty($item['date'])) {
	$item['date'] = time();
}

if (
	(
		!empty($module -> settings -> page -> topdate) &&
		time() - $item['date'] < $module -> settings -> page -> topdate &&
		(
			!isset($module -> settings -> page -> top) ||
			!$module -> settings -> page -> top
		)
	) ||
	(
		!empty($module -> settings -> page -> top) &&
		(
			(is_numeric($key) && $key < $module -> settings -> page -> top) ||
			(!is_numeric($key) && is_numeric($item['id']) && $item['id'] < $module -> settings -> page -> top)
		)
	)
) {
	$module -> var['top'] = true;
} else {
	$module -> var['top'] = false;
}

?>