<?php defined('isENGINE') or die;

if ($module -> settings['type'] === 'social') { 
	$data = lang('social');
} else {
	$data = &$module -> settings['data'];
}

if (!empty($module -> this) && $module -> this === true) {
	$module -> this = $module -> param;
}

$print = null;

if (objectIs($data)) {
	foreach ($data as $key => &$item) {
		if (objectIs($item)) {
			
			if (!empty($item['filter'])) {
				
				$item['filter'] = dataParse($item['filter'], false);
				
				if (objectIs($item['filter'])) {
					$link = null;
					foreach ($item['filter'] as $k => $i) {
						if (objectIs($i)) {
							$link .= $k . '/' . objectToString($i, ':') . '/';
						}
					}
					$item['filter'] = $link;
					unset($k, $i, $link);
				}
				
			}
			
			if (!empty($module -> this)) {
				require PATH_ASSETS . 'modules' . DS . $module -> name . DS . $module -> this . '.php';
			}
			
		}
	}
	unset($key, $item);
}

if (!empty($module -> this)) {
	$module -> template = 'default';
	$module -> tpath = $module -> path . 'templates' . DS . 'default.php';
}

/*
теперь этот модуль можно запускать двумя разными способами:

1. как обычно, и тогда будет грузиться заданный шаблон
2. ускоренная автоматизированная загрузка - через указание шаблона в параметре this

в последнем случае в ваше распоряжение сразу же поступают переменные $key и $item,
но записывать значения надо в переменную $print
*/

?>