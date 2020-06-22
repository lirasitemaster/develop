<?php defined('isCMS') or die;

// ссылки
// и условия для фильтрации

global $uri;

$module -> data['url'] = [
	'base' => '/' . $uri -> path -> base . $uri -> path -> string,
	'filter' => !empty($module -> settings['filtration']['enable']) && !empty($filter -> array) ? ($content -> type === 'all' ? 'all/' : 'filter/') : null,
	'filters' => !empty($module -> settings['filtration']['enable']) ? $filter -> url : null
];

if ($content -> type === 'alone') {
	
	// если выводится один материал, то создаем список всех материалов данной группы,
	// подсчитываем их и выясняем позицию текущего материала
	
	$list = new Content([
		null,
		$content -> parent,
		'all',
	]);
	$list -> settings();
	$list -> read();
	
	$module -> data['list'] = array_keys($list -> data);
	
	unset($list);
	
	$module -> data['current'] = array_search($content -> name, $module -> data['list']);
	$module -> data['count'] = count($module -> data['list']);
	
	$skip = (int) $module -> settings['display']['skip']['list'];
	$count = (int) $module -> settings['display']['count']['list'];
	
	// и еще подсчитываем число страниц
	
	if ($count > 0) {
		$module -> data['page'] = ceil(($module -> data['current'] + 1 - $skip) / $count);
		if ($module -> data['page'] < 1) {
			$module -> data['page'] = null;
		}
	}
	
	// кнопки навигации
	
	$module -> data['navigation'] = [
		'position' => [
			'list' => (!empty($module -> data['page']) ? ($module -> data['page'] > 1 ? 'page/' . $module -> data['page'] . '/' : null) : null) . '#' . $module -> settings['hash'] . $content -> name,
			'all' => 'all/#' . $module -> settings['hash'] . $content -> name
		],
		'extreme' => [
			'first' => $module -> data['current'] > $skip ? $module -> data['list'][$skip] : null,
			'last' => $module -> data['current'] < $module -> data['count'] - 1 ? end($module -> data['list']) : null
		],
		'navigation' => [
			'previous' => $module -> data['current'] > $skip ? $module -> data['list'][$module -> data['current'] - 1] : null,
			'next' => $module -> data['current'] >= $skip && $module -> data['current'] < $module -> data['count'] - 1 ? $module -> data['list'][$module -> data['current'] + 1] : null
		],
		'pages' => null,
		'names' => null
	];
	
	$module -> data['navigation']['names'] = [
		'first' => $module -> data['navigation']['extreme']['first'],
		'last' => $module -> data['navigation']['extreme']['last'],
		'previous' => $module -> data['navigation']['navigation']['previous'],
		'next' => $module -> data['navigation']['navigation']['next'],
		'list' => $module -> data['navigation']['position']['list'],
		'all' => $module -> data['navigation']['position']['all']
	];
	
	unset($skip, $count);
	
} else {
	
	// если это 'list' или 'all', то просто читаем список материалов
	// и также подсчитываем и выясняем позицию текущего материала
	
	$module -> data['list'] = array_keys($content -> data);
	$module -> data['current'] = null;
	$module -> data['count'] = count($module -> data['list']);
	
	// и еще подсчитываем число страниц
	
	if ($module -> settings['display']['count'][$content -> type] > 0) {
		$module -> data['page'] = !empty($content -> page) ? $content -> page : 1;
		$module -> data['pages'] = ceil(($module -> data['count'] - $module -> settings['display']['skip'][$content -> type]) / $module -> settings['display']['count'][$content -> type]);
	}
	
	// кнопки навигации
	
	if ($module -> data['pages'] > 1) {
		
		$module -> data['navigation'] = [
			'position' => [
				'list' => $content -> type !== 'list' || !empty($module -> data['url']['filter']) ? '' : null,
				'all' => $content -> type !== 'all' ? 'all/' : null
			],
			'extreme' => [
				'first' => $module -> data['page'] - 1 > 0 ? '' : null,
				'last' => $module -> data['page'] < $module -> data['pages'] ? 'page/' . $module -> data['pages'] . '/' : null
			],
			'navigation' => [
				'previous' => $module -> data['page'] - 1 > 0 ? ($module -> data['page'] - 1 > 1 ? 'page/' . ($module -> data['page'] - 1) . '/' : '') : null,
				'next' => $module -> data['page'] < $module -> data['pages'] ? 'page/' . ($module -> data['page'] + 1 < $module -> data['pages'] ? $module -> data['page'] + 1 : $module -> data['pages']) . '/' : null
			],
			'pages' => [
				'1:' . ((int) $module -> data['page'] === 1 ? 'active' : null) => (int) $module -> data['page'] === 1 && empty($module -> settings['navigation']['active']) ? null : ''
			]
		];
		
		for ($i = 2, $n = $module -> data['pages']; $n > 0 && $i <= $n; $i++) {
			$module -> data['navigation']['pages'][$i . ':' . ((int) $module -> data['page'] === $i ? 'active' : null)] = (int) $module -> data['page'] === $i && empty($module -> settings['navigation']['active']) ? null : 'page/' . $i . '/';
		}
		unset($i, $n);
		
		if (
			!empty($module -> settings['navigation']['pages']) &&
			$module -> data['pages'] > (int) $module -> settings['navigation']['pages']
		) {
			
			$pages = (int) $module -> settings['navigation']['pages'];
			$min = $module -> data['page'] - floor($pages / 2) - 1;
			
			if ($min < 0) {
				$min = 0;
			} elseif ($min > $module -> data['pages'] - $pages) {
				$min = $module -> data['pages'] - $pages;
			}
			
			$module -> data['navigation']['pages'] = array_slice($module -> data['navigation']['pages'], $min, $pages, true);
			
			unset($pages, $min);
			
		}
		
	}
	
}

