<?php defined('isCMS') or die;

/* ФУНКЦИИ ДЛЯ РАБОТЫ С ФАЙЛОВОЙ СИСТЕМОЙ */

function localList($path, $parameters = [], $recurse = false, $basepath = false) {
	
	// now dirconnect and fileconnect is localList
	
	//fileconnect($dir, $ext = false)
	//localList($path, ['return' => 'files'/*, 'type' => $ext*/]) // $ext - либо массив значений, либо строка '_:_:_'
	//dirconnect($dir, $ext = false)
	//localList($path, ['return' => 'folders'/*, 'skip' => $ext*/]) // $ext - либо массив значений, либо строка '_:_:_'
	
	/*
	*  Функция получения списка файлов или папок с определенным расширением
	*  на входе нужно указать путь к папке $name и массив параметров
	*  
	*  return - files, true, false, по-умолчанию, вернуть только файлы / folders - вернуть только папки / all - вернуть все
	*  subfolders - true/false включать в список подпапки
	*  mask - маска имени файла, при совпадении с которой, файл не будет пропущен, наличие данного правила отменяет правило skip, не работает для папок
	*  skip - список исключений, эти названия файлов и папок будут пропущены
	*  fullpath - переключает тип проверки маски и исключений: false, по-умолчанию - проверять только имена / true - проверять полный путь относительно заданного
	*  type - только для файлов, будут выведены только файлы с этими расширениями
	*  nosort - настройки сортировки - false или по-умолчанию, папки и подпапки вперемешку, затем файлы, затем подфайлы / true - вернуть разными массивами
	*  
	*  третий параметр используется только для служебных целей, т.к. функция рекурсивная при вызове параметра 'subfolders'
	*  
	*  готовый массив сортируется таким образом:
	*  сначала - папки в алфавитном порядке, включая все подпапки
	*  затем - файлы из корневой папки в алфавитном порядке
	*  затем - файлы из всех подпапок, также в алфавитном порядке
	*  
	*  функция возвращает готовый массив
	*/
	
	if (!file_exists($path) || !is_dir($path)) {
		return false;
	}
	
	$path = str_replace(['/', '\\'], DS, $path);
	if (substr($path, -1) !== DS) { $path .= DS; }
	
	$list = scandir($path);
	
	if (empty($list)) {
		return false;
	}
	
	// настраиваем параметры
	
	$parameters = objectFill($parameters, ['return', 'type', 'skip', 'fullpath']);
	
	if (empty($parameters['return']) || !is_string($parameters['return'])) {
		$parameters['return'] = 'files';
	}
	
	if (!is_array($parameters['type'])) {
		$parameters['type'] = dataParse($parameters['type']);
	}
	if (!is_array($parameters['skip'])) {
		$parameters['skip'] = dataParse($parameters['skip']);
	}
	
	if (objectIs($parameters['skip']) && !empty($parameters['fullpath'])) {
		if (!$recurse) {
			$currentpath = null;
			$basepath = strlen($path);
		}
		$currentpath = str_replace(DS, ':', substr($path, $basepath, -1));
		//echo '[' . $currentpath . ']<br>';
	}
	
	//print_r($parameters);
	
	// разбираем список
	
	$newlist = [
		'folders' => [],
		'files' => [],
		'subfolders' => [],
		'subfiles' => []
	];
	
	foreach ($list as $key => $item) {
		
		if ($item === '.' || $item === '..') {
			
			// удаляем корневые элементы
			unset($list[$key]);
			
		} elseif (is_dir($path . $item)) {
			
			// если элемент - папка
			
			$skip = false;
			
			if (objectIs($parameters['skip'])) {
				if (
					empty($currentpath) && in_array($item, $parameters['skip']) ||
					$parameters['fullpath'] && in_array($currentpath . ':' . $item, $parameters['skip'])
				) {
					//echo '[SKIP -- ' . $currentpath . ' -- ' . $item . ']<br>';
					$skip = true;
				}
			}
			
			$item .= DS;
			
			if (!$skip && $parameters['return'] !== 'files') {
				$newlist['folders'][] = $item;
			}
			
			if (!$skip && !empty($parameters['subfolders'])) {
				
				$newdir = localList($path . $item, $parameters, true, $basepath);
				foreach ($newdir['files'] as &$i) {
					$i = $item . $i;
				}
				foreach ($newdir['folders'] as &$i) {
					$i = $item . $i;
				}
				foreach ($newdir['subfiles'] as &$i) {
					$i = $item . $i;
				}
				foreach ($newdir['subfolders'] as &$i) {
					$i = $item . $i;
				}
				
				$newlist['subfiles'] = array_merge($newlist['subfiles'], $newdir['files'], $newdir['subfiles']);
				$newlist['subfolders'] = array_merge($newlist['subfolders'], $newdir['folders'], $newdir['subfolders']);
				
				unset($i, $newdir);
				
			}
			
		} elseif (is_file($path . $item)) {
			
			// если элемент - файл
			
			$file = $item;
			
			if ($parameters['return'] === 'folders') {
				unset($file);
			}
			
			if (!empty($parameters['mask']) && $file) {
				if (
					empty($parameters['fullpath']) && strpos($item, $parameters['mask']) === false ||
					!empty($parameters['fullpath']) && strpos($path . $item, $parameters['mask']) === false
				) {
					unset($file);
				}
			} elseif (objectIs($parameters['skip']) && $file) {
				$name = substr($item, 0, strrpos($item, '.'));
				if (
					!$parameters['fullpath'] && empty($currentpath) && in_array($name, $parameters['skip']) ||
					$parameters['fullpath'] && empty($currentpath) && in_array($item, $parameters['skip']) ||
					$parameters['fullpath'] && in_array($currentpath . ':' . $item, $parameters['skip'])
				) {
					//echo '[SKIP -- ' . $currentpath . ' -- ' . $item . ']<br>';
					unset($file);
				}
			}
			
			if (objectIs($parameters['type']) && $file) {
				$type = strtolower(substr($item, strrpos($item, '.') + 1));
				if (!in_array($type, $parameters['type'])) {
					unset($file);
				}
			}
			
			if (!empty($file)) {
				//echo '[' . $path . ']<br>';
				$newlist['files'][] = $file;
			}
			
		}
		
	}
	
	if (!$recurse) {
		
		if (empty($parameters['nosort'])) {
			$newlist['folders'] = array_merge($newlist['folders'], $newlist['subfolders']);
			sort($newlist['folders']);
			$newlist = array_merge($newlist['folders'], $newlist['files'], $newlist['subfiles']);
		}

	}
	
	return $newlist;
	
}

