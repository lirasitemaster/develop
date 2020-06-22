<?php defined('isENGINE') or die;

if (!empty($sets['slider']['controlblock'])) {
	
	$print = null;
	
	$print .= '<div class="' . $sets['classes']['slider']['control']['common'] . '">';
		
	if (!empty($sets['slider']['arrows'])) {
		$print .= '<div class="' . $sets['classes']['slider']['control']['previous'] . '"></div><div class="' . $sets['classes']['slider']['control']['next'] . '"></div>';
	}
	
	if (!empty($sets['slider']['dots'])) {
		$print .= '<div class="' . $sets['classes']['slider']['control']['dots'] . '"></div>';
	}
	
	$print .= '</div>';
	
	echo $print;
	unset($print);
	
}

?>