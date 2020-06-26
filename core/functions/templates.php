<?php defined('isENGINE') or die;

/* ФУНКЦИЯ ЗАГРУЗКИ СТРАНИЦЫ ШАБЛОНА */

function page($name, $target = false, $once = true){
	
	global $template;
	global $lang;

	if (is_string($name) && strpos($name, '..') !== false) {
		return false;
	}
	
	if (is_string($name) && strpos($name, ':') !== false) {
		$name = str_replace(':', DS, $name);
	}
	
	if (!$target || $target === 'inner') {
		$name = $name === true ? substr($template -> path -> page, 0, -4) : $template -> path -> init . 'inner' . DS . $name;
	} elseif ($target === 'head') {
		$name = $template -> path -> init . 'head' . DS . $name;
	} elseif ($target === 'wrapper') {
		$name = $template -> path -> init . 'wrapper' . DS . $name;
	} elseif ($target === 'html') {
		$name = $template -> path -> init . 'html' . DS . $name;
	} elseif ($target === 'template') {
		$name = PATH_TEMPLATES . $template -> name . DS . 'html' . DS . $name;
	} elseif ($target === 'item') {
		$name = file_exists(PATH_CUSTOM . 'items' . DS . $name . '.php') ? PATH_CUSTOM . 'items' . DS . $name : PATH_CORE . 'templates' . DS . 'items' . DS . $name;
	} else {
		return false;
	}
	
	if (!empty($lang) && file_exists($name . '.' . $lang -> lang . '.php')) {
		$name .= '.' . $lang -> lang;
	}
	
	$name .= '.php';
	//echo $name;
	
	if (is_array($once)) {
		return $name;
	}
	
	if (!file_exists($name)) {
		return false;
	}
	
	if ($once) {
		require_once $name;
	} else {
		require $name;
	}
	
	return true;
	
}

/* ФУНКЦИЯ ПРОВЕРКИ СТРАНИЦЫ */

function thispage($target, $second = false){
	
	global $template;
	
	$page = $template -> page['name'];
	
	if ($target === 'home') {
		
		if ($template -> page['type'] === 'home') {
			return true;
		} else {
			return false;
		}
		
	} elseif ($target === 'type') {
		
		if (!$second) {
			return $template -> page['type'];
		} elseif ($second === $template -> page['type']) {
			return true;
		} else {
			return false;
		}
		
	} elseif ($target === 'is') {
		
		if (!$second) {
			return $page;
		} elseif ($second === $page) {
			return true;
		} else {
			return false;
		}
		
	} elseif ($target === 'in') {
		
		if (!$second) {
			return $template -> page['parents'];
		} elseif (
			$second === 'home' &&
			empty($template -> page['parents'])
		) {
			return true;
		} elseif (
			is_string($second) &&
			in_array($second, $template -> page['parents'])
		) {
			return true;
		} elseif (
			is_array($second) &&
			$second === $template -> page['parents']
		) {
			return true;
		} else {
			return false;
		}
		
	} elseif ($target === 'parents') {
		
		if (!$second) {
			return $template -> page['parents'];
		} elseif (in_array($second, $template -> page['parents'])) {
			return true;
		} else {
			return false;
		}
		
	} elseif ($target === 'parameters') {
		
		if (!$second) {
			return $template -> page['parameters'];
		} elseif (in_array($second, $template -> page['parameters'])) {
			return true;
		} else {
			return false;
		}
		
	} elseif ($target === 'special') {
		
		if (
			!$second &&
			objectIs($template -> settings -> special) &&
			in_array($page, $template -> settings -> special)
		) {
			return true;
		} elseif (
			$second &&
			objectIs($template -> settings -> special) &&
			objectIs($template -> settings -> special[$second]) &&
			in_array($page, $template -> settings -> special[$second])
		) {
			return true;
		} else {
			return false;
		}
		
	} elseif ($target === 'inspecial') {
		
		if (objectIs($template -> settings -> special)) {
			
			foreach ($template -> settings -> special as $key => $item) {
				if (
					objectIs($item) &&
					in_array($page, $item)
				) {
					return $key;
				}
			}
			return false;
			
		} else {
			return false;
		}
		
	} else {
		
		return false;
		
	}
	
}

/* ФУНКЦИЯ ЗАГРУЗКИ ЭЛЕМЕНТА ЯЗЫКОВОГО ШАБЛОНА */

