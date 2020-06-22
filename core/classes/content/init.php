<?php defined('isCMS') or die;

class Content {
	
	public $name = null;
	public $parent = null;
	public $type = null;
	public $page = null;
	public $filter = [];
	public $filtration = null;
	//public $this = [];
	public $data = [];
	public $ratings = [];
	
	public $settings = [
		'parents' => null,
		'tables' => null,
		'names' => null,
		'exclude' => null,
		'dates' => null,
		'filter' => null,
		'sort' => null,
		'skip' => null,
		'pagination' => null,
		'count' => null,
		'fields' => null,
		'defaults' => null
	];
	
	// public (общедоступный) - доступ разрешен отовсюду
	// protected (защищенный) - разрешает доступ самому классу, наследующим его классам и родительским классам
	// private (закрытый) - только класс, где объявлен сам элемент, имеет к нему доступ
	
	// удалить объект можно только так:
	// $new = new Class;
	// unset($new);
	
	function __construct($parameters = null) {
		
		if (!empty($parameters)) {
			
			if (!is_array($parameters)) {
				$parameters = dataParse($parameters);
			}
			
			$this -> name = $parameters[0];
			$this -> parent = $parameters[1];
			$this -> type = $parameters[2];
			
		}
		
		if (empty($this -> parent)) {
			global $template;
			$this -> parent = $template -> page['name'];
		}
		
		global $uri;
		
		if (!empty($uri -> query -> path)) {
			
			$uri_query_path = $uri -> query -> path;
			
			// начинаем разбор строки по базовым значениям
			
			$first = array_shift($uri_query_path);
			
			if (empty($this -> type)) {
				if ($first === 'all') {
					$this -> type = 'all';
					$first = array_shift($uri_query_path);
				} elseif ($first === 'filter') {
					$this -> type = 'list';
					$this -> filtration = true;
					$first = array_shift($uri_query_path);
				}
			}
			
			if ($first === 'page') {
				$this -> page = !empty($uri_query_path) ? array_shift($uri_query_path) : 1;
				$this -> filtration = true;
				unset($first);
			} elseif (
				$this -> type !== 'all' &&
				$this -> type !== 'list' &&
				!set($this -> name)
			) {
				$this -> name = $first;
				$this -> type = 'alone';
				unset($first);
			}
			
			if (!empty($first)) {
				array_unshift($uri_query_path, $first);
			}
			
			unset($first);
			
			// продолжаем разбор строки в фильтр
			
			// раньше было так
			// if (!empty($uri_query_path)) {
			// но такая запись не дает возможности читать и открывать лист из-под модуля
			// поэтому мы изменили на
			// if (!empty($uri_query_path) && $this -> type !== 'alone') {
			// с другой стороны, у нас так и нет возможности убрать фильтрацию
			// поэтому тестируем такую запись
			// if (!empty($uri_query_path) && $this -> filtration) {
			
			if (!empty($uri_query_path) && $this -> filtration) {
				
				foreach ($uri_query_path as $key => &$item) {
					
					$this -> filter[$item] = $uri_query_path[$key + 1];
					
					unset(
						$item,
						$uri_query_path[$key + 1]
					);
					
				}
				
			}
			
			unset($uri_query_path);
			
			// смотрим, пустой ли фильтр
			if (!empty($this -> filter)) {
				$this -> filtration = true;
			} elseif (!empty($this -> filtration)) {
				
				// !!!!
				// возможно, сюда нужно добавить, что фильтр задан ошибочно
				
			}
			
		}
		
		if (!empty($parameters[3])) {
			$this -> filtration = null;
			$this -> filter = [];
		}
		
		if (empty($this -> type)) {
			$this -> type = 'list';
		}
		
	}
	
