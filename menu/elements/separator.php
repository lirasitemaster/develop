<?php defined('isENGINE') or die;
	
	// LI/A OPEN
	
	if (!(
		$module -> settings['elements']['li'] === 'disable' ||
		$module -> settings['elements']['li']['separator'] === 'disable' ||
		empty($module -> settings['separator'])
	)) {
		
		$module -> var['separator']['li'] = [
			'name' => '',
			'class' => []
		];
		
		// name
		if (
			$module -> settings['elements']['li'] === 'div' ||
			$module -> settings['elements']['li']['separator'] === 'div'
		) {
			$module -> var['separator']['li']['name'] = 'div';
		} else {
			$module -> var['separator']['li']['name'] = 'li';
		}
		
		// class
		if (!empty($module -> settings['classes']['defaults'])) {
			$module -> var['separator']['li']['class'][] = $module -> param . '_item item_separator';
		}
		if ($module -> settings['classes']['li'] && is_string($module -> settings['classes']['li']) && !isset($module -> settings['classes']['li']['separator'])) {
			$module -> var['separator']['li']['class'][] = $module -> settings['classes']['li'];
		}
		if (!empty($module -> settings['classes']['li']['separator']) && is_string($module -> settings['classes']['li']['separator'])) {
			$module -> var['separator']['li']['class'][] = $module -> settings['classes']['li']['separator'];
		}
		
		$module -> var['separator']['li'] = new htmlElement($module -> var['separator']['li']['name'], $module -> var['separator']['li']['class']);
		
	}
	
	echo htmlspecialchars($module -> settings['separator']);
	
	if (isset($module -> var['separator']['li'])) {
		$module -> var['separator']['li'] -> close();
	}
	
?>