/* ФУНКЦИИ ДЛЯ РАБОТЫ С НАСТРОЙКАМИ ЛОКАЛЬНЫХ ФАЙЛОВ */

//function dataloadjson($data, $true = false){
	
	/*
	*  Функция загрузки файла в формате json
	*  на входе нужно указать путь к файлу $data (с расширением!)
	*  
	*  функция примет файл и передаст его обработчику iniPrepareJson
	*  а затем вернет полученный массив или объект
	*/
	
/*
	if (!file_exists($data)) {
		return false;
	}
	
	$data = file_get_contents($data);
	
	$data = iniPrepareJson($data, $true);
	
	return $data;
	
}
*/

//function dataloadcsv($datafile, $settings = false){
	
	/*
	*  Функция обработки файла в формате csv
	*  на входе нужно указать путь к файлу $datafile (с названием самого файла, но без расширения!)
	*  Внимание! Файл csv должен быть в кодировке unicode / utf-8
	*  
	*  второе необязательное значение - массив настроек в формате json
	*  
	*  функция примет файл и переведет его в объект:
	*  $result ->
	*    data - массив, где хранятся данные
	*    settings - объект, где хранятся настройки
	*  
	*  настройки подключаются из файла ini с тем же названием или создаются автоматически
	*    row - число колонок
	*      если не указано, будет вычислено самостоятельно
	*    header - число строк, которые являются заголовком таблицы
	*      если не указано, то считается первая строка
	*    titlerow - колонка, которая содержит названия подразделов
	*      если не указано, то считается вторая колонка
	*    name - название или идентификатор таблицы, используется для создания класса
	*      если не указано, то ставится 'default'
	*    keys - в виде массива ["", "" ...], содержащего названия колонок
	*      если не задан, то идет циферная нумерация, начиная с нуля
	*/
	