	public function settings($custom = null) {
		
		if (empty($custom)) {
			$parent = $this -> parent;
			if (strpos($parent, '.') !== false) {
				$parent = substr($parent, 0, strpos($parent, '.'));
			}
			$custom = dbUse('content:' . $parent, 'select', ['allow' => 'type:settings', 'return' => 'alone:data']);
			unset($parent);
		} elseif (!is_array($custom)) {
			$custom = iniPrepareJson($custom, true);
		}
		
		$this -> settings = objectMerge($this -> settings, $custom, 'replace');
		
		// подключение рейтинговой системы
		
		self::ratingSet();
		
	}
	
	public function read() {
		
		$name = set($this -> name) ? ':' . $this -> name : null;
		
		//print_r($this -> settings);
		
		$parameters = self::parameters();
		
		if (objectIs($this -> settings['dates'])) {
			
			$allowc = null;
			$allowm = null;
			$denyc = null;
			$denym = null;
			
			foreach ($this -> settings['dates'] as $item) {
				
				// задаем даты по-умолчанию
				if (empty($item[0])) {
					$item[0] = (int) -2147483648;
				}
				if (empty($item[1])) {
					$item[1] = (int) time();
				}
				
				// форматируем дату согласно настройкам
				if (!empty($item[4])) {
					if (!is_numeric($item[0])) {
						$item[0] = datadatetime($item[0], $item[4], true);
					}
					if (!is_numeric($item[1])) {
						$item[1] = datadatetime($item[1], $item[4], true);
					}
				}
				
				// записываем в allow или deny
				if (empty($item[3]) || $item[3] !== 'deny') {
					if (!empty($item[2]) && $item[2] === 'modify') {
						$allowm .= ':' . $item[0] . '_' . $item[1];
					} else {
						$allowc .= ':' . $item[0] . '_' . $item[1];
					}
				} else {
					if (!empty($item[2]) && $item[2] === 'modify') {
						$denym .= ':' . $item[0] . '_' . $item[1];
					} else {
						$denyc .= ':' . $item[0] . '_' . $item[1];
					}
				}
				
			}
			
			if (!empty($allowm)) { $parameters['allow'] .= ' mtime' . $allowm; }
			if (!empty($allowc)) { $parameters['allow'] .= ' ctime' . $allowc; }
			if (!empty($denym)) { $parameters['deny'] .= ' mtime' . $denym; }
			if (!empty($denyc)) { $parameters['deny'] .= ' ctime' . $denyc; }
			
			//echo '[' . print_r($allowm, true) . ']<br>[' . print_r($allowc, true) . '][' . print_r($denym, true) . ']<br>[' . print_r($denyc, true) . ']<hr>';
			
			unset($item, $allowm, $allowc, $denym, $denyc);
			
		}
		
		$this -> data = dbUse('content' . $name, 'select', $parameters);
		
		unset($name, $parameters);
		
		// чтение рейтингов
		
		self::ratingRead();
		
		// чтение таблицы
		
		if (set($this -> settings['tables'])) {
			self::table();
		}
		
		// подготовка полученных данных
		
		if (!empty($this -> data)) {
			
			foreach ($this -> data as $key => &$item) {
				
				// чтение локальных данных
				
				if (set($this -> settings['local'])) {
					self::local($item);
				}
				
				self::prepare($item);
				
				if (empty($item)) {
					unset($this -> data[$key]);
				}
				
				//выводить здесь ошибку неправильно, т.к. ошибка сработает даже если был сторонний запрос, например, из модуля
				//так что проверки на ошибки и вывод ошибок переносим в код вызовы, после создания экземпляра класса
				
			}
			
			unset($key, $item);
			
		}
		
		// фильтрация и сортировка данных
		
		if (!empty($this -> data)) {
			
			$parameters = [];
			
			if (!empty($this -> settings['filter'])) {
				$parameters['filter'] = $this -> settings['filter'];
			}
			if (!empty($this -> settings['sort'])) {
				$parameters['sort'] = $this -> settings['sort'];
			}
			
			if (!empty($parameters)) {
				$this -> data = dbUse($this -> data, 'filter', $parameters);
			}
			
			unset($parameters);
			
			/*
			
			// фильтрация, сортировка, число и пропуск должны работать только на list, м.б. еще на all
			
			if (!empty($this -> type) && $this -> type === 'list') {
				
				if (!empty($this -> settings['skip'])) {
					$parameters['skip'] = $this -> settings['skip'];
				}
				
				if (!empty($this -> settings['pagination']) && !empty($this -> page)) {
					// если разрешено деление на страницы, то в пропуске также учитывается число материалов пропущенных страниц
					$parameters['skip'] = $this -> settings['count'] + ($this -> page - 1) * $this -> settings['count'];
					if (!$parameters['skip'] || $parameters['skip'] < 0) {
						unset($parameters['skip']);
					}
				} elseif (!empty($this -> settings['count'])) {
					$parameters['limit'] = $this -> settings['count'];
				}
				
			}
			
			if (!empty($parameters)) {
				$this -> data = dbUse($this -> data, 'filter', $parameters);
			}
			
			unset($parameters);
			
			*/
			
		}
		
		//echo '<hr>' . print_r($this -> data, true) . '<hr>';
		
		// проверять и выводить ошибку после фильтров тоже неправильно, т.к. здесь уместен вывод информации, что заданных материалов нет, но не ошибки
		//if (empty($this -> data)) {
		//	error('404', false);
		//}
		
	}
	
