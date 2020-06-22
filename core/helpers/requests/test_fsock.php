<?php

// Устанавливаем соединение
$fp = fsockopen($site, 80, $errno, $errstr, 30);

if (!$fp) {
	// Проверяем успешность установки соединения
	echo "$errstr ($errno)<br />\n";
} else {
	
	// Формируем HTTP-заголовки для передачи его серверу
	//$header = "GET $page HTTP/1.$version\r\n";
	$header = 'GET ' . $page . ' HTTP/1.' . $version . "\r\n";
	foreach ($headers as $item) {
		$header .= $item . "\r\n";
	}
	$header .= "Connection: Close\r\n\r\n";
	
	// Отправляем HTTP-запрос серверу
	fwrite($fp, $header);
	
	// Получаем ответ
	while (!feof($fp)) {
		$line .= fgets($fp, 1024);
	}
	fclose($fp);
	
}

echo $line;

?>