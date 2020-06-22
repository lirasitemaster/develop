<?php defined('isCMS') or die;

// выполняем обрезание до нужного числа
if (!empty($module -> settings -> limit)) {
	if (
		!empty($module -> settings -> order) &&
		$module -> settings -> order === 'desc'
	) {
		$module -> data['items'] = array_slice($module -> data['items'], 0 - $module -> settings -> limit);
	} else {
		$module -> data['items'] = array_slice($module -> data['items'], 0, $module -> settings -> limit);
	}
}

// объединяем с предыдущими
if (!empty($module -> settings -> merge)) {
	$module -> var['mergefile'] = $module -> var['path'] . '_merge.ini';
	
	if (!file_exists($module -> var['mergefile'])) {
		// если файла для слияния нет - то просто создаем его
		file_put_contents($module -> var['mergefile'], json_encode($module -> data['items'], JSON_UNESCAPED_UNICODE));
	} else {
		
		// а вот если он есть...
		// тогда начинаем проверять, объединять, песочить
		
		$module -> var['merge'] = json_decode(file_get_contents($module -> var['mergefile']), true);
		$module -> var['md5'] = [
			'item' => '',
			'array' => []
		];
		
		foreach ($module -> var['merge'] as $item) {
			$module -> var['md5']['array'][] = md5(json_encode($item, JSON_UNESCAPED_UNICODE));
		}
		
		foreach ($module -> data['items'] as $item) {
			$module -> var['md5']['item'] = md5(json_encode($item, JSON_UNESCAPED_UNICODE));
			if (!in_array($module -> var['md5']['item'], $module -> var['md5']['array'])) {
				// сюда мы просто добавляем отсутствующие данные в общую кучу
				$module -> var['merge'][] = $item;
			}
		}
		
		file_put_contents($module -> var['mergefile'], json_encode($module -> var['merge'], JSON_UNESCAPED_UNICODE));
		
		// а уже потом сюда можно допилить - эх, разгуляйся, душа!
		
	}
	
}

// сохраняем в файлы в материалах
if (!empty($module -> settings -> save)) {
	
	$module -> var['articlefolder'] = PATH_CONTENT . DS . $module -> settings -> save -> folder;
	
	if (!file_exists($module -> var['articlefolder'])) {
		mkdir($module -> var['articlefolder']);
	}
	
	$module -> var['md5'] = [
		'item' => '',
		'articles' => [],
		'array' => [],
		'count' => 0
	];
	
	$module -> var['md5']['articles'] = fileconnect($module -> var['articlefolder'], 'ini'); // now fileconnect is localList($path, ['return' => 'files'/*, 'type' => $ext*/])
	
	foreach ($module -> var['md5']['articles'] as $item) {
		$module -> var['md5']['array'][] = md5_file($module -> var['articlefolder'] . DS . $item);
	}
	
	if (
		empty($module -> data['items']) &&
		!empty($module -> settings -> merge)
	) {
		$module -> data['items'] = json_decode(file_get_contents($module -> var['mergefile']), true);
	}
	
	foreach ($module -> data['items'] as $item) {
		
		$module -> var['md5']['item'] = md5(json_encode($item, JSON_UNESCAPED_UNICODE));
		
		if (!in_array($module -> var['md5']['item'], $module -> var['md5']['array'])) {
			
			$module -> var['md5']['count']++;
			
			if (!empty($module -> settings -> save -> name)) {
				$module -> var['articlename'] = $item[$module -> settings -> save -> name];
			} else {
				$module -> var['articlename'] = $module -> var['md5']['count'];
			}
			$module -> var['articlename'] = $module -> var['articlefolder'] . DS . $module -> var['articlename'] . '_' . time() . '.ini';
			
			file_put_contents($module -> var['articlename'], json_encode($item, JSON_UNESCAPED_UNICODE));
			
		}
		
	}
	
}

?>