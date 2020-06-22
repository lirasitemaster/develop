<?php defined('isENGINE') or die;

if (isset($module -> settings['disable'])) {
	$module -> settings['disable'] = json_decode( json_encode($module -> settings['disable']), true );
}

if (!empty($result -> response -> items)) {
foreach ($result -> response -> items as $key => $item) {
	
	$skip = null;
	
	if (!empty($module -> settings['disable']) && in_array($item -> id, $module -> settings['disable'])) {
		$skip = true;
	} elseif (!empty($module -> settings['selfonly']) && !empty($item -> signer_id)) {
		$skip = true;
	} elseif (empty($module -> settings['repost']) && !empty($item -> copy_history[0])) {
		$skip = true;
	} elseif (!empty($module -> settings['rules'])) {
		
		if (empty($module -> settings['repost'])) {
			if (
				($module -> settings['rules'] === 'text' && !$item -> text) ||
				($module -> settings['rules'] === 'images' && !$item -> attachments) ||
				($module -> settings['rules'] === 'both' && (!$item -> text || !$item -> attachments))
			) {
				//$skip = true;
			}
			if (!empty($item -> copy_history[0])) {
				unset($item -> copy_history[0]);
			}
		} else {
			if (
				($module -> settings['rules'] === 'text' && !$item -> text && !$item -> copy_history[0] -> text) ||
				($module -> settings['rules'] === 'images' && !$item -> attachments && !$item -> copy_history[0] -> attachments) ||
				($module -> settings['rules'] === 'both' && (!$item -> text || !$item -> attachments) && (!$item -> copy_history[0] -> text || !$item -> copy_history[0] -> attachments))
			) {
				//$skip = true;
			}
		}
		
	}
	
	if ($skip) {
		$module -> settings['count']++;
	}
	
	if (!$skip && $key < $module -> settings['count']) {
		
		//print_r($item);
		//echo '<br>---------------------------<br>';
		
		//copy_history
		if (!empty($item -> copy_history[0])) {
			$item -> text = $item -> copy_history[0] -> text;
			$item -> attachments = $item -> copy_history[0] -> attachments;
		}
		
		if (isset($module -> settings['defaults'])) {
			if (!$item -> text && $module -> settings['defaults']['text'][$currlang]) {
				$item -> text = $module -> settings['defaults']['text'][$currlang];
			} elseif (!$item -> text && $module -> settings['defaults']['text']) {
				$item -> text = $module -> settings['defaults']['text'];
			}
			if (!$item -> images && $module -> settings['defaults']['images'][$currlang]) {
				$item -> images = $module -> settings['defaults']['images'][$currlang];
			} elseif (!$item -> images && $module -> settings['defaults']['images']) {
				$item -> images = $module -> settings['defaults']['images'];
			}
		}
		
		$module -> data[$key] = (object) array(
			'link' => 'http://vk.com/wall' . $item -> owner_id . '_' . $item -> id,
			'date' => $item -> date,
			'text' => $item -> text,
			'title' => mb_substr($item -> text, 0, 100),
			'images' => array()
		);
		
		foreach ($item -> attachments as $images) {
			if ($images -> type === 'photo') {
				foreach ($images -> photo -> sizes as $image) {
					if ($image -> type === 'x') {
						$module -> data[$key] -> images[] = $image -> url;
					}
				}
			}
		}
		
	}
	
}
}

unset($result);
?>