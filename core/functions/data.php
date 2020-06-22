<?php defined('isCMS') or die;

/* ФУНКЦИИ ПО ОБРАБОТКЕ ДАННЫХ */

/*
datapath
dateformatcorrect
objectLang
datasplit - Функция, которая разбивает строку на подстроки по заданным разделителям
databreak - Функция, которая разбивает строку на несколько строк по указанному количеству слов
dataphone
datalang
datanum
datanumgrammar
datadatetime
datamorpher
dataprint
*/

function datapath($str, $slash = null, $flag = null) {
	
	/*
	*  Функция корректировки формата пути
	*  на входе нужно указать строку, содержащую путь, и тип слеша
	*  
	*  если тип слеша не указан, будет подставлен системный тип слеша: DS
	*  если тип слеша указан как true, то будет подставлен тип слеша для интернет-ресурсов: '/'
	*  
	*  третий параметр необязательный, он служит для того, чтобы изменять поведение функции
	*  будьте внимательны с его использованием!
	*  'parse' - точки в пути будут заменены на слеш
	*  'type' - возвращает тип файла без точки
	*  'untype' - убирает тип файла в конце пути, включая точку
	*  'array' - вернет массив, где 0 - путь, 1 - тип файла
	*/
	
	$slash = empty($slash) ? DS : '/';
	
	if ($flag === 'parse') {
		$str = str_replace('.', $slash, $str);
	} elseif ($flag) {
		
		$dot = strrpos($str, '.');
		
		if ($dot) {
			if ($flag === 'type') {
				$str = substr($str, $dot + 1);
			} elseif ($flag === 'untype') {
				$str = substr($str, 0, $dot);
			} elseif ($flag === 'array') {
				return [
					substr($str, 0, $dot),
					substr($str, $dot + 1)
				];
			}
		}
		
		unset($dot);
		
	}
	
	return preg_replace('/[\\\\\/]+/', $slash, $str);
}

function dateformatcorrect($arr) {

	/*
	*  Функция корректировки формата даты
	*  на входе нужно указать массив $arr
	*  
	*  функция примет массив и произведет коррекцию формата даты
	*  на единственный вариант, соответствующий заданному стандарту
	*  
	*  например, дата 1.9.8 примет вид 01.09.2008
	*  
	*  параметры массива:
	*    sym - стандартный символ разделителя даты,
	*      например '.' для русского и '/' для английского языков
	*    convert - символ, который разделяет число и месяц,
	*      будет преобразован в стандартный, но число и месяц поменяются местами
	*    replace - другой символ, который разделяет дату,
	*      будет преобразован в стандартный без других изменений
	*    data - дата на входе
	*  
	*  на выходе отдает дату в исправленном формате
	*/
	
	$arr -> split = preg_split('/[\\' . $arr -> sym . '\\' . $arr -> convert . '\\' . $arr -> replace . ']/', $arr -> date, -1, PREG_SPLIT_OFFSET_CAPTURE);
	foreach ($arr -> split as &$split) {
		if (strlen($split[0]) === 1) {
			$split[0] = '0' . $split[0];
		}
	}
	
	$arr -> merge = $arr -> split[0][0];
	if (isset($arr -> split[1])) {
		$arr -> merge .= substr($arr -> date, $arr -> split[1][1] - 1, 1) . $arr -> split[1][0];
	}
	if (isset($arr -> split[2])) {
		$arr -> merge .= substr($arr -> date, $arr -> split[2][1] - 1, 1) . $arr -> split[2][0];
	}
	$arr -> date = $arr -> merge;
	
	if (strlen($arr -> date) === 5) {
		$arr -> date .= $arr -> sym . date('Y');
	} elseif (strlen($arr -> date) === 8) {
		$arr -> date = substr($arr -> date, 0, 6) . substr(date('Y'), 0, 2) . substr($arr -> date, 6, 2);
	}
	if (strpos($arr -> date, $arr -> replace)) {
		$arr -> date = str_replace($arr -> replace, $arr -> sym, $arr -> date);
	}
	if (strpos($arr -> date, $arr -> convert)) {
		$arr -> date = substr($arr -> date, 3, 2) . $arr -> sym . substr($arr -> date, 0, 2) . $arr -> sym . substr($arr -> date, 6, 4);
	}
	
	return $arr -> date;
	
}

function datasplit($target, $splitter = '\s,;', $noempty = PREG_SPLIT_NO_EMPTY) {

	/*
	*  Функция, которая разбивает строку на подстроки по заданным разделителям
	*  на входе нужно указать:
	*    исходную строку
	*    строку с разделителями (если не указана, то по-умолчанию будет назначены пробел, запятая и точка с запятой)
	*  
	*  Функция также чистит строку от квадратных и фигурных скобок
	*  
	*  на выходе отдает готовый массив
	*/
	
	/*
	if (
		strpos($item[$field_item], '[') !== false &&
		strpos($item[$field_item], ']') !== false
	) {
		$item[$field_item] = mb_substr($item[$field_item], 1, -1);
	}
	*/
	
	if (!is_string($target)) {
		return false;
	}
	
	$target = str_replace(['[', ']', '{', '}'], '', $target);
	$target = preg_split('/[' . $splitter . ']/u', $target, null, $noempty);
	
	return $target;
	
}

function dataParseTime($str) {
	
	/*
	*  Функция разбора данных времени и преобразование в абсолютное значение
	*  на входе нужно указать строку с данными $str в формате 'num:name', наприме '2:hour'
	*  или массив ['num', 'name']
	*/
	
	// преобразуем время в абсолютные единицы
	if (
		objectIs($str) ||
		strpos($str, ':') !== false ||
		is_string($str) && !is_numeric($str)
	) {
		
		if (objectIs($str)) {
			$value = $str;
		} elseif (strpos($str, ':') !== false) {
			$value = dataParse($str);
		} else {
			$value = [1, $str];
		}
		
		if (in_array($value[1], ['minute', 'hour', 'day', 'week', 'month', 'year'])) {
			$value = $value[0] * constant('TIME_' . strtoupper($value[1]));
		} else {
			$value = null;
		}
		
		$str = $value;
		unset($value);
		
	} elseif (is_numeric($str)) {
		$str = (int) $str;
	} else {
		$str = null;
	}
	
	return (int) $str;
	
}

function dataParse($str, $line = true, $object = false) {
	
	/*
	*  Функция разбора данных в значениях массива
	*  на входе нужно указать строку с данными $str
	*  
	*  функция примет строку и произведет разбор и преобразование ее в массив или объект
	*  
	*  вторым параметром указывается тип преобразования - по группам (по-умолчанию) или в одну строку
	*  третьим параметром указывается что отдается на выходе - массив (по-умолчанию) или объект
	*  
	*  пример по группам:
	*    "group:subgroup:item" -> { "group" : [ "subgroup", "item" ] }
	*    "group:subgroup1:subgroup2,item:subitem1:subitem2" -> { "group" : [ "subgroup1", "subgroup2" ], "item" : [ "subitem1", "subitem2" ] }
	*  пример в одну строку:
	*    "group:subgroup:item" -> [ "group", "subgroup", "item" ]
	*    "group:subgroup1:subgroup2,item:subitem1:subitem2" -> [ [ "group", "subgroup1", "subgroup2" ], [ "item", "subitem1", "subitem2" ] ]
	*  
	*  на выходе отдает массив или объект
	*/
	
	$str = datasplit($str);
	
	if (!$str) {
		return false;
	}
	
	$arr = [];
	
	foreach ($str as $key => $item) {
		$item = datasplit($item, ':', false);
		if (!$line) {
			$k = array_shift($item);
			$arr[$k] = $item;
		} else {
			$arr = $item;
		}
	}
	
	unset($key, $k, $item, $str);
	
	if ($object) {
		$arr = objectConvert($arr);
	}
	
	return $arr;
	
}

