<?php defined('isCMS') or die;

if (!function_exists('moduleFormOptionsGenerator')) {
	function moduleFormOptionsGenerator($target = false) {
		
		global $lang;
		
		if (
			!empty($target) &&
			is_string($target)
		) {
			if (strpos($target, ':')) {
				
				$options = dataParse($target);
				
				$call = array_shift($options);
				$param = array_shift($options);
				$subparam = array_shift($options);
				$title = array_shift($options);
				$include = array_shift($options);
				
				$target = [];
				
				if ($call === 'structure') {
					
					$call = objectGet('structure', 'structure');
					$c = 0;
					moduleFormStructureGenerator($call, $target, $c, $param);
					
				} elseif ($call === 'content') {
					
					$call = dbUse('content', 'select', ['allow' => 'parent:' . $param, 'deny' => 'type:settings']);
					
					foreach ($call as $i) {
						
						if (
							empty($include) ||
							(!empty($include) && $i['name'] === $include)
						) {
							if (!empty($title) && !empty($i['data'][$title])) {
								if (!empty($subparam) && !empty($i['data'][$subparam])) {
									$target[$i['data'][$subparam]] = $i['data'][$title];
									// числа не принимаются корректно, потому что с числами далее массив считается не ассоциативным
									// и заново там сортируется и преобразуется не сохраняя ключи, преобразование числа в строку не дает результата
									// в общем, мы делаем ключ строкой изначально и дальше его нужно формировать из поля + значение (например id1)
									// в остальном здесь та же ситуация, что и со структурой выше
								} else {
									$target['id_' . $i['id']] = $i['data'][$title];
									// здесь та же ситуация, что и выше
								}
							}
						}
						
					}
					unset($i);
					
				} elseif ($call === 'numeric') {
					
					$format = strpos($title, '.') !== false ? strlen(substr($title, strpos($title, '.') + 1)) : null;
					
					if (
						is_numeric($param) &&
						is_numeric($subparam) &&
						is_numeric($title) &&
						$param < $subparam &&
						$title > 0
					) {
						$c = 0;
						while ($param <= $subparam) {
							$target['id_' . $c] = !empty($format) || !empty($include) ? number_format($param, $format, '.', !empty($include) ? ' ' : null) : $param;
							$param += $title;
							$c++;
						}
					}
					
				} elseif ($call === 'datetime') {
					
					$include = str_replace(['(', ')', '{dot}', '{space}', '{double}'], ['{', '}', '.', ' ', ':'], $include);
					$param = datadatetime($param, $include, true);
					$subparam = datadatetime($subparam, $include, true);
					$title = datadatetime($title, $include, true, true);
					
					if (
						is_numeric($param) &&
						is_numeric($subparam) &&
						is_numeric($title) &&
						$param < $subparam &&
						$title > 0
					) {
						$c = 0;
						while ($param <= $subparam) {
							$target['id_' . $c] = datadatetime(
								$param,
								str_replace(
									['{dot}', '{space}', '{double}'],
									['.', ' ', ':'],
									$include
								)
							);
							$param += $title;
							$c++;
						}
					}
					
				}
				
			} else {
				$target = [];
			}
			
			ksort($target, SORT_NATURAL);
			return $target;
			//return (object) $target;
			
		} else {
			return $target;
		}
		
	}
}

if (!function_exists('moduleFormStructureGenerator')) {
	function moduleFormStructureGenerator($arr, &$target, &$c, $param = null) {
		
		if (objectIs($arr)) {
			foreach ($arr as $k => $i) {
				
				$k = dataParse($k);
				$k = $k[1];
				
				if (empty($param)) {
					$target['item_' . $c] = !empty(lang('menu:' . $k)) ? lang('menu:' . $k) : $k;
					$c++;
				} elseif (
					$k === $param &&
					objectIs($i)
				) {
					moduleFormStructureGenerator($i, $target, $c, $subparam);
				}
				
			}
		}
		
	}
}

if (!function_exists('moduleFormGenerate')) {
	function moduleFormGenerate($target, $module) {
		
		//global $module;
		//в глобальной области видимости объект module возникает, видимо еще до обработки - с базовыми параметрами и опциями,
		//но без путей, состояний и прочего
		//сначала я использовал только путь, но затем я подумал, что в дальнейшем возможны накладки из-за обработки исходных данных модуля
		//и поэтому решил передавать объект целиком
		//но так кушается память
		//возможно, введение namespaces исправит эту ситуацию
		
		//echo '[' . print_r($module -> path, 1) . ']<br>';
		//echo '[' . print_r(PATH_ASSETS . 'modules' . DS . $module -> name, 1) . ']<br>';
		
		$item = $module -> settings['form'];
		$item = $item[$target];
		
		if (file_exists(PATH_ASSETS . 'modules' . DS . $module -> name . DS . $module -> param . '_field.php')) {
			require PATH_ASSETS . 'modules' . DS . $module -> name . DS . $module -> param . '_field.php';
		} else {
			require $module -> elements . 'field.php';
		}
		
		unset($module);
		
	}
}

if (!function_exists('moduleFormValidate')) {
	function moduleFormValidate(&$target, &$item, &$errors, &$message, &$captcha) {
		
		if (is_array($target)) {
			foreach($target as &$i) {
				moduleFormValidate($i, $item, $errors, $message, $captcha);
			}
		} else {
			
			$target = trim($target);
			
			if (!empty($item['filter'])) {
				$item['filter'] = htmlentities($item['filter']);
				$filter = datasplit($item['filter'], ' ');
				$filter = '(' . objectToString($filter, ')|(') . ')';
				$item['filter'] = preg_match('/' . $filter . '/ui', $target);
				unset($filter);
			}
			
			if (!empty($item['clear'])) {
				$target = clear($target, $item['clear']);
			}
			
			if (
				(
					!empty($item['verify']) &&
					$item['verify'] !== 'captcha' &&
					$item['verify'] !== 'checked' &&
					(!empty($item['required']) || set($target)) &&
					!clear(
						$target,
						$item['verify'],
						true,
						[
							'minlen' => set($item['minlen'], true),
							'maxlen' => set($item['maxlen'], true),
							'minnum' => set($item['minnum'], true),
							'maxnum' => set($item['maxnum'], true)
						]
					)
				) || (
					!empty($item['verify']) &&
					$item['verify'] === 'captcha' &&
					(empty($captcha) || (string) $captcha !== mb_strtolower($target))
				) || (
					!empty($item['verify']) &&
					$item['verify'] === 'checked' &&
					$target !== true && $target !== 1 && $target !== '1'
				) || (
					!empty($item['required']) &&
					!set($target)
				) || (
					!empty($item['filter'])
				)
			) {
				$errors[] = $item['name'];
			} elseif (empty($item['nosend'])) {
				
				$target = htmlentities($target);
				$label = !empty($item['message']) ? $item['message'] : $item['default'];
				
				if (
					empty($target) &&
					!empty($item['novalue'])
				) {
					$message[$label] = htmlentities($item['novalue']);
				} elseif (!empty($item['options'])) {
					$message[$label] = $item['options'][$target];
				} else {
					$message[$label] = $target;
				}
				
				unset($label);
				
			} else {
				$target = htmlentities($target);
			}
			
		}
		
	}
}

?>