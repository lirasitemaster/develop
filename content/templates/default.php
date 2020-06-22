<?php defined('isCMS') or die;

//print_r($template -> router);
//print_r($module -> settings -> filter);
//print_r($this -> data);

global $uri;
global $lang;

$path = (object) [
	'current' => PATH_ASSETS . 'content' . DS . 'templates' . DS . (!empty($module -> param) && $module -> param !== 'default' && file_exists(PATH_ASSETS . 'content' . DS . 'templates' . DS . $module -> param . DS) ? $module -> param : $module -> data['content']['parent']) . DS,
	'local' => $uri -> site . URL_LOCAL,
	'previous' => $uri -> site . $uri -> path -> base . $uri -> path -> string,
	'parent' => null,
	'item' => null
];

//$name = 'articles';

/*
*  СПРАВКА ПО ПЕРЕМЕННЫМ И ОБЪЕКТАМ ШАБЛОНА ПО-УМОЛЧАНИЮ ДЛЯ КОНТЕНТА
*  
*  $path -> 
*    current - папка шаблона текущего раздела, путь для php
*    local - урл локальной папки
*    previous - урл предыдущего уровня
*    item - урл текущего материала
*  
*  $item - массив текущего материала
*  $data - массив данных текущего материала
*  $parent - имя родителя материала (не раздела!)
*  $name - базовое имя для классов и идентификаторов шаблона
*  
*  имя раздела: $module -> data['content']['parent'] : objectGet('content', 'parent', null, $content)
*  тип шаблона: $module -> data['content']['type'] (alone/all/list) : objectGet('content', 'type', null, $content)
*  текущая страница: $module -> data['content']['page'] : objectGet('content', 'page')
*  фильтры: $content -> filter (массив) : objectGet('content', 'filter')
*  настройки контента: $content -> settings (массив) : objectGet('content', 'settings', null, $content)
*  
*  номер материала: $item['id']
*  название материала: $item['name']
*  
*/

//print_r($content -> settings);
//global $template;
//print_r($template);
//print_r($content);
//print_r($content -> type === 'all');
//print_r($uri);

// навигация

if (
	!empty($module -> settings['navigation']['enable']) &&
	set($module -> data['navigation']) &&
	!empty($module -> settings['navigation']['position']) &&
	$module -> settings['navigation']['position'] !== 'after'
) {
	if (file_exists($path -> current . 'navigation.php')) {
		require $path -> current . 'navigation.php';
	} else {
		require $module -> elements . 'navigation.php';
	}
}

if (
	thispage('is') !== $module -> data['content']['parent'] &&
	file_exists($path -> current . 'before.php')
) {
	require $path -> current . 'before.php';
}

// фильтры

if (!empty($module -> settings['filtration']['enable'])) {
	if (file_exists($path -> current . 'filter.php')) {
		require $path -> current . 'filter.php';
	} else {
		require $module -> elements . 'filter.php';
	}
}

echo '<' . $module -> settings['display']['elements']['common'] . ' class="' . 
	$module -> settings['display']['classes']['common'] . ' ' . 
	$module -> settings['display']['classes'][$module -> data['content']['type']] . 
	(!empty($module -> settings['display']['classes']['parent']) ? ' ' . (is_string($module -> settings['display']['classes']['parent']) ? $module -> settings['display']['classes']['parent'] : null) . $module -> data['content']['parent'] : null) . 
	(empty($content -> data) && !empty($module -> settings['display']['classes']['empty']) ? ' ' . $module -> settings['display']['classes']['empty'] : null) . 
'" id="' . $module -> settings['hash'] . (!empty($module -> settings['display']['classes']['parent']) ? $module -> data['content']['parent'] : null) . '">';