function lang($name, $target = false){
	
	global $lang;
	
	// если языки выключены, т.е. $lang пустой, то всегда отдает false, независимо от запроса
	// внимание! не путайте пустой $lang и пустой $lang -> data, ведь последний содержит только языковой массив
	// 
	// false (по-умолчанию) - просто возвращает запрошенный языковой элемент, если он есть
	// true - выводит запрошенный языковой элемент, если он есть
	// return - в случае неудачи возвращает запрошенное значение как есть, либо последний запрошенный элемент массива
	// is - проверяет наличие запрошенного языкового элемента и отдает true/false
	
	if (
		empty($lang) ||
		!objectIs($lang -> data) ||
		!DEFAULT_LANG
	) {
		return false;
	}
	
	if (strpos($name, ':') !== false) {
		
		$name = dataParse($name);
		$result = $lang -> data[array_shift($name)];
		
		if (empty($result)) {
			return false;
		} else {
			foreach ($name as $item) {
				$result = $result[$item];
			}
			if (is_array($result)) {
				$result = reset($result);
			}
		}
		
	} else {
		$result = $lang -> data[$name];
	}
	
	if (!$target) {
		//return $result;
		return set($result, true);
	} elseif ($target === true) {
		//echo $result;
		echo set($result, true);
	} elseif ($target === 'return') {
		if (empty($result)) {
			if (is_array($name)) {
				$result = end($name);
			} else {
				$result = $name;
			}
		}
		return $result;
	} elseif ($target === 'is') {
		return set($result);
	}
	
}

/* ФУНКЦИЯ ПРОВЕРКИ ЯЗЫКА */

function thislang($target, $second = false){
	
	global $lang;
	
	// если языки выключены, т.е. $lang пустой, то всегда отдает false, независимо от запроса
	// внимание! не путайте пустой $lang и пустой $lang -> data, ведь последний содержит только языковой массив
	// 
	// default
	// сравнивает с языком по-умолчанию
	// 
	// is - пока не реализован
	// возможная работа:
	// если указан второй параметр - язык для сравнения, - он сравнивает и отдает true/false
	
	if (empty($lang) || !DEFAULT_LANG) {
		return false;
	}
	
	if ($target === 'default') {
		if ($lang -> lang === DEFAULT_LANG) {
			return true;
		} else {
			return false;
		}
	} elseif ($target === 'lang') {
		return !empty($lang -> lang) ? $lang -> lang : null;
	} elseif ($target === 'code') {
		return !empty($lang -> code) ? $lang -> code : null;
	}
	
}

/* ФУНКЦИЯ РАБОТЫ С ПЕРЕМЕННЫМИ ШАБЛОНА */

function variable($target, $second = false, $action = false){
	
	global $template;
	
	if (!empty($action)) {
		if ($action === 'unset') {
			unset($template -> var[$target]);
		} elseif ($action === 'verify') {
			if ($template -> var[$target] === $second) {
				return true;
			} else {
				return false;
			}
		}
	} elseif (set($second)) {
		$template -> var[$target] = $second;
	}
	
	return $template -> var[$target];
	
}
	
/* ФУНКЦИЯ ПРОВЕРКИ ОПЦИИ ШАБЛОНА */

function in($target, $second = false){
	
	// эта функция - обертка или, если угодно, прослойка между шаблоном и ядром
	
	// первый параметр задает раздел
	// в данном случае формулировка частная, разделом могут быть:
	// options - опции шаблона
	// libraries - библиотеки
	// *parameters - параметры строки вызова, может быть также параметры шаблона или контента
	// template - параметры шаблона
	
	// если задан второй параметр, то проверяется наличие $target в разделе $second
	// если второй параметр не задан, то возвращается содержимое раздела $second
	
	
	if ($target === 'options') {
		
		global $template;
		
		if (!$second) {
			return $template -> settings -> options;
		} elseif (objectIs($template -> settings -> options) && in_array($second, $template -> settings -> options)) {
			return true;
		} else {
			return false;
		}
		
	} elseif ($target === 'libraries') {
		
		global $libraries;
		
		if (!$second) {
			return $libraries;
		} elseif (objectIs($libraries)) {
			
			if (strpos($second, ':') === false) {
				
				foreach ($libraries as $key => $item) {
					if (strpos($key, $second . ':') === 0) {
						return true;
					}
				}
				
			} elseif (array_key_exists($second, $libraries)) {
				return true;
			}
			
			return false;
			
		} else {
			return false;
		}
		
	}
	
}

