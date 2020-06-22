<?php defined('isCMS') or die;

/*

Данный модуль заменяет $content -> display();

Раз мы сделали подготовку контента частью ядра, мы можем им оперировать как угодно.
Однако делать вывод контента частью ядра неправильно, т.к. это идет вразрез с принципами разделения данных.

Т.е. вывод контента мы осуществляем через модуль.
Ну и значит переносим сюда всю логику.

Здесь очень важно не путать шаблоны контента и шаблоны модуля:

	шаблоны контента будут грузиться по-умолчанию при условии загрузки дефолтного шаблона контента,
	этого, к слову, вполне достаточно для большинства ситуаций;
	шаблоны контента грузятся из папки 'assets/content/templates' из подпапки с названием родителя;
	на одном уровне с папкой шаблонов расположены папки таблиц и локальных данных
	
	шаблоны модуля будут грузиться как обычные шаблоны любого другого модуля,
	они могут понадобится вам при выводе модуля с различными параметрами и если вас не устраивает дефолтный шаблон
	эти шаблоны грузятся из папки 'assets/modules/content/templates';

*/

//print_r($module);
//print_r($template);

$module -> this = dataParse($module -> this);

if (
	$module -> param === 'default' &&
	empty($module -> this[0])
) {
	
	// подключаем текущие данные контента
	global $content;
	
	// меняем тип вывода контента
	if (
		!empty($module -> this[1]) &&
		($module -> this[1] === 'all' || $module -> this[1] === 'list')
	) {
		$content -> type = $module -> this;
	}
	
	// меняем параметры фильтрации
	if (!empty($module -> this[2])) {
		$content -> filtration = null;
	}
	
} else {
	
	// создаем новую переменную контента
	
	$content = new Content([
		$module -> settings['names'],
		!empty($module -> this[0]) ? $module -> this[0] : $module -> settings['parent'],
		!empty($module -> this[1]) ? $module -> this[1] : $module -> settings['type'],
		!empty($module -> this[2]) ? $module -> this[2] : null,
	]);
	
	// здесь мы можем задавать параметры для чтения контента
	// новым, последним параметром, можно устанавливать отключение фильтрации
	// это очень может пригодиться для вызова модуля внутри другого модуля
	
	$content -> settings();
	if (objectIs($module -> settings['content'])) {
		$content -> settings($module -> settings['content']);
	}
	if (!empty($module -> settings['exclude'])) {
		$content -> settings('{"exclude" : "' . $module -> settings['exclude'] . '"}');
	}
	$content -> read();
	
}

// задаем общие данные

$module -> data = [
	'list' => [], // список названий материалов
	'top' => [], // список материалов в топе
	'extended' => [], // список материалов, кроме названий должен содержать данные для вывода, которые могут браться из настроек
	'current' => null, // позиция текущего материала в списке
	'count' => null, // всего материалов
	'page' => null, // текущая страница
	'pages' => null, // всего страниц
	'navigation' => null, // кнопки навигации
	'content' => [
		// параметры контента
		'parent' => $content -> parent,
		'type' => $content -> type,
		'page' => $content -> page
	]
];

// обрабатываем настройки элементов

if (
	objectIs($module -> settings['display']['elements']['common']) &&
	!empty($module -> settings['display']['elements']['common'][$module -> data['content']['type']])
) {
	$module -> settings['display']['elements']['common'] = $module -> settings['display']['elements']['common'][$module -> data['content']['type']];
} elseif (
	empty($module -> settings['display']['elements']['common']) ||
	!is_string($module -> settings['display']['elements']['common'])
) {
	$module -> settings['display']['elements']['common'] = 'div';
}

if (
	objectIs($module -> settings['display']['elements']['item']) &&
	!empty($module -> settings['display']['elements']['item'][$module -> data['content']['type']])
) {
	$module -> settings['display']['elements']['item'] = $module -> settings['display']['elements']['item'][$module -> data['content']['type']];
} elseif (
	empty($module -> settings['display']['elements']['item']) ||
	!is_string($module -> settings['display']['elements']['item'])
) {
	$module -> settings['display']['elements']['item'] = 'div';
}