	private function parameters() {
		
		$parameters = [
			'allow' => 'parent:' . (!empty($this -> settings['parents']) ? $this -> settings['parents'] : $this -> parent),
			'deny' => 'type:settings',
			//'return' => $this -> type === 'alone' ? 'alone' : 'name'
			'limit' => $this -> type === 'alone' ? 1 : null,
			'return' => 'name'
		];
		
		if (strpos($parameters['allow'], '.')) {
			$parameters['allow'] = str_replace('.', '+', $parameters['allow']);
		}
		if (!empty($this -> settings['names'])) {
			$parameters['allow'] .= ' name:' . $this -> settings['names'];
		}
		if (!empty($this -> settings['exclude'])) {
			$parameters['deny'] .= ' name:' . $this -> settings['exclude'];
		}
		if (!empty($this -> settings['disable'])) {
			$parameters['deny'] .= ' parent:' . $this -> settings['disable'];
		}
		
		return $parameters;
		
	}
	
	private function prepare(&$item) {
		
		$data = &$item['data'];
		
		// первым делом обработали данные по языкам
		
		if (objectIs($data)) {
			objectLang($data);
		}
		
		// теперь подготавливаем новые поля
		
		if (!empty($this -> settings['fields'])) {
			
			foreach ($this -> settings['fields'] as $key => $field) {
				
				if (!objectIs($field)) {
					continue;
				}
				
				$type = empty($field['type']) ? 'text' : $field['type'];
				$value = !empty($data[$key]) ? $data[$key] : null;
				
				//($type === 'counter') { $this -> ratings }
				//echo '[' . $value . ' -- ' . $key . ' -- ' . print_r($field, true) . ']<br>';
				
				// значение по маске
				
				if (empty($value) && !empty($field['mask'])) {
					
					if (objectIs($field['mask'])) {
						objectLang($field['mask']);
					} elseif (!empty($field['mask'])) {
						
						// раньше было
						// if (!objectIs($field['mask']) && !empty($field['mask']) && mb_strpos($field['mask'], '{') !== false)
						
						$field['mask'] = preg_replace_callback(
							'/\{(.*?)\}/u',
							function ($matches) use($item) {
								
								$matches = dataParse($matches[1]);
								$result = null;
								
								if (!empty($matches) && !empty($matches[0]) && !empty($matches[1])) {
									
									if (
										$matches[0] === 'data' &&
										!empty($item['data'][$matches[1]])
									) {
										$result = $item['data'][$matches[1]];
									} elseif (
										$matches[0] === 'db' &&
										!empty($item[$matches[1]])
									) {
										$result = $item[$matches[1]];
									} elseif (
										$matches[0] === 'lang'
									) {
										unset($matches[0]);
										$result = lang(objectToString($matches, ':'));
									} elseif (
										$matches[0] === 'rating'
									) {
										if (
											$matches[1] === 'count' ||
											$matches[1] === 'total' ||
											$matches[1] === 'average'
										) {
											$result = $this -> ratings[$item['name']]['data']['counter'][$matches[1]];
										} elseif (
											$matches[1] === 'min' ||
											$matches[1] === 'max'
										) {
											$result = $this -> settings['rating']['counter'][ $matches[1] === 'min' ? 0 : 1 ];
										} else {
											$result = $this -> ratings[$item['name']]['data'][$matches[1]];
										}
										
										if (objectIs($result) || empty($result)) {
											$result = 0;
										}
									}
									
								}
								
								return $result;
								
							},
							$field['mask']
						);
						
						$value = $field['mask'];
						
						//echo '[' . $value . ']<br>';
						//echo '[' . $field['mask'] . ']<br>';
						
					}
					
				}
				
				// значение из default
				
				if (empty($value) && !empty($field['default'])) {
					if (is_array($field['default'])) {
						$value = array_rand($field['default']);
					} else {
						$value = $field['default'];
					}
				}
				
				// значение по ключам полей
				
				if (!empty($field['keys'])) {
					
					unset($value);
					$value = [];
					
					if (!is_array($field['keys'])) {
						$field['keys'] = dataParse($field['keys']);
					}
					
					foreach ($field['keys'] as $i) {
						if (!empty($data[$i])) {
							$value[] = $i;
						}
					}
					
					unset($i);
					
				}
				
				// специальные условия для разных типов
				
				if (!empty($value)) {
					
					if (is_array($value)) {
						foreach ($value as &$i) {
							$i = self::types($type, $field, $i);
						}
						unset($i);
						$value = objectClear($value);
					} else {
						$value = self::types($type, $field, $value);
					}
					
				}
				
				// преобразовать значение в массив
				// повторная проверка нужна потому что после всех преобразований $value может остаться пустым
				
				if (!empty($value) && !empty($field['array'])) {
					$value = datasplit($value);
				}
				
				$data[$key] = $value;
				
			}
			
			unset($type, $value, $key, $field);
			//print_r($data);
		}
		
		// теперь подготавливаем умолчания в элементе по полям массива данных
		
		if (objectIs($this -> settings['defaults'])) {
			
			$defaults = [
				'title',
				'description',
				'image',
				'top',
				'allow',
				'deny',
				'ftime'
			];
			
			foreach ($defaults as $i) {
				if (!empty($this -> settings['defaults'][$i])) {
					$item[$i] = $data[$this -> settings['defaults'][$i]];
				}
			}
			
			unset($defaults, $i);
			
		}
		
		// на основе ftime, если он был задан, убираем материалы с истекшим сроком
		
		if (!empty($item['ftime']) && $item['ftime'] < time()) {
			$item = null;
		}
		
		// на основе allow и deny, если один из них или оба были заданы, убираем материалы, запрещенные к публикации
		
		if (isset($item['allow']) && !$item['allow']) {
			$item = null;
		}
		if (!empty($item['deny'])) {
			$item = null;
		}
		
		// сюда нужно добавить point - оценки, голосования и статистику
		// сюда нужно добавить pagination - разделение материалов на страницы
		
		// завершаем обработку
		
		unset($data);
		
	}
	
