<?php defined('isENGINE') or die;

// vendor теперь ОБЯЗАТЕЛЕН, иначе библиотека считается СИСТЕМНОЙ (т.е. вендор будет в папке system)
// это касается в первую очередь библиотек, которые НЕ размещаются на github

// если у вас не получилось загрузить библиотеку, попробуйте поискать ее на сайте https://packagist.org/
// и в разделе "Details" найдите ссылку на гитхаб - она будет подписана как "Canonical Repository URL"

// также вы можете использовать версии библиотек в качестве вариантов
// например, вы можете внести в базу данных вариант версии библиотеки, например, 1.0-dev
// и загружать в этом варианте несколько доп.стилей, или, наоборот, исключить часть из них

// также можно добавлять какие-либо исключения при загрузке библиотеки по типам: указывая типы через точку:
// "тип" для включения только его и/или "!тип" для его

// также вы можете создавать шаблоны загрузки, которые позволят всегда загружать ту версию библиотеки, которая вам нужна
// при этом для обновления вам нужно будет просто положить файлы новых версий в папку библиотеки

// если в вашем проекте используются библиотеки разных версий, рекомендуем включить версионность в настройках системного компоузера
// для этого добавьте в конфиге в default -> composer после первого значения ("install" или "download") - ":version"
// иначе вам грозит ошибка в продакшене или постоянное обновление в режиме разработки

// если вас не устраивает системный компоузер, вы также можете установить привычный вам и обновлять библиотеки через него!
// для этого, возможно, понадобится либо изменить папку назначения в вашем компоузере, либо переименовать папку библиотек в системе

global $template;
global $libraries;

$libraries = (object) [
	'db' => dbUse('libraries', 'select'),
	'empty' => [],
	'list' => [],
	'preload' => [],
	'process' => [],
	'update' => []
];

if (objectIs($libraries -> db)) {
	
	foreach ($libraries -> db as $item) {
		if (empty($item['type'])) {
			$item['type'] = 'system';
		}
		$libraries -> process[$item['name'] . ':' . $item['type']] = $item['data'];
	}
	
	//unset($libraries -> db, $item);
	unset($item);
	
}

if (
	objectIs($template -> settings -> libraries)
) {
	
	// выполняем обработку
	init('libraries', 'process');
	
	// выполняем проверку
	init('libraries', 'verify');
	
}

?>