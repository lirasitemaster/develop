<?php defined('isENGINE') or die;

// задаем базовые данные
$module -> var['page'] = [];
$module -> var['temp'] = '';
$module -> var['agent'] = '';

// ОБОРАЧИВАЕМ ВЫДЕРГИВАНИЕ В ФУНКЦИЮ
require_once $module -> process . 'functions.php';

// получаем ресурс
if (!file_exists($module -> var['link'])) {
	
	if (!empty($module -> settings -> useragent)) {
		if ($module -> settings -> useragent === true) {
			$module -> var['agent'] = USER_AGENT;
		} else {
			$module -> var['agent'] = $module -> settings -> useragent;
		}
	}
	
	// вот сюда итерации типа
	if (!empty($module -> settings -> try)) {
		$m = $module -> settings -> try -> count;
		$p = $module -> settings -> try -> pause;
		$s = $module -> settings -> try -> size;
	} else {
		$m = 1;
		$p = 1;
		$s = 1;
	}
	
	$c = 0;
	while ($c < $m) {
		
		// начало исходного кода, без итераций
		if (!empty($module -> settings -> curl)) {
			$module -> var['temp'] = url_get_contents($module -> settings -> link, $module -> var['agent']);
		} else {
			$module -> var['temp'] = file_get_contents($module -> settings -> link);
		}
		// а это конец исходного кода, без итераций
		
		if (
			!empty($module -> var['temp']) &&
			strlen($module -> var['temp']) > $s
		) {
			break;
		} else {
			sleep($p);
			$c++;			
		}
		
	}
	// вот досюда
	//echo $c . '<br>' . strlen($module -> var['temp']);
	
	$module -> var['temp'] = preg_replace('/\r|\n|\r\n/', '', $module -> var['temp']);
	if (in_array('mbstring', get_loaded_extensions())) {
		$module -> var['temp'] = mb_convert_encoding($module -> var['temp'], 'UTF-8', mb_detect_encoding($module -> var['temp']));
	}
	
	file_put_contents($module -> var['link'], $module -> var['temp']);
	
}
$module -> data = file_get_contents($module -> var['link']);

// создаем массив целей из настроек
$module -> var['target'] = array_flip(array_keys((array)$module -> settings -> target));
foreach ($module -> var['target'] as $key => &$item) {
	$item = $module -> settings -> target -> $key;
	$item = str_replace(['[tag:open]', '[tag:close]'], ['<', '>'], $item);
	//$item = htmlentities($item);
}
unset($key, $item);

// формируем информацию со страницы
if (!empty($module -> settings -> page)) {
	$new = moduleExtractClear($module -> settings -> page, $module -> data);
	if (!empty($new)) {
		$module -> var['page'] = $new;
	}
	unset($new);
}

// предварительно чистим
$module -> data = preg_replace('/.*?<body.*?>/i', '', $module -> data);
$module -> data = preg_replace('/<\/body>.*/i', '', $module -> data);
$module -> data = preg_replace('/<script.*?<\/script>/i', '', $module -> data);
$module -> data = preg_replace('/<style.*?<\/style>/i', '', $module -> data);
$module -> data = preg_replace('/<\!--.*?-->/i', '', $module -> data);

// делаем выборку
$module -> data = preg_replace('/(' . $module -> var['target']['start'] . ')(.*)?' . $module -> var['target']['end'] . '/i', '$1$2', $module -> data);
$module -> data = preg_replace('/' . $module -> var['target']['start'] . '.*?(' . $module -> var['target']['itemstart'] . ')/i', '$1', $module -> data);

// выбираем элемент/элементы
preg_match_all('/' . $module -> var['target']['itemstart'] . '(.*?)' . $module -> var['target']['itemend'] . '/i', $module -> data, $module -> var['matches']);
$module -> var['matches'] = $module -> var['matches'][0];

// задаем массив данных
$module -> data = [
	'page' => $module -> var['page'],
	'items' => []
];

// выполняем сортировку
if (
	!empty($module -> settings -> order) &&
	$module -> settings -> order === 'desc'
) {
	$module -> var['matches'] = array_reverse($module -> var['matches']);
}

// выдираем элементы
foreach ($module -> var['matches'] as $key => $item) {
	//echo htmlentities($item) . '<br><hr><br>';
	$new = moduleExtractClear($module -> settings -> create, $item);
	if (!empty($new)) {
		$module -> data['items'][$key] = $new;
	}
	unset($new);
}
unset($key, $item);

file_put_contents($module -> var['file'], json_encode($module -> data, JSON_UNESCAPED_UNICODE));

?>