	private function types($type, $field, $value) {
		
		if ($type === 'text') {
			
			if (!empty($field['len'])) {
				
				if ($field['len'] === 'break') {
					
					$value = mb_substr($value, 0, mb_stripos($value, '{break}'));
					
				} elseif (empty($field['units'])) {
					
					$value = mb_substr($value, 0, (int) $field['len']);
					
				} else {
					
					$array = null;
					$append_symbol = ' ';
					
					if ($field['units'] === 'words') {
						
						$array = datasplit($value, '\s');
						
					} elseif ($field['units'] === 'phrase') {
						
						$array = datasplit($value, '.');
						$append_symbol = '. ';
						
					} elseif ($field['units'] === 'paragraph') {
						
						$array = preg_split('/\r\n|\r|\n|\<\/?(br|hr|p|h\d{1}).*?\>/u', $value, null, PREG_SPLIT_NO_EMPTY);
						$append_symbol = PHP_EOL;
						
					}
					
					if (!empty($array)) {
						
						$array = array_slice($array, 0, $field['len']);
						$value = objectToString($array, $append_symbol);
						
					}
					
					unset($array, $append_symbol);
					
				}
				
			}
			
			if (!empty($field['clear'])) {
				$value = clear($value, $field['clear']);
			}
			
		} elseif ($type === 'date') {
			
			if (empty($field['in']) && !empty($field['out'])) {
				$value = datadatetime($value, $field['out']);
			} elseif (!empty($field['in']) && empty($field['out'])) {
				$value = datadatetime($value, $field['in'], true);
			} elseif (!empty($field['in']) && !empty($field['out'])) {
				$value = datadatetime($value, $field['in'], $field['out']);
			}
			
		} elseif ($type === 'numeric') {
			
			if (!empty($field['random']) && empty($value)) {
				$value = mt_rand($field['min'], $field['max']);
			}
			
			if ($value < $field['min']) {
				$value = $field['min'];
			} elseif ($value > $field['max']) {
				$value = $field['max'];
			}
			
			$value = datanum(
				$value,
				!empty($field['grammar']) ? dataParse($field['grammar']) : $field['convert'],
				$field['multiply']
			);
			
		} elseif ($type === 'list') {
			
			if (!empty($field['list'])) {
				
				if (!is_array($field['list'])) {
					$field['list'] = dataParse($field['list']);
				}
				
				$associate = objectKeys($field['list']);
				
				if ($associate) {
					
					if (array_key_exists($value, $field['list'])) {
						$value = $field['list'][$value];
					} else {
						$value = null;
					}
					
				} else {
					
					if (!in_array($value, $field['list'])) {
						$value = null;
					}
					
				}
				
				unset($associate);
				
			}
			
		} elseif ($type === 'boolean') {
			
			$value = !empty($value) ? true : false;
			
		}
		
		return $value;
		
	}
	
