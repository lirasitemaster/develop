<?php defined('isENGINE') or die;

if (!(DEFAULT_MODE === 'develop' && SECURE_BLOCKIP === 'developlist')) {
	return false;
}

$nolocal = 0;

// создаем список папок для копирования

$folders = [
	PATH_ASSETS,
	PATH_DATABASE
];

if (empty($nolocal)) {
	$folders[] = PATH_LOCAL;
}

$list = localList(PATH_TEMPLATES, ['return' => 'folders', 'skip' => 'administrator:base:error:restore']);
foreach ($list as $item) {
	$folders[] = PATH_TEMPLATES . $item;
}
unset($item, $list);

//print_r($folders);
//exit;

// создаем архив

$zip = new ZipArchive();
$filename = PATH_SITE . $_SERVER['HTTP_HOST'] . date('.Y-m-d') . (!empty($nolocal) ? '.nolocal' : null) . '.backup.zip';

if (file_exists($filename)) {
	unlink($filename);
}

if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
	exit("Невозможно открыть <$filename>\n");
}

// добавляем в архив список файлов из папок

foreach ($folders as $item) {
	
	$i = localList($item, ['return' => 'files', 'subfolders' => true]);
	
	foreach ($i as $file) {
		$zip->addFile($item . $file, str_replace(PATH_SITE, '', $item . $file));
		//echo str_replace(PATH_SITE, '', $item . $file) . "\n";
		//echo $file . "\n";
	}
	
}

unset($item, $i, $file);

// добавляем в архив список файлов из корневой папки

foreach (['configuration', 'developlist', 'blacklist', 'whitelist'] as $item) {
	$zip->addFile(PATH_SITE . $item . '.ini', $item . '.ini');
}

unset($item);

// закрываем архив

echo "numfiles: " . $zip->numFiles . "\n";
echo "status:" . $zip->status . "\n";
echo "local:" . (empty($nolocal) ? "include" : "not include") . "\n";
$zip->close();

exit;

?>