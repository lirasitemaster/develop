<?php defined('isENGINE') or die;

// иногда происходит такая ситуация
// после распаковки сервер почему-то сразу же начинает устанавливать библиотеки,
// как будто бы он перезагружается с индексной страницы
// почему так происходит, хз

// как быть с точкой в некоторых именах?
// если имя содержит точку, то она преобразуется в спец.знак
//if (strpos($item[0], '.') !== false) { $item[0] = str_replace('.', '--', $item[0]); }
// если имя содержит спец.знак, то он преобразуется в точку
//if (strpos($item[0], '--') !== false) { $item[0] = str_replace('--', '.', $item[0]); }

global $libraries;

$libraries -> update = [];

//echo '<pre>' . print_r($libraries, 1) . '</pre><br>';
//exit;

foreach ($libraries -> empty as $key => $item) {
	
	//$item = dataParse($item);
	
	// проверяем наличие названия
	// иначе пропускаем обработку и переходим к следующему
	
	if (empty($item[0])) {
		
		logging('composer - unknown library by \'' . $item[1] . '\' was skipped');
		continue;
		
	} elseif (empty($item[1]) || $item[1] === 'system') {
		
		// обработка системной библиотеки
		
		if (!funcLibraries_System($item[0])) {
			logging('composer - system library \'' . $item[0] . '\' was not copied');
		}
		
	} elseif (!file_exists(PATH_LIBRARIES . $item[1] . DS . $item[0])) {
		
		// обработка удаленной библиотеки
		// сначала проверяем наличие и скачиваем
		
		// пробуем через packagist
		
		$u = localRequestUrl('https://repo.packagist.org/p/' . $item[1] . '/' . $item[0] . '.json', null, 'get');
		$l = null;
		
		if (!empty($u)) {
			
			$u = json_decode($u, true);
			$vers = null;
			
			if (!empty($u['packages'][$item[1] . '/' . $item[0]])) {
				$vers = array_keys($u['packages'][$item[1] . '/' . $item[0]]);
			}
			
			//echo '[' . print_r($vers, 1) . ']<br>';
			
			if (objectIs($vers)) {
				
				$numarr = [];
				
				foreach ($vers as $k => $i) {
					
					$numver = substr($i, 0, 1) === 'v' ? substr($i, 1) : (is_numeric(substr($i, 0, 1)) ? $i : 0);
					$nummax = $item[2] . '.9999';
					
					if (
						empty($item[2]) && is_numeric(substr($numver, 0, 1)) ||
						!empty($item[2]) && version_compare($numver, $item[2], '>=') && version_compare($numver, $nummax, '<=')
					) {
						$numarr[$numver] = $vers[$k];
					}
					
				}
				
				$vers = $numarr;
				
				unset($k, $i, $numver, $nummax, $numarr);
				
				if (objectIs($vers)) {
					
					krsort($vers, SORT_NATURAL);
					$item[2] = reset($vers);
					
					$u = $u['packages'][$item[1] . '/' . $item[0]][$item[2]];
					//$u = $u['dist']['url'];
					$u = substr($u['source']['url'], 0, -4) . '/archive/' . $u['version'] . '.zip';
					
					$l = set($u, true);
					//echo '[' . print_r($u, 1) . ']<br>';
					
					$vers = array_keys($vers);
					$item[2] = reset($vers);
				}
				
			}
			
			unset($vers, $u);
			
		}
		
		if (empty($l)) {
			
			// пробуем напрямую через github
			
			$u = localRequestUrl('https://api.github.com/repos/' . $item[1] . '/' . $item[0] . '/releases', null, 'get');
			
			if (!empty($u)) {
				$u = json_decode($u, true);
				//echo '[' . print_r($u, 1) . ']<br>';
			}
			
			if (empty($u)) {
				$u = localRequestUrl('https://api.github.com/repos/' . $item[1] . '/' . $item[0] . '/tags', null, 'get');
				if (!empty($u)) {
					$u = json_decode($u, true);
					//echo '[' . print_r($u, 1) . ']<br>';
				}
			}
			
			if (!empty($u)) {
				
				$vers = null;
				
				//echo '[' . print_r($u, 1) . ']<br>';
				
				if (objectIs($u)) {
					
					foreach ($u as $k => $i) {
						
						$numver = substr($i['name'], 0, 1) === 'v' ? substr($i['name'], 1) : $i['name'];
						
						// иногда, оказывается, поле 'name' может содержать не номер версии, особенно когда читаются не релизы, а теги
						// и тогда нужна доп.проверка по 'tag_name'
						
						if (!version_compare('0', $numver, '>=')) {
							$name = $i['name'];
						} else {
							$name = $i['tag_name'];
							$numver = substr($i['tag_name'], 0, 1) === 'v' ? substr($i['tag_name'], 1) : $i['tag_name'];
						}
						
						//echo '==[' . $name . ' == ' . version_compare('0', $name, '>=') . ']==<br>';
						
						if (
							!is_numeric(substr($numver, 0, 1)) ||
							!empty($item[2]) && version_compare($numver, $item[2]) < 0
						) {
							unset($u[$k]);
						} else {
							//$vers[$numver] = $i['zipball_url'];
							//$vers[$numver] = str_ireplace('/releases/tag/', '/archive/', $i['html_url']) . '.zip';
							$vers[$numver] = 'https://github.com/' . $item[1] . '/' . $item[0] . '/archive/' . $name . '.zip';
							//echo '[' . print_r($i, 1) . ']<br>';
						}
						
					}
					unset($k, $i, $numver);
					
					if (objectIs($vers)) {
						arsort($vers, SORT_NATURAL);
						$k = array_keys($vers);
						$item[2] = array_shift($k);
						$l = array_shift($vers);
						unset($k);
					}
					
				}
				
				unset($vers, $u);
				
			}
			
		}
		
		unset($u);
		
		// закончили пробовать
		
		if (empty($l)) {
			
			logging('composer - library \'' . $item[0] . '\' by \'' . $item[1] . '\' not found links in repository');
			//continue;
			
		} else {
			
			if (!file_exists(PATH_LIBRARIES . $item[1])) { mkdir(PATH_LIBRARIES . $item[1]); }
			
			$file = PATH_LIBRARIES . $item[1] . DS . $item[1] . '-' . $item[0] . '.zip';
			$folder = PATH_LIBRARIES . $item[1] . DS . $item[0] . DS;
			$temp = PATH_LIBRARIES . $item[1] . DS . 'temp' . DS;
			
			// скачиваем
			// но только в том случае, если в папке родителя нет скачанного файла
			
			if (!file_exists($file)) {
				localSaveFromUrl($file, $l);
			}
			
			// распаковываем
			// но только в том случае, если нет папки
			
			if (!file_exists($folder)) {
				
				if (file_exists($temp) && is_dir($temp)) { funcLibraries_Delete($temp); }
				if (file_exists($folder) && is_dir($folder)) { funcLibraries_Delete($folder); }
				
				mkdir($temp, 0777);
				//mkdir($folder, 0777);
				
				if (file_exists($file)) {
					localUnzip($file, $temp, false);
				} else {
					logging('composer - library \'' . $item[0] . '\' by \'' . $item[1] . '\' temporary file not found');
				}
				
				$target = localList($temp, ['return' => 'folders']);
				
				if (objectIs($target)) {
					$target = array_shift($target);
					if (rename($temp . $target, $folder)) {
						funcLibraries_Delete($temp);
						unlink($file);
					} else {
						logging('composer - library \'' . $item[0] . '\' by \'' . $item[1] . '\' install not completed');
					}
				} else {
					logging('composer - library \'' . $item[0] . '\' by \'' . $item[1] . '\' temporary folder not found');
				}
				
			}
			
			//echo '[' . print_r($a, 1) . ']<br>';
			//echo '[' . print_r($l, 1) . ']<br>';
			//echo '[' . print_r($item, 1) . ']<hr>';
			
			unset($file, $folder, $temp);
			
		}
		
		unset($u, $l);
		
	}
	
	// теперь проверяем наличие в базе данных и формируем новую запись
	
	$exist = null;
	
	if (objectIs($libraries -> db)) {
		
		foreach ($libraries -> db as $i) {
			
			// если имя содержит спец.знак, то он преобразуется в точку
			if (strpos($i['name'], '--') !== false) { $i['name'] = str_replace('--', '.', $i['name']); }
			
			if ($i['name'] === $item[0] && $i['type'] === $item[1]) {
				$exist = true;
			}
			
		}
		unset($i);
		
	}
	
	//echo '[' . print_r($item, 1) . ']<br>';
	//echo '[' . $exist . ']<br>';
	
	if ($exist) {
		
		unset($libraries -> empty[$key]);
		
	} else {
		
		if ($item[1] === 'system') {
			
			$ini = PATH_CORE . 'install' . DS . 'database' . DS . 'libraries' . DS . $item[0] . '.system.ini';
			$data = null;
			
			if (file_exists($ini)) {
				$data = localFile($ini);
				if ($data) {
					
					// если имя содержит точку, то она преобразуется в спец.знак
					if (strpos($item[0], '.') !== false) { $item[0] = str_replace('.', '--', $item[0]); }
					
					$libraries -> update[] = [
						'name' => $item[0],
						'type' => 'system',
						'data' => $data
					];
					
					unset($libraries -> empty[$key]);
					
				} else {
					logging('composer - system library \'' . $item[0] . '\' was not added to database');
				}
			}
			
			unset($ini, $data);
			
		} else {
			
			$folder = PATH_LIBRARIES . $item[1] . DS . $item[0] . DS;
			
			$data = [];
			
			foreach (['package', 'bower'] as $i) {
				
				//echo '[' . $folder . $i . '.json' . ']<br>';
				
				$datalist = json_decode(localOpenFile($folder . $i . '.json'), true);
				
				foreach (['version', 'authors', 'license', 'description', 'homepage'] as $k) {
					if (empty($data[$k]) && !empty($datalist[$k])) {
						$data[$k] = $datalist[$k];
					}
				}
				
				// разбор данных из 'package'
				if ($i === 'package') {
					if (!empty($datalist['main'])) {
						$data['data'][] = $datalist['main'];
					}
					if (!empty($datalist['style'])) {
						$data['data'][] = $datalist['style'];
					}
				}
				
				// разбор данных из 'bower'
				if ($i === 'bower' && !empty($datalist['main'])) {
					if (is_array($datalist['main'])) {
						$data['data'] = array_merge($data['data'], $datalist['main']);
					} else {
						$data['data'][] = $datalist['main'];
					}
				}
				
			}
			
			if (objectIs($data['data'])) {
				foreach ($data['data'] as &$i) {
					if (!empty($i) && is_string($i)) {
						for ($si = 0, $sl = strlen($i); $si < $sl; $si++) {
							if (strpos($i, '.') === 0 || strpos($i, '/') === 0) {
								$i = substr($i, 1);
							}
						}
						if (stripos($i, '.min.') !== false) {
							$i = str_ireplace('.min.', '.', $i);
						}
						unset($si, $sl);
					}
				}
				unset($i);
				$data['data'] = array_unique($data['data']);
			}
			
			//echo '[' . print_r($data['data'], 1) . ']<br>';
			
			if (objectIs($data['data'])) {
				
				foreach ($data['data'] as &$i) {
					if (strrpos($i, '.') !== false) {
						$type = substr($i, strrpos($i, '.') + 1);
					} else {
						
						$pd = substr($i, 0, strrpos($i, '/'));
						$pf = substr($i, strrpos($i, '/') + 1);
						
						$t = localList(PATH_LIBRARIES . $item[1] . DS . $item[0] . DS . $pd, ['return' => 'files']);
						
						if (objectIs($t)) {
							foreach (['css', 'js', 'scss', 'less'] as $si) {
								if (in_array($pf . '.' . $si, $t)) {
									$data['data'][] = $i . '.' . $si;
								}
							}
						}
						unset($pf, $pd, $si, $t);
						continue;
						
					}
					
					$version = !empty($data['version']) ? $data['version'] : $item[2];
					//$i = str_replace('/', '\/', $i);
					$data['path'][$version]['local'][$type][] = $i;
					$i = str_replace($version, '{version}', $i);
					$data['path']['default']['local'][$type][] = $i;
					
					unset($type, $version);
				}
				unset($i);
				
			} else {
				
				$data = null;
				$ini = PATH_CUSTOM . 'libraries' . DS . $item[0] . '.' . $item[1] . '.ini';
				
				if (!file_exists($ini)) {
					logging('library \'' . $item[0] . '\' by \'' . $item[1] . '\' not found json files in installed folder and was be tried take defaults from system install folder', 'composer - library \'' . $item[0] . '\' by \'' . $item[1] . '\' not found json');
					$ini = PATH_CORE . 'install' . DS . 'database' . DS . 'libraries' . DS . $item[0] . '.' . $item[1] . '.ini';
				}
				
				if (file_exists($ini)) {
					$data = localFile($ini);
					if ($data) {
						
						// если имя содержит точку, то она преобразуется в спец.знак
						if (strpos($item[0], '.') !== false) { $item[0] = str_replace('.', '--', $item[0]); }
						
						$libraries -> update[] = [
							'name' => $item[0],
							'type' => $item[1],
							'data' => $data
						];
						
						unset($libraries -> empty[$key]);
						
					}
				}
				
				unset($ini, $data);
			}
			unset($data['data']);
			
			//echo '[' . print_r($data, 1) . ']<hr>';
			
			unset($i, $k, $datalist);
			
		}
		
		// подготавливаем данные для записи
		
		if (!empty($data['path'])) {
			
			unset($libraries -> empty[$key]);
			
			// если имя содержит точку, то она преобразуется в спец.знак
			if (strpos($item[0], '.') !== false) { $item[0] = str_replace('.', '--', $item[0]); }
			
			$libraries -> update[] = [
				'name' => $item[0],
				'type' => $item[1],
				'data' => json_encode($data['path'])
			];
			
		}
		
	}
	
}

