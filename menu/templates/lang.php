<?php defined('isENGINE') or die;
	
	// NAV OPEN
	
	if (
		$module -> settings -> bootstrap ||
		$module -> settings -> elements -> nav
	) {
		
		$module -> var['menu']['nav'] = [];
		
		$module -> var['menu']['nav'][] = $module -> param;
		if ($module -> settings -> bootstrap) {
			$module -> var['menu']['nav'][] = 'navbar';
		}
		if ($module -> settings -> classes -> nav) {
			$module -> var['menu']['nav'][] = $module -> settings -> classes -> nav;
		}
		
		$module -> var['menu']['nav'] = new htmlElement('nav', $module -> var['menu']['nav'], $module -> param);
		
	}
	
	require $module -> elements . 'elements.php';
	
	// DIV BODY OPEN
	
	if ($module -> settings -> elements -> body) {
		
		$module -> var['menu']['divbody'] = [
			'class' => [],
			'id' => ''
		];
		
		$module -> var['menu']['divbody']['class'] = [];
		
		$module -> var['menu']['divbody']['class'][] = $module -> param . '_body';
		if (
			$module -> settings -> collapse &&
			$module -> settings -> bootstrap
		) {
			$module -> var['menu']['divbody']['class'][] = 'collapse navbar-collapse';
		}
		if ($module -> settings -> classes -> body) {
			$module -> var['menu']['divbody']['class'][] = $module -> settings -> classes -> body;
		}
		
		if ($module -> settings -> bootstrap) {
			$module -> var['menu']['divbody']['id'] = 'navbar_' . $module -> param;
		} elseif (!isset($module -> var['menu']['nav'])) {
			$module -> var['menu']['divbody']['id'] = $module -> param;
		}
		
		$module -> var['menu']['divbody'] = new htmlElement('div', $module -> var['menu']['divbody']['class'], $module -> var['menu']['divbody']['id']);
		
	}
	
	// BEFORE
	
	if ($module -> settings -> elements -> before) {
		require $module -> path . DS . 'templates' . DS . $module -> settings -> elements -> before . '.php';
	}
	
	// UL/DIV OPEN
	
	if (
		$module -> settings -> elements -> ul === 'disable' ||
		$module -> settings -> elements -> ul -> menu === 'disable'
	) {
	} else {
		
		$module -> var['menu']['ul'] = [
			'name' => '',
			'class' => [],
			'id' => '',
		];
		
		// name
		if (
			$module -> settings -> elements -> ul === 'div' ||
			$module -> settings -> elements -> ul -> menu === 'div'
		) {
			$module -> var['menu']['ul']['name'] = 'div';
		} else {
			$module -> var['menu']['ul']['name'] = 'ul';
		}
		
		// class
		if (!$module -> settings -> elements -> nav) {
			$module -> var['menu']['ul']['class'][] = $module -> param;
		}
		if ($module -> settings -> bootstrap) {
			$module -> var['menu']['ul']['class'][] = 'navbar-nav';
		}
		if (
			$module -> settings -> collapse &&
			$module -> settings -> bootstrap &&
			!$module -> settings -> elements -> body
		) {
			$module -> var['menu']['ul']['class'][] = 'collapse navbar-collapse';
		}
		
		if ($module -> settings -> classes -> ul && is_string($module -> settings -> classes -> ul)) {
			$module -> var['menu']['ul']['class'][] = $module -> settings -> classes -> ul;
		}
		if (!empty($module -> settings -> classes -> ul -> menu)) {
			$module -> var['menu']['ul']['class'][] = $module -> settings -> classes -> ul -> menu;
		}
		
		//id
		if ($module -> settings -> bootstrap) {
			$module -> var['menu']['ul']['id'] = 'navbar_' . $module -> param;
		} elseif (!isset($module -> var['menu']['nav']) && !isset($module -> var['menu']['divbody'])) {
			$module -> var['menu']['ul']['id'] = $module -> param;
		}
		
		$module -> var['menu']['ul'] = new htmlElement($module -> var['menu']['ul']['name'], $module -> var['menu']['ul']['class'], $module -> var['menu']['ul']['id']);
		
	}
	
	// BEFOREITEMS
	
	if ($module -> settings -> elements -> beforeitems) {
		require $module -> path . DS . 'templates' . DS . $module -> settings -> elements -> beforeitems . '.php';
	}
	
	// ITEMS
	
	//$type === 'item';
	
	foreach ($lang -> list as $target) :
	
	$type === 'item';
	require $module -> elements . 'link_open.php';
	
?>
	
<a href="<?php
	
	if ($target !== ROOT_LANG) {
		echo $template -> url . '/' . $target . $template -> curr -> path;
	} else {
		echo $template -> url . $template -> curr -> path;
	}
	
?>" class="<?php
	
	echo $module -> param . '_link' . ' ';
	
	if ($target === $lang -> lang) {
		echo $module -> param . '_link__active' . ' ';
	}
	
	if ($module -> settings -> bootstrap) {
		echo 'nav-link' . ' ';
	}
	
	if ($module -> settings -> classes -> link && is_string($module -> settings -> classes -> link) && !isset($module -> settings -> classes -> link -> $type)) {
		echo $module -> settings -> classes -> link . ' ';
	}
	
	// перенос классов с элемента li
	if (!isset($module -> var[$type]['li'])) {
		
		echo $module -> param . '_item item_' . $target . ' ';
		
		if ($module -> settings -> bootstrap) {
			echo 'nav-item' . ' ';
		}
		
		if ($module -> settings -> classes -> li && is_string($module -> settings -> classes -> li) && !isset($module -> settings -> classes -> li -> $type)) {
			echo $module -> settings -> classes -> li . ' ';
		}
		
	}
	
?>">
<?php
	
	require $module -> elements . 'link_inside.php';
	require $module -> elements . 'link_close.php';
	
	endforeach;
	
	// AFTERITEMS
	
	if ($module -> settings -> elements -> afteritems) {
		require $module -> path . DS . 'templates' . DS . $module -> settings -> elements -> afteritems . '.php';
	}
	
	// UL/DIV CLOSE
	
	if (isset($module -> var['menu']['ul'])) {
		$module -> var['menu']['ul'] -> close();
	}
	
	// AFTER
	
	if ($module -> settings -> elements -> after) {
		require $module -> path . DS . 'templates' . DS . $module -> settings -> elements -> after . '.php';
	}
	
	// DIV BODY CLOSE
	
	if (isset($module -> var['menu']['divbody'])) {
		$module -> var['menu']['divbody'] -> close();
	}
	
	// NAV CLOSE
	
	if (isset($module -> var['menu']['nav'])) {
		$module -> var['menu']['nav'] -> close();
	}
	
?>