function dataImage($data, $customset = null) {
	
	/*
	*  Функция автоматического подставления изображений в содержимое
	*  
	*  вероятнее всего, функция временная и в релизе останется в виде прослойки для dataprint
	*  также ее функционал может быть изменен
	*  
	*  на входе нужно указать строку с данными $data и настройки $set
	*  
	*  на выходе отдает готовую строку
	*  
	*  чтобы правильно пользоваться функцией, её необходимо вызывать в шаблоне при выводе текста, например так:
	*  <?= dataImage($data['description'], $module -> settings['images']); ?>
	*  настройки можно брать из настроек модуля вывода контента
	*  
	*  ЧТО ЕЩЕ ДОБАВИТЬ?
	*  УКАЗАНИЕ ПРЕФИКСА
	*  СКАНИРОВАНИЕ ПАПКИ С ИЗОБРАЖЕНИЯМИ
	*  ЗАДАНИЕ СПИСКОМ ВРУЧНУЮ, ХОТЯ ЭТО БЕССМЫСЛЕННО ДЛЯ ПОЛЬЗОВАТЕЛЯ
	*  НО НА САМОМ ДЕЛЕ, СЕЙЧАС ЭТО НЕ ПРИНЦИПИАЛЬНО
	*/
	
	$set = [
		'base' => '\{img[\:\w\s]*?\}',
		// базовый шаблон поиска - это лучше не трогать
		'folder' => null,
		// папка, в которой будут искаться изображения
		// задается в формате 'folder:subfolder' или 'folder.subfolder'
		'ext' => 'jpg',
		// расширение файлов
		'tag' => 'p',
		// тег, внутри которого будет искаться текст
		'place' => 'after',
		// поиск текста до (before) или после (after) изображения
		'wrapper' => ['<p>', '</p>'],
		// обертка изображении - массив, начало и конец
		'class' => null,
		// задать базовый класс для всех изображений,
		// к нему будут прибавлены персональные классы
		'data' => null
		// задать массивом параметры данных для всех изображений:
		// ['target' => 'null', 'rest' => 'fiction'] преобразуется в 'data-target="null" data-rest="fiction"'
	];
	
	if (objectIs($customset)) {
		$set = array_merge($set, $customset);
	}
	
	$find = 
		($set['place'] === 'before' ? '(<' . $set['tag'] . '>.*?<\/' . $set['tag'] . '>).*?' : null) . 
		$set['base'] . 
		($set['place'] === 'after' ? '.*?<' . $set['tag'] . '>(.*?)<\/' . $set['tag'] . '>' : null)
	;
	
	$replace = preg_replace_callback(
		'/' . $find . '/ui',
		function($matches) use($item, $set) {
			static $i; $i++;
			
			if (objectIs($matches)) {
				
				$newset = null;
				
				preg_match('/' . $set['base'] . '/ui', $matches[0], $newset);
				$newset = datasplit(substr(array_shift($newset), 1, -1), ':', null);
				
				if (!empty($newset[1])) {
					$title = $newset[1];
				}
				if (!empty($newset[2])) {
					$class = (!empty($set['class']) ? $set['class'] . ' ' : null) . $newset[2];
				}
				
				unset($newset);
				
				$preg = $matches;
				$matches = array_shift($matches);
				
				if (empty($title) || !is_string($title)) {
					
					if ($set['place'] === 'before') {
						preg_match_all('/<' . $set['tag'] . '>[\s\r\n]*?(.*?)[\s\r\n]*?<\/' . $set['tag'] . '>/ui', array_shift($preg), $preg);
						if (objectIs($preg[1])) {
							preg_match_all('/(.*?)\./ui', array_pop($preg[1]), $preg);
							$preg = objectIs($preg[1]) ? trim(array_pop($preg[1])) : $preg[1];
						} else {
							$preg = $preg[1];
						}
					} elseif ($set['place'] === 'after') {
						unset($preg[0]);
						if (objectIs($preg)) {
							preg_match_all('/(.*?)\./ui', array_shift($preg), $preg);
							$preg = objectIs($preg[1]) ? trim(array_shift($preg[1])) : $preg[1];
						} else {
							$preg = $preg[1];
						}
					} else {
						$preg = null;
					}
					
				} else {
					$preg = $title;
				}
				
				$file = PATH_LOCAL . str_replace([':', '.'], DS, $set['folder']) . DS . $i . '.' . $set['ext'];
				
				if (file_exists($file)) {
					$preg = $set['wrapper'][0] . '<img
						src="/' . URL_LOCAL . str_replace([':', '.'], '/', $set['folder']) . '/' . $i . '.jpg' . 
						(DEFAULT_MODE === 'develop' ? '?' . filemtime($file) : null) . 
						'" alt="' . $preg . '" title="' . $preg . '"' . 
						(!empty($class) ? ' class="' . $class . '"' : null) . 
						(objectIs($set['data']) ? ' data-' . objectToString($set['data'], '" data-', '="') . '"' : null) . 
					' />' . $set['wrapper'][1];
				} else {
					$preg = null;
				}
				
				return preg_replace('/' . $set['base'] . '/ui', $preg, $matches);
				
			}
			
		},
		$data
	);
	
	return preg_replace('/' . $set['base'] . '/ui', null, $replace);
	
}

function dataInString($target, $str) {
	
	/*
	*  Функция поиска данных в строке перечисления
	*  
	*  на входе нужно указать строку с данными $str и искомые данные $target
	*  если вы хотите искать одновременно несколько данных,
	*  то отправьте их в виде массива $target
	*  или в виде строки $target с перечислением данных через пробел или запятую
	*  функция примет строку и произведет поиск данных в ней
	*  
	*  важно учесть, что указанные данные ищутся целиком:
	*    "group subgroup item" - "oup", "gro" не будут найдены
	*  
	*  на выходе отдает true, если данные были найдены, и false, если нет
	*/
	
	$str = ' ' . $str . ' ';
	
	if (!is_array($target)) {
		if (is_object($target)) {
			$target = (array) $target;
		} elseif (
			strpos($target, ' ') !== false ||
			strpos($target, ',') !== false ||
			strpos($target, ';') !== false
		) {
			$target = datasplit($target);
		} else {
			$target = [$target];
		}
	}
	
	foreach ($target as $item) {
		if (stripos($str, ' ' . trim($item) . ' ') !== false) {
			return true;
		}
	}
	
	return false;
	
}

