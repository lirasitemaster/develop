<?php defined('isENGINE') or die;

// Проверяем версию php

if (version_compare(PHP_VERSION, CMS_MINIMUM_PHP, '<')) {
	error('php', false, true);
}

// Проверяем существование модулей php

$phpmods = [
	'mbstring'
];

foreach ($phpmods as $item) {
	if (!extension_loaded($item)) {
		error('system', false, '[not php module : ' . $item . ']');
	}
}

// Проверяем существование важных системных констант

$constants = [
	'set' => [
		'DEFAULT_SCHEME', 'DEFAULT_HOST', 'DEFAULT_PROCESSOR', 'DEFAULT_ERRORS', 'USERS_EMAIL', 'DEFAULT_LANG', 'DEFAULT_USERS', 'DEFAULT_CUSTOM', 'DEFAULT_MODE',
		'SECURE_REQUEST', 'SECURE_BLOCKIP', 'SECURE_USERS', 'SECURE_RIGHTS', 'SECURE_SESSIONTIME', 'SECURE_PROCESSTIME', 'SECURE_CSRF', 'SECURE_WRITING',
		'LOG_MODE', 'LOG_SORT', 'USERS_RIGHTS'
	],
	'noempty' => [
		'DEFAULT_PROCESSOR', 'DEFAULT_ERRORS', 'DEFAULT_LANG'
	]
];

foreach ($constants['set'] as $item) {
	if (!defined($item)) {
		error('system', false, '[not system constant ' . $item . ']');
	}
}
foreach ($constants['noempty'] as $item) {
	if (!constant($item)) {
		error('system', false, '[empty system constant ' . $item . ']');
	}
}

// Проверяем взаимодействие констант

if (
	!DEFAULT_USERS && (SECURE_RIGHTS || SECURE_CSRF || USERS_RIGHTS || SECURE_USERS) ||
	SECURE_WRITING && (!defined(DB_WRITINGUSER) || !defined(DB_WRITINGPASS) || !DB_WRITINGUSER || !DB_WRITINGPASS)
) {
	error('system', false, '[system constants is set wrong : ' . 
		'SECURE_RIGHTS or SECURE_CSRF or USERS_RIGHTS or SECURE_USERS without DEFAULT_USERS // ' .
		'SECURE_RIGHTS without USERS_RIGHTS // ' .
		'SECURE_WRITING without DB_WRITINGUSER or DB_WRITINGPASS' .
	']');
}

// Проверяем существование системных папок

$folders = [
	'assets', 'database', 'cache', 'core', 'libraries', 'local', 'log', 'modules', 'templates'
];

foreach ($folders as $item) {
	$item = strtoupper($item);
	if (
		!defined('NAME_' . $item) ||
		!defined('URL_' . $item) ||
		!defined('PATH_' . $item) ||
		!file_exists(constant('PATH_' . $item)) ||
		!is_dir(constant('PATH_' . $item))
	) {
		if (
			!file_exists(constant('PATH_' . $item)) &&
			SECURE_BLOCKIP === 'developlist'
		) {
			mkdir(constant('PATH_' . $item));
		} else {
			error('system', false, '[not system folder from path constant : ' . $item . ']');
		}
	}
}

// Проверяем существование системных функций

$functions = [
	'init', 'logging', 'error', 'cookie', 'clear', 'crypting', 'set'
];

foreach ($functions as $item) {
	if (!function_exists($item)) {
		error('system', false, '[not system function : ' . $item . ']');
	}
}

unset($item, $phpmods, $constants, $folders, $functions);

?>