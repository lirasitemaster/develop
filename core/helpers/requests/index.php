<?php

//http://0iscms.ru/com/
//[REDIRECT_URL] => /com/index.php = REQUEST_URI without file
//[PHP_SELF] => /index.php = [SCRIPT_NAME]
//http://0hi.ru/rooms/apartments/
//[REDIRECT_URL] => /rooms/apartments/ = REQUEST_URI
//http://0hi.ru/rooms/apartments/index.php
//[REDIRECT_URL] => /rooms/apartments/index.php = REQUEST_URI
//
//REDIRECT_URL применяется только
//в c:\OSPanel\domains\0iscms.ru\www\modules\form\query.php
//и c:\OSPanel\domains\0iscms.ru\www\modules\order\query.php
//для определения пути к модулю - лучше придумать что-нибудь другое, например брать путь из параметров uri
//
//PHP_SELF применяется только
//в c:\OSPanel\domains\0hp.ru\includes\administrator.php
//для хэша, но PHP_SELF всегда будет равен SCRIPT_NAME
//
//PATH_INFO используется преимущественно в конструкциях вида
//$_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['PATH_INFO'];
//и всегда равно REDIRECT_URL или REQUEST_URI без параметров и адреса страницы,
//так что лучше использовать вместо них путь из параметров uri
//
//HTTP_REFERER - работает и в apache, и в nginx
//Адрес страницы (если есть), с которой браузер пользователя перешёл на эту страницу.
//Этот заголовок устанавливается браузером пользователя.
//Не все браузеры устанавливают его, а некоторые в качестве дополнительной возможности
//позволяют изменять содержимое заголовка HTTP_REFERER.
//Одним словом, нельзя доверять этому заголовку.
//Используется только в verification в проверке источника запроса
//Необязателен, является лишь одним из определителей запроса, притом не самым главным

// Еще нужно разобрать query на составляющие и записать в массив
// Но, возможно, это стоит сделать позже
// Да и многие преобразования со строками тоже, может быть, лучше сделать позже
// например, после предварительных проверок

// проверка на открытие файлов и преобразования url
// http://0iscms.ru/tests/index.php       должно стать 0iscms.ru/tests/
// http://0iscms.ru/tests/index.php/      должен дать ошибку 404
// http://0iscms.ru/tests                 должно стать 0iscms.ru/tests/
// http://0iscms.ru/tests/file.ini        должен перенаправить на ошибку 404
// http://0iscms.ru/tests/file.php        должен перенаправить на ошибку 404
// http://0iscms.ru/tests/file.set        должен открыться
// http://0iscms.ru/tests/script.js       должен открыться
// http://0iscms.ru/tests/style.css       должен открыться
// http://0iscms.ru/tests/file.ini/       должен перенаправить на ошибку 404
// http://0iscms.ru/tests/file.php/       должен перенаправить на ошибку 404
// http://0iscms.ru/tests/file.set/       должен вызвать ошибку 404
// http://0iscms.ru/tests/file/set/       должен открыть index.php
// http://0iscms.ru/core/                 должен открыть index.php
// http://0iscms.ru/core/verification.php должен перенаправить на ошибку 404
// http://0iscms.ru/tests/script.js/      должен вызвать ошибку 404
// http://0iscms.ru/tests/style.css/      должен вызвать ошибку 404
// http://0iscms.ru/configuration.php     должен перенаправить на ошибку 404
// http://0iscms.ru/configuration.ini     должен перенаправить на ошибку 404
// http://0iscms.ru/index.php/            должен дать ошибку 404
// http://0iscms.ru/index.php             должен открыть index.php
// http://0iscms.ru/                      должен открыть index.php
// http://0iscms.ru                       должен открыть index.php
// http://0iscms.ru/robots.txt            должен открыть index.php с xml
// http://0iscms.ru/sitemap.xml           должен открыть index.php с xml
// http://0iscms.ru/books/open.xml?p=10   должен открыть index.php с xml

// НАСТРОЙКИ

$protocol = 'http';
//$protocol = 'habr';

$site = '0iscms.ru';
//$site = 'film.fwmakc.ru';
//$site = 'ajlkfjweoifjweofweofi.ru';

$host = $site;
//$host = '';
//$host = '0iscms.ru';

//$page = '/';
//$page = '/configuration.php';
$page = '/robots.txt';
//$page = '/coin/rosdi/?v=1&n=12';
//$page = '//coin///rosdi/index.php?v=1&n=12';
//$page = '//coin///roi/ind.php?v=1&n=12';
//$page = '/tests/';
//$page = '/tests/style.css/';
//$page = '/php-curl-get.html';

$version = 1;

$url = $protocol . '://' . $site . $page;

$headers = [
	'Host: ' . $host,
	//'Referer: ' . $_SERVER['HTTP_HOST'],
	//'User-Agent: blalbalblablblalbal',
	//'User-Agent: ',
	'User-Agent: ' . USER_AGENT,
	//'Cookie: UID=tkfilj72b1jicottusduc94e16c16d321ef5549bbf3b1feda5d3; SID=jicottusdun1kor6tkfilj72b1;'
];

// ТЕСТЫ ЗАПРОСОВ

echo '<br><hr>' . $_SERVER['SERVER_PROTOCOL'] . '<hr>';

//require_once 'test_curl.php';
//require_once 'test_get_contents.php';
require_once 'test_fsock.php';
require_once 'test_form.php'; // отправка этой формы дает HTTP_REFERER

?>