function databreak($target, $splitter = 1, $line = '<br>') {

	/*
	*  Функция, которая разбивает строку на несколько строк по указанному количеству слов
	*  на входе нужно указать:
	*    исходную строку
	*    число, после какого каждого слова нужно разбить строку или массив чисел
	*    строку-разделитель, по-умолчанию - тег разрыва строки br
	*  
	*  Если вторым аргументом указан массив, то в нем должны быть перечислены слова,
	*  после которых будет идти разбивка
	*  
	*  Функция использует datasplit() и clear()
	*  
	*  Примеры использования:
	*  databreak($target) - разбивает каждое слово по строке
	*  databreak($target, 2) - делает на одной строке по два слова
	*  databreak($target, [1,2,8]) - выполняет перенос на новую строку после 1-го, 2-го и 8-го слов
	*  databreak($target, 2, '|') - после каждых двух слов вставляет разделитель '|'
	*  
	*  Также, например внутри тега 'p' можно сделать такой разделитель: '</p><p>';
	*  
	*  на выходе отдает готовую строку
	*/
	
	$target = datasplit($target, '\s');
	$line = clear($line);
	$len = count($target) - 1;
	$pos = 0;
	$out = '';
	
	foreach ($target as $key => $item) {
		
		$key = (int) $key;
		
		if (is_numeric($splitter)) {
			
			$splitter = (int) $splitter;
			
			if (
				$key !== 0 &&
				$key % $splitter === 0
			) {
				$out = trim($out) . '<br>';
				$out = trim($out) . "\n";
			}
			
			$out .= $item . ' ';
			
		} elseif (is_array($splitter)) {
			
			if (
				$key !== 0 &&
				$key === $splitter[$pos]
			) {
				$out = trim($out) . '<br>';
				$pos++;
			}
			
			$out .= $item . ' ';
			
		}
		
		
	}
	
	return $out;
	
}

function dataphone($target, $format = false, $langcode = false) {
	
	/*
	*  Функция, которая форматирует номер телефона в нужном языковом формате
	*  на входе нужно указать:
	*    номер телефона в виде числа или строки
	*    формат
	*      false - по-умолчанию, берет формат из параметра $lang -> phone
	*      true - или если не задан $lang -> phone, просто преобразует в 11-значный набор чисел: 79001234567
	*  
	*  шаблон формата очень простой - все цифры нужно заменить на 'x'
	*  например, шаблон "+x (xxx) xxx-xx-xx" для номера "79001234567" выдаст "+7 (900) 123-45-67"
	*  а шаблон "+x xxx xxx xx xx" для номера "79001234567" выдаст "+7 900 123 45 67" и т.д.
	*  
	*  внимание! если вы используете это для форматирования телефонного номера,
	*  не забудьте указать символ '+' впереди вызова функции
	*  
	*  примеры использования:
	*  <a href="tel:+<?= dataphone(lang('information:phone:0'), true, 'ru'); ?>"> - для установки универсальной ссылки на телефон 8/+7
	*  dataphone(lang('information:phone:0'), '+7 xxx xxx-xx-xx') - для форматирования
	*  
	*  на выходе отдает готовую строку
	*/
	
	$target = preg_replace('/[^0-9]/', '', $target);
	$def = lang('phone:default');
	
	if (
		!$format &&
		!empty(lang('phone:format'))
	) {
		$format = lang('phone:format');
	} elseif (
		!$format ||
		$format === true
	) {
		$format = str_repeat('x', strlen($target));
	}
	
	$format = preg_replace('/X/', 'x', $format);
	$format = preg_replace('/[^xX0-9 +—‒–().,_:;\-\[\]\{\}]/', '', $format);
	
	if (
		!empty($target) &&
		strlen($target) < 11 &&
		!empty($def)
	) {
		$i = 0;
		while (strlen($target) < 11) {
			$n = 11 - strlen($target) - 1;
			$target = $def[$n] . $target;
		}
	} elseif (
		empty($target) &&
		!empty($def)
	) {
		return $def;
	} elseif (empty($target)) {
		return false;
	}
	
	// прямой перебор (сейчас)
	/*
	$i = 0;
	$l = strlen($format);
	
	while (strpos($format, 'x') !== false) {
		
		$n = strpos($format, 'x');
		$format[$n] = $target[$i];
		$i++;
		
		if ($i >= $l) {
			break;
		}
		
	}
	*/
	// обратный перебор (тестовый)
	
	$i = strlen($target) - 1;
	
	while (strpos($format, 'x') !== false) {
		
		$n = strrpos($format, 'x');
		$format[$n] = $target[$i];
		$i--;
		
		if ($i < 0) {
			break;
		}
		
	}
	
	// языковые настройки
	
	if (
		$langcode
	) {
		if ($langcode === 'ru') {
			if (
				strlen($format) === 11 &&
				$format[0] == '8'
			) {
				$format[0] = '7';
			} elseif (strlen($format) === 10) {
				$format = '7' . $format;
			}
		}
	}
	
	return $format;
	
}

function datalang($target, $current = false, $s = false, $convert = false, $split = null) {

	/*
	*  Функция, которая выводит переменную в нужном языковом формате
	*  на входе нужно указать:
	*    переменную
	*    раздел языкового массива
	*    параметры морфера (падеж, число)
	*    параметры преобразования
	*      l - lowercase, все строчные буквы
	*      u - uppercase, все заглавные буквы
	*      c - case, первые буквы слов - заглавные
	*      по-умолчанию все буквы - как есть
	*  
	*  если вместо переменной будет указан массив, то функция переведет массив согласно
	*  языку и автоматически вызовет функцию objectToString
	*  
	*  на выходе отдает готовую строку
	*/
	
	global $lang;
	
	//print_r($lang -> data[$current]);
	//echo $target . ' ';
	
	if (is_string($target)) {
		
		if (
			!$current &&
			!empty($lang -> data[$target])
		) {
			$target = $lang -> data[$target];
		} elseif (
			$current &&
			!empty($lang -> data[$current][$target])
		) {
			$target = $lang -> data[$current][$target];
		}
		
		if ($s) {
			$target = datamorpher($target, $s);
		}
		
		if ($convert) {
			if ($convert === 'l') {
				$target = mb_convert_case($target, MB_CASE_LOWER);
			} elseif ($convert === 'u') {
				$target = mb_convert_case($target, MB_CASE_UPPER);
			} elseif ($convert === 'c') {
				$target = mb_convert_case($target, MB_CASE_TITLE);
			} elseif ($convert === 't') {
				$target = mb_convert_case(mb_substr($target, 0, 1), MB_CASE_UPPER) . mb_convert_case(mb_substr($target, 1), MB_CASE_LOWER);
			}
		}
		
	} elseif (objectIs($target)) {
		
		foreach ($target as &$sample) {
			$sample = datalang($sample, $current, $s, $convert);
		}
		
		$target = objectToString($target, !empty($split) ? $split : (!empty($lang -> data['counter']['split']) ? $lang -> data['counter']['split'] : ' '));
		
	}
	
	return $target;
	
}

