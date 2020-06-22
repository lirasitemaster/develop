<?php defined('isENGINE') or die;

$target = 'default';

if (!empty($module -> this)) {
	$target = $module -> this;
} elseif (!empty($module -> settings['target'])) {
	$target = $module -> settings['target'];
}

$target = dataParse($target);
if (empty($target[1])) {
	$target[1] = $target[0];
}

global $uri;

if (
	!empty($uri -> query -> array['status']) &&
	$uri -> query -> array['status'] === 'complete'
) {
	
	$complete = true;
	$shop = null;
	$cart = null;
	$prices = null;
	
} else {
	
	$complete = null;
	
	$shop = new Shop($target[0]);
	$shop -> read($target[1]);
	
	$module -> data = &$shop -> order;
	$cart = &$shop -> cart;
	$prices = &$shop -> prices;
	
}

?>