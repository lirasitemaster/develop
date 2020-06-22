<?php defined('isCMS') or die;

// задаем базовые данные
$module -> var['path'] = $module -> path . DS . 'data' . DS . $module -> param;
$module -> var['file'] = $module -> var['path'] . '_json.ini';
$module -> var['link'] = $module -> var['path'] . '_test.html';

// проверяем время
if (!empty($module -> settings -> time)) {
	
	// вот отсюда vvv
	$time = datasplit($module -> settings -> time);
	foreach ($time as &$item) {
		if (is_numeric($item)) {
			$item = (int) $item;
		} elseif ($item === 'min') {
			$item = TIME_MINUTE;
		} elseif ($item === 'hour') {
			$item = TIME_HOUR;
		} elseif ($item === 'day') {
			$item = TIME_DAY;
		} elseif ($item === 'week') {
			$item = TIME_WEEK;
		} elseif ($item === 'month') {
			$item = TIME_MONTH;
		}
	}
	$time = $time[0] * $time[1];
	// вот досюда ^^^
	// все это обернуть в функцию data... например datatime
	
	$module -> var['timefile'] = $module -> var['path'] . '_time.ini';
	$module -> var['time'] = 0;
	
	if (file_exists($module -> var['timefile'])) {
		$module -> var['time'] = (int) file_get_contents($module -> var['timefile']) + $time;
	}
	
	if (
		$module -> var['time'] < time()
	) {
		if (file_exists($module -> var['file'])) {
			unlink($module -> var['file']);
		}
		if (file_exists($module -> var['link'])) {
			unlink($module -> var['link']);
		}
		file_put_contents($module -> var['timefile'], time());
	}
	
}

if (file_exists($module -> var['file'])) {
	$module -> data = dataloadjson($module -> var['file'], true);
} else {
	require_once $module -> process . 'file.php';
}

require_once $module -> process . 'actions.php';

/*

что еще нужно сделать:

+ отладить работу page

+ сделать возможность добавлять парсеные строки в другой файл (склеивать два json),
но нужно делать это аккуратно, только если парсинг прошел успешно -
например, проверять файлы (param_json_master, param_json_1, param_json_2, ...200), делать сортировку и объединять их в один

+ еще один вариант - сделать возможность добавлять парсеные файлы в папку материалов,
но здесь тоже нужно быть аккуратным, чтобы парсить во-первых только успешные данные,
а во-вторых, проверять, какие данные уже были, чтобы не повторяться (может быть по последнему файлу, хотя если файлы по именам, то хрен разберешь),
ну и в-третьих, чтобы был выбор названия файлов

- сохранять не только в json, но и в csv

- парсить несколько страниц, если они заданы в массиве

- парсить несколько страниц, задав линк с пагинацией и диапазон,
причем чтобы диапазон учитывал
начало: от 2 до 10,
разряды: 1 и 01,
страницу без пагинации: site.ru и site.ru?page=2,
а также шаг прироста, например от 5 до 20 c шагом 5: 5-10-15-20

*/

?>