<?php defined('isENGINE') or die;
	
	if (!empty($module -> settings['separator']) && $element['level'] == 1) {
		if ($first) {
			$first = null;
		} else {
			require $module -> elements . 'separator.php';
		}
	}
	
	require $module -> elements . 'link_open.php';
	
?><a href="<?php
	
	if ($element['type'] === 'home') {
		echo $uri -> site . $uri -> path -> base;
	} elseif ($element['type'] === 'nolink') {
		echo 'javascript:void(0);';
	} elseif ($element['type'] === 'none' || $element['type'] === 'action') {
		echo str_replace('.', '/', $element['value']);
	} else {
		echo $uri -> site . str_replace('.', '/', $element['value']);
	}
	
?>" class="<?php
	
	echo $module -> param . '_link ' . $module -> param . '_link__' . $type . ' ';
	
	if (
		$element['name'] === thispage('is') ||
		$element['name'] === objectGet('content', 'name')
	) {
		echo $module -> param . '_link__active' . ' ';
	}
	
	if ($module -> settings['bootstrap']) {
		if ($type === 'item') {
			echo 'nav-link' . ' ';
		} elseif ($type === 'subitem') {
			echo 'dropdown-item' . ' ';
		} elseif ($type === 'submenu') {
			echo 'nav-link dropdown-toggle' . ' ';
		}
	}
	
	if ($module -> settings['classes']['link'] && is_string($module -> settings['classes']['link']) && !isset($module -> settings['classes']['link'][$type])) {
		echo $module -> settings['classes']['link'] . ' ';
	}
	if (!empty($module -> settings['classes']['link'][$type]) && is_string($module -> settings['classes']['link'][$type])) {
		echo $module -> settings['classes']['link'][$type] . ' ';
	}
	
	// установка класса для ссылки на главную страницу
	/*
	if ($module -> settings['classes']['link'] && is_string($module -> settings['classes']['link']) && !isset($module -> settings['classes']['link'][$type])) {
		echo $module -> settings['classes']['link'] . ' ';
	}
	*/
	
	// перенос классов с элемента li
	if (!isset($module -> var[$type]['li'])) {
		echo $module -> param . '_item item_' . $element['name'] . ' ';
		
		if ($module -> settings['bootstrap'] && ($type === 'item' || $type === 'submenu')) {
			echo 'nav-item' . ' ';
		}
		if ($module -> settings['bootstrap'] && $type === 'submenu') {
			echo 'dropdown' . ' ';
		}
		if ($type === 'submenu' && $module -> settings['classes']['submenu']) {
			echo $module -> settings['classes']['submenu'] . ' ';
		}
		
		if ($module -> settings['classes']['li'] && is_string($module -> settings['classes']['li']) && !isset($module -> settings['classes']['li'][$type])) {
			echo $module -> settings['classes']['li'] . ' ';
		}
		if (!empty($module -> settings['classes']['li'][$type]) && is_string($module -> settings['classes']['li'][$type])) {
			echo $module -> settings['classes']['li'][$type] . ' ';
		}
		if (
			$element['name'] === thispage('is') ||
			$element['name'] === objectGet('content', 'name') ||
			$element['type'] === 'home' && $module -> settings['elements']['homeactive']
		) {
			echo $module -> param . '_item__active' . ' ';
			if ($module -> settings['bootstrap']) { echo 'active' . ' '; }
			if ($module -> settings['classes']['active']) { echo $module -> settings['classes']['active'] . ' '; }
		}
		if (
			$element['type'] === 'home' && $module -> settings['classes']['home']
		) {
			echo $module -> settings['classes']['home'] . ' ';
		}
		
	}
	
?>"<?php
	
	if (is_array($module -> settings['modal']) && in_array($element['name'], $module -> settings['modal'])) {
		
		echo 'data-toggle="modal"' . ' ';
		echo 'data-target="#' . $element['name'] . '"' . ' ';
		
	}
	if ($type === 'submenu' && $module -> settings['bootstrap']) {
		
		echo 'data-toggle="dropdown"' . ' ';
		echo 'role="button"' . ' ';
		echo 'aria-haspopup="true"' . ' ';
		echo 'aria-expanded="false"' . ' ';
		
	}
	
?>><?php
	
	require $module -> elements . 'link_inside.php';
	
?></a><?php
	
	if ($type === 'submenu') {
		require $module -> elements . 'submenu.php';
	}
	
	require $module -> elements . 'link_close.php';
	
?>