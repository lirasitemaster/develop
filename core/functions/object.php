<?php defined('isENGINE') or die;

/*
=					< objectClear		< ... - Функция очищает массив от пустых элементов
=					< objectLang		< datareplacelang - Функция выбора языковых вариантов в массиве
objectAssociate		< objectKeys		< datakeys - Функция которая проверяет, ассоциативный массив или индексный
objectExtractLevel	< objectExtract		< dataextract - Функция которая производит извлечение данных в многомерных массивах или объектах
=					< objectMerge		< datamerge - Функция которая производит объединение данных в многомерных массивах или объектах
=					< objectArray		< dataarray - Функция, которая проверяет входящее значение, массив это или объект
objectExtract		< objectObject		< dataobject - Функция, которая извлекает значение из объекта, воспринимая его как массив
=					< objectConvert		< dataconvert - Функция, которая преобразует массив в объект и объект в массив
=					< objectToString	< dataarraytostring - Функция, которая преобразует объект или массив в строку
*/

function objectGet($object, $parameter = false, $merge = null){
	
	// Универсальная функция запроса данных системного объекта
	
	if (!$parameter) {
		return $object;
	}
	
	$result = null;
	$mergetype = null;
	
	if ($object === 'content') {
		
		global $content;
		
		if (empty($content)) {
			return null;
		}
		
		if ($parameter === 'name') {
			$result = $content -> name;
		} elseif ($parameter === 'parent') {
			$result = $content -> parent;
			// $result = end(reset($content -> data)['parent']); // старое условие, нигде не используется
		} elseif ($parameter === 'first') {
			$result = reset($content -> data);
		} elseif ($parameter === 'type') {
			$result = $content -> type;
		} elseif ($parameter === 'filtration') {
			$result = $content -> filtration;
		} elseif ($parameter === 'page') {
			$result = $content -> page;
		}
		
	} elseif ($object === 'template') {
		
		global $template;
		
		if (empty($template)) {
			return null;
		}
		
		if ($parameter === 'name') {
			$result = $template -> name;
		} elseif ($parameter === 'section') {
			$result = !empty($template -> section) ? $template -> section : null;
		} elseif ($parameter === 'device') {
			$result = !empty($template -> device -> type) ? $template -> device -> type : null;
		} elseif ($parameter === 'os') {
			$result = !empty($template -> device -> os) ? $template -> device -> os : null;
		}
		
	} elseif ($object === 'structure') {
		
		global $structure;
		
		if (empty($structure)) {
			return null;
		}
		
		if ($parameter === 'structure') {
			$result = $structure;
		}
		
	} elseif ($object === 'user') {
		
		global $user;
		
		if (empty($user)) {
			return null;
		}
		
		if (strpos($parameter, 'authorised') !== false) {
			
			if (
				!empty($user -> sid) &&
				defined('isALLOW') &&
				isALLOW
			) {
				$result = true;
			} else {
				$result = false;
			}
			
		}
		
	} elseif ($object === 'uri') {
		
		global $uri;
		
		if (empty($uri)) {
			return null;
		}
		
		if ($parameter === 'site') {
			$result = $uri -> site;
		} elseif ($parameter === 'url') {
			$result = $uri -> url;
		} elseif ($parameter === 'host') {
			$result = $uri -> host;
		}
		
	} elseif ($object === 'lang') {
		
		global $lang;
		
		if (empty($lang)) {
			return null;
		}
		
		if (strpos($parameter, 'data') !== false) {
			
			if (
				!objectIs($lang -> data) ||
				!DEFAULT_LANG
			) {
				return false;
			}
			
			$name = dataParse($parameter);
			array_shift($name);
			$result = $lang -> data;
			
			if (objectIs($result) && objectIs($name)) {
				foreach ($name as $item) {
					$result = $result[$item];
				}
				unset($item);
			}
			
			unset($name);
			
		} elseif ($parameter === 'section') {
			$result = !empty($template -> section) ? $template -> section : null;
		} elseif ($parameter === 'device') {
			$result = !empty($template -> device -> type) ? $template -> device -> type : null;
		} elseif ($parameter === 'os') {
			$result = !empty($template -> device -> os) ? $template -> device -> os : null;
		}
		
	}
	
	if (!empty($merge)) {
		if (empty($mergetype)) {
			if ($result === $merge) {
				$result = true;
			} else {
				$result = false;
			}
		} elseif ($mergetype === 'inarray') {
			if (
				objectIs($result) &&
				in_array($merge, $result)
			) {
				$result = true;
			} else {
				$result = false;
			}
		} elseif ($mergetype === 'inkeys') {
			if (
				objectIs($result) &&
				array_key_exists($merge, $result)
			) {
				$result = true;
			} else {
				$result = false;
			}
		}
	}
	
	return $result;
	
}