/* ФУНКЦИЯ ВЫЗОВА МОДУЛЯ */

function module($arr, $special = null){
	
	global $template;
	
	// разбираем массив настроек
	
	if (!is_array($arr)) { $arr = [$arr]; }
	
	if (!empty($arr[0]) && $arr[0] === NAME_CORE) {
		logging('module warning - attempt to use system core as module');
		return null;
	} elseif (strpos($arr[0], ':') !== false) {
		$arr[0] = dataParse($arr[0]);
		$arr[4] = PATH_MODULES . $arr[0][1] . DS;
		$arr[0] = $arr[0][0];
	} else {
		$arr[4] = PATH_MODULES . 'isengine' . DS;
	}
	
	if (empty($arr[1])) { $arr[1] = 'default'; }
	
	if (empty($arr[2])) {
		if (
			$arr[1] !== 'default' && (
				file_exists($arr[4] . $arr[0] . DS . 'templates' . DS . $arr[1] . '.php') ||
				file_exists(PATH_CUSTOM . 'modules' . DS . $arr[0] . DS . $arr[1] . '.php')
			)
		) {
			$arr[2] = $arr[1];
		} else {
			$arr[2] = 'default';
		}
	}
	
	$custom = !empty($arr[3]) ? iniPrepareJson($arr[3], true) : null;
	
	// создаем объект модуля
	
	$module = (object) [
		'name' => $arr[0],
		'param' => $arr[1],
		'template' => $arr[2],
		'cpath' => file_exists(PATH_CUSTOM . 'modules' . DS . $arr[0] . DS) && is_dir(PATH_CUSTOM . 'modules' . DS . $arr[0] . DS) ? PATH_CUSTOM . 'modules' . DS . $arr[0] . DS : null,
		'path' => $arr[4] . $arr[0] . DS,
		'elements' => $arr[4] . $arr[0] . DS . 'elements' . DS,
		'process' => $arr[4] . $arr[0] . DS . 'process' . DS,
		'this' => null,
		'return' => null,
		'settings' => null,
		'from' => null,
		'tpath' => null,
		'data' => null,
		'var' => []
	];
	
	//print_r($module);
	
	unset($arr);
	
	//echo '<div class="hiddeninfomodule" style="display: none">' . print_r($module, true) . '</div>';
	
	// назначаем специальное значение
	
	if (
		is_string($special) ||
		is_numeric($special)
	) {
		$module -> this = clear($special, 'format');
	} elseif (
		!is_object($special) &&
		!is_array($special) &&
		!is_bool($special)
	) {
		$module -> this = null;
	} else {
		$module -> this = $special;
	}
	
	// создаем служебную информацию для инспектора
	
	if (in('options', 'inspect')) {
		global $loadingLog;
		$loadingLog .= 'module ' . $module -> name . ' as ' . $module -> param . ' with ' . $module -> template . ' template ';
	}
	
	// читаем манифест модуля
	
	$manifest = localFile($module -> path . 'manifest.ini');
	
	if (!empty($manifest)) {
		
		$manifest = iniPrepareJson($manifest);
		
		if (set($manifest -> libraries)) {
			foreach ($manifest -> libraries as $item) {
				if (!in('libraries', $item)) {
					logging('module \'' . $module -> name . '\' was not opening - not find needed library \'' . str_replace(':', '\' by \'', $item) . '\'');
					unset($manifest);
					break;
				}
			}
			unset($item);
		}
	}
	
	if (empty($manifest)) {
		logging('module \'' . $module -> name . '\' was not opening - not find manifest');
		return false;
	}
	
	// готовим настройки
	
	$settings = dbUse('modules:' . $module -> param . ($module -> param !== 'default' ? ':default' : null), 'select', ['allow' => 'parent:' . $module -> name, 'return' => 'name:data']);
	
	if (objectIs($settings)) {
		$keys = array_keys($settings);
		if (in_array($module -> param, $keys)) {
			$settings = $settings[$module -> param];
		} else {
			$settings = $settings['default'];
		}
		unset($keys);
	}
	
	//print_r($settings);
	
	if (empty($settings)) {
		$module -> from = 'module';
		$settings = localFile($module -> path . 'data' . DS . $module -> param . '.ini');
		if (empty($settings)) {
			$settings = localFile($module -> path . 'data' . DS . 'default.ini');
		}
		if (!empty($settings)) {
			$settings = iniPrepareJson($settings, true);
		}
	} else {
		$module -> from = 'db';
	}
	
	// готовим пути
	
	$module -> tpath = PATH_CUSTOM . 'modules' . DS . $module -> name . DS . $module -> template . '.php';
	if (!file_exists($module -> tpath)) {
		$module -> from .= ':module';
		$module -> tpath = $module -> path . 'templates' . DS . $module -> template . '.php';
	} else {
		$module -> from .= ':custom';
	}
	
	// выполняем проверку настроек и путей
	
	if (isset($loadingLog)) { $loadingLog .= 'from ' . $module -> from . ' '; }
	
	if (empty($settings) || !file_exists($module -> tpath)) {
		if (isset($loadingLog)) { $loadingLog .= 'was not opening\n'; }
		logging('module \'' . $module -> name . '\' error data -- ' . print_r($module, true)/*json_encode($module, JSON_UNESCAPED_UNICODE)*/, 'module \'' . $module -> name . '\' was not opening - not find ' . (empty($settings) ? 'settings' : 'template'));
		unset($module, $settings);
		return false;
	}
	
	// расширяем настройки дополнительными значениями
	
	if (objectIs($custom)) {
		$settings = objectMerge($settings, $custom, 'replace');
	}
	
	// добавляем настройки в объект модуля
	
	objectLang($settings);
	$module -> settings = $settings;
	unset($settings, $custom);
	
	//echo '<br><hr><pre>' . print_r($module -> settings, true) . '</pre><hr><br>';
	
	// подготавливаем классы
	
	$module -> settings['classes'] = funcModuleMergeClasses($module -> settings['classes'], $module -> settings['js']);
	$module -> settings['js'] = funcModuleMergeJs($module -> settings['classes'], $module -> settings['js']);
	
	// создаем переменные-ссылки
	
	$sets = &$module -> settings;
	$elements = &$module -> settings['elements'];
	$labels = &$module -> settings['labels'];
	$options = &$module -> settings['options'];
	$var = &$module -> var;
	$script = &$template -> script;
	
	$js = $module -> settings['js'];
	$classes = $module -> settings['classes'];
	//$data = &$module -> data;
	$data = $module -> data;
	
	// загружаем пути
	
	require $module -> path . 'init.php';

	// загружаем шаблон
	
	if (!empty($module -> tpath)) {
		require $module -> tpath;
	}
	
	// загружаем скрипты
	
	if (file_exists($module -> cpath . 'scripts' . $module -> param . '.php')) {
		require $module -> cpath . 'scripts' . $module -> param . '.php';
	}
	
	// завершаем работу модуля
	
	if (isset($loadingLog)) { $loadingLog .= 'was opening complete\n'; }
	
	$return = set($module -> return, true);
	
	unset(
		$module,
		$sets,
		$classes,
		$elements,
		$labels,
		$options,
		$script
	);
	
	return $return;
	
}

function funcModuleMergeClasses($arrTarget = null, $arrMerged = null) {
	
	if (objectIs($arrTarget) && objectIs($arrMerged)) {
		
		foreach ($arrMerged as $k => $i) {
			if (objectIs($i)) {
				$arrTarget[$k] = funcModuleMergeClasses($arrTarget[$k], $i);
			} elseif (!empty($i)) {
				$arrTarget[$k] .= ' ' . $i;
			}
		}
		unset($k, $i);
		
	}
	
	return $arrTarget;
	
}

function funcModuleMergeJs($arrTarget = null, $arrMerged = null) {
	
	if (objectIs($arrTarget)) {
		
		foreach ($arrTarget as $k => $i) {
			if (objectIs($i)) {
				$arrMerged[$k] = funcModuleMergeJs($arrMerged[$k], $i);
			} elseif (empty($arrMerged[$k])) {
				$ii = datasplit($i, ' ');
				$arrMerged[$k] = objectIs($ii) ? array_shift($ii) : (!empty($ii) ? $ii : null);
				unset($ii);
			}
		}
		unset($k, $i);
		
	}
	
	return $arrMerged;
	
}

?>