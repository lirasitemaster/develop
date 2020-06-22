<?php

define('DS', DIRECTORY_SEPARATOR);
define('PATH_INSTALL', realpath(__DIR__) . DS);
define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DS);
define('URL_SITE', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/');
define('URL_INSTALL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);

//echo PATH_INSTALL . '<br>';
//echo PATH_ROOT . '<br>';
//$paths = json_decode(file_get_contents(PATH_INSTALL . 'paths.ini'));
//print_r($paths);
//echo '<pre>' . print_r($_SERVER, true) . '</pre>';
//exit;

$install = PATH_INSTALL . 'install.zip';
$status = [];

if (file_exists(PATH_ROOT . 'index.php')) {
	$status[] = 'INDEX file exists on ROOT site folder';
}

if (!extension_loaded('zip')) {
	$status[] = 'Missing php extension \'ZIP\'';
}

if (!file_exists($install)) {
	$status[] = 'Missing \'INSTALL.ZIP\' file on \'INSTALL\' folder';
}

if (empty($status)) {
	
	$zip = new ZipArchive;
	$res = $zip -> open($install);
	
	if ($res === true) {
		
		$zip -> extractTo(PATH_ROOT);
		$zip -> close();
		
		$status[] = 'Unpack COMPLETE!';
		$status[] = 'Wait for Installing all needed libraries!';
		$status[] = '<hr>You can connect to site and change settings.<br>Also you can <a href="' . URL_SITE . '">open site</a> now!';
		
	} else {
		$status[] = 'Unzip ERROR!';
	}

}

echo '<html><head></head><body>';

foreach ($status as $item) {
	echo $item . '<br>';
}

echo '</body></html>';

exit;

?>