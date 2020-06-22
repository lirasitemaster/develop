<?php defined('isENGINE') or die;

if (!function_exists('funcModuleMenu_Custom')) {
	function funcModuleMenu_Custom($structure, $settings, &$result) {
		
		/*
		*  Функция, которая создает структуру по кастомным настройкам
		*  основное значение этой функции - рекурсивный вызов для вложенных элементов
		*  
		*  можно было бы пробегать по кастомному меню,
		*  но здесь структура пробегается всего один раз
		*  и поэтому функция меньше тратит ресурсы сервера
		*  
		*  раздельные if вместо if-else позволяют включать одинаковые элементы с разными параметрами
		*  например, documents и documents:list
		*/
		
		foreach ($structure as $key => $item) {
			
			$custom = dataParse($key);
			
			//print_r($custom[1]);
			//echo '[' . print_r($settings, true) . ']<br>';
			//echo '[' . print_r($settings[$custom[1]], true) . ']<br>';
			
			if (in_array($custom[1], $settings)) {
				
				$result[$custom[1]] = [
					$key,
					null
				];
				
			}
			
			if (in_array($custom[1] . ':list', $settings) && is_array($item)) {
				
				$result[$custom[1] . ':list'] = [
					'list',
					[]
				];
				
				foreach ($item as $k => $i) {
					$result[$custom[1] . ':list'][1][$k] = null;
				}
				
			}
			
			if (in_array($custom[1] . ':open', $settings) && is_array($item)) {

				$result[$custom[1] . ':open'] = [
					$key,
					$item
				];
				
			}
			
			if (is_array($item)) {
				funcModuleMenu_Custom($item, $settings, $result);
			}
			
		}
		
	}
}

if (!function_exists('funcModuleMenu_Generator')) {
	function funcModuleMenu_Generator($settings) {
		
		/*
		*  Функция, которая создает структуру из вложенных материалов
		*/
		
		// если в настройках был указан генератор,
		// подготавливаем вложения в структуру из материалов
		
		// почему нельзя сделать генератор на уровне структуры, а приходится его использовать только в меню
		// во-первых, нужно как-то задавать параметры генератора в структуре
		// во-вторых, в структуре невозможно хранить языковые данные материалов
		// в-третьих, вывод материалов в разных меню может быть разным
		// если как-то решить эти три проблемы, то генератор можно и перенести
		// но это будет скорее всего не в структуре, а в подготовке шаблона, там же, где чтение контента
		
		$result = [];
		
		foreach ($settings as $key => $item) {
			
			// парсим каждый элемент генератора
			// загружаем все материалы заданного родителя
			// устанавливаем счетчик на ноль
			
			$item = dataParse($item);
			$data = dbUse('content', 'select', ['allow' => 'parent:' . $key, 'return' => 'name:data']);
			$c = 0;
			
			if (empty($data)) {
				continue;
			}
			
			// парсим каждый материал
			
			foreach ($data as $k => $i) {
				
				$c++;
				
				// останавливаем перебор, если число материалов превышает заданное
				// но только в том случае, если нет фильтра и нет сортировки
				
				if (empty($item[2]) && empty($item[3]) && !empty($item[1]) && $c > $item[1]) {
					break;
				}
				
				// пропускаем текущий материал, если он соответствует фильтру
				
				if (!empty($item[2]) && empty($i[$item[2]])) {
					continue;
				}
				
				// читаем имя материала согласно настройке генератора
				// и добавляем его в список
				
				$name = $i[$item[0]];
				if (is_array($name) || is_object($name)) {
					objectLang($name);
				}
				$result[$key][$k] = clear($name, 'notagsspaced tospaces');
				
				unset($name);
				
			}
			
			// работа с текущим списком материалов
			// HEY-HEEEY! А нельзя ли сортировать прямо в запросе в базу данных???
			
			if (!empty($result[$key]) && is_array($result[$key])) {
				
				// если заданы параметры сортировки, то сортируем список материалов
				
				if (
					!empty($item[3]) && is_string($item[3]) &&
					!empty($item[4]) && is_string($item[4])
				) {
					if ($item[3] === 'key') {
						if ($item[4] === 'asc') {
							ksort($result[$key], SORT_NATURAL);
						} elseif ($item[4] === 'desc') {
							krsort($result[$key], SORT_NATURAL);
						}
					} else {
						if ($item[4] === 'asc') {
							asort($result[$key], SORT_NATURAL);
						} elseif ($item[4] === 'desc') {
							arsort($result[$key], SORT_NATURAL);
						}
					}
				}
				
				// если задан лимит материалов, а число материалов больше,
				// то обрезаем материалы
				
				if (!empty($item[1]) && count($result[$key]) > $item[1]) {
					$result[$key] = array_slice($result[$key], 0, $item[1]);
				}
				
			}
			
			unset($data, $k, $i, $key, $item, $с);
		}
		
		return $result;
		
	}
}

