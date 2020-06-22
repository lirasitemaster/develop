<?php defined('isCMS') or die;

$filter = (object) [
	'items' => null,
	'string' => null,
	'url' => null,
	'array' => [],
	'data' => [],
	'counter' => [],
	'form' => $module -> settings['filtration']['filter'],
	'options' => $module -> settings['filtration']['options'],
	'class' => $module -> settings['filtration']['classes']
];

//print_r($content);
//print_r(count($content -> data));

if (
	objectIs($filter -> form) &&
	count($filter -> form)
) {
	
	// здесь мы создаем автофильтр
	
	if (
		!empty($filter -> options['auto']) &&
		objectIs($content -> data)
	) {
		
		foreach ($content -> data as $item) {
			
			foreach ($filter -> form as $filter_key => $filter_item) {
				
				if (!is_array($filter_item) && array_key_exists($filter_key, $item['data'])) {
					
					//!array_key_exists($filter_key, $filter -> data) // новая строка
					
					if (is_array($item['data'][$filter_key])) {
						
						foreach ($item['data'][$filter_key] as $i) {
							
							if (objectIs($filter -> data[$filter_key]) && array_key_exists($i, $filter -> data[$filter_key])) {
								continue;
							}
							
							$l = lang('filter:' . $i);
							$filter -> data[$filter_key][$i] = $l ? $l : $i;
							
							if (!isset($filter -> counter[$filter_key][$i])) {
								$filter -> counter[$filter_key][$i] = 1;
							} elseif (array_key_exists($i, $filter -> counter[$filter_key])) {
								$filter -> counter[$filter_key][$i]++;
							}
							
						}
						unset($i, $l);
						
					} elseif (!objectIs($filter -> data[$filter_key]) || !array_key_exists($item['data'][$filter_key], $filter -> data[$filter_key])) {
						
						$l = lang('filter:' . $item['data'][$filter_key]);
						$filter -> data[$filter_key][$item['data'][$filter_key]] = $l ? $l : $item['data'][$filter_key];
						
						if (!isset($filter -> counter[$filter_key][$item['data'][$filter_key]])) {
							$filter -> counter[$filter_key][$item['data'][$filter_key]] = 1;
						} elseif (array_key_exists($item['data'][$filter_key], $filter -> counter[$filter_key])) {
							$filter -> counter[$filter_key][$item['data'][$filter_key]]++;
						}
						
						unset($l);
						
					}
					
				} elseif (objectIs($filter_item) && !isset($filter -> data[$filter_key])) {
					$filter -> data[$filter_key] = null;
				}
				
			}
			
			unset($filter_key, $filter_item);
			
			//echo '<pre>' . print_r($filter, true) . '</pre><br>';
			
		}
		
		unset($item);
		
		// здесь мы удаляем все одинаковые значения
		// а затем сортируем получившийся массив
		
		if (objectIs($filter -> data)) {
			
			foreach ($filter -> data as $key => &$item) {
				
				$sort = dataParse($filter -> form[$key]);
				
				if (objectIs($sort) && !empty($sort[1])) {
					$filter -> form[$key] = $sort[0];
				}
				
				if (objectIs($item)) {
					
					$item = objectClear($item, false, true);
					// то же самое:
					//$item = array_unique($item);
					//$item = array_diff($item, ['']);
					
					if (objectIs($sort) && !empty($sort[1])) {
						
						// regular - SORT_REGULAR, обычное сравнение элементов
						// numeric - SORT_NUMERIC, числовое сравнение элементов
						// string - SORT_LOCALE_STRING, сравнивает элементы как строки с учетом текущей локали
						// * по-умолчанию сортировка производится с флагом SORT_NATURAL и SORT_FLAG_CASE
						// * подробнее - см. https://www.php.net/manual/ru/function.sort.php
						
						if (!empty($sort[2])) {
							if ($sort[2] === 'string') {
								$sort[2] = SORT_LOCALE_STRING;
							} elseif ($sort[2] === 'numeric') {
								$sort[2] = SORT_NUMERIC;
							} else {
								$sort[2] = SORT_REGULAR;
							}
						}
						
						if ($sort[1] === 'shuffle') {
							shuffle($item);
						} else {
							if ($sort[1] === 'desc') {
								arsort(
									$item,
									empty($sort[2]) ? SORT_NATURAL | SORT_FLAG_CASE : $sort[2]
								);
							} else {
								asort(
									$item,
									empty($sort[2]) ? SORT_NATURAL | SORT_FLAG_CASE : $sort[2]
								);
							}
						}
						
					} else {
						asort($item, SORT_LOCALE_STRING | SORT_FLAG_CASE);
					}
					
				}
				
			}
			
		}
		
		unset($key, $item);
		
	}
	
	// здесь мы обрабатываем обычным фильтром
	
	foreach ($filter -> form as $filter_key => $filter_item) {
		
		if (objectIs($filter_item)) {
			
			$filter -> data[$filter_key] = $filter_item['data'];
			$filter -> form[$filter_key] = $filter_item['type'];
			
		}
		
	}
	
	unset($filter_key, $filter_item);
	
}

// здесь мы фильтруем массив данных по заданным в пути параметрам

if (!empty($content -> filter)) {
	
	if (!empty($content -> filter['items'])) {
		$filter -> items = $content -> filter['items'];
	}
	
	unset($content -> filter['items']);
	
	foreach ($content -> filter as $key => &$item) {
		
		$item = clear($item, 'urldecode');
		$filter -> string .= $key . ':' . $item . ' ';
		$filter -> url .= $key . '/' . $item . '/';
		
		if (
			$filter -> form[$key] === 'numeric' ||
			$filter -> form[$key] === 'range' ||
			$filter -> form[$key] === 'range_bootstrap' ||
			$filter -> form[$key] === 'range_jqueryui'
		) {
			$sym = '_';
		} elseif ($filter -> form[$key] === 'and') {
			$sym = '+';
		} else {
			$sym = ':';
		}
		$item = datasplit($item, $sym);		
		
	}
	
	unset($key, $item);
	
	$filter -> array = $content -> filter;
	
	//print_r($content -> filter);
	//$f = objectToString($content -> filter, ' ', ':');
	//echo $filter -> string;
	
	$content -> data = dbUse($content -> data, 'filter', ['filter' => $filter -> string]);
	
}

// дополнительное условие вывода количества результатов на странице

if (!empty($filter -> items) && $content -> type === 'list') {
	$module -> settings['display']['count'][$content -> type] = $filter -> items;
}

//echo '<pre>' . print_r($filter, true) . '</pre><hr>';

?>