	private function table() {
		
		// этим методом мы подгружаем данные из таблицы в массив данных контента
		// метод был публичным, так что мы могли вызывать его в любой момент
		// хотя он автоматически вызывается при чтении данных
		// но теперь он закрыт в целях безопасности данных, а автоматического вызова более чем достаточно
		
		// а еще мы теперь здесь же инициализируем библиотеку для работы с excel
		
		global $template;
		$template = objectMerge($template, (object) ['settings' => (object) ['libraries' => ['excel']]]);
		
		//if (empty($template)) { $template = (object) []; }
		//if (empty($template -> settings)) { $template -> settings = (object) []; }
		//if (empty($template -> settings -> libraries)) { $template -> settings -> libraries = []; }
		//if (!in_array('excel', $template -> settings -> libraries)) { $template -> settings -> libraries[] = 'excel'; }
		
		init('libraries', 'first');
		init('libraries', 'second');
		
		// здесь мы инициализируем функции по работе с локальными данными
		// хотя вообще, на будущее, наверное надо перенести инициализацию контента ниже,
		// уже после того, как будут загружены языки и прочее
		// по крайней мере, тогда работа с контентом станет удобнее - можно будет включить много функций и фишек
		// минус такого позднего включения - если нет запрошенного материала, сначала будет загружено много всего, и только потом вылезет ошибка
		// можно сделать проверку материала в базе данных дополнительной функцией внутри класса
		// но остается вопрос, как будет работать интерпретатор языка без загрузки локальных функций при создании экземпляра класса
		
		init('functions', 'local');
		
		foreach ($this -> settings['tables'] as $item) {
			
			$item = dataParse($item);
			
			// 0 - name, address
			// 1 - file extension
			// 2 - addition type
			// 3 - custom function
			// 4 - skip rows and maybe custom attributes
			
			if ($item[1] === 'csv_excel') {
				$special = [';', '', 'CP1251'];
				$item[1] = 'csv';
			}
			
			$path = PATH_ASSETS . 'content' . DS . 'tables' . DS . $item[0] . '.' . $item[1];
			
			if (!file_exists($path)) {
				continue;
			}
			
			if (DEFAULT_CUSTOM && !empty($item[3])) {
				
				// этот код может казаться небезопасным,
				// однако он защищен тем, что вызываются только те функции, которые были инициализированы ранее
				
				if (!function_exists($item[3])) {
					init('custom', 'content' . DS . 'functions' . DS . $item[3]);
				}
				if (function_exists($item[3])) {
					$table = $item[3]($path, $item);
					// данный код может давать предупреждения антивируса, однако он является безопасным
				}
				
			} else {
				
				$table = localOpenTable(
					$path,
					$item[1],
					[
						'return' => 'name',
						'fields' => (empty($item[2]) && empty($this -> data) || $item[2] === 'create') ? null : ['id', 'name'],
						'names' => ($this -> type === 'alone') ? dataParse($this -> name) : null,
						'special' => !empty($special) ? $special : null,
						'encoding' => !empty($special[2]) ? $special[2] : null,
						'skip' => !empty($item[4]) ? str_replace('.', ':', $item[4]) : null
					]
				);
				
			}
			
			if (empty($table)) {
				return null;
			}
			
			if (empty($item[2]) && empty($this -> data) || $item[2] === 'create') {
				$this -> data = $table;
			} elseif (empty($item[2]) && !empty($this -> data) || $item[2] === 'replace') {
				$table = array_intersect_key($table, $this -> data);
				//$this -> data = array_merge_recursive($this -> data, $table);
				$this -> data = array_replace_recursive($this -> data, $table);
			} elseif ($item[2] === 'merge') {
				$this -> data = array_merge_recursive($this -> data, $table);
			}
			
			//echo $path . '<br>';
			//print_r($table);
			//echo '<hr>';
			//print_r($this -> data);
			//echo '<hr>';
			
		}
		
		unset($item);
		
		if (!empty($this -> name)) {
			$names = dataParse($this -> name);
			if (objectIs($names)) {
				$this -> data = array_intersect_key($this -> data, array_flip($names));
			}
			unset($names);
		}
		
	}
	
