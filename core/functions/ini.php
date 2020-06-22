<?php defined('isENGINE') or die;

/* ФУНКЦИИ ПО ОБРАБОТКЕ INI-ФАЙЛОВ */ 

function iniPrepareJson($data, $format = false, $skip = true){
	
	/*
	*  Функция обработки данных в формате json
	*  на входе нужно указать данные в виде строковой переменной
	*  
	*  функция примет данные и
	*    переведет их в массив, если второй параметр $format задан true/structure/content
	*    переведет их в объект, если второй параметр $format задан false или не задан
	*/
	
	if (empty($data)) {
		return null;
	}
	
	if ($format === 'structure') {
		$data = str_replace(['[', ']'], ['{','}'], $data);
		$data = preg_replace('/(["\}])(\s+\")/u', '$1,$2', $data);
		$data = preg_replace_callback(
			'/(\"[\w:\-_.]+\"[^ :],?)/u', 
			function ($matches, $i=0) {
				static $i;
				return '"' . ++$i . '" : ' . $matches[1];
			},
			$data
		);
	} elseif ($format === 'content') {
		$data = preg_replace('/([\"\'])\s{2,}/u', '$1 ', $data);
		$data = preg_replace('/\s{2,}([\"\'])/u', ' $1', $data);
		$data = htmlspecialchars($data, ENT_NOQUOTES);
		$data = clear($data, 'tags tospaces');
		if (in_array('mbstring', get_loaded_extensions())) {
			$data = mb_convert_encoding($data, 'UTF-8', mb_detect_encoding($data));
		}
	}
	
	// clear comments [//...]
	$data = preg_replace('/([^\:\"\'])\s*?\/\/.*?([$\r\n])/u', '$1$2', $data);
	// clear line breaks
	//$data = preg_replace('/\r\n|\r|\n/u', '', $data);
	$data = preg_replace('/\r\n\s*|\r\s*|\n\s*/u', '', $data);
	// clear comments [/*...*/]
	$data = preg_replace('/\/\*.*?\*\//u', '$1', $data);
	//$data = preg_replace('/\[\s*\]/u', '[""]', $data);
	$data = preg_replace('/\[\s*\]/u', '[]', $data);
	
	$data = clear($data);
	
	if ($format) {
		$data = json_decode($data, true);
		if ($format === 'content') {
			objectLang($data);
		}
	} else {
		$data = json_decode($data);
	}
	
	if ($skip && objectIs($data)) {
		iniPrepareJson_into($data);
	}
	
	unset($format);
	
	return $data;
	
}

function iniPrepareJson_into(&$data) {
	
	/*
	*  Дочерняя рекурсивная функция обработки ключей массива
	*  Необходима для работы функции iniPrepareJson
	*/
	
	foreach ($data as $key => &$item) {
		if (
			mb_strpos($key, '!') === 0 ||
			!is_array($item) && strpos($item, '!') === 0
		) {
			unset($data[$key]);
		} elseif (is_array($item) || is_object($item)) {
			iniPrepareJson_into($item);
		}
	}
	
}

function iniPrepareArray($data, $format = false) {
	
	/*
	*  Функция перевода данных из массива в формат json
	*  на входе нужно указать данные в виде массива
	*  
	*  функция примет его и переведет в формат json
	*/
	
	// новая версия
	
	$data = json_encode($data, $format ? JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE : JSON_UNESCAPED_UNICODE);
	
	return $data;
	
}

