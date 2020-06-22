<?php defined('isENGINE') or die;

// Инициализация ядра, основа - задаем пространства имен, определяем функцию инициализации компонентов системы

// НУЖНО ПРОВЕРИТЬ, КАК ВСЕ ЭТО РАБОТАЕТ

// А ЕЩЕ ЗАДАТЬ СООТВЕТСТВУЮЩИЙ ФАЙЛ В БАЗЕ ДАННЫХ - ЧЕРНЫЙ СПИСОК ИЛИ БЕЛЫЙ СПИСОК IP
// И ЗАДАТЬ НАСТРОЙКИ БЕЗОПАСНОСТИ - НАПРИМЕР, БЛОКИРОВКА IP ПО ЧЕРНОМУ СПИСКУ ИЛИ ПРОПУСК IP ТОЛЬКО ПО БЕЛОМУ СПИСКУ

// Все компоненты в системе построены по принципу:
// файл - название компонента, который загружает все части по очереди или по определенным правилам, условиям
// папка - с названием компонента, содержит части компонента, которые можно загрузить принудительно

// задаем пространство имен

// загружаем базовые системные функции

require_once PATH_CORE . 'functions' . DS . 'system.php';

// установка проверочной куки и проверка работы кук

init('init', 'cookie');

// Проверяем систему

if (defined('DEFAULT_MODE') && DEFAULT_MODE === 'develop') {
	init('init', 'system');
}

// Проверяем источник запроса

if (SECURE_REQUEST) {
	init('init', 'request');
} else {
	define('isREQUEST', true);
	define('isORIGIN', true);
}

// инициализируем верификацию пользователя
// по ip через whitelist/blacklist/developlist
if (SECURE_BLOCKIP) {
	init('init', 'blockip');
}

// записываем в логи затраченную память сервера в начале инициализации системы
if (DEFAULT_MODE === 'develop' && SECURE_BLOCKIP === 'developlist') {
	logging('memory at system initialization is ' . round(memory_get_usage() / 1024, 2) . ' kb and in peak is ' . round(memory_get_peak_usage() / 1024, 2) . ' kb', 'memory 0 - at system initialization');
}

// инициализируем разбор путей
// там же проверяем на вызов ошибки

init('init', 'uri');

// установка пользователя с проверкой сессий и кук

if (DEFAULT_USERS) {
	// здесь задается глобальный объект $user
	init('users', 'first');
} else {
	//define('isALLOW', true);
	define('isALLOW', false);
}

// инициализируем функции по работе с данными
// первый большой прирост памяти на функции
init('functions', 'object');
init('functions', 'ini');
init('functions', 'data');

// инициализируем драйвера на чтение из базы данных
// снова прирост памяти на функции драйвера
init('drivers', 'first');

// инициализируем функции кастомного ядра
if (DEFAULT_CUSTOM) {
	init('custom', 'core' . DS . 'functions' . DS . 'init');
}

if (DEFAULT_USERS) {
	init('users', 'second');
}

// инициализируем разбор языков
// здесь задается глобальный объект $lang
// также uri очищается от языков и теперь его можно проверять как угодно
init('init', 'languages');

// инициализируем процессор
init('processor', 'first');

// инициализируем разбор структуры сайта,
// а также проверяем доступ пользователя к разделам из структуры сайта
init('init', 'structure');

// запускаем выполнение кода кастомного ядра
if (DEFAULT_CUSTOM) {
	init('custom', 'core' . DS . 'files' . DS . 'init');
}

// записываем в логи затраченную память сервера до инициализации шаблона
if (DEFAULT_MODE === 'develop' && SECURE_BLOCKIP === 'developlist') {
	logging('memory before load template is ' . round(memory_get_usage() / 1024, 2) . ' kb and in peak is ' . round(memory_get_peak_usage() / 1024, 2) . ' kb', 'memory 1 - before load template');
}

// инициализируем CSRF-токен
if (SECURE_CSRF) {
	csrf(true);
}

// инициализируем шаблонизатор
init('templates', 'first');

// инициализируем роутер
init('templates', 'router');

// запускаем шаблонизатор
init('templates', 'second');

// записываем в логи затраченную память сервера после инициализации шаблона
if (DEFAULT_MODE === 'develop' && SECURE_BLOCKIP === 'developlist') {
	logging('memory after load template is ' . round(memory_get_usage() / 1024, 2) . ' kb and in peak is ' . round(memory_get_peak_usage() / 1024, 2) . ' kb', 'memory 2 - after load template');
}

// показатели старой системы в этой точке: 7.0 x64: [1082816  911288] | 5.6: [598280 559440]
// показатели новой системы в этой точке:  7.0 x64: [1275664 1142432] | 5.6: [807704 752568]
// показатели новой системы в этой точке:  7.0 x64: [1307616 1176704] | 5.6: [807704 752568]
//echo memory_get_peak_usage() . ' ' . memory_get_usage() . '<br>';

/* === */

// это краткие тестилки для авторизации - можно будет удалить

//init('helpers', 'words' . DS . 'trenazher');
//init('helpers', 'print');

if (DEFAULT_USERS) {
	//init('helpers', 'sessionwrite');
	//init('helpers', 'form');
	//echo 'показываем пользователя<br>';
	//echo '<pre>' . print_r($user, true) . '</pre>';
}


/* === */

// завершаем работу системы

unset(
	$content,
	$dictionary,
	$lang,
	$libraries,
	$process,
	$seo,
	$structure,
	$template,
	$uri,
	$user,
	$userstable
);

if (LOG_MODE === 'panic') {
	logging('loading complete');
}

exit;

?>