function objectIs($arr = null) {
	
	/*
	*  Функция проверяет, является ли заданная переменная системным объектом
	*  На данный момент требования просты: !empty() && is_array()
	*/
	
	//if (is_array($arr) && set($arr)) {
	//if (!empty($arr) && (is_array($arr) || is_object($arr))) {
	if (!empty($arr) && is_array($arr)) {
		return true;
	} else {
		return false;
	}
	
}

function objectFill($arr = [], $parameters = []) {
	
	/*
	*  Функция проверяет существование параметров в массиве
	*  и если параметр не существует, она задает его пустым
	*  
	*  В результате возвращает готовый массив
	*/
	
	if (empty($parameters)) {
		return $arr;
	}
	
	foreach ($parameters as $i) {
		if (!isset($arr[$i])) {
			$arr[$i] = null;
		}
	}
	unset($i);
	
	return $arr;
	
}

function objectClear($arr, $keys = false, $unique = false) {
	
	/*
	*  Функция очищает массив от пустых элементов
	*/
	
	$arr = array_diff($arr, [null]);
	
	if ($unique) {
		$arr = array_unique($arr);
	}
	
	if ($keys) {
		$arr = array_values($arr);
	}
	
	return $arr;
}

function objectLang(&$arr) {
	
	/*
	*  Функция выбора языковых вариантов в массиве
	*  на входе нужно указать массив $arr
	*  
	*  функция примет массив и произведет поиск и замену языковых вариантов
	*  на единственный вариант, соответствующий текущему установленному языку
	*  по языковому коду
	*  
	*  например, массив { 'answer' : { 'ru' : 'привет', 'en' : 'hello' } } примет вид:
	*    если на сайте установлен английский язык { 'answer' : 'hello' }
	*    если на сайте установлен русский язык { 'answer' : 'привет' }
	*  
	*  на выходе ничего не отдает, т.к. работает напрямую с указанным массивом
	*/
	
	global $lang;
	
	if (empty($lang) || !is_array($arr) && !is_object($arr)) {
		return null;
	}
	
	$l = $lang -> lang;
	
	if (is_array($arr) && isset($arr[$l])) {
		$arr = $arr[$l];
	} elseif (is_object($arr) && isset($arr -> $l)) {
		$arr = $arr -> $l;
	}
	
	if (is_array($arr) || is_object($arr)) {
		foreach ($arr as &$item) {
			objectLang($item);
		}
		unset($item);
	}
	
	unset($arr, $l);
	
	/*
	global $lang;
	
	if (empty($lang) || !objectIs($arr)) {
		return null;
	}
	
	$l = $lang -> lang;
	
	foreach ($arr as &$item) {
		
		if (is_array($item) && isset($item[$l])) {
			$item = $item[$l];
		} elseif (is_object($item) && isset($item -> $l)) {
			$item = $item -> $l;
		}
		
		if (is_array($item) || is_object($item)) {
			objectLang($item);
		}
		
		unset($arr, $item);
		
	}
	
	unset($l);
	*/
	
}