function iniPrepareForm($arr) {
	
	/*
	*  Функция перевода данных из формы, полученных методом get или post
	*  в многоуровневый массив
	*  
	*  на входе нужно указать данные в виде специального одноуровневого массива
	*  каждый ключ в нем может содержать имена родительских элементов, разделенные двоеточием
	*  функция примет его и переведет в многоуровневый массив
	*  
	*  функция работает с помощью дочерней рекурсивной функции
	*  
	*  Например такой массив:
	*  [ "grandparent:parent:key" => "value" ]
	*  Будет преобразован в такой:
	*  [ "grandparent" => [ "parent" => [ "key" => "value" ] ] ]
	*  
	*  Одноуровневый массив задается функцией iniBuildForm
	*  Многоуровневый массив нужен для правильного преобразования в формат json функцией iniPrepareArray
	*  
	*  на выходе получается готовый многоуровневый массив
	*/
	
	$out = [];
	
	foreach ($arr as $key => $item) {
		$key = explode(':', $key);
		$count = count($key);
		iniPrepareForm_into($out, $key, $item, 0, $count);
	}
	
	return $out;
	
}

function iniPrepareForm_into(&$out, $key, $item, $index, $count) {
	
	/*
	*  Дочерняя рекурсивная функция обработки ключей массива
	*  Необходима для работы функции iniPrepareForm
	*/
	
	if ($index < $count - 1) {
		
		if (!isset($out[$key[$index]])) {
			$out[$key[$index]] = [];
		}
		
		$arr = &$out[$key[$index]];
		$index++;
		
		iniPrepareForm_into($arr, $key, $item, $index, $count);
		
	} elseif ($index == $count - 1) {
		
		if (!isset($out[$key[$index]])) {
			$out[$key[$index]] = $item;
		}
		
	}
	
}

function iniMerge($arr, $json) {
	
	/*
	*  Функция добавления данных из одноуровневого массива в исходные данные в формате json
	*  
	*  на входе нужно указать данные в виде специального одноуровневого массива
	*  каждый ключ в нем может содержать имена родительских элементов, разделенные двоеточием
	*  как правило, это массив из формы, полученной методом get или post
	*  функция примет его и переведет в многоуровневый массив
	*  
	*  затем она считает исходные данные в формате json, переведет их в многоуровневый массив
	*  после чего сделает слияние этих двух массивов в один
	*  и в конце переведет его обратно в формат json
	*  
	*  по-сути это просто надстройка, автоматизирующая работу предыдущих функций
	*  
	*  на выходе получается готовая строка в формате json
	*/
	
	if (
		empty($arr) ||
		empty($json) ||
		(
			!is_object($arr) &&
			!is_array($arr)
		) ||
		(
			!is_object($json) &&
			!is_array($json) &&
			!is_string($json)
		)
	) {
		return false;
	}
	
	if (is_object($arr)) {
		$arr = objectConvert($arr);
	}
	
	if (
		is_object($json) ||
		is_array($json)
	) {
		$json = json_encode($json);
	}
	
	$arr = iniPrepareForm($arr);
	$json = iniPrepareJson($json, true);
	
	$out = objectMerge($json, $arr, 'replace');
	$out = iniPrepareArray($out);
	
	return $out;
	
}