	private function local(&$item) {
		
		// этим методом мы подгружаем данные из локальных файлов в массив данных контента
		// здесь мы инициализируем функции по работе с локальными данными
		// это больше для подстраховки, т.к. на момент инициализации класса, эти функции уже должны быть загружены
		
		init('functions', 'local');
		$data = &$item['data'];
		global $lang;
		
		foreach ($this -> settings['local'] as $key) {
			
			$key = dataParse($key);
			
			// 0 - field name, key
			// 1 - encodings
			// 2 - addition type
			// 3 - custom function
			// 4 - maybe custom attributes
			
			if ((empty($key[2]) || $key[2] !== 'replace') && !empty($data[$key[0]])) {
				continue;
			}
			
			$path = PATH_ASSETS . 'content' . DS . 'local' . DS . $this -> parent . DS . $item['name'] . '.' . $key[0];
			
			if (!empty($lang) && file_exists($path . '.' . $lang -> lang . '.ini')) {
				$path .= '.' . $lang -> lang . '.ini';
			} elseif (file_exists($path . '.ini')) {
				$path .= '.ini';
			} else {
				continue;
			}
			
			if (DEFAULT_CUSTOM && !empty($key[3])) {
				
				if (!function_exists($key[3])) {
					init('custom', 'content' . DS . 'functions' . DS . $key[3]);
				}
				if (function_exists($key[3])) {
					$file = $key[3]($path, $key);
				}
				
			} else {
				$file = localFile($path);
			}
			
			if (!empty($key[1])) {
				if (strpos($key[1], '.') !== false) {
					$key[1] = datasplit($key[1], '.');
				}
				$file = mb_convert_encoding($file, 'UTF-8', $key[1]);
			}
			
			$data[$key[0]] = $file;
			
		}
		
		unset($file, $path, $key);
		
	}
	
