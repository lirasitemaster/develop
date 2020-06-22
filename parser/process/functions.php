<?php defined('isCMS') or die;

if (!function_exists('url_get_contents')) {
	function url_get_contents($url, $useragent='cURL', $headers=false, $follow_redirects=true, $debug=false) {

		// initialise the CURL library
		$ch = curl_init();

		// specify the URL to be retrieved
		curl_setopt($ch, CURLOPT_URL,$url);

		// we want to get the contents of the URL and store it in a variable
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

		// specify the useragent: this is a required courtesy to site owners
		curl_setopt($ch, CURLOPT_USERAGENT, $useragent);

		// ignore SSL errors
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		// return headers as requested
		if ($headers==true){
			curl_setopt($ch, CURLOPT_HEADER,1);
		}

		// only return headers
		if ($headers=='headers only') {
			curl_setopt($ch, CURLOPT_NOBODY ,1);
		}

		// follow redirects - note this is disabled by default in most PHP installs from 4.4.4 up
		if ($follow_redirects==true) {
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
		}

		// if debugging, return an array with CURL debug info and the URL contents
		if ($debug==true) {
			$result['contents']=curl_exec($ch);
			$result['info']=curl_getinfo($ch);
		}

		// otherwise just return the contents as a variable
		else $result=curl_exec($ch);

		// free resources
		curl_close($ch);

		// send back the data
		return $result;
		
	}
}

if (!function_exists('moduleExtractClear')) {
	function moduleExtractClear($target, $item) {
		
		$return = [];
		
		// задаем разрешение на запись по-умолчанию
		$y = true;
		
		foreach ($target as $k => $i) {
			
			// базовые настройки
			$i -> start = str_replace(['[tag:open]', '[tag:close]'], ['<', '>'], $i -> start);
			$i -> end = str_replace(['[tag:open]', '[tag:close]'], ['<', '>'], $i -> end);
			$q = '/.*' . $i -> start . '(.*?)' . $i -> end . '.*/i';
			//echo htmlentities($q) . '<br>';
			$str = '';
			
			// проверяем, есть ли в элементе нужный запрос
			if (preg_match('/' . $i -> start . '/', $item)) {
				$str = preg_replace($q, '$1', $item);
			}
			
			// чистим элемент
			if (!empty($i -> clear)) {
				$str = clear($str, $i -> clear);
				//echo $str;
				//echo $i -> clear . '<br>';
			}
			
			// отключаем разрешение на запись, если есть фильтры и значение попадает под один из них
			if (!empty($i -> filter)) {
				
				/*
				// один вариант - через stripos/mb_stripos
				$ext = in_array('mbstring', get_loaded_extensions()); // EDITED FOR NOT PHP MODULE
				
				if (is_array($i -> filter)) {
					foreach ($i -> filter as $filter) {
						if (
							($ext && mb_stripos($filter, $str) !== false) ||
							(!$ext && stripos($filter, $str) !== false) ||
							(!$str && $filter === true)
						) {
							$y = false;
						}
					}
				} elseif (
					($ext && mb_stripos($i -> filter, $str) !== false) ||
					(!$ext && stripos($i -> filter, $str) !== false) ||
					(!$str && $i -> filter === true)
					// ВОТ ЭТО УСЛОВИЕ МЫ ДОБАВИЛИ !!!!!!!!!!!!!!!!!!!!!!!!!!!!! <<<<<<<<<<<<<<<<------------==========
					// ЧТОБЫ МОЖНО БЫЛО ФИЛЬТРОВАТЬ ПО ПУСТЫМ СТРОКАМ
				) {
					$y = false;
				}
				*/
				
				// другой вариант - через preg_match:
				$i -> filter = htmlentities($i -> filter);
				$filter = datasplit($i -> filter, ' ');
				$filter = '(' . objectToString($filter, $splitter = ')|(') . ')';
				if (
					preg_match('/' . $filter . '/', $str) ||
					(!$str && $i -> filter === true)
				) {
					$y = false;
				}
				
			}
			
			// записываем в массив данных в зависимости от разрешения на запись
			if (!empty($y)) {
				$return[$k] = $str;
			} else {
				unset($return);
			}
			
			unset($str);
			
		}
		
		unset($k, $i);
		
		return $return;
		
	}
}

?>