<?php

$ch = curl_init();  
curl_setopt($ch, CURLOPT_URL, $url);
//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "HEAD");
if ($version) {
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
} else {
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
}
curl_setopt($ch, CURLOPT_HEADER, true);

//curl_setopt($ch, CURLOPT_HTTPGET, true);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// CURLOPT_CUSTOMREQUEST	Собственный метод запроса, используемый вместо "GET" или "HEAD" при выполнении HTTP-запроса. Это полезно при запросах "DELETE" или других, более редких HTTP-запросах. Корректными значениями будут такие как "GET", "POST", "CONNECT" и так далее; т.е. не вводите здесь всю строку с HTTP-запросом.
// CURLOPT_HTTPHEADER		Массив устанавливаемых HTTP-заголовков, в формате array('Content-type: text/plain', 'Content-length: 100')
// CURLOPT_HTTP_VERSION:
// CURL_HTTP_VERSION_1_0; // принудительное использование HTTP/1.0
// CURL_HTTP_VERSION_1_1; // принудительное использование HTTP/1.1
// CURLOPT_CONNECT_TO		Соединяться с указанный хостом по указанному порту, игнорируя URL. Принимает массив строк формата HOST:PORT:CONNECT-TO-HOST:CONNECT-TO-PORT.

$response = curl_exec($ch);

if ($output === FALSE) {
	echo "cURL Error: " . curl_error($ch);
} else {
	$info = curl_getinfo($ch);
}

curl_close($ch);

var_dump($response);
var_dump($info);

?>