	// этими методами мы управляем рейтинговой системой
	
	private function ratingSet() {
		
		$counter = &$this -> settings['rating']['counter'];
		
		if (!empty($counter)) {
			
			if (is_numeric($counter)) {
				$counter = [$counter, $counter];
			} else {
				$counter = dataParse($counter);
			}
			
			if (!objectIs($counter)) {
				$counter[0] = -1;
				$counter[1] = 1;
			} else {
				if (empty($counter[0])) {
					$counter[0] = $counter[1];
				}
				if (empty($counter[1])) {
					$counter[1] = $counter[0];
				}
				if (count($counter) > 2) {
					$counter = array_slice($counter, null, 2);
				}
			}
			
		}
		
		unset($counter);
		
		$this -> settings['rating'] = objectClear($this -> settings['rating']);
		
	}
	
	private function ratingRead() {
		
		$ratings = &$this -> ratings;
		
		$name = set($this -> name) ? ':' . $this -> name : null;
		$parameters = self::parameters();
		
		$ratings = dbUse('ratings' . $name, 'select', $parameters);
		
		unset($name, $parameters);
		
	}
	
	public function ratingPrepare($data = null, $simple = null) {
		
		// обработка рейтингов - это упрощение информации для вывода
		// всегда должно идти последним
		// после обработки остальные функции по работе с рейтингами будут работать неправильно
		// исключение составляет вызов процессов, но это потому что там работа с рейтингами будет идти заново, без обработки
		
		$ratings = &$this -> ratings;
		
		if (objectIs($data)) {
			$ratings = array_intersect_key($ratings, array_flip($data));
		}
		
		if (objectIs($ratings)) {
			foreach ($ratings as &$item) {
				$item['data'] = array_intersect_key($item['data'], $this -> settings['rating']);
				if ($simple) {
					unset($item['data']['data']);
					$item = $item['data'];
				}
			}
			unset($item);
		}
		
		if (!$simple && objectIs($this -> data)) {
			$list = array_keys($this -> data);
			foreach ($list as $item) {
				if (!array_key_exists($item, $ratings)) {
					$ratings[$item] = [
						'name' => $item,
						'data' => []
					];
					//echo '<br>{{' . $item . '}}<br>';
				}
			}
			unset($item, $list);
		}
		
		//$r = $ratings;
		//$r = $this -> data;
		//$r = array_keys($r);
		//echo '<br>{{' . print_r($r, true) . '}}<br>';
		
	}
	