if (
	objectIs($module -> settings['display']['elements']['wrapper']) &&
	!empty($module -> settings['display']['elements']['wrapper'][$module -> data['content']['type']])
) {
	$module -> settings['display']['elements']['wrapper'] = $module -> settings['display']['elements']['wrapper'][$module -> data['content']['type']];
} elseif (
	empty($module -> settings['display']['elements']['wrapper']) ||
	!is_string($module -> settings['display']['elements']['wrapper'])
) {
	$module -> settings['display']['elements']['wrapper'] = 'div';
}

// преобразование родителя

if (strpos($module -> data['content']['parent'], '.') !== false) {
	$module -> data['content']['parent'] = datasplit($module -> data['content']['parent'], '.');
} elseif (strpos($module -> data['content']['parent'], ':') !== false) {
	$module -> data['content']['parent'] = datasplit($module -> data['content']['parent'], ':');
}

if (is_array($module -> data['content']['parent'])) {
	$module -> data['content']['parent'] = reset($module -> data['content']['parent']);
}

// сортируем данные

if (!empty($module -> settings['display']['sort'][$content -> type])) {
	$content -> data = dbUse($content -> data, 'filter', ['sort' => $module -> settings['display']['sort'][$content -> type]]);
}

// инициализация топа

if (
	$content -> type === 'list' &&
	!empty($module -> settings['top']['enable'])
) {
	require $module -> path . 'process' . DS . 'top.php';
}

// инициализация фильтрации

if (!empty($module -> settings['filtration']['enable'])) {
	
	// обрабатываем настройки
	if (objectIs($module -> settings['filtration'][$content -> type])) {
		foreach ($module -> settings['filtration'][$content -> type] as $k => $i) {
			$module -> settings['filtration'][$k] = $i;
		}
		unset($k, $i, $module -> settings['filtration'][$content -> type]);
	}
	
	require $module -> path . 'process' . DS . 'filtration.php';
}

// инициализация навигации

if (!empty($module -> settings['navigation']['enable']) && !empty($content -> data)) {
	
	// обрабатываем настройки
	if (objectIs($module -> settings['navigation'][$content -> type])) {
		foreach ($module -> settings['navigation'][$content -> type] as $k => $i) {
			$module -> settings['navigation'][$k] = $i;
		}
		unset($k, $i, $module -> settings['navigation'][$content -> type]);
	}
	
	require $module -> path . 'process' . DS . 'navigation.php';
}

// инициализация отображения

if ($content -> type !== 'alone' && !empty($content -> data)) {
	
	$skip = empty($content -> filter) ? (int) $module -> settings['display']['skip'][$content -> type] : null;
	$count = (int) $module -> settings['display']['count'][$content -> type];
	
	// пропускаем заданное число материалов
	
	if ($content -> page - 1 > 0) {
		$skip = $skip + ($content -> page - 1) * $count;
	}
	
	if ($skip && $skip > 0) {
		$content -> data = array_slice($content -> data, $skip);
	}
	
	//unset($skip);
	
	// ограничиваем материалы заданным числом
	
	if ($count && $count > 0) {
		$content -> data = array_slice($content -> data, 0, $count);
	}
	
	//unset($count);
	
}

// инициализация рейтингов

if (!empty($content -> data) && !empty($content -> ratings)) {
	require $module -> path . 'process' . DS . 'rating.php';
}

// инициализация магазина

if (
	!empty($content -> settings['shop']) && defined('CORE_SHOP') && CORE_SHOP
) {
	
	$shop = new Shop( $content -> settings['shop'] === true ? $content -> parent . ':default' : $content -> settings['shop'] );
	$shop -> refresh();
	//$content -> settings['shop'] === true ? $content -> parent : $content -> settings['shop'];
	//echo '[' . print_r($shop, 1) . ']';
	
}

//echo '<pre>' . print_r($content -> ratings, true) . '</pre><hr>';

//print_r($module -> data);
//print_r($content);

?>