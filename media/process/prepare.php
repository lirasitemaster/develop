<?php defined('isENGINE') or die;

if (empty($init['list'])) {
	return;
}

$mansory = false;
$cols = null;
$tiles = null;
$class = 'slider';

$prefix = (!empty($name) && $name !== 'default' ? $name : 'media') . '_';

if (empty($sets['slider']['enable']) && !empty($sets['gallery']['enable'])) {
	$class = 'gallery';
	if (!empty($sets['gallery']['mansory'])) {
		$mansory = true;
		if (is_numeric($sets['gallery']['mansory'])) {
			$cols = (int) $sets['gallery']['mansory'];
		} else {
			$tiles = dataParse($sets['gallery']['mansory']);
			//print_r($tiles);
			//exit;
		}
	}
}

// подготовка 

if ($mansory && $cols && $cols > 1) {
	
	$list = array_fill_keys(range(0, $cols - 1), []);
	foreach ($init['list'] as $key => $item) {
		$list[$key % $cols][] = $item;
	}
	unset($item);
	
} elseif ($mansory && objectIs($tiles)) {
	//echo '<p style="font-size: 14px;">' . print_r($tiles, true) . '</p>';
	
	$list = [];
	$f = 0;
	$m = count($init['list']);
	
	for ($c = 0, $l = count($tiles) - 1; $c <= $l; $c++) {
		
		$i = $tiles[$c];
		
		if ($f >= $m || $i == 0) {
			break;
		}
		
		if (strpos($i, '.') !== false) {
			$i = datasplit($i, '.', false);
			if (empty($i[0])) { $i[0] = 1; }
			if (empty($i[1]) || $i[1] < $i[0]) { $i[1] = $i[0]; }
			$i = mt_rand($i[0], $i[1]);
		}
		if (empty($i) || !is_numeric($i)) {
			$i = 1;
		}
		
		$list[] = array_slice($init['list'], $f, $i);
		
		$f = $f + $i;
		
		if ($l == $c) {
			$c = -1;
		}
		
		//echo '<p style="font-size: 14px;">' . print_r($i, true) . '</p>';
		
	}
	
	unset($i, $l, $c, $m, $f);
	
	//print_r($tiles);
	//print_r($list);
	//exit;
	
} else {
	$list = [$init['list']];
}

unset($init['list']);
$current = [];

?>