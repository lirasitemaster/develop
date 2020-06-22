<?php defined('isENGINE') or die;

if (!empty($sets['content']) && empty($sets['contentdisable'])) {
	
	$print = null;
	
	$print .= '<div class="' . $sets['classes']['contentslider']['common'] . '"><div class="contentslider-' . $name . ' ' . $sets['classes']['contentslider']['container'] . ' ' . $sets['classes']['subcontrol'] . '">';
	
	foreach ($list as $current) {
		
		if ($module -> param === 'pages') {
			$print .= '<div class="' . $sets['classes']['contentslider']['item'] . ' ' . $sets['classes']['contentslider']['noitem'] . '"></div>';
		}
		
		foreach ($current as $item) {
			$itemname = substr($item, 0, strripos($item, '.'));
			$print .= '<div class="' . $sets['classes']['contentslider']['item'] . '">' . $init['content'][$itemname] . '</div>';
		}
		unset($item, $itemname);
		
		if ($module -> param === 'pages') {
			$print .= '<div class="' . $sets['classes']['contentslider']['item'] . ' ' . $sets['classes']['contentslider']['noitem'] . '"></div>';
		}
		
	}
	unset($current);
	
	$print .= '</div></div>';
	
	echo $print;
	unset($print);
	
}

?>