function objectKeys($arrTarget) {

	/*
	*  Функция которая проверяет, ассоциативный массив или индексный
	*  на входе нужно указать проверяемый массив
	*  
	*  проверка идет по значениям ключей массива, и если все они числовые,
	*  то считается, что массив неассоциативный
	*  однако, массив вида [ 0 => 'a', 1 => 'b', 'key' => 'c' ]
	*  будет считаться уже ассоциативным
	*  
	*  на выходе отдает:
	*  true, если массив ассоциативный
	*  false, если массив индексный, т.е. неассоциативный
	*/
	
	if (count(array_filter(array_keys($arrTarget), 'is_string')) > 0) {
		return true;
	} else {
		return false;
	}
	
}

function objectExtract($arrTarget, $arrExtract, $convert = false) {

	/*
	*  Функция которая производит извлечение данных в многомерных массивах или объектах
	*  на входе нужно указать:
	*    целевой массив или объект, ИЗ котороГО будем извлекать данные - $arrTarget
	*    и массив или объект, согласно котороМУ будем извлекать эти данные - $arrExtract
	*  
	*  Третий аргумент может принимать значение true
	*  и тогда результирующий массив будет преобразован в объект и наоборот
	*  
	*  Если вы хотите извлечь значение из многомерного массива, использовать так:
	*  $arr = objectExtract($arr, ['field', 'field', 'field']);
	*  Например, если $arrTarget = ['a' => ['b' => ['c' => 1, 'd' => 2]]]
	*  и вам надо извлечь d, то используйте такой вызов:
	*  $arr = objectExtract($arrTarget, ['a', 'b', 'd']);
	*  
	*  на выходе отдает готовый массив $arrTarget
	*/
	
	foreach($arrExtract as $i) {
		if (array_key_exists($i, $arrTarget)) {
			if (is_array($arrTarget)) {
				$arrTarget = $arrTarget[$i];
			} elseif (is_object($arrTarget)) {
				$arrTarget = $arrTarget -> $i;
			} 
		} else {
			break;
		}
	}
	
	if (
		$convert &&
		(
			is_array($arrTarget) ||
			is_object($arrTarget)
		)
	) {
		$arrTarget = objectConvert($arrTarget);
	}
	
	return $arrTarget;
	
}

function objectMergeLevel($arrTarget, $arrFill, $value = null) {

	/*
	*  Функция которая производит объединение данных в многомерных массивах или объектах
	*  на входе нужно указать:
	*    целевой массив или объект, которЫЙ будем заполнять - $arrTarget
	*    и массив или объект, который содержит ключи, которЫМИ будем заполнять arrTarget - $arrFill
	*    третий, необязательный, аргумент - это значение
	*  
	*  Например, если указать:
	*  objectMergeLevel(['data' => null], ['a', 'b', 'c'], 'value')
	*  то на выходе получим такой массив:
	*  [ 'data' => ['a' => ['b' => ['c' => 'value']]] ];
	*  
	*  при этом, особенность данной функции в том, что она дополняет массив и не стирает другие имеющиеся в нем поля
	*/
	
	if (!is_array($arrTarget) || !is_array($arrFill)) {
		return null;
	}
	
	$arrFill = array_reverse($arrFill);
	$c = count($arrFill);
	$item = $value;
	
	if (!empty($c) && is_int($c)) {
		for ($i = 0; $i < $c; $i++) {
			$item = [array_shift($arrFill) => $item];
		}
	}
	
	unset($arrFill, $c, $i, $value);	
	
	return array_merge_recursive($arrTarget, $item);
	
}

