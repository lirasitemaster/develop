<?php defined('isCMS') or die;

global $libraries;

$libraries -> update = [];
print_r($libraries);

exit;

foreach ($libraries -> empty as $key => $item) {
	
	//$item = dataParse($item);
	
	// проверяем наличие названия и вендора, и чтобы вендор не был системой
	// иначе пропускаем обработку и переходим к следующему
	
	if (empty($item[0])) {
		
		logging('composer - unknown library by \'' . $item[1] . '\' was skipped');
		continue;
		
	} elseif (empty($item[1]) || $item[1] === 'system') {
		
		$lib = funcLibraries_System($item[0]);
		$ini = PATH_CORE . 'install' . DS . 'database' . DS . 'libraries' . DS . $item[0] . '.system.ini';
		$is = null;
		$data = null;
		
		if (!empty($libraries -> db)) {
			foreach ($libraries -> db as $i) {
				if (
					$i['name'] === $item[0] &&
					$i['type'] === 'system'
				) {
					$is = true;
					break;
				}
			}
		}
		
		if (!$is && file_exists($ini)) {
			$data = localFile($ini);
			if ($data) {
				$libraries -> update[] = [
					'name' => $item[0],
					'type' => 'system',
					'data' => $data
				];
			}
		}
		
		if (($data || $is) && $lib) {
			unset($libraries -> empty[$key]);
		}
		
		logging('composer - system library \'' . $item[0] . '\' was ' . (!$lib ? 'not ' : '') . 'copied ' . (!$data ? 'and not ' : 'but was ') . 'added to database');
		
		unset($i, $lib, $ini, $is, $data);
		
		continue;
		
	}
	
	// устанавливаем версии
	
	$version = (object) [
		'need' => $item[2],    // запрошенная
		'local' => null,       // которая лежит на локале
		'git' => null,         // подходящая с гитхаба
		'gitname' => null      // версия на гитхабе или назначаем мастер
	];
	
	// формируем объект с данными о библиотеке на github
	
	$git = (object) [
		'url' => 'https://github.com/' . $item[1] . '/' . $item[0] . '/raw/',
		//'url' => 'https://raw.githubusercontent.com/' . $item[1] . '/' . $item[0] . '/',
		'download' => 'https://github.com/' . $item[1] . '/' . $item[0] . '/archive/',
		'local' => PATH_LIBRARIES . $item[1] . DS . $item[0] . DS,
		'data' => [
			'name' => $item[0],
			'vendor' => $item[1],
			'version' => null,
			'authors' => null,
			'license' => null,
			'description' => null,
			'temp' => [],
			'path' => []
		],
		'settings' => dataParse(DEFAULT_COMPOSER),
		'datasheets' => ['package', 'bower'],
		'datalist' => [],
	];
	
	// устанавливаем локальную версию
	
	if (!empty($git -> settings[1])) {
		
		// если есть разбивка по версиям,
		// то просто ищем папку версии
		
		$version -> local = funcLibraries_Version($git -> local, $version -> need);
		if ($version -> local) {
			$git -> local .= $version -> local . DS;
		}
		
	} else {
		
		// если разбивки по версиям нет,
		// то ищем файлы-описания внутри папки библиотеки
		// и читаем версию из них
		
		foreach ($git -> datasheets as $i) {
			
			$file = localOpenFile($git -> local . $i . '.json');
			$temp = [];
			
			if ($file) {
				$temp = json_decode($file, true);
				if (!empty($temp['version'])) {
					$version -> local = $temp['version'];
					break;
				}
			}
			
		}
		
	}
	
	// запрашиваем версии на github
	// и по итогам присваиваем первую подходящую версию
	// но только, если такой локальной версии нет
	// если же локальная версия совпадает не полностью, нам все равно стоит проверить последнюю версию с гитхаба
	
	if (
		!$version -> local || $version -> need && $version -> need !== $version -> local
	) {
		
		$versions = localOpenUrl('https://api.github.com/repos/' . $item[1] . '/' . $item[0] . '/tags', 'curl');
		
		if (!empty($versions)) {
			$versions = json_decode($versions, true);
			foreach ($versions as $i) {
				if (!$version -> need) {
					if (strpos($i['name'], 'v') === 0) {
						$version -> git = substr($i['name'], 1);
					} else {
						$version -> git = $i['name'];
					}
					break;
				} elseif (strpos($i['name'], $version -> need) === 1) {
					$version -> git = substr($i['name'], 1);
					break;
				} elseif (strpos($i['name'], $version -> need) === 0) {
					$version -> git = $i['name'];
					break;
				}
			}
			unset($i);
		}
		
		unset($versions);
		
	}
	
	// сравниваем версии и решаем, нужно нам делать закачку или нет
	
	//print_r($version);
	
	if (
		$version -> local &&
		$version -> git &&
		$version -> git === $version -> local
	) {
		
		$version -> git = null;
		$version -> git = null;
		
	} elseif (
		$version -> local &&
		$version -> git &&
		$version -> git !== $version -> local
	) {
		
		$versions = [
			'local' => datasplit($version -> local, '.'),
			'git' => datasplit($version -> git, '.')
		];
		
		foreach ($versions['local'] as $k => $i) {
			// сравниваем значения local и git
			
			if (!set($versions['git'][$k])) {
				
				// если у git нет данной части версии для сравнения
				// значит, local победил
				
				$version -> git = null;
				break;
				
			} elseif ($versions['local'][$k] > $versions['git'][$k]) {
				
				// раз сравнение продолжилось, значит у git есть что сравнивать
				// если local больше git, значит local победил
				
				$version -> git = null;
				break;
				
			} elseif ($versions['local'][$k] < $versions['git'][$k]) {
				
				// если local меньше git, значит git победил
				
				$version -> local = null;
				$git -> settings[2] = 'update';
				break;
				
			} elseif (!set($versions['local'][$k + 1])) {
				
				// раз сравнение продолжилось, значит git и local одинаковые
				// но если следующей части версии у local нет, значит git победил
				
				$version -> local = null;
				$git -> settings[2] = 'update';
				break;
				
			}
			
			// продолжаем сравнение для следующих частей версии
			
		}
		
		unset($k, $i, $versions);
		
	} else {
	//} elseif (
	//	!$version -> local
	//) {
		
		$git -> settings[2] = 'update';
		
	}
		
	
	$version -> gitname = $version -> git ? 'v' . $version -> git : ($version -> local ? 'v' . $version -> local : 'master');
	
	$git -> url .= $version -> gitname . '/';
	$git -> download .= $version -> gitname . '.zip';
	
	//print_r($version);
	//print_r($git);
	
	// может быть сюда потом встроить не только по url и не только для github, но и для локальных zip, типа:
	//local test: filesystem\libraries\name\
	//$git -> url = PATH_LIBRARIES . $item . DS;
	
	//остановка выполнения дальнейшего кода!
	//print_r($git);
	//continue;
	
	// получение сведений о библиотеке из файлов-описаний и разбор данных
	
	foreach ($git -> datasheets as $i) {
		
		if (!$version -> git && $version -> local) {
			// для локальных данных
			$git -> datalist[$i] = json_decode(localOpenFile($git -> local . $i . '.json'), true);
		} else {
			// для всех остальных случаев данные берутся из url
			//$git -> datalist[$i] = localOpenUrl($git -> url . $i . '.json', 'curl');
			$git -> datalist[$i] = json_decode(localOpenUrl($git -> url . $i . '.json', 'curl'), true);
		}
		
		foreach (['version', 'authors', 'license', 'description', 'homepage'] as $k) {
			if (empty($git -> data[$k]) && !empty($git -> datalist[$i][$k])) {
				$git -> data[$k] = $git -> datalist[$i][$k];
			}
		}
		
		// разбор данных из 'package'
		if ($i === 'package') {
			if (!empty($git -> datalist[$i]['main'])) {
				$git -> data['temp'][] = $git -> datalist[$i]['main'];
			}
			if (!empty($git -> datalist[$i]['style'])) {
				$git -> data['temp'][] = $git -> datalist[$i]['style'];
			}
		}
		
		// разбор данных из 'bower'
		if ($i === 'bower' && !empty($git -> datalist[$i]['main'])) {
			if (is_array($git -> datalist[$i]['main'])) {
				$git -> data['temp'] = array_merge($git -> data['temp'], $git -> datalist[$i]['main']);
			} else {
				$git -> data['temp'][] = $git -> datalist[$i]['main'];
			}
		}
		
	}
	
	unset($i, $k, $git -> datasheets, $git -> datalist);
	
	// теперь обрабатываем пути
	
	foreach ($git -> data['temp'] as &$i) {
		if (strpos($i, '.') === 0) {
			$i = substr($i, 1);
		}
		if (strpos($i, '/') === 0) {
			$i = substr($i, 1);
		}
	}
	
	unset($i);
	
	// здесь мы будем создавать описатель библиотеки
	// и попутно скачивать файлы, если такие настройки прописаны в DEFAULT_COMPOSER
	
	// убираем min
	// ЭТО ДЕЛАЕТСЯ Т.К. В ДАЛЬНЕЙШЕМ ВСЕ РАВНО БУДЕТ ПРОВЕРЯТЬСЯ, ЕСТЬ ЛИ МИНИФИЦИРОВАННАЯ ВЕРСИЯ
	// И ЕСЛИ ОНА ЕСТЬ, ТО ЗАГРУЖАТЬСЯ БУДЕТ ИМЕННО ОНА!!!
	foreach ($git -> data['temp'] as &$i) {
		if (strrpos($i, '.min.') !== false) {
			$i = str_replace('.min.', '.', $i);
		}
	}
	unset($i);
	
	// убираем дубли
	$git -> data['temp'] = array_unique($git -> data['temp']);
	
	// редактируем версию под запрошенную
	if (!empty($git -> settings[1])) {
		$git -> data['version'] = $version -> need;
	}
	
	foreach ($git -> data['temp'] as $i) {
		
		$path = [
			'folders' => datasplit($i, '\/'),
			'file' => null,
			'type' => null,
			'current' => null
		];
		
		$path['file'] = array_pop($path['folders']);
		$path['type'] = strrpos($path['file'], '.');
		if ($path['type'] === false) { continue; }
		$path['type'] = substr($path['file'], $path['type'] + 1);
		
		$git -> data['path'][$git -> data['version']]['local'][$path['type']][] = $i;
		$git -> data['path'][$git -> data['version']]['cdn'][$path['type']][] = $git -> url . $i;
		
		// задаем дефолтные настройки
		
		$def = [
			'str' => str_replace(
				[$item[0], $item[1], $git -> data['version']],
				['{name}', '{vendor}', '{version}'],
			$i),
			'url' => str_replace($git -> data['version'], '{version}', $git -> url)
		];
		
		$git -> data['path']['default']['local'][$path['type']][] = $def['str'];
		$git -> data['path']['default']['cdn'][$path['type']][] = $def['url'] . $def['str'];
		
		unset($def);
		
		// если задан 'download', будем качать только нужные файлы
		
		if (
			//$version -> git &&
			$git -> data['version'] &&
			!empty($git -> settings[0]) &&
			$git -> settings[0] === 'download'
		) {
			
			// добавляем к массиву папок папку библиотеки и папку версии,
			// потому что создавать папки будем потоком
			
			if (!empty($git -> settings[1])) {
				array_unshift($path['folders'], $git -> data['version']);
			}
			array_unshift($path['folders'], $item[1], $item[0]);
			
			// создаем папки по порядку согласно массиву
			
			foreach ($path['folders'] as $k) {
				$path['current'] .= $k . DS;
				if (!file_exists(PATH_LIBRARIES . $path['current'])) {
					mkdir(PATH_LIBRARIES . $path['current']);
				}
			}
			
			// проверяем наличие файла и если файла нет, то создаем его
			// на данный момент создаем так
			// предварительно проверяем установку 'update' и если она включена и файл существует, удаляем его
			// затем читаем файл с гитхаба и записываем содержимое в новый файл
			
			if (
				file_exists(PATH_LIBRARIES . $path['current'] . $path['file']) &&
				!empty($git -> settings[2]) &&
				$git -> settings[2] === 'update'
			) {
				unlink(PATH_LIBRARIES . $path['current'] . $path['file']);
			}
			
			if (!localSaveFromUrl(PATH_LIBRARIES . $path['current'] . $path['file'], $git -> url . $i)) {
				echo 'oops! file [' . $path['file'] . '] from library [' . $git -> data['name'] . '] was not be downloaded<br>';
			}
			
		}
		
		//print_r($path);
		
	}
	
	unset($i, $k, $path, $git -> data['temp']);
	
	// если задан 'install', будем качать архив и распаковывать его целиком
	
	if (
		//$version -> git &&
		$git -> data['version'] &&
		!empty($git -> settings[0]) &&
		$git -> settings[0] === 'install' &&
		extension_loaded('zip')
	) {
		
		$path = [
			'folders' => [$item[1], $item[0]],
			'target' => null,
			'current' => null,
			'string' => null,
		];
		
		if (!empty($git -> settings[1])) {
			array_push($path['folders'], $git -> data['version']);
		}
		
		$path['target'] = array_pop($path['folders']);
		$path['string'] = PATH_LIBRARIES . objectToString($path['folders'], DS) . DS;
		
		// создаем папки по порядку согласно массиву
		
		foreach ($path['folders'] as $k) {
			$path['current'] .= $k . DS;
			if (!file_exists(PATH_LIBRARIES . $path['current'])) {
				mkdir(PATH_LIBRARIES . $path['current']);
			}
		}
		
		// предварительно проверяем установку 'update' и если она включена и папка существует, удаляем ее
		
		if (
			file_exists($path['string'] . $path['target']) &&
			!empty($git -> settings[2]) &&
			$git -> settings[2] === 'update'
		) {
			funcLibraries_Delete($path['string'] . $path['target']);
		}
		
		if (!file_exists($path['string'] . $path['target'])) {
			
			// проверяем, есть ли папка и есть ли уже готовый файл архива,
			// потому что устанавливать будем только если папки нет
			
			localSaveFromUrl(PATH_LIBRARIES . $item[0] . '-' . $version -> gitname . '.zip', $git -> download, true);
			
			// распаковываем архив, в случае отсутствия архива возвращается false
			localUnzip(PATH_LIBRARIES . $item[0] . '-' . $version -> gitname . '.zip', $path['string'], false);
			
			if (
				file_exists($path['string'] . $path['target']) &&
				is_dir($path['string'] . $path['target'])
			) {
				chmod($path['string'] . $path['target'], 0777);
			}
			
			rename(
				$path['string'] . $item[0] . '-' . ($version -> gitname === 'master' ? $version -> gitname : $version -> git),
				$path['string'] . $path['target']
			);
			
		}
		
	}
	
	//print_r($git -> settings);
	
	if (!empty($git -> data['path'])) {
		
		unset($libraries -> empty[$key]);
		
		//$libraries -> update[$item[0] . ':' . $item[1]] = $git -> data['path'];
		$libraries -> update[] = [
			'name' => $item[0],
			'type' => $item[1],
			'data' => json_encode($git -> data['path'])
		];
		
	} else {
		
		logging('composer - library \'' . $item[0] . '\' by \'' . $item[1] . '\' not found description file or links in description file in repository');
		
	}
	
	unset($version);
	
	//echo htmlentities(print_r($git, true));
	//echo '<a href="' . $git -> download . '">download ' . $git -> data['name'] . '</a>';
	
}