//echo '<br><br><pre>' . print_r($libraries, 1) . '</pre>';

if (set($libraries -> update)) {
	
	//production
	//reload(
	//	'/' . DEFAULT_PROCESSOR . '/composer/update/?hash=' . crypting(time()) . '&data=' . base64_encode(json_encode($libraries -> update)),
	//	null,
	//	['Content-Type' => 'text/html; charset=UTF-8']
	//);
	//header('Content-Type: text/html; charset=UTF-8');
	//header('Location: /' . DEFAULT_PROCESSOR . '/composer/update/?hash=' . crypting(time()) . '&data=' . base64_encode(json_encode($libraries -> update)));
	
	//curl
	$f = objectProcess('system:composer');
	$f['array']['data'] = base64_encode(json_encode($libraries -> update));
	//$l = $f['link'] . $f['string'] . '&data=' . base64_encode(json_encode($libraries -> update));
	//$t = localRequestUrl($l, null, 'post');
	$t = localRequestUrl($f['link'], $f['array'], 'post');
	//echo '[' . print_r($f, 1) . ']<br>';
	//echo '[' . print_r($t, 1) . ']<br>';
	unset($f, $t);
	
}

if (set($libraries -> empty)) {
	logging(print_r($libraries -> empty, 1), 'one or some libraries have not been installed');
	echo '[one or some libraries have not been installed -- see more in logs]<br>';
} else {
	echo '[all libraries installed]<br>All ok, but you can see more in logs.<br>Refresh this page or <a href="/">open site</a> now!';
}

exit;

?>