/*
	if (!file_exists($datafile . '.csv') || filesize($datafile . '.csv') === 0) {
		return false;
	}
	
	$data = (object) array(
		'data' => array(),
		'settings' => (object) array()
	);
	
	if (!$settings) {
		$data -> settings = dataloadjson($datafile . '.ini');
	} elseif (is_object($settings)) {
		$data -> settings = $settings;
	} elseif (is_string($settings)) {
		$data -> settings = json_decode($settings);
	}
	
	if (!empty($data -> settings)) {
		if (isset($data -> settings -> header)) {
			$data -> settings -> header --;
		}
		if (isset($data -> settings -> row)) {
			$data -> settings -> row --;
		}
		if (isset($data -> settings -> titlerow)) {
			$data -> settings -> titlerow --;
		}
	}
	
	if ($handle = fopen($datafile . '.csv', "r")) {
		$row = 0;
		
		while ($file = fgetcsv($handle)) {
			
			if ($row === 0) {
				$keys = $file;
			}
			
			if (
				!empty($data -> settings -> keys) &&
				(
					(
						!empty($data -> settings -> titlerow) &&
						$row == $data -> settings -> titlerow
					) || (
						empty($data -> settings -> titlerow) &&
						$row === 0
					)
				)
			) {
				unset($file);
			}
			
			if (isset($data -> settings -> alias) && is_string($data -> settings -> alias)) {
				$row = $file[ array_search($data -> settings -> alias, $keys) ];
			}
			
			$num = (!empty($file)) ? count($file) : 0;
			
			for ($c = 0; $c < $num; $c++) {
				if (isset($data -> settings -> keys) && is_array($data -> settings -> keys)) {
					$data -> data[$row][$data -> settings -> keys[$c]] = $file[$c];
				} elseif (isset($data -> settings -> keys) && $data -> settings -> keys === true && $row !== 0) {
					$data -> data[$row][$keys[$c]] = $file[$c];
				} else {
					$data -> data[$row][] = $file[$c];
				}
			}
			
			$row++;
		}
		fclose($handle);
	}
	
	//print_r($data -> data[0]);
	
	$tsettings_default = array(
		'cols' => count($data -> data) - 1,
		'name' => 'default',
		'header' => 0,
		//'row' => count($data -> data[0]),
		'row' => count(reset($data -> data)),
		'titlerow' => 1
	);
	
	foreach ($tsettings_default as $key => $item) {
		if (!isset($data -> settings -> $key)) {
			//$data -> settings -> $key = $item;
			$data -> settings = (object) array_merge( (array) $data -> settings, array(
				$key => $item
			));
		}
	}
	
	unset($row, $handle, $file, $num, $c, $tsettings_default, $key, $item, $settings, $datafile);
	
	return $data;
	
}
*/

//function datasavecsv($arr, $datafile){
	
	/*
	*  Функция обработки массива и записи в файл в формате csv
	*  на входе нужно указать путь к файлу $datafile (с названием самого файла, но без расширения!)
	*  и массив
	*
	*  Внимание! Файл csv будет в кодировке unicode / utf-8
	*  
	*  Функция примет массив, построчно подготовит его и запишет в файл
	*/
	
/*
	if ($handle = fopen($datafile . '.csv', "w")) {
		if (is_array($arr[0])) {
			foreach ($arr as $item) {
				fputcsv($handle, $item);
			}
			unset($item);
		} else {
			fputcsv($handle, $arr);
		}
		fclose($handle);
	} else {
		return false;
	}
	
	unset($handle, $datafile, $arr);
	
	return true;
	
}
*/

/* БАЗОВЫЕ ФУНКЦИИ ДЛЯ ЧТЕНИЯ ЛОКАЛЬНЫХ ФАЙЛОВ */

function localOpenFile($target, $array = false) {
	
	/*
	*  Функция открывает файл $target и читает его построчно
	*  
	*  на входе нужно указать полный путь к файлу с названием и расширением
	*  второй параметр - разрешен ли вывод в массив
	*  по-умолчанию запрещен, т.е. вывод идет в строку
	*  
	*  вывод через функцию file_get_contents по сравнению с fopen+fgets+fclose
	*  оказывается быстрее при том же потреблении памяти, т.к. использует memory mapping
	*  
	*  функция вернет массив или строку
	*/
	
	if (!file_exists($target)) {
		return false;
	}
	
	if (!$array) {
		return file_get_contents($target);
	}
	
	$lines = [];
	
	$handle = fopen($target, "r");
	while(!feof($handle)) {
		$lines[] = fgets($handle);
	}
	fclose($handle);
	
	return $lines;
}

