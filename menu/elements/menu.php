<?php defined('isENGINE') or die;

// NAV OPEN

if (
	($module -> settings['bootstrap'] || $module -> settings['elements']['nav']) &&
	$module -> settings['elements']['nav'] !== 'disable'
) {
	
	$module -> var['menu']['nav'] = [];
	
	if (!empty($module -> settings['classes']['defaults'])) {
		$module -> var['menu']['nav'][] = $module -> param;
	}
	if ($module -> settings['bootstrap']) {
		$module -> var['menu']['nav'][] = 'navbar';
	}
	if ($module -> settings['classes']['nav']) {
		$module -> var['menu']['nav'][] = $module -> settings['classes']['nav'];
	}
	
	$module -> var['menu']['nav'] = new htmlElement('nav', $module -> var['menu']['nav'], $module -> param);
	
}

require $module -> elements . 'elements.php';

// DIV BODY OPEN

if ($module -> settings['elements']['body']) {
	
	$module -> var['menu']['divbody'] = [
		'class' => [],
		'id' => ''
	];
	
	$module -> var['menu']['divbody']['class'] = [];
	
	if (!empty($module -> settings['classes']['defaults'])) {
		$module -> var['menu']['divbody']['class'][] = $module -> param . '_body';
	}
	if (
		$module -> settings['collapse'] &&
		$module -> settings['bootstrap']
	) {
		$module -> var['menu']['divbody']['class'][] = 'collapse navbar-collapse';
	}
	if ($module -> settings['classes']['body']) {
		$module -> var['menu']['divbody']['class'][] = $module -> settings['classes']['body'];
	}
	
	if ($module -> settings['bootstrap']) {
		$module -> var['menu']['divbody']['id'] = 'navbar_' . $module -> param;
	} elseif (!isset($module -> var['menu']['nav'])) {
		$module -> var['menu']['divbody']['id'] = $module -> param;
	}
	
	$module -> var['menu']['divbody'] = new htmlElement('div', $module -> var['menu']['divbody']['class'], $module -> var['menu']['divbody']['id']);
	
}

// BEFORE

if ($module -> settings['elements']['before']) {
	if (file_exists(PATH_CUSTOM . 'modules' . DS . $module -> name . DS . $module -> settings['elements']['before'] . '.php')) {
		require PATH_CUSTOM . 'modules' . DS . $module -> name . DS . $module -> settings['elements']['before'] . '.php';
	} else {
		require $module -> path . 'templates' . DS . $module -> settings['elements']['before'] . '.php';
	}
}

// UL/DIV OPEN

if (
	$module -> settings['elements']['ul'] === 'disable' ||
	$module -> settings['elements']['ul']['menu'] === 'disable'
) {
} else {
	
	$module -> var['menu']['ul'] = [
		'name' => '',
		'class' => [],
		'id' => '',
	];
	
	// name
	if (
		$module -> settings['elements']['ul'] === 'div' ||
		$module -> settings['elements']['ul']['menu'] === 'div'
	) {
		$module -> var['menu']['ul']['name'] = 'div';
	} else {
		$module -> var['menu']['ul']['name'] = 'ul';
	}
	
	// class
	if (
		!empty($module -> settings['classes']['defaults']) &&
		(
			!$module -> settings['elements']['nav'] ||
			$module -> settings['elements']['nav'] === 'disable'
		)
	) {
		$module -> var['menu']['ul']['class'][] = $module -> param;
	}
	if ($module -> settings['bootstrap']) {
		$module -> var['menu']['ul']['class'][] = 'navbar-nav';
	}
	if (
		$module -> settings['collapse'] &&
		$module -> settings['bootstrap'] &&
		!$module -> settings['elements']['body']
	) {
		$module -> var['menu']['ul']['class'][] = 'collapse navbar-collapse';
	}
	
	if ($module -> settings['classes']['ul'] && is_string($module -> settings['classes']['ul'])) {
		$module -> var['menu']['ul']['class'][] = $module -> settings['classes']['ul'];
	}
	if (!empty($module -> settings['classes']['ul']['menu'])) {
		$module -> var['menu']['ul']['class'][] = $module -> settings['classes']['ul']['menu'];
	}
	
	//id
	if ($module -> settings['bootstrap']) {
		$module -> var['menu']['ul']['id'] = 'navbar_' . $module -> param;
	} elseif (!isset($module -> var['menu']['nav']) && !isset($module -> var['menu']['divbody'])) {
		$module -> var['menu']['ul']['id'] = $module -> param;
	}
	
	$module -> var['menu']['ul'] = new htmlElement($module -> var['menu']['ul']['name'], $module -> var['menu']['ul']['class'], $module -> var['menu']['ul']['id']);
	
}

// BEFOREITEMS

if ($module -> settings['elements']['beforeitems']) {
	if (file_exists(PATH_CUSTOM . 'modules' . DS . $module -> name . DS . $module -> settings['elements']['beforeitems'] . '.php')) {
		require PATH_CUSTOM . 'modules' . DS . $module -> name . DS . $module -> settings['elements']['beforeitems'] . '.php';
	} else {
		require $module -> path . 'templates' . DS . $module -> settings['elements']['beforeitems'] . '.php';
	}
}

// ITEM/SUBMENU

funcModuleMenu_Create($data, $module);

// AFTERITEMS

if ($module -> settings['elements']['afteritems']) {
	if (file_exists(PATH_CUSTOM . 'modules' . DS . $module -> name . DS . $module -> settings['elements']['afteritems'] . '.php')) {
		require PATH_CUSTOM . 'modules' . DS . $module -> name . DS . $module -> settings['elements']['afteritems'] . '.php';
	} else {
		require $module -> path . 'templates' . DS . $module -> settings['elements']['afteritems'] . '.php';
	}
}

// UL/DIV CLOSE

if (isset($module -> var['menu']['ul'])) {
	$module -> var['menu']['ul'] -> close();
}
unset($module -> var['menu']['ul']);

// AFTER

if ($module -> settings['elements']['after']) {
	if (file_exists(PATH_CUSTOM . 'modules' . DS . $module -> name . DS . $module -> settings['elements']['after'] . '.php')) {
		require PATH_CUSTOM . 'modules' . DS . $module -> name . DS . $module -> settings['elements']['after'] . '.php';
	} else {
		require $module -> path . 'templates' . DS . $module -> settings['elements']['after'] . '.php';
	}
}

// DIV BODY CLOSE

if (isset($module -> var['menu']['divbody'])) {
	$module -> var['menu']['divbody'] -> close();
}

// NAV CLOSE

if (isset($module -> var['menu']['nav'])) {
	$module -> var['menu']['nav'] -> close();
}

// подгрузка off-canvas меню

if (!empty($module -> settings['offcanvas']['enable'])) {
	require $module -> elements . 'offcanvas.php';
}

// подгрузка модальных окон

if (
	$module -> settings['bootstrap'] &&
	is_array($module -> settings['modal']) &&
	count($module -> settings['modal'])
) :
	foreach ($module -> settings['modal'] as $i) :
		
?>
	
	<!-- Modal -->
	<div id="<?= $i; ?>" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				
				<?php page($i); ?>
				
			</div>
		</div>
	</div>
	
<?php
		
	endforeach;
	unset($i);
endif;

?>