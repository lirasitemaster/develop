<?php defined('isENGINE') or die;

// разные варианты помещения контента в топ

if ($module -> settings['top']['type'] === 'count') {
	
	$module -> data['top'] = array_slice($content -> data, 0, (int) $module -> settings['top']['value']);
	
	if (!empty($module -> settings['top']['sort'])) {
		$module -> data['top'] = dbUse($module -> data['top'], 'filter', ['sort' => $module -> settings['top']['sort']]);
	}
	
} else {
	
	$parameters = [];
	
	if ($module -> settings['top']['type'] === 'date') {
		
		$value = dataParse($module -> settings['top']['value']);
		
		$parameters['allow'] = '';
		
		if (!empty($value[0]) && $value[0] === 'modify') {
			$parameters['allow'] .= 'm';
		} else {
			$parameters['allow'] .= 'c';
		}
		
		$parameters['allow'] .= 'time:';
		
		$value[1] = dataParseTime($value[1], $value[2]);
		
		$value[3] = time();
		
		$parameters['allow'] .= ($value[3] - $value[1]) . '_' . $value[3];
		
	} elseif ($module -> settings['top']['type'] === 'allow') {
		$parameters['allow'] = $module -> data['value'];
	} elseif ($module -> settings['top']['type'] === 'deny') {
		$parameters['deny'] = $module -> data['value'];
	} elseif ($module -> settings['top']['type'] === 'filter') {
		$parameters['filter'] = $module -> data['value'];
	}
	
	if (!empty($module -> settings['top']['sort'])) {
		$parameters['sort'] = $module -> settings['top']['sort'];
	}
	
	$module -> data['top'] = dbUse($content -> data, 'filter', $parameters);
	
	unset($parameters);
	
}

// обработка топа

if (!empty($module -> data['top'])) {
	
	// ограничиваем материалы в топе
	
	if (!empty($module -> settings['top']['limit'])) {
		$module -> data['top'] = array_slice($module -> data['top'], 0, (int) $module -> settings['top']['limit']);
	}
	
	// ряд настроек для обработки топа
	
	$top = array_keys($module -> data['top']);
	$mark = null;
	
	if (!empty($module -> settings['top']['mark'])) {
		$mark = dataParse($module -> settings['top']['mark']);
		if (empty($mark[1])) { $mark[1] = true; }
	}
	
	// преобразование топа в список
	
	if (!empty($module -> settings['top']['list'])) {
		$module -> data['top'] = $top;
	}
	
	// обработка топового контента в общем списке
	
	foreach ($top as $item) {
		
		if (!empty($module -> settings['top']['delete'])) {
			
			// удаляем
			unset($content -> data[$item]);
			
		} elseif (!empty($mark)) {
			
			// маркируем
			$content -> data[$item]['data'][$mark[0]] = $mark[1];
			
		}
		
	}
	
	unset($item, $top, $mark);
	
}

?>