function localReadFile($target) {
	
	/*
	*  Функция открывает файл $target и читает его построчно
	*  на входе нужно указать полный путь к файлу с названием и расширением
	*  
	*  отличие от предыдущей функции в том, что эта действует через генератор
	*  и потребляет меньше оперативной памяти - размером ровно на одну строку
	*  
	*  функция возвращает текущую строку итерации
	*/
	
	if (!file_exists($target)) {
		return null;
	}
	
	$handle = fopen($target, "r");
	while(!feof($handle)) {
		yield fgets($handle);
	}
	fclose($handle);
}

function localFile($filename, $funcname = false, $values = false) {
	
	/*
	*  Универсальная функция, объединяющая две предыдущие
	*  Базовая для работы с локальными данными
	*  
	*  на входе нужно указать полный путь к файлу с названием и расширением
	*  вторым параметром - название функции-обработчика
	*  третьим параметром - дополнительные значения
	*  
	*  функция предназначена для построчной обработки больших файлов
	*  
	*  если файл не существует, функция вернет 'false',
	*  так что перед ее вызовом не нужно проводить предварительную проверку
	*  
	*  если указан второй параметр, то функция работает через генератор
	*  если второй параметр не указан, то функция работает через поток
	*  
	*  функция-обработчик должна включать одну переменную - передаваемую в функцию строку генератора
	*  если вы хотите использовать другие значения, используйте второй параметр в качестве массива
	*  
	*  пример функции-обработчика без доп.параметров и ее вызов:
	*  
	*  function test($str) {
	*    echo '<p>' . $str . </p>;
	*  }
	*  localFile($filename, 'test');
	*  
	*  пример функции-обработчика c одним доп.параметром и ее вызов:
	*  
	*  function test($str, $trim) {
	*    if ($trim) {
	*      $srt = trim($srt);
	*    }
	*    echo '<p>' . $str . </p>;
	*  }
	*  localFile($filename, 'test', 1);
	*  
	*  пример функции-обработчика c доп.параметрами в виде массива и ее вызов:
	*  
	*  function test($str, $params) {
	*    if ($params['trim']) {
	*      $srt = trim($srt);
	*    }
	*    if ($params['tags']) {
	*      $srt = '<p>' . $str . </p>;
	*    }
	*    if ($params['end']) {
	*      $srt = $srt . PHP_EOL;
	*    }
	*    echo $str;
	*  }
	*  localFile($filename, 'test', ['trim', 'tags']);
	*  
	*  примеры использования с файлом объемом 2.36 mb
	*  в скобках указана память после выполнения операции и потребляемая на операцию память
	*  
	*  $str = localFile($filename);
	*    echo '<pre>' . $str . '</pre>'; // [2.37 mb - 6.88 mb]
	*    echo '<pre>', $str, '</pre>'; //  [2.37 mb - 2.38 mb]
	*  $str = '';
	*  localFile($filename, 'ad');
	*    echo '<pre>' . $str . '</pre>'; // [2.37 mb - 6.88 mb]
	*    echo '<pre>', $str, '</pre>'; // [2.37 mb - 2.38 mb]
	*  echo '<pre>', localFile($filename, 'ec'), '</pre>'; // [120.95 kb - 133.39 kb]
	*  unset($str); // [121.17 kb]
	*  
	*  функции, использованные в примере:
	*  function ec($a) { echo $a; }
	*  function ad($a) { global $str; $str .= $a; }
	*  
	*  Обратите внимание, что использование генератора для построчной обработки
	*  существенно экономит оперативную память системы!
	*/
	
	if (!file_exists($filename)) {
		return false;
	}
	
	if (!$funcname) {
		return localOpenFile($filename, $values);
	}
	
	$iterator = localReadFile($filename);
	
	if (objectIs($iterator)) {
		foreach ($iterator as $iteration) {
			if ($values) {
				$funcname($iteration, $values);
			} else {
				$funcname($iteration);
			}
		}
	} else {
		return false;
	}
	
}

