<?php defined('isENGINE') or die;

if (!empty($sets['mainslider']['enable'])) {
	
	$print = null;
	
	$print .= '<div class="' . $sets['classes']['mainslider']['common'] . '">';
	
	if (empty($sets['slider']['controlblock']) && !empty($sets['mainslider']['arrows'])) {
		$print .= '<div class="' . $sets['classes']['mainslider']['control']['common'] . ' ' . $sets['classes']['mainslider']['control']['previous'] . '"></div>';
	}
	
	$print .= '<div class="mainslider-' . $name . ' ' . $sets['classes']['mainslider']['container'] . ' ' . $sets['classes']['subcontrol'] . '">';
	
	foreach ($list as $current) {
	foreach ($current as $item) {
		
		$itemname = substr($item, 0, strripos($item, '.'));
		$print .= '<div class="' . $sets['classes']['mainslider']['item'] . '">';
		
		if (!empty($sets['gallery']['enable'])) {
			$print .= '<a data-fancybox="gallery-' . $name . '" href="' . $init['url'] . $item . '">';
		}
	
		if (!empty($sets['special']) && $sets['special'] === 'background') {
			
			$print .= '<div class="' . $sets['classes']['mainslider']['image'] . '" style="background-image:url(\'' . $init['url'] . $item . '\');"></div>';
			
			if (!empty($sets['gallery']['thumbs'])) {
				$print .= '<img src="' . $init['url'] . $item . '" class="' . $sets['classes']['gallery']['thumbs'] . '">';
			}
			
		} else {
			
			$print .= '<img ' . (!empty($sets['special']) && $sets['special'] === 'lazy' ? 'data-lazy' : 'src') . '="' . $init['url'] . $item . '" class="' . $sets['classes']['mainslider']['image'] . (!empty($sets['gallery']['thumbs']) ? ' ' . $sets['classes']['gallery']['thumbs'] : null) . '"' . (!empty($sets['seo']) && !empty($init['captions'][$itemname]['seo']) ? ' alt="' . $init['captions'][$itemname]['seo'] . '"' : null) . '/>';
			
		}
		
		if (!empty($sets['gallery']['enable'])) {
			$print .= '</a>';
		}
		
		if (!empty($sets['mainslider']['captions']) && !empty($init['captions'][$itemname]['full'])) {
			$print .= '<div class="' . $sets['classes']['mainslider']['caption'] . (!empty($sets['gallery']['captions']) ? ' ' . $sets['classes']['gallery']['caption'] : null) . '">' . $init['captions'][$itemname]['full'] . '</div>';
		}
		
		$print .= '</div>';
		
	}
	unset($item, $itemname);
	}
	unset($current);
	
	$print .= '</div>';
	
	if (empty($sets['slider']['controlblock']) && !empty($sets['mainslider']['arrows'])) {
		$print .= '<div class="' . $sets['classes']['mainslider']['control']['common'] . ' ' . $sets['classes']['mainslider']['control']['next'] . '"></div>';
	}
	
	$print .= '</div>';
	
	echo $print;
	unset($print);
	
}

?>