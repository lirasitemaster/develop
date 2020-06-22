<?php

// переинициализируем драйвера обратно только на чтение из базы данных

if (defined('isREAD')) {
	error('403', false, 'is hack attempt to change system constant isREAD');
}

if (defined('isWRITE') && isWRITE) {
	define('isREAD', true);
} else {
	define('isREAD', false);
}

if (isREAD && SECURE_WRITING) {
	
	if (DB_TYPE === 'local') {
		
		// переинициализация не требуется
		
	} elseif (DB_TYPE === 'csv') {
		
	} elseif (DB_TYPE === 'mysqli' || DB_TYPE === 'mysqli-test') {
		
		// закрываем старое соединение
		
		// и открываем новое
		
		$dbset = [
			'host' => defined('DB_HOST') && DB_HOST ? DB_HOST : 'localhost',
			'user' => DB_USER,
			'pass' => DB_PASS,
			'db' => DB_NAME,
			'port' => defined('DB_PORT') && DB_PORT ? DB_PORT : null,
			'charset' => 'utf8'
		];
		
		$db = new SafeMysql($dbset);
		
	} elseif (DB_TYPE === 'pdo') {
		
		// закрываем старое соединение
		
		// и открываем новое
		
	}
	
}

?>