function localLink($path, $folder, $prefix = true, $variant = null, $return = null) {
	
	/*
	*  Простая функция, которая проверяет указанных файл
	*  и возвращает путь к нему вместе с параметром версии
	*  
	*  Первым параметром задается путь
	*  Вторым параметром - системная папка
	*  Третий, необязательный параметр - префикс, который назначается принудительно
	*   - если 'true', то устанавливается от времени изменения файла
	*   - если пустой, то не устанавливается
	*  Четвертый, необязательный параметр - минимизированный вариант файла
	*  Пятый, необязательный параметр - тип возврата
	*  
	*  Путь читается в формате '/', '\' или ':'
	*  Например, 'path\to\file.ext' вернет 'path/to/file.ext?mtime'
	*/
	
	$path = clear($path, 'urldecode');
	
	if (!empty($variant)) {
		$point = strrpos($path, '.');
		$pathv = substr($path, 0, $point) . '.' . $variant . substr($path, $point);
		$filev = constant('PATH_' . strtoupper($folder)) . str_replace(['/', '\\', ':'], DS, $pathv);
		unset($point);
	} else {
		$pathv = null;
		$filev = null;
	}
	
	if (
		!empty($pathv) &&
		!empty($filev) &&
		file_exists($filev)
	) {
		$file = $filev;
		$path = $pathv;
	} else {
		$file = constant('PATH_' . strtoupper($folder)) . str_replace(['/', '\\', ':'], DS, $path);
		if (!file_exists($file)) {
			return null;
		}
	}
	
	//global $uri;
	return constant('URL_' . strtoupper($folder)) . str_replace(['/', '\\', ':'], '/', $path) . (!empty($prefix) ? '?' . ($prefix === true ? filemtime($file) : $prefix) : null);
	
}

function localCopy($from, $to, $rewrite = true){
	
	/*
	*  функция, позволяющая скопировать папку со всем содержимым
	*  в другую папку на сервере
	*  
	*  первый параметр - адрес исходной папки
	*  второй параметр - адрес, куда копировать (это может быть несуществующая папка)
	*  третий параметр - перезапись файлов, если они имеются
	*/
	
	if (!file_exists($from)) {
		return false;
	}
	
	if (is_dir($from)) {
		
		@mkdir($to);
		$d = dir($from);
		
		while (( $entry = $d -> read() ) !== false) {
			if ($entry == '.' || $entry == '..') continue;
			localCopy($from . DS . $entry, $to . DS . $entry, $rewrite);
		}
		
		$d -> close();
		
	} elseif (!file_exists($to) || $rewrite) {
		copy($from, $to);
	}
	
}

function localSaveFromUrl($filename, $url, $delete = false) {
	
	/*
	*  функция, позволяющая сохранить файл на сервере,
	*  содержимое которого прочитано из url
	*  
	*  первым параметром передается адрес файла для сохранения на сервере
	*  второй параметр - адрес ссылки
	*  третий параметр определяет поведение при условии, если файл с таким именем уже существует:
	*    false - по-умолчанию, ничего не делать, пропускать работу функции и возвращать false
	*    true - сперва удалять файл, а затем записывать его заново
	*/
	
	if (file_exists($filename)) {
		if (!$delete) {
			return false;
		} else {
			unlink($filename);
		}
	}
	
	$file = file_get_contents($url);
	
	if (empty($file)) {
		return false;
	} elseif (file_put_contents($filename, $file)) {
		return true;
	} else {
		return false;
	}
	
}