function iniBuildForm($arr, $prefix = 'data', $parent = null, $settings = null) {
	
	/*
	*  Функция построения формы из массива настроек
	*  на входе нужно указать массив и префикс
	*  
	*  массив должен быть подготовлен функциями iniPrepareJson или iniPrepareForm
	*  префикс задает ключ массива get или post, куда будут получены данные из формы
	*  префикс может быть задан любой, но по-умолчанию это 'data'
	*  
	*  функция отдает строку в виде формы
	*/
	
	if (empty($prefix)) {
		return false;
	}
	
	if (empty($settings)) {
		$settings = '{
			"elements" : {
				"ul" : {
					"home" : "ul",
					"inner" : "ul",
					"array" : "ul"
				},
				"li" : {
					"home" : "li",
					"inner" : "li",
					"array" : "li"
				}
			},
			"classes" : {
				"ul" : {
					"home" : "",
					"inner" : "",
					"array" : ""
				},
				"li" : {
					"home" : "first",
					"inner" : "inner",
					"array" : ""
				}
			}
		}';
	}
	
	$settings = json_decode($settings);
	
	$out = '';
	
	if (!$parent) {
		$out .= '<div class="accordion" id="accordion_' . $prefix . '">';
	} else {
		$out .= '<ul>';
	}
	
	if (empty($arr)) {
		
		$out .= '<div class="card">';
		
		$out .= '<input name="data[' . $prefix . '][' . set($parent, $parent . ':') . '0]" value="">';
		
		$out .= '</div>';
		
	} else {
		
		foreach ($arr as $key => $item) {
			
			if (!$parent) {
				$out .= '<div class="card">';
			} else {
				$out .= '<li>';
			}
			
			if (!is_numeric($key)) {
				
				if (!$key) {
					$key = 'none';
				}
				
				if (!$parent) {
					$out .= '<div class="card-header" id="head_' . $prefix . '_' . $key . '">';
					$out .= '<h5 class="m-0">';
					$out .= '<button class="btn btn-link p-0 m-0 collapsed" type="button" data-toggle="collapse" data-target="#collapse_' . $prefix . '_' . $key . '" aria-expanded="false" aria-controls="collapse_' . $prefix . '_' . $key . '">';
					$out .= $key;
					$out .= '</button>';
					$out .= '</h5>';
					$out .= '</div>';
				} else {
					$out .= '<span>' . $key . '</span>';
				}
				
				/*
				if (is_array($item)) {
					$out .= '<span class="title">' . $key . '</span>';
				} else {
					$out .= '<span>' . $key . '</span>';
				}
				*/
			}
			
			if (!$parent) {
				$out .= '<div id="collapse_' . $prefix . '_' . $key . '" class="collapse" aria-labelledby="head_' . $prefix . '_' . $key . '" data-parent="#accordion_' . $prefix . '">';
				$out .= '<div class="card-body">';
			}
			
			if (is_array($item)) {
				$out .= iniBuildForm($item, $prefix, (!empty($parent) ? $parent . ':' . $key : $key));
			} else {
				$out .= '<input name="data[' . $prefix . '][' . set($parent, $parent . ':') . $key . ']" value="' . $item . '">';
			}
			
			if (!$parent) {
				$out .= '</div>';
				$out .= '</div>';
			}
			
			if (!$parent) {
				$out .= '</div>';
			} else {
				$out .= '</li>';
			}
			
		}
		
	}
	
	if (!$parent) {
		$out .= '</div>';
	} else {
		$out .= '</ul>';
	}
	
	return $out;
	
}

function iniBuildBlock($arr, $prefix = 'data', $keys = null) {
	
	/*
	*  Функция выборки одного ключа из массива настроек
	*  и построения для нее блока изменения значения
	*  на входе нужно указать массив, префикс и ключ
	*  
	*  массив должен быть подготовлен функциями iniPrepareJson или iniPrepareForm
	*  префикс задает ключ массива get или post, куда будут получены данные из формы
	*  префикс может быть задан любой, но по-умолчанию это 'data'
	*  
	*  ключ 
	*  
	*  функция отдает строку в виде формы
	*/
	
	$json = json_encode($arr);
	$out = '';
	
	if (!$keys) {
		$out = iniBuildForm($arr, $prefix, null);
	} elseif (is_string($keys)) {
		$keys = json_decode($keys, true);
	} elseif (is_object($keys)) {
		$keys = json_decode(json_encode($keys), true);	
	} elseif ($keys === true) {
		
		foreach ($arr as $key => $item) {
			$out .= iniBuildBlock($item, $prefix, $key);
		}
		
	} else {
		
		$out = objectExtract($arr, $keys);
		$keys = objectToString($keys, $splitter = ':');
		
		if (is_array($out)) {
			$out = iniBuildForm($out, $prefix, $keys);
		} else {
			$out = '<input name="data[' . $prefix . '][' . $keys . ']" value="' . $out . '">';
		}
		
	}
	
	$out .= '<input type="hidden" name="data[json]" value="' . htmlentities($json) . '" />';
	return $out;
	
}

?>