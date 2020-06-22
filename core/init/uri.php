<?php defined('isCMS') or die;

global $uri;

$uri = (object) [
	'scheme' => 'http',
	'host' => $_SERVER['HTTP_HOST'],
	'path' => (object) [
		'base' => '/',
		'string' => $_SERVER['REQUEST_URI'],
		'array' => [],
		'file' => ''
	],
	'query' => (object) [
		'string' => (!empty($_SERVER['QUERY_STRING'])) ? '?' . $_SERVER['QUERY_STRING'] : '',
		'array' => $_GET,
		'path' => []
	],
	'www' => false,
	'url' => '',
	'previous' => null,
	'site' => null,
	'target' => '',
	'slash' => false,
	'refresh' => false
];

if (
	(!empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] === 'https') ||
	(!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS'] !== 'off'))
) {
	$uri -> scheme = 'https';
} elseif (
	!empty($_SERVER['REQUEST_SCHEME']) &&
	$_SERVER['REQUEST_SCHEME'] !== 'http' &&
	$_SERVER['REQUEST_SCHEME'] !== 'https'
) {
	error('400', false);
}

if (substr($uri -> host, 0, 4) === 'www.') {
	$uri -> www = true;
}

if (
	DEFAULT_SCHEME !== $uri -> scheme &&
	(DEFAULT_SCHEME === 'http' || DEFAULT_SCHEME === 'https')
) {
	$uri -> scheme = DEFAULT_SCHEME;
	$uri -> refresh = true;
}

if (
	DEFAULT_HOST === 'www' && !$uri -> www
) {
	$uri -> host = 'www.' . $uri -> host;
	$uri -> refresh = true;
} elseif (
	DEFAULT_HOST !== 'www' && $uri -> www
) {
	$uri -> host = substr($uri -> host, 4);
	$uri -> refresh = true;
}

if (strpos($uri -> path -> string, '//') !== false) {
	$uri -> path -> string = preg_replace('/\/{2,}/', '/', $uri -> path -> string);
	$uri -> refresh = true;
}

$uri -> target = strpos($uri -> path -> string, '?');
if ($uri -> target !== false) {
	$uri -> path -> string = substr($uri -> path -> string, 0, $uri -> target);
}

if (substr($uri -> path -> string, -1) === '/') {
	$uri -> slash = true;
}

$uri -> path -> array = preg_split('/\//', $uri -> path -> string, null, PREG_SPLIT_NO_EMPTY);

if (
	DEFAULT_LANG &&
	!empty($uri -> path -> array) &&
	DEFAULT_LANG === reset($uri -> path -> array)
) {
	array_shift($uri -> path -> array);
	$uri -> refresh = true;
}

// Проверяем на вызов ошибки
// Также оставляем в проверке старый вызов ошибки, но это только в случае пустого пути
// и только для того, чтобы сохранить перенаправление через настройки сервера apache или nginx

if (
	!empty($uri -> path -> array) &&
	reset($uri -> path -> array) === DEFAULT_ERRORS ||
	(empty($uri -> path -> array) || reset($uri -> path -> array) === 'index.php') &&
	!empty($uri -> query -> array) &&
	array_key_exists('error', $uri -> query -> array)
) {
	error(
		!empty($uri -> path -> array[1]) ? $uri -> path -> array[1] : '404',
		false,
		LOG_MODE === 'panic' ? 'redirecting to error from uri' : null
	);
}

$uri -> path -> file = array_pop($uri -> path -> array);

$uri -> path -> string = '';
if (!empty($uri -> path -> array)) {
	$uri -> path -> string .= implode('/', $uri -> path -> array) . '/';
	//if (preg_match('/\s|\%20|\./', $uri -> path -> string)) {
	if (preg_match('/\s/', $uri -> path -> string)) {
		error('404', false, 'error 404 from uri -- incorrect path');
	}
}

$uri -> target = strrpos($uri -> path -> file, '.');