function objectMerge($arrTarget, $arrFill, $convert = false) {

	/*
	*  Функция которая производит объединение данных в многомерных массивах или объектах
	*  на входе нужно указать:
	*    целевой массив или объект, которЫЙ будем заполнять - $arrTarget
	*    и массив или объект, которЫМ будем заполнять arrTarget - $arrFill
	*  
	*  Третий аргумент может принимать значения
	*    array - для принудительной конвертации в массив
	*    object - для принудительной конвертации в объект
	*    replace - для принудительной замены значений при их совпадении
	*  
	*  чтобы просто добавить значение в объект или массив, использовать так:
	*  $obj = objectMerge($obj, (object) ['field' => 'value']);
	*  $arr = objectMerge($arr, ['field' => 'value']);
	*  
	*  на выходе отдает готовый массив $arrTarget
	*/
	
	if (empty($arrTarget)) {
		return !empty($arrFill) && is_object($arrFill) || is_array($arrFill) ? $arrFill : null;
	} elseif (
		!is_object($arrTarget) &&
		!is_array($arrTarget)
	) {
		return $arrTarget;
	} elseif (
		!is_object($arrFill) &&
		!is_array($arrFill) &&
		empty($arrFill)
	) {
		return $arrFill;
	}
	
	foreach ($arrFill as $k => $i) {
		
		if (is_array($i) || is_object($i)) {
			
			if (is_object($arrTarget)) {
				
				if (!isset($arrTarget -> $k)) {
					$arrTarget = (object) array_merge(
						(array) $arrTarget,
						array($k => $i)
					);
				} elseif (!$arrTarget -> $k) {
					$arrTarget -> $k = $i;
					
					if ($convert === 'array') {
						$arrTarget[$k] = (array) ($arrTarget[$k]);
					}
					
				} else {
					$arrTarget -> $k = objectMerge($arrTarget -> $k, $i, $convert);
				}
				
			} elseif (is_array($arrTarget)) {
				
				if (empty($arrTarget[$k])) {
					$arrTarget[$k] = $i;
					
					if ($convert === 'object') {
						$arrTarget[$k] = (object) ($arrTarget[$k]);
					}
					
				} else {
					$arrTarget[$k] = objectMerge($arrTarget[$k], $i, $convert);
				}
				
			}
			
		} else {
			
			if (is_object($arrTarget)) {
				
				if ($convert === 'replace') {
					unset($arrTarget -> $k);
				}
				
				if (!isset($arrTarget -> $k)) {
					$arrTarget = (object) array_merge(
						(array) $arrTarget,
						array($k => $i)
					);
				} elseif (!$arrTarget -> $k) {
					$arrTarget -> $k = $i;
				}
				
			} elseif (is_array($arrTarget)) {
				
				if ($convert === 'replace') {
					unset($arrTarget[$k]);
				}
				
				if (empty($arrTarget[$k])) {
					$arrTarget[$k] = $i;
				}
				
			}
			
		}
		
	}
	
	if ($convert && $convert !== 'replace') {
		$arrTarget = json_encode($arrTarget);
	}
	if ($convert === 'array') {
		$arrTarget = json_decode($arrTarget, true);
	} elseif ($convert === 'object') {
		$arrTarget = json_decode($arrTarget);
	}
	
	return $arrTarget;
	
}

function objectArray($item, $convert = false) {

	/*
	*  Функция, которая проверяет входящее значение, массив это или объект
	*  и если указан второй параметр, то возвращает массив
	*/
	
	if (
		is_array($item) ||
		is_object($item)
	) {
		if (!$convert) {
			return true;
		} else {
			return (array) $item;
		}
	} else {
		if (!$convert) {
			return false;
		} else {
			return [$item];
		}
	}
	
}