function datanum($target, $convert = false, $multiply = false) {

	/*
	*  Функция, которая выводит число в нужном языковом формате
	*  на входе нужно указать:
	*    число
	*    тип конверсии
	*      false - по-умолчанию, просто преобразует в число
	*      bits - разделяет разряды
	*      dec - ставит два знака после запятой
	*      bitsdec или decbits - разделяет разряды и ставит два знака после запятой
	*      split - разделяет число на два числа - до запятой и после запятой и возвращает в виде массива
	*      digits - добавляет впереди к числу нули до длины строки, указанной в $multiply
	*        не считает за разряды числа после точки или запятой, т.е. одинаково выдаст 001 и 001.5
	*        это единственный раз, когда $multiply не используется как множитель
	*      add - добавляет после числа окончание
	*        могут быть параметры, указанные через двоеточие, согласно правилам морфинга,
	*        сами окончания задаются в $lang -> datetime -> add, где индекс соответствует числу,
	*        а 0 - для всех чисел, для которых индекс не задан или не найден,
	*        для самой цифры 0 индекс не применяется
	*      array - преобразует в массив, единицы, десятки, сотни и т.д.
	*      если указать массив значений, то преобразует в текстовое представление
	*        первое значение - падеж
	*        второе значение - трансформация
	*          l - lowercase, все строчные буквы
	*          u - uppercase, все заглавные буквы
	*          c - case, первые буквы заглавные
	*          по-умолчанию все буквы - как есть
	*        третье значение - форма слова, существительное/прилигательное
	*    множитель - на сколько число будет умножено
	*      
	*  
	*  если вместо переменной будет указан массив,
	*  то функция рекурсивно вызовет себя
	*  и вернет готовый массив чисел
	*  
	*  на выходе отдает готовое число
	*/
	
	if (is_array($target)) {
		$target = [$target, '', []];
		foreach ($target[0] as $target[1]) {
			$target[3][] = datanum($target[1], $convert, $multiply);
		}
		$target = $target[3];
		return $target;
	}
	
	if (
		strpos($target, ',') !== false ||
		strpos($target, '.') !== false
	) {
		$target = str_replace(',', '.', $target);
		$target = (float) $target;
	} else {
		$target = (int) $target;
	}
	
	if (
		$multiply &&
		$convert !== 'digits'
	) {
		$target = $target * $multiply;
	}
	
	if (
		$convert !== false &&
		$convert !== 'split' &&
		$convert !== 'digits' &&
		$convert !== 'array'
	) {
		global $lang;
	}
	
	if ($convert === 'bits') {
		$target = number_format(
			$target,
			0,
			'',
			(isset($lang -> counter -> bit)) ? $lang -> counter -> bit : ' '
		);
	} elseif ($convert === 'dec') {
		$target = number_format(
			$target,
			2,
			(isset($lang -> counter -> dec)) ? $lang -> counter -> dec : ' ',
			''
		);
	} elseif ($convert === 'bitsdec' || $convert === 'decbits') {
		$target = number_format(
			$target,
			2,
			(isset($lang -> counter -> dec)) ? $lang -> counter -> dec : ' ',
			(isset($lang -> counter -> bit)) ? $lang -> counter -> bit : ' '
		);
	} elseif ($convert === 'split') {
		$target = datasplit($target, '.');
		if (!isset($target[1])) {
			$target[1] = 0;
		}
	} elseif ($convert === 'digits') {
		if (
			!empty($multiply) &&
			$multiply > 1
		) {
			$multiply = $multiply - strlen($target);
			if (strpos($target, '.') !== false) {
				$multiply = $multiply + strlen(substr($target, strpos($target, '.')));
			}
			if ($multiply > 0) {
				$target = str_repeat('0', $multiply) . $target;
			}
		}
	} elseif ($convert === 'array') {
		$target = str_replace($lang -> counter -> bit, '', $target);
		$target = (int) $target;
		$target = str_split($target);
	} elseif (is_string($convert) && substr($convert, 0, 3) === 'add') {
		
		$convert = [
			'morph' => substr($convert, 4),
			'string' => ''
		];
		
		if (strpos($convert['morph'], '::') !== false) {
			$convert['morph'] = str_replace(':::', ':0:0:', $convert['morph']);
			$convert['morph'] = str_replace('::', ':0:', $convert['morph']);
		}
		
		$convert['morph'] = datasplit($convert['morph'], ':');
		
		if (is_array($lang -> datetime -> add) && array_key_exists($target, $lang -> datetime -> add)) {
			$convert['string'] = $lang -> datetime -> add[$target];
		} elseif (is_array($lang -> datetime -> add)) {
			$convert['string'] = $lang -> datetime -> add[0];
		} else {
			$convert['string'] = $lang -> datetime -> add;
		}
		
		$target .= ($target) ? datamorpher(
			$convert['string'],
			$convert['morph']
		) : false;
		$convert = '';
		
	} elseif (is_array($convert)) {
		
		$target = str_replace($lang -> counter -> bit, '', $target);
		//$target = 1532201; // тестовое число
		$target = (int) $target;
		
		global $morph;
		
		//print_r($convert);
		//print_r($morph -> grammar);
		
		if ($target > 0 && isset($morph) && isset($morph -> grammar)) {
			
			$len = (strlen($target) % 3) ? 3 - strlen($target) % 3 : 0;
			$target = str_repeat(0, $len) . $target;
			$target = [ array_reverse(str_split($target, 3)), [], '', 0, [], [] ];
			// массив target:
			// [0] - массив числа, который мы разбираем на составляющие
			// [1] - массив готовых данных, куда мы записываем разбор
			// [2] - значение текущего числа
			// [3] - длина массива текущих данных
			// [4] - массив параметров морфинга
			// [5] - массив вторых параметров морфинга (если есть)
			// переменная $digit - значение текущего разряда: нет, тысячи, миллионы
			// переменная $type - значение выбранного формата: число/дата
			
			foreach ($target[0] as $target_key => $target_item) {
				
				$target_item = array_reverse(str_split($target_item));
				$target[3] = 0;
				
				// грамматика
				$target[4] = [$convert[0], $convert[1], $convert[2]];
				if ($target[4][2] === 'date') {
					$type = 'dates';
					$convert[1] = (!empty($convert[3])) ? $convert[3] : false;
				} else {
					$type = 'numbers';
				}
				
				foreach ($morph -> grammar -> $type -> base as $k => $i) {
					if ($i !== 'skip' && $k < 3) {
						$target[4][$k] = $i;
					}
				}
				
				if ($target_key === 1) {
					$digit = 'thousand';
				} elseif ($target_key === 2) {
					$digit = 'million';
				} else {
					$digit = 'first';
				}
				
				if ($target_item[1] === '1') {
					
					// это для чисел 10-19
					$target[2] = $target_item[1] . $target_item[0];
					$target = datanumgrammar(
						$target,
						$target[2],
						$type,
						$digit,
						$convert[1]
					);
					
				} else {
					
					if (
						($target_item[0] && $target_key === 0) ||
						($target_item[0] > 0 && $target_key > 0)
					) {
						// это для единиц (1-9)
						$target[2] = $target_item[0];
						$target = datanumgrammar(
							$target,
							$target[2],
							$type,
							$digit,
							$convert[1]
						);
					}
					
					if ($target_item[1]) {
						// это для десятков (20, 30, 40...)
						$target[2] = $target_item[1] . '0';
						$target = datanumgrammar(
							$target,
							$target[2],
							$type,
							$digit,
							$convert[1]
						);
					}
					
				}
				
				if ($target_item[2]) {
					// это для сотен (100, 200, 300, 400...)
					$target[2] = $target_item[2] . '00';
					$digit = 'hundred';
					$target = datanumgrammar(
						$target,
						$target_item[2],
						$type,
						$digit,
						$convert[1]
					);
				}
				
				if (
					$target_key === 1 ||
					$target_key === 2
				) {
					$target[1] = array_merge(
						array_slice($target[1], 0, $target[3]),
						[
							datalang(
								'1' . str_repeat('000', $target_key),
								'counter',
								$target[5],
								(!empty($convert[1])) ? $convert[1] : false
							)
						],
						array_slice($target[1], $target[3])
					);
				}
				
			}
			
			$target = objectToString($target[1]);
			//print_r($target);
			
		}
		
	}
	
	return $target;
	
}