if ($uri -> target !== false) {
	$uri -> target = substr($uri -> path -> file, $uri -> target + 1);
	
	if (
		$uri -> path -> file === 'index.php' &&
		!$uri -> slash
	) {
		$uri -> path -> file = '';
		$uri -> refresh = true;
	} elseif (
		$uri -> target === 'php' ||
		$uri -> target === 'ini' ||
		$uri -> slash
	) {
		error('404', false);
	} elseif (
		$uri -> target === 'htm' ||
		$uri -> target === 'html' ||
		$uri -> target === 'xml' ||
		$uri -> target === 'json' ||
		$uri -> target === 'txt' ||
		$uri -> target === 'script'
	) {
		if (
			empty($uri -> path -> array) &&
			//file_exists(PATH_CORE . 'generators' . DS . substr($uri -> path -> file, 0, -1 - strlen($uri -> target)) . '.php')
			file_exists(PATH_CORE . 'generators' . DS . $uri -> path -> file . '.php')
		) {
			//header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true, 200);
			//require_once PATH_CORE . 'generators' . DS . substr($uri -> path -> file, 0, -1 - strlen($uri -> target)) . '.php';
			require_once PATH_CORE . 'generators' . DS . $uri -> path -> file . '.php';
			exit;
		} else {
			if (empty($uri -> query -> string)) {
				$uri -> query -> string = '?';
			} else {
				$uri -> query -> string .= '&';
			}
			$uri -> path -> array = [DEFAULT_PROCESSOR, 'files', $uri -> target];
			$uri -> path -> file = substr($uri -> path -> file, 0, -1 - strlen($uri -> target));
			//$uri -> refresh = true;
		}
	} elseif (
		set($uri -> path -> file) &&
		!empty($uri -> target) &&
		!file_exists(PATH_SITE . str_replace(['/', '\\'], [DS, DS], $uri -> path -> string) . $uri -> path -> file)
	) {
		error('404', false, 'error 404 from uri -- file not exist');
	} else {
		$uri -> query -> string = '';
	}
	
	//$uri -> target = 'file';
	
} else {
	//$uri -> target = 'dir';
	if (set($uri -> path -> file)) {
		$uri -> path -> array[] = $uri -> path -> file;
		$uri -> path -> string .= $uri -> path -> file . '/';
		$uri -> path -> file = '';
	}
	if (!$uri -> slash) {
		$uri -> refresh = true;
	}
	
	// предыдущая страница через куки
	if (cookie('current-url', true) !== $uri -> path -> string) {
		cookie('previous-url', clear(cookie('current-url', true), 'simpleurl'));
	}
	if (
		!$uri -> refresh &&
		reset($uri -> path -> array) !== DEFAULT_ERRORS &&
		reset($uri -> path -> array) !== DEFAULT_PROCESSOR
	) {
		cookie('current-url', $uri -> path -> string);
	}
	$uri -> previous = cookie('previous-url', true);
	
}

$uri -> site = $uri -> scheme . '://' . $uri -> host . $uri -> path -> base;
$uri -> url = $uri -> site . $uri -> path -> string . $uri -> path -> file . $uri -> query -> string;
$uri -> path -> base = null;

if ($uri -> refresh) {
	if (DEFAULT_MODE === 'develop') { logging('system will be redirected from incorrect request'); }
	reload($uri -> url, 301);
	//header($_SERVER['SERVER_PROTOCOL'] . ' 301 Moved Permanently', true, 301);
	//header('Location: ' . $uri -> url);
	//exit;
} else {
	header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true, 200);
}

unset($uri -> slash, $uri -> refresh, $uri -> target);

/*
//echo '<pre>' . print_r($_SERVER['REQUEST_URI'], true) . '</pre>';
echo '<pre>' . print_r($_SERVER, true) . '</pre>';
echo '<pre>' . print_r($uri, true) . '</pre>';
echo file_get_contents(PATH_SITE . DS . 'tests' . DS . 'file.ini');
include PATH_SITE . 'tests' . DS . 'file.php';
exit;
*/

?>