if (!empty($module -> data['navigation'])) {
	foreach ($module -> data['navigation'] as $key => &$item) {
		if (objectIs($item)) {
			foreach ($item as &$i) {
				if (!is_null($i) && $key !== 'names') {
					if ($key !== 'position') {
						$i = $module -> data['url']['filter'] . $i . $module -> data['url']['filters'];
					}
					$i = $module -> data['url']['base'] . $i;
				}
			}
		}
	}
	unset($i, $item, $key);
}

// обрабатываем лейблы

$labels = &$module -> settings['navigation']['labels'];

if (!empty($labels) && !is_array($labels)) {
	
	if ($content -> type === 'alone') {
		
		$data = $module -> data['navigation']['names'];
		$data = objectClear($data);
		$keys = array_keys($data);
		$t = $labels;
		
		$labels = null;
		
		foreach ($keys as $i) {
			$labels[$i] = $t === 'names' || strpos($t, 'content') !== false ? $data[$i] : lang('action:' . $i);
		}
		unset($i);
		
		//echo print_r($labels, 1) . '<br>';
		
		//if ($t === 'content') {
		if (strpos($t, 'content') !== false) {
			
			$t = dataParse($t);
			array_shift($t);
			
			$c = null;
			$c = new Content([
				objectToString($labels, ':'),
				$module -> data['content']['parent'],
				'all'
			]);
			
			$c -> settings();
			$c -> read();
			
			foreach ($labels as $k => &$i) {
				if ($k === 'list' || $k === 'all') {
					$i = lang('action:' . $k);
				} else {
					$i = $c -> data[$i];
					if (objectIs($t) && objectIs($i)) {
						foreach ($t as $it) {
							$i = $i[$it];
						}
						unset($it);
					}
				}
			}
			unset($i, $k, $c);
			
		}
		
		unset($labels, $keys, $data, $t);
		
	} elseif (!empty($module -> settings['navigation']['custom'])) {
		
		$labels = null;
		
		foreach ($module -> settings['navigation']['custom'] as $i) {
			$i = dataParse($i);
			if (!empty($i[1])) {
				$l = lang('action:' . $i[1]);
				$labels[$i[1]] = !empty($l) ? $l : $i[1];
				unset($l);
			}
		}
		unset($i);
		
	}
}

// функция вызова кнопок навигации

if (!function_exists('funcModuleContent_navigation')) {
	function funcModuleContent_navigation($name, $item, $nav = null, $tpl) {
		
		$class = $nav['classes'];
		$label = $nav['labels'];
		
		$print = null;
		
		$name = dataParse($name);
		if ($name[0] === 'pages') {
			$key = !empty($name[2]) ? $name[2] : null;
			$page = !empty($name[1]) ? $name[1] : null;
			$name = $name[0];
		} else {
			$key = $name[1];
			$page = $label[$key];
			$name = $name[0];
			$item = $item[$name][$key];
		}
		
		if (!empty($nav['noempty']) && empty($item)) {
			// сбрасываем пустые
			$tpl = null;
		} else {
			// читаем шаблон
			require $tpl;
		}
		
		if (!empty($tpl)) {
			$print = '<li class="' . $class['common'] . set($class[$name], ' ' . $class[$name]) . set($class[$key], ' ' . $class[$key]) . set(is_null($item), ' ' . $class['disable']) . '">' . $tpl . '</li>';
			
			/*
			$print .= '<li class="' . $class['common'] . set($class[$name], ' ' . $class[$name]) . set($class[$key], ' ' . $class[$key]) . set(is_null($item), ' ' . $class['disable']) . '">';
			$print .= '<a href="' . (is_null($item) ? 'javascript:void(0);' : $item) . '" class="' . $class['item'] . '">';
			$print .= '<span class="' . $class['wrapper'] . '">' . $page . '</span>';
			$print .= '</a></li>';
			*/
		}
		
		unset($page, $key, $name);
		
		return $print;
		
	}
}

//echo print_r($data['names'][$key], 1) . '<br>';
//echo print_r($content, 1) . '<br>';
//echo print_r($module -> data, 1) . '<br>';
//echo print_r($module -> settings['navigation']['labels'], 1) . '<br>';

?>