if (empty($content -> data)) {
	
	if (!empty($module -> settings['empty']['enable'])) {
		if (file_exists($path -> current . 'empty.php')) {
			require $path -> current . 'empty.php';
		} else {
			require $module -> elements . 'empty.php';
		}
	}
	
} else {
	
	if (!empty($module -> data['top'])) {
		if (file_exists($path -> current . 'top.php')) {
			require $path -> current . 'top.php';
		} else {
			require $module -> elements . 'top.php';
		}
	}
	
	if (!empty($module -> settings['wrapper']['enable'])) {
		
		echo '<' . $module -> settings['display']['elements']['wrapper'] . ' class="' . 
			$module -> settings['wrapper']['classes']['common'] . ' ' . 
			$module -> settings['wrapper']['classes'][$module -> data['content']['type']] . 
			(!empty($module -> settings['wrapper']['classes']['parent']) ? ' ' . (is_string($module -> settings['wrapper']['classes']['parent']) ? $module -> settings['wrapper']['classes']['parent'] : null) . $module -> data['content']['parent'] : null) . 
		'">';
		
	}
	
	if (!empty($module -> settings['media']['enable'])) {
		$media = [
			'list' => [],
			'content' => [],
			'captions' => [],
			'options' => $module -> settings['media']['options']
		];
	}
	
	foreach ($content -> data as $key => $item) {
		
		$data = $item['data'];
		
		// здесь НЕЛЬЗЯ использовать objectGet('content', '...') потому, что эта функция использует глобальный объект $content
		// а в ряде случаев, например, при запросе модуля контента из-под шаблона другого модуля контента, это не будет работать правильно
		// однако, в шаблоне слишком много вызовов этой функции, и потому мы решили задавать правильные данные при инициализации
		// все они хранятся в массиве $module -> data['content']
		
		$parent = objectIs($item['parent']) ? end($item['parent']) : $module -> data['content']['parent'];
		$rating = [
			'sets' => $content -> settings['rating'],
			'this' => $content -> ratings[$key]
		];
		
		$path -> item = $path -> previous . $item['name'] . '/';
		
		//require $module -> elements . 'defaults.php';
		
		if (!empty($module -> settings['media']['enable'])) {
			
			$name = $item['name'];
			
			if (
				file_exists($path -> current . 'media.php')
			) {
				require $path -> current . 'media.php';
			} else {
				require $module -> elements . 'media.php';
			}
			
			$media['content'][$name] = htmlentities($media['content'][$name]);
			
			if (objectIs($media['captions'][$name])) {
				foreach ($media['captions'][$name] as &$i) {
					$i = htmlentities($i);
				}
				unset($i);
			} else {
				$media['captions'][$name] = htmlentities($media['captions'][$name]);
			}
			
			unset($name);
			
		} else {
			
			echo '<' . $module -> settings['display']['elements']['item'] . ' class="' . $module -> settings['display']['classes']['item'] . '" id="' . $module -> settings['hash'] . str_replace(' ', '_', $item['name']) . '">';
			
			if (
				thispage('is') !== $module -> data['content']['parent'] &&
				file_exists($path -> current . 'inner.php')
			) {
				require $path -> current . 'inner.php';
			} elseif (
				$module -> data['content']['type'] === 'alone' &&
				file_exists($path -> current . 'alone.php')
			) {
				require $path -> current . 'alone.php';
			} elseif (
				$module -> data['content']['type'] === 'all' &&
				file_exists($path -> current . 'all.php')
			) {
				require $path -> current . 'all.php';
			} elseif (
				$module -> data['content']['type'] === 'list' &&
				file_exists($path -> current . 'list.php')
			) {
				require $path -> current . 'list.php';
			} elseif (
				file_exists($path -> current . 'default.php')
			) {
				require $path -> current . 'default.php';
			} else {
				
				require $module -> elements . 'empty.php';
				echo '</' . $module -> settings['display']['elements']['item'] . '>';
				break;
				
			}
			
			echo '</' . $module -> settings['display']['elements']['item'] . '>';
			
		}
		
	}
	
	unset($key, $item, $data);
	
	if (!empty($module -> settings['media']['enable'])) {
		
		$media['options']['folder'] = $module -> settings['media']['folder'];
		
		$media = json_encode(
			array_merge(
				['list' => $media['list']],
				['content' => $media['content']],
				['captions' => $media['captions']],
				$media['options']
			),
			JSON_UNESCAPED_UNICODE
		);
		
		//print_r($media);
		
		module([
			'media',
			$module -> settings['media']['param'],
			$module -> settings['media']['template'],
			$media
		]);
		unset($media);
	}
	
	if (!empty($module -> settings['wrapper']['enable'])) {
		echo '</' . $module -> settings['display']['elements']['wrapper'] . '>';
	}
	
}

echo '</' . $module -> settings['display']['elements']['common'] . '>';

// навигация

if (
	!empty($module -> settings['navigation']['enable']) &&
	set($module -> data['navigation']) &&
	!empty($module -> settings['navigation']['position']) &&
	$module -> settings['navigation']['position'] !== 'before'
) {
	if (file_exists($path -> current . 'navigation.php')) {
		require $path -> current . 'navigation.php';
	} else {
		require $module -> elements . 'navigation.php';
	}
}

if (
	thispage('is') !== $module -> data['content']['parent'] &&
	file_exists($path -> current . 'after.php')
) {
	require $path -> current . 'after.php';
}

?>