function datanumgrammar($target, $compare, $type, $digit, $convert = false) {

	/*
	*  Вспомогательная функция для функции datanum, определяет грамматику
	*  на входе нужно указать:
	*    массив (или объект, содержащий массив) с условиями
	*    массив настроек грамматики
	*  
	*  функция обрабатывает условия согласно заданным настройкам грамматики
	*  и возвращает готовый массив настроек грамматики
	*  
	*  правила грамматики target[4]:
	*  [0] - $convert[0] - падеж
	*  [1] - false - число, род
	*  [2] - $convert[2] - часть речи
	*  значения: false, skip, значение
	*  
	*  на выходе отдает массив настроек грамматики
	*/
	
	global $morph;
	
	if (!isset($morph -> grammar -> $type)) {
		$type = 'numbers';
	}
	
	if (!isset($morph -> grammar -> $type -> $digit)) {
		$digit = 'first';
	}
	
	if (
		(int) $compare == 1
	) {
		// это для значений 1
		$part = 'one';
	} elseif (
		(int) $compare >= $morph -> grammar -> $type -> $digit -> minmax[3] &&
		(int) $compare <= $morph -> grammar -> $type -> $digit -> minmax[4]
	) {
		// это для значений от min до max (2-4)
		$part = 'minmax';
	} else {
		// для всех остальных значений
		$part = 'all';
	}
	
	foreach ($morph -> grammar -> $type -> $digit -> $part as $k => $i) {
		
		if (strpos($i, ':') === false) {
			
			if ($i !== 'skip' && $k < 3) {
				$target[4][$k] = $i;
				$target[5][$k] = $i;
			}
			
		} else {
			
			$i = [
				substr($i, 0, strpos($i, ':')),
				substr($i, strpos($i, ':') + 1)
			];
			
			if ($i[0] !== 'skip' && $k < 3) {
				$target[4][$k] = $i[0];
			}
			
			if ($i[1] !== 'skip' && $k < 3) {
				$target[5][$k] = $i[1];
			}
			
		}
		
	}
	
	if (
		(
			$compare == 1 &&
			!empty($morph -> grammar -> $type -> $digit -> one[3])
		) || (int) $compare > 1
	) {
		array_unshift(
			$target[1],
			datalang(
				$target[2],
				'counter',
				$target[4],
				($convert) ? $convert : false
			)
		);
		$target[3]++;
	}
	
	return $target;
	
}