//print_r($libraries);
//exit;

// в итоге, все работает
// а теперь еще есть проверка на наличие библиотеки локально
// ну и еще композер создает файл с дефолтными настройками, чтобы версия и пр. заменялись на текстовые переменные

// ДА НАХ ЕЩЕ НОВЫЙ ФАЙЛ СОЗДАВАТЬ - ВСЁ ПИХАЕМ СЮДА!!!

//здесь нужно записывать $libraries -> update в базу данных

//https://0iscms.ru/about/news/
//https://0iscms.ru/process/composer/update/?hash=crypting(time())

// записываем в базу данных сведения о библиотеках

if (set($libraries -> update)) {
	
	//production
	//header('Content-Type: text/html; charset=UTF-8');
	//header('Location: /' . DEFAULT_PROCESSOR . '/composer/update/?hash=' . crypting(time()) . '&data=' . base64_encode(json_encode($libraries -> update)));
	
	//curl
	//global $uri;
	//$t = localRequestUrl($uri -> site . DEFAULT_PROCESSOR . '/system/composer/', 'hash=' . crypting(time() + TIME_HOUR) . '&csrf=' . csrf() . '&data=' . base64_encode(json_encode($libraries -> update)), 'post');
	$f = objectProcess('system:composer');
	$t = localRequestUrl($f['link'], $f['string'] . '&data=' . base64_encode(json_encode($libraries -> update)), 'post');
	echo '[' . $t . ']<br>';
	unset($f, $t);
	
	//test:
	//header('Content-Type: text/html; charset=UTF-8');
	//header('Location: /' . DEFAULT_PROCESSOR . '/composer/update/?hash==1234567890&data=' . base64_encode(json_encode($libraries -> update)));
	//echo 'https://0iscms.ru/' . DEFAULT_PROCESSOR . '/composer/update/?hash=' . crypting(time()) . '&data=' . base64_encode(json_encode($libraries -> update));
	
}

if (set($libraries -> empty)) {
	echo '[one or some libraries have not been installed -- see more in logs]<br>';
}

if (
	set($libraries -> update) ||
	set($libraries -> empty)
) {
	exit;
}

?>