function objectObject($objTarget, $arrTarget, $output = false) {

	/*
	*  Функция, которая извлекает значение из объекта, воспринимая его как массив
	*  на входе нужно указать:
	*    исходный объект
	*    конечный элемент объекта в виде значения массива или объекта
	*    опция - если true, то возвращать в случае ошибки значение $arrTarget
	*  
	*  по-сути, это просто упрощение записи
	*  (((array)$object -> element)[$item -> name]) или $object -> element -> {$item -> name}
	*  но универсальная вне зависимости от версии PHP и чтобы не повторять ее в коде много раз
	*  
	*  также данная функция удобна тем, что ее можно вызывать без проверок
	*  на существование объекта, массива или их значений
	*  
	*  на выходе отдает значение элемента или пустое значение
	*/
	
	if (
		!$objTarget ||
		!$arrTarget
	) {
		
		if ($arrTarget && $output) {
			return $arrTarget;
		} else {
			return false;
		}
	} elseif (is_object($objTarget)) {
		return $objTarget -> $arrTarget;
	} else {
		return $objTarget[$arrTarget];
	}
	
}

function objectConvert($arrTarget, $convert = false) {

	/*
	*  Функция, которая преобразует массив в объект и объект в массив
	*  на входе нужно указать:
	*    исходный объект или массив
	*    конвертер:
	*      false - по-умолчанию, преобразует объект в массив и наоборот целиком
	*      true - преобразует объект в массив и наоборот только по внешнему уровню
	*      reset - сбрасывает все ключи массива
	*      level - преобразует одномерный массив [a,b,c,d,e] в многомерный [a=>[b=>[c=>[d=>[e=>'']]]]]
	*  
	*  на выходе отдает готовый массив или объект
	*/
	
	if ($convert === 'level') {
		
		if (is_array($arrTarget)) {
			$convert = false;
		} elseif (is_object($arrTarget)) {
			$convert = true;
			$arrTarget = json_decode(json_encode($arrTarget), true);
		} else {
			return;
		}
		
		$type = 'object';
		$item = [];
		$lastitem = 0;
		$arrTarget = array_reverse($arrTarget);
		
		foreach ($arrTarget as $i){
			if (empty($item)) {
				$item[$i] = '';
				$lastitem = $i;
			} else {
				$item[$i] = $item;
				if (isset($item[$lastitem])) { unset($item[$lastitem]); }
				$lastitem = $i;
			}
		}
		
		if ($convert) {
			$item = json_decode(json_encode($item));
		}
		
		$arrTarget = $item;
		unset($item, $lastitem);
		
	} elseif (is_array($arrTarget) && !$convert) {
		$arrTarget = json_decode(json_encode($arrTarget));
	} elseif (is_object($arrTarget) && !$convert) {
		$arrTarget = json_decode(json_encode($arrTarget), true);
	} elseif (is_array($arrTarget) && $convert === 'reset') {
		$arrTarget = (object) array_values( (array) $arrTarget);
	} elseif (is_object($arrTarget) && $convert === 'reset') {
		$arrTarget = (array) array_values( (array) $arrTarget);
	} elseif (is_array($arrTarget)) {
		$arrTarget = (object) $arrTarget;
	} else {
		$arrTarget = (array) $arrTarget;
	}
	
	return $arrTarget;

}