function datadatetime($target = false, $format = false, $convert = false, $utc = false) {

	/*
	*  Функция, которая выводит дату в нужном языковом формате
	*  на входе нужно указать:
	*    дату, по-умолчанию - текущая дата
	*    формат
	*      false - по-умолчанию, будет взята из языковых настроек или выведено число в формате UNIX
	*      true - без преобразования
	*      любой строковый формат форматирует дату согласно приведенным данным
	*    также дополнительно введены значения convert, которые также могут быть подставлены в format
	*    если convert не используется
	*      array - относительное время, сколько дней, часов, минут, секунд (например, 2 дня, 1 час)
	*      absolute - абсолютное время, общее целое число дней, общее целое число часов и т.д. (например, 2 дня, 49 часов)
	*    последний, четвертый параметр, задает дату без смещения временных зон (по гринвичу)
	*    он всегда используется четвертым
	*  
	*  общий принцип таков:
	*    маленькая буква - номер,
	*    большая буква - название
	*    одна буква - короткий формат
	*    две буквы - полный формат
	*  например:
	*    m - номер месяца, 1
	*    mm - номер месяца с нулем, 01
	*    M - сокращенное название месяца, jan
	*    ММ - полное название месяца, january
	*    .a (латинская 'a' в конце) - добавление к номеру окончания
	*      (задается в $lang -> datetime -> add, где индекс соответствует числу,
	*      а 0 - для всех чисел, для которых индекс не задан или не найден,
	*      для самой цифры 0 индекс не применяется)
	*  переменные:
	*    y - год
	*    m - месяц
	*    d - день
	*    h - часы (+hour)
	*    i - минуты (+min)
	*    s - секунды (+sec)
	*  доп.переменные:
	*    w - номер дня недели
	*  
	*  если на входе была указана дата, то она разбирается по следующему принципу:
	*    число или числовой текст - дата в абсолютном формате UNIX
	*    любой иной строковый формат - дата форматируется согласно языковым настройкам
	*    в формате массива дата разбирается по порядку согласно приведенным данным
	*  
	*  переменные даты в строковом формате нужно указывать в фигурных скобках {M}, {D} и т.д.
	*  если нужно отформатировать строковое представление даты,
	*  после переменной через двоеточие указать параметры морфинга слова
	*  (падеж, число и род (второе - необязательно)), например: {M:r:e}
	*  
	*  datadatetime() / datadatetime('') / datadatetime('','') выдаст текущую дату в заданном формате
	*  datadatetime('', '{yy}') выдаст текущую дату в указанном формате, в данном примере - текущий год
	*  datadatetime('',true) выдаст текущую дату в абсолютном формате
	*  datadatetime(1511110001) / datadatetime('1511110001') выдаст заданную дату в заданном формате
	*  datadatetime(1511110001,true) / datadatetime('1511110001',true) вернет абсолютную заданную дату
	*  datadatetime(1511110001,'...') вернет заданную дату в прописанном ... формате
	*  datadatetime(['2018','12','21']) разбирает массив год/месяц/день/час/мин/сек и возвращает в абсолютном формате
	*  datadatetime('21.12.1984') разберет строку по заданному формату и вернет в абсолютном формате
	*  datadatetime('21.12.1984', '{dd}.{mm}.{yy}', true) разберет строку согласно прописанному формату, переведет ее в абсолютный формат и выдаст
	*  datadatetime('21.12.1984', false, '{yy}') разберет строку, переведет ее в абсолютный формат и выдаст в нужном формате
	*  datadatetime('21.12.1984', false, '{d} {MM:r} {yy} г.') выдаст дату в виде 21 декабря 1984 г.
	*  
	*  в примерах значения '', '0', 0, false, null - дадут одинаковый результат
	*  значения true, '1', 1 - также дадут одинаковый между собой результат
	*  
	*  на выходе отдает готовую строку
	*/
	
	global $lang;
	
	$date = [
		'year' => '',
		'month' => '',
		'day' => '',
		'hour' => '',
		'minute' => '',
		'second' => '',
		'absolute' => '',
		'data' => '',
	];
	
	// $rules[0] - массив разрешенных входящих переменных
	// $rules[1] - массив соответствий в формате даты php
	// $rules[2] - массив текстовых переменных
	// $rules[3] - массив минимальных значений: по-умолчанию, длина, добавление
	// $rules[4] - формат, переданный в функцию
	$rules = [
		[
			'y', 'yy', 'Y', 'YY', 'ya', 'yya',
			'm', 'mm', 'M', 'MM', 'ma', 'mma',
			'd', 'dd', 'D', 'DD', 'da', 'dda',
			'h', 'hh', 'H', 'HH', 'hour',
			'i', 'ii', 'min',
			's', 'ss', 'sec',
			'w', 'ww', 'W', 'WW',
			'utc', 'gmt'
		],
		[
			'y', 'Y', 'y', 'Y', 'y', 'Y',
			'n', 'm', 'n', 'n', 'n', 'm',
			'j', 'd', 'j', 'j', 'j', 'd',
			'g a', 'h A', 'G', 'H', 'H',
			'i', 'i', 'i',
			's', 's', 's',
			(empty($lang -> data['datetime']['firstday']) || $lang -> data['datetime']['firstday'] == '7') ? 'w' : 'N',
			(empty($lang -> data['datetime']['firstday']) || $lang -> data['datetime']['firstday'] == '7') ? 'w' : 'N',
			(empty($lang -> data['datetime']['firstday']) || $lang -> data['datetime']['firstday'] == '7') ? 'w' : 'N',
			(empty($lang -> data['datetime']['firstday']) || $lang -> data['datetime']['firstday'] == '7') ? 'w' : 'N',
			' U', ' U'
		],
		[
			'Y', 'YY', 'M', 'MM', 'D', 'DD', 'W', 'WW',
			'ya', 'yya', 'ma', 'mma', 'da', 'dda'
		],
		[
			'year' =>   ['1970', 4, 9999, '19'],
			'month' =>  ['01', 2, 12, '0'],
			'day' =>    ['01', 2, 31, '0'],
			'hour' =>   ['00', 2, 23, '0'],
			'minute' => ['00', 2, 59, '0'],
			'second' => ['00', 2, 59, '0']
		],
		$format
	];
	
	if (!$format && !$convert) { $convert = true; }
	
	$format = str_replace(
		array_map(
			function($i){
				return '{' . $i . '}';
			},
			$rules[0]
		),
		$rules[1],
		($format && $format !== true) ? $format : $lang -> data['datetime']['format']
	);
	
	if (!set($target) || $target === true) {
		
		if (!$format) {
			return date('U');
		} elseif ($rules[4]) {
			$date['data'] = getdate();
			$date = [
				'year' => $date['data']['year'],
				'month' => $date['data']['mon'],
				'day' => $date['data']['mday'],
				'hour' => $date['data']['hours'],
				'minute' => $date['data']['minutes'],
				'second' => $date['data']['seconds'],
				'absolute' => $target,
				'data' => '',
			];
			$convert = $rules[4];
		} else {
			return date($format);
		}
		
	} elseif (is_numeric($target) && strpos($target, '.') === false) {
		
		if (!$convert) {
			
			$date['data'] = getdate($target);
			$date = [
				'year' => $date['data']['year'],
				'month' => $date['data']['mon'],
				'day' => $date['data']['mday'],
				'hour' => $date['data']['hours'],
				'minute' => $date['data']['minutes'],
				'second' => $date['data']['seconds'],
				'absolute' => $target,
				'data' => '',
			];
			$convert = $rules[4];
			//print_r($date);
			//return date('U', $target);
			
		} else {
			return date($format, $target);
		}
		
	} elseif (is_array($target)) {
		
		$date['data'] = array_keys($date);
		
		foreach ($target as $key => $item) {
			$date[ $date['data'][$key] ] = $item;
		}
		
		$date['data'] = '';
		
	} elseif (is_string($target)) {
		
		$date = array_intersect_key(date_parse_from_format($format, $target), $date);
		
	}
	
	// новые условия
	if ($convert === 'array') {
		
		// относительное время - сколько дней, часов, минут, секунд (например, 2 дня, 1 час)
		
		$date['data'] = (int) $date['absolute'];
		$date['year'] = floor($date['data'] / TIME_YEAR); $date['data'] = $date['data'] - $date['year'] * TIME_YEAR;
		$date['month'] = floor($date['data'] / TIME_MONTH); $date['data'] = $date['data'] - $date['month'] * TIME_MONTH;
		$date['day'] = floor($date['data'] / TIME_DAY); $date['data'] = $date['data'] - $date['day'] * TIME_DAY;
		$date['hour'] = floor($date['data'] / TIME_HOUR); $date['data'] = $date['data'] - $date['hour'] * TIME_HOUR;
		$date['minute'] = floor($date['data'] / TIME_MINUTE); $date['data'] = $date['data'] - $date['minute'] * TIME_MINUTE;
		$date['second'] = $date['data'];
		unset($date['data']);
		return $date;
	}
	if ($convert === 'absolute') {
		
		// абсолютное время - общее целое число дней, общее целое число часов и т.д. (например, 2 дня, 49 часов)
		
		/*
		1 min = 60 sec
		1 hour = 60 min = 3600 sec
		1 day = 24 hours = 1440 min = 86400 sec
		1 week = 7 days = 168 hours = 10080 min = 604800 sec
		1 month ~ 30 days ~ 720 hours ~ 43200 min ~ 2592000 sec
		1 year = 12 month ~ 365 days ~ 8760 hours ~ 525600 min ~ 31536000 sec
		1 year = 12 month = 365,25 days = 8766 hours = 525960 min = 31557600 sec
		*/
		
		//$date['data'] = 1271602;
		$date['data'] = (int) $date['absolute'];
		$date['year'] = floor($date['data'] / TIME_YEAR);
		$date['month'] = floor($date['data'] / TIME_MONTH);
		$date['day'] = floor($date['data'] / TIME_DAY);
		$date['hour'] = floor($date['data'] / TIME_HOUR);
		$date['minute'] = floor($date['data'] / TIME_MINUTE);
		$date['second'] = $date['data'];
		unset($date['data']);
		return $date;
	}
	// конец новых условий
	
	foreach($rules[3] as $k => $i) {
		
		if (
			!$date[$k] ||
			(int) $date[$k] < 0 ||
			(int) $date[$k] > $i[2] ||
			strlen($date[$k]) > $i[1]
		) {
			$date[$k] = $i[0];
		} elseif (strlen($date[$k]) < $i[1]) {
			$date[$k] = $i[3] . $date[$k];
		}
		
	}
	
	// ДОБАВЛЕНО новое условие - UTC, полезно для мелких дат
	$date['absolute'] = strtotime($date['year'] . ':' . $date['month'] . ':' . $date['day'] . ' ' . $date['hour'] . ':' . $date['minute'] . ':' . $date['second'] . (!empty($utc) ? ' UTC' : null));
	
	/*
	// а здесь - парсим временную зону и делаем смещение по времени, но это на самом деле не нужно
	$date['data'] = [
		(substr(date('P'), 0, 1) === '-') ? false : true,
		(int) substr(date('P'), 1, 2),
		(int) substr(date('P'), 4, 2)
	];
	
	if ($date['data'][0] === true) {
		$date['absolute'] = $date['absolute'] + ($date['data'][1] * 60 + $date['data'][2]) * 60;
	} else {
		$date['absolute'] = $date['absolute'] - ($date['data'][1] * 60 + $date['data'][2]) * 60;
	}
	*/
	
	if ($convert === true) {
		return (int) $date['absolute'];
	}
	
	// преобразование времени в формат $lang -> data['datetime']['format']
	
	preg_match_all('/\{([\w\:]+)?\}/', $convert, $date['data']);
	
	$date['data'][0] = [
		'original' => '',
		'morph' => '',
		'string' => ''
	];
	
	foreach ($date['data'][1] as $i) {
		
		$date['data'][0]['original'] = $i;
		
		// доп.опция - замена пропущенных значений на пустые
		if (strpos($i, '::') !== false) {
			$i = str_replace(':::', ':0:0:', $i);
			$i = str_replace('::', ':0:', $i);
		}
		
		$date['data'][0]['morph'] = datasplit($i, ':');
		
		$i = array_shift($date['data'][0]['morph']);
		$k = array_search($i, $rules[0]);
		
		if (in_array($i, $rules[0])) {
			
			$date['data'][0]['string'] = date($rules[1][$k], $date['absolute']);
			
			if (in_array($i, $rules[2])) {
			//if (count($date['data'][0]['morph'])) {
			// это старый кусок кода, заточенный на то, чтобы выводить дату в текстовом виде
			// автоматом, когда указываются параметры морфинга
			// однако, на самом деле вывод даты в текстовом виде должен быть только при
			// определенных переменных: Y/YY для года, M/MM для месяца, D/DD для дня, W/WW для дня недели
				
				if (
					!isset($date['data'][0]['morph'][2]) ||
					!$date['data'][0]['morph'][2] ||
					!isset($date['data'][0]['morph'][3]) ||
					!$date['data'][0]['morph'][3]
				) {
					
					$date['data'][0]['morph'][3] = $date['data'][0]['morph'][2];
					
					// здесь условия вывода названий месяцев
					// i: yy/mm/d и т.д.
					// $date['data'][0]['string']: 1984/12/21 и т.д.
					if (array_key_exists($i, (array)$lang -> data['datetime'])) {
						$target = $date['data'][0]['string'];
						$date['data'][0]['string'] = $lang -> data['datetime'][$i];
						$date['data'][0]['string'] = $date['data'][0]['string'][$target];
						$date['data'][0]['morph'][2] = false;
					} elseif (substr($i, -1, 1) === 'a') {
						$date['data'][0]['morph'][2] = 'add';
					} else {
						$date['data'][0]['morph'][2] = 'date';
					}
					// конец кода
					
				}
				
				if ($date['data'][0]['morph'][2] === 'date') {
					$date['data'][0]['string'] = datanum(
						$date['data'][0]['string'],
						$date['data'][0]['morph']
					);
				} elseif ($date['data'][0]['morph'][2] === 'add') {
					unset(
						$date['data'][0]['morph'][2],
						$date['data'][0]['morph'][3]
					);
					$date['data'][0]['string'] = datanum(
						$date['data'][0]['string'],
						'add:' . objectToString($date['data'][0]['morph'], ':')
					);
				} else {
					$date['data'][0]['string'] = datamorpher(
						$date['data'][0]['string'],
						$date['data'][0]['morph']
					);
				}
				
				if ($date['data'][0]['morph'][3]) {
					$date['data'][0]['string'] = datalang(
						$date['data'][0]['string'],
						'',
						'',
						$date['data'][0]['morph'][3]
					);
				}
				
			}
			
			$convert = str_replace(
				'{' . $date['data'][0]['original'] .  '}',
				$date['data'][0]['string'],
				$convert
			);
			
		}
		
		unset($k, $i);
		
	}
	
	/*
	// а здесь - просто проверка значений
	print_r($convert);
	echo '<br><hr>';
	print_r($date);
	echo '<hr><br>';
	*/
	
	$date = $convert;
	
	//return objectToString($date);
	return $date;
	
}