	private function ratingCount($name, $type, $data) {
		
		/*
		мы создаем в базе данных рейтингов еще одну запись - она касается юников - уникальных посетителей
		если в параметрах учета рейтингов материала заданы юники, то в раздел данных материала из базы рейтингов добавляется еще один массив
		
		ключи - это pid'ы
		а внутри - неассоциативный массив действий, которые уже были сделаны юником и теперь запрещены для него
		
		плюс еще добавляем опцию учета только авторизованных посетителей
		*/
		
		if (empty($type) || !is_string($type) || empty($data) || empty($this -> settings['rating'][$type])) {
			return null;
		}
		
		$rating = &$this -> ratings[$name]['data'];
		
		if (empty($rating)) {
			
			$rating = [
				'counter' => [
					'count' => null,
					'total' => null,
					'average' => null
				],
				'views' => null,
				'display' => null,
				'target' => null,
				'unique' => [],
				'data' => []
			];
			
		}
		
		if (!empty($this -> settings['rating']['unique'])) {
			$pid = userPID();
			if (
				objectIs($rating['unique']) &&
				objectIs($rating['unique'][$pid]) &&
				in_array($type, $rating['unique'][$pid])
			) {
				return null;
			}
		}
		
		if (!empty($this -> settings['rating']['authorised'])) {
			global $user;
			if (empty($user -> uid)) {
				return null;
			}
		}
		
		$this -> ratings[$name]['id'] = $this -> data[$name]['id'];
		$this -> ratings[$name]['name'] = $this -> data[$name]['name'];
		$this -> ratings[$name]['type'] = $this -> data[$name]['type'];
		$this -> ratings[$name]['self'] = $this -> data[$name]['self'];
		$this -> ratings[$name]['parent'] = $this -> data[$name]['parent'];
		
		if ($type === 'counter') {
			
			if (
				$data < $this -> settings['rating']['counter'][0] ||
				$data > $this -> settings['rating']['counter'][1]
			) {
				return null;
			}
			
			$rating['counter']['count']++;
			$rating['counter']['total'] += (int) $data;
			$rating['counter']['average'] = round($rating['counter']['total'] / $rating['counter']['count']);
			
		} elseif (
			$type === 'views' ||
			$type === 'display' ||
			$type === 'target'
		) {
			$rating[$type]++;
		}
		
		if (!empty($this -> settings['rating']['data'])) {
			global $user;
			
			//echo '[' . cookie('PID', true) . ']<br>';
			//print_r($user);
			
			$rating['data'][] = [
				'type' => $type,
				'vote' => $data,
				'authorised' => defined('isALLOW') && isALLOW ? true : false,
				'time' => time(),
				'ip' => $user -> ip,
				'agent' => USER_AGENT
			];
		}
		
		if (!empty($this -> settings['rating']['unique'])) {
			$rating['unique'][$pid][] = $type;
		}
		
		return true;
		
	}
	
	public function ratingAdd($type = null, $data = 1, $global = null) {
		
		//if (empty($type) || !is_string($type) || empty($data) || empty($this -> settings['rating'][$type])) {
		//	return null;
		//}
		
		$result = [];
		
		if (objectIs($this -> ratings)) {
			foreach ($this -> ratings as $item) {
				if (is_array($type)) {
					foreach ($type as $i) {
						$result[] = self::ratingCount($item['name'], $i, $data);
					}
					unset($i);
				} else {
					$result[] = self::ratingCount($item['name'], $type, $data);
				}
			}
			unset($item);
		} elseif (is_array($type)) {
			foreach ($type as $i) {
				$result[] = self::ratingCount($this -> name, $i, $data);
			}
			unset($i);
		} else {
			$result[] = self::ratingCount($this -> name, $type, $data);
		}
		
		//echo '<hr>SET:<br>' . print_r($set, true);
		//echo '<hr>RATING:<br>' . print_r($this -> ratings, true);
		
		if (set($result)) {
			
			if (empty($global)) {
				dbUse('ratings', 'write', $this -> ratings);
			} else {
				
				$f = objectProcess('content:global');
				localRequestUrl($f['link'], $f['string'] . '&close=1&data=' . base64_encode(json_encode($this -> ratings)), 'post');
				unset($f);
				
				//global $uri;
				//localRequestUrl($uri -> site . DEFAULT_PROCESSOR . '/content/global/', 'hash=' . crypting(time() + TIME_HOUR) . '&csrf=' . csrf() . '&close=1&data=' . base64_encode(json_encode($this -> ratings)), 'post');
				
				//echo '[[[';
				//echo '<a href="' . $uri -> site . DEFAULT_PROCESSOR . '/content/global/' . '?hash=' . crypting(time() + TIME_HOUR) . '&csrf=' . csrf() . '&close=1&data=' . base64_encode(json_encode($this -> ratings)) . '" target="_blank">link</a>';
				//echo $uri -> site . DEFAULT_PROCESSOR . '/content/global/' . '?hash=' . crypting(time() + TIME_HOUR) . '&csrf=' . csrf() . '&close=1';
				//echo objectProcess('content:global', true) . '&close=1';
				//echo ']]]';
				
			}
			
		}
		
		//$result = $rating;
		//return $result;
		
	}
	
	//echo 'name : ' . $this -> name . '<br>parent : ' . $this -> parent . '<br>type : ' . $this -> type . '<hr>';
	//echo '::' . print_r($rating, true) . '<br>';
	
}

?>