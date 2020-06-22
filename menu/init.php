<?php defined('isENGINE') or die;

global $template;
global $structure;
global $content;
global $uri;

require_once $module -> process . 'processor.php';

if ($module -> this) {
	//if (thispage('is') !== 'home') {
	if (!thispage('home')) {
		$module -> settings['custom'] = thispage('is');
	} elseif (count($template -> page['parents'])) {
		$module -> settings['custom'] = end($template -> page['parents']);
	}
}

// обновляем структуру, если задано кастомизирование

if (!empty($module -> settings['custom'])) {
	
	if (!is_array($module -> settings['custom'])) {
		$module -> settings['custom'] = [$module -> settings['custom']];
	}
	
	$data = [];
	$result = [];
	$custom = [];
	
	funcModuleMenu_Custom($structure, $module -> settings['custom'], $result);
	$result = array_merge(array_flip($module -> settings['custom']), $result);
	
	foreach ($result as $item) {
		if ($item[0] === 'list') {
			foreach ($item[1] as $k => $i) {
				$data[$k] = $i;
			}
		} else {
			$data[$item[0]] = $item[1];
		}
	}
	
} else {
	$data = $structure;
}

// генератор материалов

if (
	!empty($module -> settings['generator']) &&
	is_array($module -> settings['generator'])
) {
	$module -> data = funcModuleMenu_Generator($module -> settings['generator']);
}

//echo '<hr>settings-generator:<br>' . print_r($module -> settings['generator'], true);
//echo '<hr>list:<br>' . print_r($module -> data, true);
//echo '<hr>structure:<br>' . print_r($structure, true);

?>