function datamorpher($target, $arrOut = false) {

	/*
	*  Функция которая склоняет существительные
	*  на входе нужно указать:
	*    исходное слово
	*    выходные параметы
	*      падеж - i/r/d/v/t/p (им/род/дат/вен/твор/пред)
	*      число и род - e/m (ед.ч. муж. род / множ.ч.) + j/s (ед.ч. жен. род / ед.ч. сред. род), если есть
	*      часть речи - sush/pril (сущ. / прилаг.), если есть
	*      если параметров нет, считается, что это инфинитив:
	*      существительное единственного числа заданного рода
	*      (мужского, если родов несколько) в именительном падеже
	*  
	*  на выходе отдает готовое значение
	*/
	
	if (!$target) {
		return false;
	} elseif (in_array('mbstring', get_loaded_extensions())) {
		$target = mb_strtolower($target); // EDITED FOR NOT PHP MODULE
	}
	
	global $lang;
	$morph = $lang -> data['morph'];
	
	if (!set($morph)) {
		return $target;
	}
	
	if (!$arrOut) {
		$arrOut = [
			false,
			false,
			false
		];
	} elseif (!is_array($arrOut)) {
		$arrOut = datasplit($arrOut, ':');
	}
	
	if (empty($arrOut[0])) { $arrOut[0] = $morph['declension'][0]; }
	
	if (empty($arrOut[1])) {
		$arrOut[1] = 0;
	} elseif (in_array($arrOut[1], $morph['forms'])) {
		$arrOut[1] = array_search($arrOut[1], $morph['forms']);
	} else {
		$arrOut[1] = 1;
	}
	
	if (empty($arrOut[2])) { $arrOut[2] = $morph['parts'][0]; }
	
	global $dictionary;
	
	//echo $target . ' ';
	//print_r($arrOut);
	//echo '<br>';
	
	if (
		array_key_exists($target, $dictionary) &&
		(
			isset($dictionary[$target][$arrOut[0]]) ||
			isset($dictionary[$target][$arrOut[2]])
		)
	) {
		
		if (isset($dictionary[$target][$arrOut[2]][$arrOut[0]])) {
			
			if (isset($dictionary[$target][$arrOut[2]][$arrOut[0]][$arrOut[1]])) {
				return $dictionary[$target][$arrOut[2]][$arrOut[0]][$arrOut[1]];
			} else {
				return $dictionary[$target][$arrOut[2]][$arrOut[0]][0];
			}
			
		} elseif (isset($dictionary[$target][$arrOut[0]][$arrOut[1]])) {
			
			// если искомое слово содержит $arrOut[1], то значит, у него нет форм
			return $dictionary[$target][$arrOut[0]][$arrOut[1]];
			
		} else {
			
			// иначе считаем, что форм слова нет и возвращаем инфинитив
			return $dictionary[$target][$arrOut[0]][0];
			
		}
		
	} else {
		return $target;
	}
	
}