function objectToString($arrTarget, $splitter = ' ', $keys = false) {

	/*
	*  Функция, которая преобразует объект или массив в строку
	*  на входе нужно указать:
	*    исходный объект или массив
	*    строковый разделитель, по-умолчанию - пробел
	*      если указать массив, то его первое значение будет разделителем, а последнее будет последним разделителем
	*      например, если для массива ['раз', 'два', 'три'] указать [', ', ' и '], то результат будет: 'раз, два и три'
	*    флаг, который позволяет, рабирать ли ключи в массиве
	*      например, для массивов ['a' => 1, 'b' => 2] и ['a', 'b'] при значении true результаты будут 'a: 1, b: 2' и '0: a, 1: b'
	*      а для этих же массивов, но при значении false результаты будут '1, 2' и 'a, b'
	*  
	*  на выходе отдает готовую строку
	*/
	
	if (is_string($arrTarget) || empty($arrTarget)) {
		return null;
	} elseif (is_object($arrTarget)) {
		$arrTarget = objectConvert($arrTarget);
	}
	
	$arrTarget = [$arrTarget, '', '', '', ''];
	if ($keys) {
		reset($arrTarget[0]);
		$arrTarget[3] .= key($arrTarget[0]);
		$arrTarget[3] .= ((is_string($keys)) ? $keys : ': ');
	}
	$arrTarget[3] .= array_shift($arrTarget[0]);
	
	if (is_array($splitter) && count($arrTarget[0])) {
		$arrTarget[4] = array_pop($splitter);
		if ($keys) {
			end($arrTarget[0]);
			$arrTarget[4] .= key($arrTarget[0]);
			$arrTarget[4] .= ((is_string($keys)) ? $keys : ': ');
		}
		$arrTarget[4] .= array_pop($arrTarget[0]);
		$splitter = array_shift($splitter);
	}
	
	if (count($arrTarget[0])) {
		foreach ($arrTarget[0] as $arrTarget[2] => $arrTarget[1]) {
			$arrTarget[3] .= $splitter;
			if ($keys) {
				$arrTarget[3] .= $arrTarget[2];
				$arrTarget[3] .= ((is_string($keys)) ? $keys : ': ');
			}
			$arrTarget[3] .= $arrTarget[1];
		}
	}
	
	if ($arrTarget[4]) {
		$arrTarget[3] .= $arrTarget[4];
	}
	
	$arrTarget = $arrTarget[3];
	
	return $arrTarget;

}

function objectRights($db, $query, $rights) {
	
	/*
	*  Функция обработки и подготовки прав пользователя для доступа к базе данных
	*  
	*  На самом деле эта функция читает массив прав, читает тип запроса к БД
	*  и затем выдает группу прав только для этого запроса
	*/
	
	$defaults = [
		'read' => true,
		'write' => USERS_RIGHTS,
		'create' => USERS_RIGHTS,
		'delete' => USERS_RIGHTS
	];
	
	// несколько небольших правок правил под запросы
	$arr = [
		'read' => ['select', 'count', 'verify', 'connect', 'filter'],
		'write' => ['write'],
		'create' => ['create'],
		'delete' => ['delete']
	];
	
	foreach ($arr as $key => $item) {
		if (in_array($query, $item)) {
			$query = $key;
			break;
		}
	}
	unset($key, $item);
	
	if (!empty($rights[$db][$query])) {
		return $rights[$db][$query];
	} elseif (!empty($rights[$query])) {
		return $rights[$query];
	} elseif (!empty($defaults[$query])) {
		return $defaults[$query];
	} else {
		return false;
	}
	
}

function objectProcess($target, $time = 0, $status = null) {
	
	/*
	*  Функция обработки и подготовки данных, необходимых для валидации процесса
	*  
	*  Эта функция может использоваться для curl и для форм
	*  
	*  Параметр 'check' хоть и присутствует, но проверка его идет (внезапно!) не в процессе,
	*  а в функции messageSend в файле template из папки functions !!!
	*/
	
	$target = DEFAULT_PROCESSOR . '/' . str_replace(':', '/', $target) . '/' . (!empty($status) ? $status . '/' : null);
	
	if (!empty($time)) {
		$time = dataParseTime($time);
	}
	
	$time = time() + $time + 1;
	
	global $uri;
	
	return [
		'action' => '/' . $target,
		'link' => $uri -> site . $target,
		'string' => '?hash=' . crypting($time) . '&csrf=' . csrf() . '&check=',
		'array' => [
			'hash' => crypting($time),
			'csrf' => csrf(),
			'check' => ''
		],
		'fields' => [
			'hash' => '<input type="hidden" name="hash" value="' . crypting($time) . '" readonly>',
			'csrf' => '<input type="hidden" name="csrf" value="' . csrf() . '" readonly>',
			'check' => '<input type="text" name="check" value="" style="display:none!important;">'
		]
	];
	
}

?>