if (!function_exists('funcModuleMenu_Create')) {
	function funcModuleMenu_Create($data, $module, $counter = null) {
		
		/*
		*  Функция, которая создает меню
		*  
		*  Это рекурсивная функция, которая перебирает структуру или заданную ее часть,
		*  разбирает ее ключи и согласно разным проверкам меняет настройки текущего элемента меню
		*  а затем идет вызов элемента меню
		*  
		*  Основная задача этой функции - пропускать уровни вложенности, раскрывать группы
		*  и добавлять пункты, если был вызван генератор материалов
		*/
		
		if (empty($data) || !is_array($data)) {
			return false;
		}
		
		$first = true;
		$counter++;
		
		foreach ($data as $key => $item) {
			
			if (empty($key)) {
				continue;
			}
			
			//print_r($key);
			
			$element = array_combine(['id', 'name', 'type', 'value', 'template', 'level'], dataParse($key));
			
			if (
				!empty($module -> settings['levels']) && $element['level'] > $module -> settings['levels'] ||
				!empty($module -> settings['disable']) && is_array($module -> settings['disable']) && in_array($element['name'], $module -> settings['disable'])
			) {
				continue;
			} elseif ($element['type'] === 'group') {
				funcModuleMenu_Create($item, $module, $counter);
			} else {
				
				if (
					$element['type'] === 'content' &&
					!empty($module -> settings['generator']) && is_array($module -> settings['generator']) &&
					!empty($module -> data[$element['name']]) && is_array($module -> data[$element['name']]) &&
					array_key_exists($element['name'], $module -> settings['generator'])
				) {
					
					//echo '<br>[content will be generated!]<br>' . print_r($module -> data[$element['name']], true) . '<br>';
					
					// сейчас главное - сделать массив, куда записать параметры генератора
					// нужно указать, что мы хотим видеть в меню, хотя на данный момент это - только заголовок,
					// но в будущем, возможно, также заголовок, дескрипшн и картинка
					// затем записать путь, вложенный по имени материала относительно текущего пути элемента
					// затем установить проверку в выводе имени, причем в нужной локали
					// другое главное - сделать $item массивом, куда записать эти данные
					
					unset($item);
					$item = [];
					
					foreach ($module -> data[$element['name']] as $k => $i) {
						//echo '[' . $k . ' : ' . $i . ']<br>';
						$item[ $element['id'] . '.' . $element['name'] . ':' . $k . ':generated:' . $element['value'] . $k . '.::' . ($element['level'] + 1) ] = null;
					}
					unset($k, $i);
					
				}
				
				global $template;
				global $content;
				global $uri;
				
				$type = is_array($item) ? 'submenu' : ($element['level'] === 1 ? 'item' : 'subitem');
				
				require $module -> elements . 'link.php';
				
				//echo '<section></section>';
				
			}
			
		}
		
		unset($item, $key, $element, $type, $first, $data);
		
	}
}

?>