function dataprint($item, $clear = false, $return = false) {
	
	/*
	*  Функция которая выводит данные на экран с преобразованием переменных
	*  на входе нужно указать:
	*    исходный текст
	*    параметры очистки (необязательно) - см. clear
	*    по-умолчанию удаляет все запрещенные теги, а у остальных чистит все свойства
	*    третий параметр - необязательный, он служит для возврата получившегося преобразования
	*    однако третий параметр будет некорректно работать с переменными module и page
	*  
	*  переменные заключаются в фигурные скобки
	*  параметры внутри разделяются двоеточиями и запятыми:
	*  {var:param,param:param}
	*  
	*  переменные, которые принимаются на вход:
	*    module - выводит в заданном месте модуль
	*      параметры:
	*      - название модуля
	*      - параметр модуля
	*      - шаблон модуля (необязательно)
	*      - доп.параметр (необязательно)
	*      например:
	*      {module:articles,param,template:all}
	*      {module:articles,param}
	*      подробнее см. функцию module
	*    lang - выводит текст из языкового объекта
	*      параметры:
	*      - объект
	*      - раздел
	*      - морфинг (необязательно)
	*      - преобразование (необязательно)
	*      например:
	*      {lang:phone,0:information::u}
	*      {lang:title}
	*      подробнее см. функцию datalang
	*    link - выводит относительную ссылку
	*      параметры:
	*      - страницы сайта через слеш (/) или лучше через запятую (,)
	*      - имя ссылки
	*      - классы
	*      например:
	*      {link:documents/about:о нас:button}
	*      {link:documents,about:о нас:button}
	*      если хотите указать внешнюю ссылку, то начните ее через двойной слеш (//),
	*      но в этом случае разделение через запятые работать не будет:
	*      {link://www.site.com:о нас:button}
	*    page - загружает страницу
	*      параметры:
	*      - адрес страницы с папками через двойной обратный слеш (\\) или лучше через запятую (,)
	*      - тип страницы
	*      например:
	*      {page:pages,rightbox:html}
	*      {page:pages\\rightbox:html}
	*      {page:cookies:item}
	*      подробнее см. функцию page
	*    var - выводит одну из указанных переменных
	*      - site - адрес сайта без http
	*      - url - адрес сайта с http
	*      - path - адрес страницы сайта, с которой поступил запрос
	*      - lang - текущий язык сайта
	*      - langcode - текущий языковой код сайта
	*      - ip - текущий ip-адрес пользователя
	*      например:
	*      {var:site}
	*      если вы хотите получить, скажем, полный путь, используйте сочетание:
	*      {var:url}{var:path}
	*  
	*  на выходе ничего не отдает, вместо этого печатает на экран готовое значение
	*/
	
	if (!$clear) { $clear = 'tags cleartags leavespaces'; }
	$var = [];
	$var['return'] = '';
	
	if (preg_match_all("/\{.+?\}/", $item, $var['matches'])) {
		
		$var['text'] = preg_split("/\{(.+?)\}/", $item);
		
		foreach ($var['matches'][0] as &$var['item']) {
			
			$var['item'] = substr($var['item'], 1, -1);
			$var['item'] = preg_split("/\:/", $var['item']);
			
			if ($var['item'][0] === 'module') {
				
				$var['item'][1] = preg_split("/\,/", $var['item'][1]);
				if (empty($var['item'][2])) { $var['item'][2] = false; }
				
			} elseif ($var['item'][0] === 'page') {
				
				$var['item'][1] = str_replace(',', '\\', $var['item'][1]);
				
			} elseif ($var['item'][0] === 'var' && !empty($var['item'][1])) {
				
				if ($var['item'][1] === 'site') {
					$var['item'][1] = $_SERVER['SERVER_NAME'];
				} elseif ($var['item'][1] === 'path') {
					$var['item'][1] = $_SERVER['REQUEST_URI'];
				} elseif ($var['item'][1] === 'url') {
					$var['item'][1] = (((isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] === 'https') || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')) ? 'https' : 'http') . '://' . $_SERVER['SERVER_NAME'] . (($_SERVER['SERVER_PORT'] != 80) ? ':' . $_SERVER['SERVER_PORT'] : '');
				} elseif ($var['item'][1] === 'ip') {
					$var['item'][1] = ip();
				} elseif ($var['item'][1] === 'lang') {
					if (!empty($lang -> lang)) {
						$var['item'][1] = $lang -> lang;
					} elseif (!empty($currlang)) {
						$var['item'][1] = $currlang;
					} else {
						$var['item'][1]= '';
					}
				} elseif ($var['item'][1] === 'langcode') {
					if (!empty($lang -> code)) {
						$var['item'][1] = $lang -> code;
					} elseif (!empty($currlang)) {
						$var['item'][1] = mb_strtoupper($currlang);
					} else {
						$var['item'][1]= '';
					}
				}
				
			} elseif ($var['item'][0] === 'lang') {
				
				if (empty($var['item'][4])) { $var['item'][4] = false; }
				if (empty($var['item'][3])) { $var['item'][3] = false; }
				if (empty($var['item'][2])) { $var['item'][2] = false; }
				
				if (strpos($var['item'][1], ',') !== false) {
					
					$var['item'][1] = explode(',', $var['item'][1]);
					
					$var['item'][1][0] = datalang(
						$var['item'][1][0],
						$var['item'][2],
						$var['item'][3],
						$var['item'][4]
					);
					
					if (!is_array($var['item'][1][0])) {
						$var['item'][1][0] = objectConvert($var['item'][1][0]);
					}
					
					if (isset($var['item'][1][0][$var['item'][1][1]])) {
						$var['item'][1] = $var['item'][1][0][$var['item'][1][1]];
					}
					
				} else {
					$var['item'][1] = datalang(
						$var['item'][1],
						$var['item'][2],
						$var['item'][3],
						$var['item'][4]
					);
				}
				
			} elseif (
				$var['item'][0] === 'link' &&
				!empty($var['item'][2])
			) {
				
				if (strpos($var['item'][1], ',') !== false) {
					$var['item'][1] = str_replace(',', '/', $var['item'][1]);
				}
				
				$var['item'][1] = htmlspecialchars_decode(clear($var['item'][1], 'simpleurl'));
				
				if (
					$var['item'][1][0] === '/' &&
					$var['item'][1][1] === '/'
				) {
					$var['item'][1] = '<a href="' . $var['item'][1] . '"' . (!empty($var['item'][3]) ? ' class="' . $var['item'][3] . '"' : '') . '>' . $var['item'][2] . '</a>';
				} else {
					if ($var['item'][1][0] === '/') {
						$var['item'][1] = substr($var['item'][1], 1);
					}
					//$var['item'][1] = str_replace(',', '/', $var['item'][1]);
					//global $template;
					$var['item'][1] = '<a href="/' . $var['item'][1] . '"' . (!empty($var['item'][3]) ? ' class="' . $var['item'][3] . '"' : '') . '>' . $var['item'][2] . '</a>';
				}
				
				
			}
			
		}
		unset($var['item']);
		
		foreach ($var['text'] as $var['key'] => $var['item']) {
			
			if ($return) {
				$var['return'] .= clear($var['item'], $clear);
			} else {
				echo clear($var['item'], $clear);
			}
			
			$var['add'] = $var['matches'][0][$var['key']];
			
			if ($var['add'][0] === 'module' && !$return) {
				module($var['add'][1], $var['add'][2]);
			} elseif ($var['add'][0] === 'page' && !$return) {
				page($var['add'][1], $var['add'][2], false);
			} elseif ($return) {
				$var['return'] .= $var['add'][1];
			} else {
				echo $var['add'][1];
			}
			
		}
		
		unset(
			$var['key'],
			$var['item'],
			$var['add'],
			$var['matches']
		);
		
	} else {
		
		if ($return) {
			$var['return'] .= clear($item, $clear);
		} else {
			echo clear($item, $clear);
		}
		
	}
	
	if ($return) {
		return $var['return'];
	}
	
}

?>