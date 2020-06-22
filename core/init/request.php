<?php defined('isCMS') or die;

// здесь может возникнуть проблема, связанная с тем, что
// добропорядочные сайты отдают свой реферер, например google
// и соответственно, запрещать им доступ не имеет смысла
// возможно, стоит создать список разрешенных рефереров, как blockip
// но грузить и проверать его как опцию безопасности

// проверка должна быть такого вида
// в списке: 'google.com'
// проверяются: '//google.com' и '//www.google.com'
// проверяются по HTTP_REFERER, HTTP_ORIGIN и ORIGIN

if (
	!defined('USER_AGENT') || !constant('USER_AGENT') ||
	(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '//' . $_SERVER['SERVER_NAME']) === false) ||
	(isset($_SERVER['HTTP_ORIGIN']) && strpos($_SERVER['HTTP_ORIGIN'], '//' . $_SERVER['SERVER_NAME']) === false) ||
	(isset($_SERVER['ORIGIN']) && strpos($_SERVER['ORIGIN'], '//' . $_SERVER['SERVER_NAME']) === false)
) {
	define('isORIGIN', false);
} else {
	define('isORIGIN', true);
}

// Проверяем разрешения на запросы с других сайтов

if (
	// разрешено все
	SECURE_REQUEST === true ||
	// разрешено то, что указано
	stripos(SECURE_REQUEST, $_SERVER['REQUEST_METHOD']) !== false
) {
	define('isREQUEST', true);
} else {
	// не разрешено
	define('isREQUEST', false);
}

// Определяем плохие запросы - запрещенные и из сторонних источников

if (!isREQUEST && !isORIGIN) {
	error('403', false, 'not isREQUEST and not isORIGIN, it was a forbidden request');
}

?>