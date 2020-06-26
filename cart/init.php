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

if (!empty($sets['return'])) {
	
	$return = preg_replace_callback('/\{(.*?)\}/ui', function($i) use ($shop) {
		
		$i = dataSplit($i[1], '.');
		
		$r = array_shift($i);
		$r = $shop -> $r;
		
		if (objectIs($i)) {
			foreach ($i as $ii) {
				$r = objectIs($r) && !objectIs($ii) && array_key_exists($ii, $r) ? $r[$ii] : null;
			}
			unset($ii);
		}
		
		unset($i);
		return $r;
		
	}, $sets['return']);
	
	unset($module);
	
	$module = (object) [
		'return' => $return
	];
	
	unset($return);
	
}

?>