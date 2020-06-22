<?php defined('isENGINE') or die;
	
	// INNER OPEN/CLOSE
	
	if (is_array($module -> settings['elements']['inner'])) {
		foreach ($module -> settings['elements']['inner'] as $i) {
		
			$module -> var[$type]['inner'] = [];
			
			if (!empty($module -> settings['classes']['defaults'])) {
				$module -> var[$type]['inner'][] = $i;
			}
			if ($module -> settings['classes']['inner']) { $module -> var[$type]['inner'][] = $module -> settings['classes']['inner']; }
			
			$module -> var[$type]['inner'] = new htmlElement('div', $module -> var[$type]['inner']);
			
			echo lang('menu:' . $i . ':' . $element['name']);
			
			$module -> var[$type]['inner'] -> close();
			
		}
	}
	
	if ($module -> settings['elements']['linkimage']) :
		
		// IMAGEWRAPPER OPEN
		
		if ($module -> settings['elements']['imagewrapper']) {
		
			$module -> var[$type]['imagewrapper'] = [];
			
			if (!empty($module -> settings['classes']['defaults'])) {
				$module -> var[$type]['imagewrapper'][] = $module -> param . '_imagewrapper';
			}
			if ($module -> settings['classes']['imagewrapper']) { $module -> var[$type]['imagewrapper'][] = $module -> settings['classes']['imagewrapper']; }
			
			$module -> var[$type]['imagewrapper'] = new htmlElement('div', $module -> var[$type]['imagewrapper']);
			
		}
		
		$path = ($module -> settings['elements']['linkimage'] === true) ? $module -> param : $module -> settings['elements']['linkimage'];
		
		if (file_exists(PATH_LOCAL . DS . $path .  DS . $element['name'] . '.png')) :
		
?>
	
	<img class="<?php
		
		echo $module -> param . '_linkimage' . ' ';
		if ($module -> settings['classes']['linkimage']) { echo $module -> settings['classes']['linkimage'] . ' '; }
		
	?>" src="<?= $uri -> site . URI_LOCAL . '/' . $path . '/' . $element['name'] . '.png'; ?>" />
	
<?php
		
		endif;
		unset($path);
		
		// IMAGEWRAPPER CLOSE
		
		if (isset($module -> var[$type]['imagewrapper'])) {
			$module -> var[$type]['imagewrapper'] -> close();
		}
		
	endif;
	
	// ICONS OPEN/CLOSE
	
	if ($module -> settings['icons'][$element['name']]) {
		$module -> var[$type]['icon'] = new htmlElement('i', $module -> settings['icons'][$element['name']]);
		$module -> var[$type]['icon'] -> close();
	}
	
	// LINKWRAPPER OPEN/CLOSE
	
	if ($module -> settings['elements']['linkwrapper']) {
	
		$module -> var[$type]['linkwrapper'] = [
			'name' => '',
			'class' => []
		];
		
		if ($module -> settings['elements']['linkwrapper'] === 'p') {
			$module -> var[$type]['linkwrapper']['name'] = 'p';
		} elseif ($module -> settings['elements']['linkwrapper'] === 'div') {
			$module -> var[$type]['linkwrapper']['name'] = 'div';
		} else {
			$module -> var[$type]['linkwrapper']['name'] = 'span';
		}
		
		if (!empty($module -> settings['classes']['defaults'])) {
			$module -> var[$type]['linkwrapper']['class'][] = $module -> param . '_linkwrapper';
		}
		if ($module -> settings['classes']['linkwrapper']) { $module -> var[$type]['linkwrapper']['class'][] = $module -> settings['classes']['linkwrapper']; }
		
		$module -> var[$type]['linkwrapper'] = new htmlElement($module -> var[$type]['linkwrapper']['name'], $module -> var[$type]['linkwrapper']['class']);
		
	}
	
	if ($element['type'] === 'generated') {
		
		$parent = datasplit($element['id'], '.');
		
		if (
			!empty($module -> data) && is_array($module -> data) &&
			!empty($parent) && is_array($parent) &&
			!empty($module -> data[$parent[1]][$element['name']])
		) {
			echo $module -> data[$parent[1]][$element['name']];
		} else {
			echo $element['name'];
		}
		
		unset($parent);
		
	} else {
		echo lang('menu:' . $element['name'], 'return');
	}
	
	if (isset($module -> var[$type]['linkwrapper'])) {
		$module -> var[$type]['linkwrapper'] -> close();
	}
	
	// SR-ONLY OPEN/CLOSE
	
	if (
		$type !== 'submenu' &&
		(
			$element['name'] === thispage('is') ||
			$element['name'] === objectGet('content', 'name')
		)
	) {
		$module -> var[$type]['sr'] = new htmlElement('span', 'sr-only');
		echo '(current)';
		$module -> var[$type]['sr'] -> close();
	}
	
?>