<?php defined('isCMS') or die;

$print = null;

$print .= '<div class="' . $sets['classes']['slider']['common'] . ($mansory ? ' ' . $sets['classes']['mansory']['common'] : null) . (objectIs($tiles) ? ' ' . $sets['classes']['tiles'] : null) . '">';

if (empty($sets['slider']['controlblock']) && !empty($sets['slider']['arrows'])) {
	$print .= '<div class="' . $sets['classes']['slider']['control']['common'] . ' ' . $sets['classes']['slider']['control']['previous'] . '"></div>';
}

if (!empty($sets['slider']['enable'])) {
	$print .= '<div class="slider-' . $name . ' ' . $sets['classes']['slider']['container'] . ' ' . $sets['classes']['subcontrol'] . '">';
} elseif ($mansory) {
	$print .= '<div class="' . $sets['classes']['mansory']['container'] . '">';
}

foreach ($list as $current) {
	
	if ($mansory) {
		$print .= '<div class="' . $sets['classes']['mansory']['column'] . '"' . (objectIs($tiles) ? ' data-mansory="' . count($current) . '"' : null) . '>';
	}

	if ($module -> param === 'pages') {
		
		$print .= '<div class="' . $sets['classes']['slider']['item'] . ' ' . $sets['classes']['slider']['noitem'] . ($mansory ? ' ' . $sets['classes']['mansory']['item'] : null) . '"><div class="' . $sets['classes']['slider']['image'] . '"></div></div>';
		
	}
	
	foreach ($current as $item) {
		
		$itemname = substr($item, 0, strripos($item, '.'));
		$print .= '<div class="' . $sets['classes']['slider']['item'] . ($mansory ? ' ' . $sets['classes']['mansory']['item'] : null) . '">';
			
		if (!empty($sets['gallery']['enable']) && empty($sets['mainslider']['enable'])) {
			$print .= '<a data-fancybox="gallery-' . $name . '" href="' . $init['url'] . $item . '">';
		}
		
		if (!empty($sets['special']) && $sets['special'] === 'background') {
			
			$print .= '<div class="' . $sets['classes']['slider']['image'] . '" style="background-image:url(\'' . $init['url'] . $item . '\');"></div>';
			
			if (empty($sets['mainslider']['enable']) && !empty($sets['gallery']['thumbs'])) {
				$print .= '<img src="' . $init['url'] . $item . '" class="' . $sets['classes']['gallery']['thumbs'] . '">';
			}
			
		} else {
			
			$print .= '<img ' . (!empty($sets['special']) && $sets['special'] === 'lazy' ? 'data-lazy' : 'src') . '="' . $init['url'] . $item . '" class="' . $sets['classes']['slider']['image'] . (!empty($sets['gallery']['thumbs']) ? ' ' . $sets['classes']['gallery']['thumbs'] : null) . '"' . (!empty($sets['seo']) && !empty($init['captions'][$itemname]['seo']) ? ' alt="' . $init['captions'][$itemname]['seo'] . '"' : null) . ' />';
			
		}
		
		if (!empty($sets['gallery']['enable']) && empty($sets['mainslider']['enable'])) {
			$print .= '</a>';
		}
		
		if (!empty($sets['slider']['captions']) && !empty($init['captions'][$itemname]['default'])) {
			
			$print .= '<div class="' . $sets['classes']['slider']['caption'] . (!empty($sets['gallery']['captions']) && empty($sets['mainslider']['enable']) ? ' ' . $sets['classes']['gallery']['caption'] : null) . '">' . $init['captions'][$itemname]['default'] . '</div>';
			
		}
		
		$print .= '</div>';
		
	}
	unset($item, $itemname);
	
	if ($module -> param === 'pages') {
		
		$print .= '<div class="' . $sets['classes']['slider']['item'] . ' ' . $sets['classes']['slider']['noitem'] . ($mansory ? ' ' . $sets['classes']['mansory']['item'] : null) . '"><div class="' . $sets['classes']['slider']['image'] . '"></div></div>';
		
	}
	
	if ($mansory) {
		$print .= '</div>';
	}
	
}
unset($current);

if (!empty($sets['slider']['enable']) || $mansory) {
	$print .= '</div>';
}

if (empty($sets['slider']['controlblock']) && !empty($sets['slider']['arrows'])) {
	$print .= '<div class="' . $sets['classes']['slider']['control']['common'] . ' ' . $sets['classes']['slider']['control']['next'] . '"></div>';
}

if (empty($sets['slider']['controlblock']) && !empty($sets['slider']['dots'])) {
	$print .= '<div class="' . $sets['classes']['slider']['control']['common'] . ' ' . $sets['classes']['slider']['control']['dots'] . '"></div>';
}

$print .= '</div>';

echo $print;
unset($print);

?>