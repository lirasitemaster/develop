<?php defined('isENGINE') or die;

function funcLibraries_Version($path, $need = null) {
	
	/*
	*  Данная функция сканирует локальную папку и выбирает версию
	*/
	
	$version = null;
	$versions = localList($path, ['return' => 'folders']);
	
	if (objectIs($versions)) {
		rsort($versions, SORT_NATURAL);
		foreach ($versions as $i) {
			if (
				($need && strpos($i, $need) === 0) ||
				(!$need && preg_match('/^\d[\d\.]+?\\\/', $i))
			) {
				$version = substr($i, 0, -1);
				break;
			}
		}
		unset($i);
	}
	unset($versions);
	
	return $version;
	
}

function funcLibraries_System($name) {
	
	/*
	*  Данная функция копирует системную библиотеку
	*/
	
	if (!file_exists(PATH_LIBRARIES . 'system')) {
		mkdir(PATH_LIBRARIES . 'system');
	}
	
	//$from = PATH_CORE . 'install' . DS . 'libraries' . DS . $name;
	$from = PATH_CORE . 'install' . DS . 'libraries' . DS . $name . '.zip';
	$to = PATH_LIBRARIES . 'system' . DS . $name;
	
	if (file_exists($to)) {
		return true;
	} else {
		//return localCopy($from, $to, false);
		return localUnzip($from, $to, false);
	}
	
	//echo '[' . $name . ' -- ' . $from . ' --> ' . $to . ' -- ' . print_r($c) . ']<br>';
	
}

function funcLibraries_Delete($path) {
	
	/*
	*  Функция, удаляющая папку
	*/
	
	if (
		strrpos($path, PATH_LIBRARIES) !== 0 ||
		strpos($path, '..') !== false
	) {
		return false;
	}
	
	if (is_dir($path) === true) {
		$files = array_diff(scandir($path), array('.', '..'));
		foreach ($files as $file) {
			funcLibraries_Delete(realpath($path) . '/' . $file);
		}
		return rmdir($path);
	} elseif (is_file($path) === true) {
		return unlink($path);
	}
	
	return false;
	
}

?>