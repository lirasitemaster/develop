<?php defined('isCMS') or die;

global $lang;
global $template;
global $dictionary;

$dictionary = [];

// загрузка языковых файлов
// здесь язык - предок файла, назначение - имя файла, а название шаблона - тип файла

// сюда еще нужно сделать проверку существования записей/разделов common dictionary morph
// во всех присутствующих в системе языковых пакетах
// и если таких записей нет, установить их из install

// загружаем стандартный языковой пакет

$db = dbUse(
	'languages:' . $lang -> lang . ':common:menu:custom:filter' . (in('options', 'dictionary') ? ':morph:dictionary' : null),
	'select',
	['allow' => 'parent:' . $lang -> lang, 'deny' => 'type']
);

foreach ($db as $item) {
	
	if (empty($item['data'])) {
		continue;
	} elseif ($item['name'] === 'dictionary') {
		$dictionary = $item['data'];
	} elseif (
		$item['name'] === 'filter' ||
		$item['name'] === 'custom' ||
		$item['name'] === 'menu' ||
		$item['name'] === 'morph'
	) {
		$lang -> data[$item['name']] = $item['data'];
	} else {
		$lang -> data = array_merge(
			$lang -> data,
			$item['data']
		);
	}
	
}

unset($db);

// загружаем языковой пакет для текущего шаблона

$db = dbUse(
	'languages:' . $lang -> lang . ':common:menu:custom:filter' . (in('options', 'dictionary') ? ':morph:dictionary' : null) . (objectGet('template', 'section') ? ':section' : null),
	'select',
	['allow' => 'parent:' . $lang -> lang . ' type:' . $template -> name]
);

if (!empty($db)) {
	
	foreach ($db as $item) {
		
		if (empty($item['data'])) {
			continue;
		} elseif ($item['name'] === 'dictionary') {
			$dictionary = array_merge(
				$dictionary,
				$item['data']
			);
		} elseif (
			$item['name'] === 'filter' ||
			$item['name'] === 'custom' ||
			$item['name'] === 'menu' ||
			$item['name'] === 'section' ||
			$item['name'] === 'morph'
		) {
			if (objectIs($lang -> data[$item['name']])) {
				$lang -> data[$item['name']] = array_merge(
					$lang -> data[$item['name']],
					$item['data']
				);
			} else {
				$lang -> data[$item['name']] = $item['data'];
			}
		} else {
			$lang -> data = array_merge(
				$lang -> data,
				$item['data']
			);
		}
		
	}
	
}

unset($db);

//echo '<pre style="font-size: 10px; line-height: 6px;">';
//echo $template -> name . '<br>';
//echo count($db) . '<br>';
//print_r($db);
//print_r($lang);
//echo '</pre>';
//exit;


/*
// загрузка языковых файлов
// здесь язык - тип файла, остальное - имя файла

$db = dbUse(
	'languages:' . $lang -> lang . ':common:custom:menu' . (in('options', 'dictionary') ? ':morph:dictionary' : ''),
	'select',
	['allow' => 'type:' . $lang -> lang]
);

foreach ($db as $item) {
	
	if (empty($item['data'])) {
		continue;
	} elseif ($item['type'] === 'dictionary') {
		$lang -> dictionary = $item['data'];
	} elseif (
		$item['type'] === 'custom' ||
		$item['type'] === 'menu'
	) {
		$lang -> data[$item['type']] = $item['data'];
	} else {
		$lang -> data = array_merge(
			$lang -> data,
			$item['data']
		);
	}
	
}

echo '<pre style="font-size: 10px; line-height: 6px;">';
//print_r($db);
print_r($lang);
echo '</pre>';
exit;

// загрузка языковых файлов
// здесь язык - имя файла, остальное - тип файла

$db = dbUse('languages:' . $lang -> lang, 'select');

foreach ($db as $item) {
	
	if (empty($item['data'])) {
		continue;
	} elseif (
		$item['type'] === 'dictionary' && in('options', 'dictionary')
	) {
		$lang -> dictionary = $item['data'];
	} elseif (
		empty($item['type']) ||
		$item['type'] === 'common' ||
		$item['type'] === 'morph' && in('options', 'dictionary')
	) {
		$lang -> data = array_merge(
			$lang -> data,
			$item['data']
		);
	} else {
		$lang -> data[$item['type']] = $item['data'];
	}
	
}

echo '<pre style="font-size: 10px; line-height: 6px;">';
//print_r($db);
print_r($lang);
echo '</pre>';
exit;
*/

?>