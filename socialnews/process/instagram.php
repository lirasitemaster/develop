<?php defined('isCMS') or die;

$result = $result -> data;

foreach ($result as $key => $item) {
	
	if (isset($module -> settings['disable']) && in_array($item -> id, $module -> settings['disable'])) {
		
		$module -> settings['count']++;
		
	} elseif ($key < $module -> settings['count']) {
		
		$module -> data[$key] = (object) array(
			'date' => $item -> created_time,
			'text' => $item -> caption -> text,
			'link' => $item -> link,
			'images' => array()
		);
		
		if ($item -> type === 'image' || $item -> type === 'video') {
			
			$module -> data[$key] -> images[] = $item -> images -> standard_resolution -> url;
			
			if ($item -> type === 'video') {
				$module -> data[$key] -> video = $item -> videos -> standard_resolution -> url;
			}
			
		} elseif ($item -> type === 'carousel') {
			
			foreach ($item -> carousel_media as $images) {
				$module -> data[$key] -> images[] = $images -> images -> standard_resolution -> url;
			}
			
		}
		
	}
	
}

unset($result);

?>