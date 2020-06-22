<?php defined('isCMS') or die;

global $template;
global $libraries;
global $lang;

foreach ($template -> settings -> libraries as $item) {
	
	$item = dataParse($item);
	
	// 0 - name
	// 1 - vendor
	// 2 - version
	// 3 - variant/cdn
	// 4 - include/exclude types
	// 5 - place loading template or mode for php libraries
	
	$item = objectFill($item, [0, 1, 2, 3, 4, 5]);
	
	if (empty($item[1])) {
		$item[1] = 'system';
	}
	
	//echo '[' . print_r($item, 1) . ']<br>';
	
	// если имя содержит точку, то она преобразуется в спец.знак
	if (strpos($item[0], '.') !== false) { $item[0] = str_replace('.', '--', $item[0]); }
	
	if (
		objectIs($libraries -> process)
	) {
		$current = $libraries -> process[$item[0] . ':' . $item[1]];
	} else {
		$current = null;
	}
	
	// прогон предварительной обработки
	
	if (objectIs($current)) {
		
		// предварительная проверка по версии
		if (!empty($item[2]) && empty($current[$item[2]])) {
			
			// читаем все версии, какие есть в настройках, кроме default
			$keys = array_flip(array_keys($current));
			unset($keys['default']);
			
			// фильтруем версии и оставляем самую подходящую
			// если же подходящей версии не нашлось,
			// версия остается прежней, а у нас есть еще условия ниже
			if (!empty($keys)) {
				krsort($keys);
				foreach ($keys as $k => $i) {
					if (strpos($k, $item[2]) === 0) {
						$item[2] = $k;
						break;
					}
				}
				unset($k, $i);
			}
			unset($keys);
			
		}
		
		if (!empty($item[2]) && !empty($current[$item[2]])) {
			// если версия совпадает, то берем ее настройки
			$current = $current[$item[2]];
		} elseif (!empty($current['default'])) {
			// если версия не совпадает, то берем настройки по умолчанию
			$current = $current['default'];
		} else {
			// иначе вообще удаляем библиотеку
			unset($current);
		}
		
	}
	
	// если имя содержит спец.знак, то он преобразуется в точку
	if (strpos($item[0], '--') !== false) { $item[0] = str_replace('--', '.', $item[0]); }
	
	// устанавливаем локальный адрес для папок с библиотеками
	
	$path = PATH_LIBRARIES . $item[1] . DS . $item[0] . DS;
	
	// проверяем наличие библиотек и сведений о них
	
	if (
		empty($current) ||
		!file_exists($path)
	) {
		$libraries -> empty[] = $item;
	} else {
		
		// разбираем исключения
		
		$rules = ['allow' => null, 'deny' => null];
		if (!empty($item[4])) {
			foreach (datasplit($item[4], '.') as $i) {
				if (strpos($i, '!') === 0) {
					$rules['deny'][] = substr($i, 1);
				} else {
					$rules['allow'][] = $i;
				}
			}
			unset($i);
		}
		
		// устанавливаем тип данных
		// это сделано, чтобы не повторять обработку дважды
		
		if (!empty($item[3]) && !empty($current[$item[3]])) {
			$target = $item[3];
		} else {
			$target = 'local';
		}
		
		// записываем библиотеку в список
		
		$libraries -> list[$item[0] . ':' . $item[1]] = [
			'name' => $item[0],
			'vendor' => $item[1],
			'version' => $item[2],
			'variant' => $item[3], //variant/cdn
			'place' => $item[5],
			'data' => []
		];
		
		foreach ($current[$target] as $k => $i) {
			
			if (
				objectIs($rules['deny']) && in_array($k, $rules['deny']) ||
				objectIs($rules['allow']) && !in_array($k, $rules['allow'])
			) {
				continue;
			}
			
			foreach ($i as $str) {
				
				if (strpos($str, '{') !== false) {
					$str = str_ireplace(
						['{name}', '{version}', '{lang}', '{langu}', '{langcode}', '{langcodeu}'],
						[$item[0], $item[2], $lang -> lang, strtoupper($lang -> lang), $lang -> code, strtoupper($lang -> code)],
					$str);
				}
				
				//if ($target === 'local') {
				if (strpos($target, 'cdn') === false) {
					$str = $item[1] . '/' . $item[0] . '/' . $str;
				}
				
				if ($k === 'php') {
					if (empty($item[5]) || $item[5] === DEFAULT_MODE) {
						$libraries -> preload[] = $str;
					}
				} else {
					$libraries -> list[$item[0] . ':' . $item[1]]['data'][$k][] = $str;
				}
				
			}
		}
		
		unset($k, $i, $str, $target);
		
	}
	
	unset($path);
	
	/*
	print_r($current);
	echo '<br>';
	print_r($item);
	echo '<br>';
	echo '<hr>';
	*/
	
}

unset($libraries -> process);

?>