function localOpenUrl($url, $method = false, $redirect = 0) {
	
	/*
	*  функция, позволяющая загружать файл с сервера,
	*  содержимое которого прочитано из url
	*  
	*  первым параметром передается url файла
	*  второй параметр - метод
	*  
	*  третий параметр служебный, служит для предотвращения более 1 редиректа
	*/
	
	$target = null;
	
	if (!$method || $method === true) {
		
		$target = file_get_contents($url);
		
	} elseif ($method === 'curl' && extension_loaded('curl')) {
		
		$init = curl_init();
		curl_setopt($init, CURLOPT_URL, $url);
		curl_setopt($init, CURLOPT_HTTPGET, true);
		curl_setopt($init, CURLOPT_USERAGENT, USER_AGENT);
		curl_setopt($init, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($init, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		//curl_setopt($init, CURLOPT_HEADER, true);
		
		$target = curl_exec($init);
		//$errors = curl_error($init);
		
		// вывод ошибок пока закомментирован, т.к. он не отлажен
		
		// код ниже осуществляет проверку заголовков и статусов
		// если, например, статус не равен 200, то возвращает false
		// а если задан редирект, то переходит по нему
		
		// начало кода
		$info = [
			'headers' => curl_getinfo($init),
			'code' => null,
			'redirect' => null
		];
		
		if (!empty($info['headers']['http_code'])) {
			$info['code'] = $info['headers']['http_code'];
		}
		if (!empty($info['headers']['redirect_url'])) {
			$info['redirect'] = $info['headers']['redirect_url'];
		}
		
		unset($info['headers']);
		
		if ($info['redirect']) {
			if ($redirect > 1) {
				$target = false;
			} else {
				$target = localOpenUrl($info['redirect'], $method, $redirect++);
			}
		} elseif ($info['code'] != '200') {
			$target = false;
		}
		// конец кода
		
		if (DEFAULT_MODE === 'develop' && !empty($errors)) {
			//$target = print_r($errors, true);
		}
		
		curl_close($init);
		
	} elseif ($method === 'fsock') {
		
		// Устанавливаем соединение
		$init = fsockopen($url, -1, $errno, $errstr, 30);
		
		if (!$init && DEFAULT_MODE === 'develop') {
			
			// Проверяем успешность установки соединения
			$target = $errstr . ' (' . $errno . ')';
			
		} else {
			
			// Формируем HTTP-заголовки для передачи его серверу
			$header = 'GET ' . $url . ' ' . $_SERVER['SERVER_PROTOCOL'] . "\r\n";
			$header .= 'User-Agent: ' . USER_AGENT . "\r\n";
			$header .= "Connection: Close\r\n\r\n";
			
			// Отправляем HTTP-запрос серверу
			fwrite($init, $header);
			
			// Получаем ответ
			while (!feof($init)) {
				$target .= fgets($init, 1024);
			}
			
			// Закрываем соединение
			fclose($init);
			
		}
		
	}
	
	return $target;
	
}

function localRequestUrl($url, $data = false, $method = false, $redirect = 0) {
	
	/*
	*  функция, позволяющая запросить какой-либо url
	*  
	*  первым параметром передается url
	*  второй параметр - данные
	*  третий параметр - метод (любой метод обращается к вызову через curl)
	*    * на данный момент поддерживается только post и только через curl
	*    * также нужно быть внимательным, чтобы отправлять запрос с абсолютной ссылкой, а не относительной
	*  
	*  четвертый параметр служебный, служит для предотвращения более 1 редиректа
	*    * на данный момент редирект убран из кода
	*/
	
	$target = null;
	
	
	if (!$method || $method === true) {
		
		$target = file_get_contents($url);
		
	} elseif (is_string($method) && extension_loaded('curl')) {
		
		$init = curl_init();
		
		curl_setopt($init, CURLOPT_URL, $url);
		
		if (strtolower($method) === 'post') {
			curl_setopt($init, CURLOPT_POST, true);
			curl_setopt($init, CURLOPT_POSTFIELDS, $data);
		} else {
			curl_setopt($init, CURLOPT_HTTPGET, true);
			//$url .= ''
		}
		
		curl_setopt($init, CURLOPT_USERAGENT, USER_AGENT);
		curl_setopt($init, CURLOPT_RETURNTRANSFER, true);
		
		$target = curl_exec($init);
		
		// код ниже осуществляет проверку заголовков и статусов
		// если, например, статус не равен 200, то возвращает false
		// а если задан редирект, то переходит по нему
		
		// начало кода
		$info = [
			'headers' => curl_getinfo($init),
			'code' => null
		];
		
		if (!empty($info['headers']['http_code'])) {
			$info['code'] = $info['headers']['http_code'];
		}
		
		unset($info['headers']);
		
		if ($info['code'] != '200') {
			$target = false;
		}
		// конец кода
		
		curl_close($init);
		
	}
	
	return $target;
	
}

function localUnzip($filename, $path, $delete = true) {
	
	/*
	*  функция, позволяющая распаковать файл, хранящийся на сервере
	*  
	*  первым параметром передается имя и адрес файла на сервере
	*  вторым - путь для распаковки
	*  третий параметр определяет поведение после успешной распаковки:
	*    false - оставить исходный файл архива
	*    true - удалить его (по-умолчанию)
	*/
	
	if (
		!extension_loaded('zip') ||
		!file_exists($filename)
	) {
		return false;
	}
	
	$zip = new ZipArchive;
	
	$res = $zip -> open($filename);
	
	if ($res === true) {
		
		$zip -> extractTo($path);
		$zip -> close();
		
		if ($delete === true) {
			if (!unlink($filename)) {
				return false;
			}
		}
		
		return true;
		
	} else {
		return false;
	}
	
}

function localOpenTable($datafile, $dataformat, $settings = null){
	
	/*
	*  Функция обработки файла в формате csv
	*  на входе нужно указать путь к файлу $datafile (с названием самого файла, но без расширения!)
	*  Внимание! Файл csv должен быть в кодировке unicode / utf-8
	*  
	*  второе необязательное значение - массив настроек в формате json
	*  
	*  функция примет файл и переведет его в массив $data - массив, где хранятся данные
	*  
	*  настройки:
	*    return - если задан, то по значению этой колонки будет назван ключ массива
	*    names - если задан, то по будут загружены строки только с этими именами,
	*    * работает если задан return или по номеру строки, учитывая что счет начинается с нуля, и еще первая строка может быть пропущена, если не заданы ключи
	*    limit - если задан, то выводится только указанное число строк
	*    * удобно использовать в связке с names, чтобы не нагружать систему
	*    skip - номера пропускаемых строк
	*    * могут быть заданы в виде массива или в виде данных через двоеточие
	*    fields - если задан, то по значениям этого массива будут заполняться главные колонки (не data)
	*    * например, если вам нужно пропустить 'ctime' и 'mtime', просто укажите массив без них
	*    //rowskip - строки, которые нужно пропустить
	*    //colskip - колонки, которые нужно пропустить
	*    keys - массив ключей, которые выступят ключами в массиве данных
	*    * если вам нужно сделать вложенные данные, используйте двоеточие или точку:
	*    * keys = ["parent:one", "parent:two"]
	*    * keys = "parent.one:parent.two"
	*/
	
	/*
	echo "<table>";
	// Получили строки и обойдем их в цикле
	$rowIterator = $sheet->getRowIterator();
	foreach ($rowIterator as $row) {
		// Получили ячейки текущей строки и обойдем их в цикле
		$cellIterator = $row->getCellIterator();
		
		echo "<tr>";
		
		foreach ($cellIterator as $cell) {
			echo "<td>" . $cell->getCalculatedValue() . "</td>";
		}
		
		echo "</tr>";
	}
	echo "</table>";
	*/
	
	if (!file_exists($datafile) || filesize($datafile) === 0) {
		return null;
	}
	
	$data = [];
	
	if (!empty($settings) && is_string($settings)) {
		$settings = json_decode($settings, true);
	}
	
	$stat = stat($datafile);
	
	// Общие настройки
	
	$keys = !empty($settings['keys']) ? (is_array($settings['keys']) ? $settings['keys'] : dataParse($settings['keys'])) : null;
	$skip = !empty($settings['skip']) ? (is_array($settings['skip']) ? array_fill_keys($settings['skip'], null) : array_fill_keys(dataParse($settings['skip']), null)) : null;
	$fields = !empty($settings['fields']) ? $settings['fields'] : ['id', 'name', 'type', 'parent', 'ctime', 'mtime', 'self'];
	if (objectIs($settings['names'])) {
		$settings['limit'] = count($settings['names']);
	}
	
	// Если формат excel
	
	if ($dataformat === 'xls' || $dataformat === 'xlsx') {
		
		// Проверяем существование необходимых расширений php и загруженной библиотеки
		
		//if (!in('libraries', 'excel:system')) {
		if (!class_exists('Simple' . mb_strtoupper($dataformat))) {
			logging('local table open false - not \'excel\' library');
			return null;
		}
		
		if ($dataformat === 'xls') {
			$excel = SimpleXLS::parse($datafile);
		} elseif ($dataformat === 'xlsx') {
			$excel = SimpleXLSX::parse($datafile);
		}
		
		if (empty($excel)) {
			logging('local table open false - this excel format is wrong or not supported');
			return null;
		} else {
			
			foreach ($excel->rows() as $row => $file) {
				
				// ПОСТРОЧНАЯ ОБРАБОТКА
				
				if ($row === 0 && empty($keys)) {
					$keys = $file;
					continue;
				} elseif (!empty($skip) && array_key_exists($row, $skip)) {
					unset($skip[$row]);
					continue;
				}
				
				$num = (!empty($file)) ? count($file) : 0;
				$name = $row;
				
				if (!empty($settings['return']) && !empty($keys) && in_array($settings['return'], $keys)) {
					$name = $file[array_search($settings['return'], $keys)];
				}
				
				if (objectIs($settings['names']) && !in_array($name, $settings['names'])) {
					continue;
				}
				
				for ($c = 0; $c < $num; $c++) {
					
					if (!empty($settings['encoding'])) {
						$file[$c] = mb_convert_encoding($file[$c], 'UTF-8', $settings['encoding']);
					}
					
					$k = objectIs($keys) && isset($keys[$c]) ? str_replace('.', ':', $keys[$c]) : $c;
					
					if (strpos($k, ':')) {
						
						$k = dataParse($k);
						$data[$name] = objectMergeLevel($data[$name], $k, $file[$c]);
						
					} elseif (in_array($k, $fields)) {
						$data[$name][$k] = $file[$c];
					} else {
						$data[$name]['data'][$k] = $file[$c];
					}
					
				}
				
				if (empty($data[$name]['ctime']) && in_array('ctime', $fields)) {
					$data[$name]['ctime'] = $stat['ctime'];
				}
				if (empty($data[$name]['mtime']) && in_array('mtime', $fields)) {
					$data[$name]['mtime'] = $stat['mtime'];
				}
				
				if (!empty($settings['limit']) && $settings['limit'] <= $row) {
					break;
				}
				
				// КОНЕЦ ПОСТРОЧНОЙ ОБРАБОТКИ
				
			}
		}
		
		unset($excel);
		
	}

	// Если формат csv
	
	if ($dataformat === 'csv' && $handle = fopen($datafile, "r")) {
		
		$row = 0;
		$delimiter = !empty($settings['special']) && !empty($settings['special'][0]) ? $settings['special'][0] : ',';
		$enclosure = !empty($settings['special']) && !empty($settings['special'][1]) ? $settings['special'][1] : '"';
		
		while ($file = fgetcsv($handle, null, $delimiter, $enclosure)) {
			
			// ПОСТРОЧНАЯ ОБРАБОТКА
			
			if ($row === 0 && empty($keys)) {
				$keys = $file;
				unset($file);
				continue;
			} elseif (!empty($skip) && array_key_exists($row, $skip)) {
				unset($skip[$row]);
				$row++;
				continue;
			}
			
			$num = (!empty($file)) ? count($file) : 0;
			$name = $row;
			
			if (!empty($settings['return']) && !empty($keys) && in_array($settings['return'], $keys)) {
				$name = $file[array_search($settings['return'], $keys)];
			}
			
			if (objectIs($settings['names']) && !in_array($name, $settings['names'])) {
				unset($file);
				continue;
			}
			
			for ($c = 0; $c < $num; $c++) {
				
				if (!empty($settings['encoding'])) {
					$file[$c] = mb_convert_encoding($file[$c], 'UTF-8', $settings['encoding']);
				}
				
				$k = objectIs($keys) && isset($keys[$c]) ? str_replace('.', ':', $keys[$c]) : $c;
				
				if (strpos($k, ':')) {
					
					$k = dataParse($k);
					$data[$name] = objectMergeLevel($data[$name], $k, $file[$c]);
					
				} elseif (in_array($k, $fields)) {
					$data[$name][$k] = $file[$c];
				} else {
					$data[$name]['data'][$k] = $file[$c];
				}
				
			}
			
			if (empty($data[$name]['ctime']) && in_array('ctime', $fields)) {
				$data[$name]['ctime'] = $stat['ctime'];
			}
			if (empty($data[$name]['mtime']) && in_array('mtime', $fields)) {
				$data[$name]['mtime'] = $stat['mtime'];
			}
			
			$row++;
			
			if (!empty($settings['limit']) && $settings['limit'] <= $row) {
				break;
			}
			
			// КОНЕЦ ПОСТРОЧНОЙ ОБРАБОТКИ
			
		}
		
		fclose($handle);
		
	}
	
	unset($row, $name, $handle, $delimiter, $enclosure, $fields, $file, $num, $c, $k, $keys, $stat, $settings, $dataformat, $datafile);
	
	return $data;
	
}

?>