<?php defined('isCMS') or die;

	// UL/DIV OPEN
	
	if (!(
		$module -> settings['elements']['ul'] === 'disable' ||
		$module -> settings['elements']['ul']['submenu'] === 'disable'
	)) {
		
		$module -> var['submenu']['ul'][$counter] = [
			'name' => '',
			'class' => []
		];
		
		// name
		if (
			$module -> settings['bootstrap'] ||
			$module -> settings['elements']['ul'] === 'div' ||
			$module -> settings['elements']['ul']['submenu'] === 'div'
		) {
			$module -> var['submenu']['ul'][$counter]['name'] = 'div';
		} else {
			$module -> var['submenu']['ul'][$counter]['name'] = 'ul';
		}
		
		// class
		if ($module -> settings['bootstrap']) {
			$module -> var['submenu']['ul'][$counter]['class'][] = 'dropdown-menu';
		}
		if ($module -> settings['classes']['ul'] && is_string($module -> settings['classes']['ul']) && !isset($module -> settings['classes']['ul']['submenu'])) {
			$module -> var['submenu']['ul'][$counter]['class'][] = $module -> settings['classes']['ul'];
		}
		if (!empty($module -> settings['classes']['ul']['submenu']) && is_string($module -> settings['classes']['ul']['submenu'])) {
			$module -> var['submenu']['ul'][$counter]['class'][] = $module -> settings['classes']['ul']['submenu'];
		}
		
		$module -> var['submenu']['ul'][$counter] = new htmlElement($module -> var['submenu']['ul'][$counter]['name'], $module -> var['submenu']['ul'][$counter]['class']);
		
	}
	
	// SUBITEM
	
	funcModuleMenu_Create($item, $module, $counter);
	
	// UL/DIV CLOSE
	
	if (isset($module -> var['submenu']['ul'][$counter])) {
		$module -> var['submenu']['ul'][$counter] -> close();
	}
	
	unset($module -> var['submenu']['ul'][$counter]);

?>