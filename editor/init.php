<?php defined('isENGINE') or die;

// СКОРЕЕ ВСЕГО НАДО БУДЕТ ЗАМЕНИТЬ array_merge В ДРАЙВЕРЕ В ЗАПИСИ НА array_replace

/*
пишем модуль, который для начала просто читает данные из базы данных

потом он будет их выводить в массив

потом он будет читать настройки, и в зависимости от настроек, сможет выводить:
- нужные строки (фильтр по имени)
- нужные поля
- определенные поля - только для чтения, другие - также для записи
- определение разрешений внутри массивов значений: для добавления, для удаления, неассоциированные массивы или ассоциированные массивы с указанием ключей
- определение разрешений для записей: добавление, удаление
- именование полей
- все это делать через настройки, соответственно, для них можно задавать мультиязычные значения
- вывод в виде массива (это самый первый этап)
- вывод в виде json (возможно, понадобится для преобразования)
- вывод в виде таблицы и в виде формы
- запись по аяксу (потом)

*/

//echo '<pre>' . print_r($module, 1) . '</pre>';

$db = $module -> settings['db'];
$name = $module -> settings['name'];
$parameters = ['return' => 'alone'];

if (!empty($module -> this)) {
	
	$splitter = strpos($module -> this, ':');
	
	if ($splitter === false) {
		$splitter = strlen($module -> this);
	}
	
	$db = substr($module -> this, 0, $splitter);
	$name = substr($module -> this, $splitter);
	
}

if (!empty($module -> settings['allow'])) {
	$parameters['allow'] = $module -> settings['allow'];
}
if (!empty($module -> settings['filter'])) {
	$parameters['filter'] = $module -> settings['filter'];
}

$module -> data = dbUse($db . ':' . $name, 'select', $parameters);

unset($parameters);

$id = md5($module -> name . '-' . $module -> param . '-' . $db . '-' . $name . '-' . $module -> data['type'] . '-' . iniPrepareArray($module -> data['parent']));

//print_r($module -> data);
//exit;

$tbl = [];

$schema = objectIs($module -> settings['schema']) ? $module -> settings['schema'] : dbUse('schemas:' . $name, 'select', ['allow' => 'parent:' . $db, 'return' => 'alone:data']);

if (!objectIs($schema)) {
	$schema = [
		'title' => $module -> this ? $module -> this : $module -> param,
		'type' => 'object',
		'properties' => []
	];
}

if (objectIs($module -> data)) {
	
	$_SESSION['writedb'] = $db;
	//$_SESSION['writename'] = $module -> data['name'];
	//$_SESSION['writeparent'] = iniPrepareArray($module -> data['parent']);
	
	if (objectIs($module -> settings['data'])) {
		foreach ($module -> settings['data'] as $item) {
			$tbl[$item] = set($module -> data['data'][$item], true);
		}
		unset($item);
	} else {
		$tbl = $module -> data['data'];
	}
	
	foreach ($tbl as $k => $i) {
		if (!array_key_exists($k, $schema['properties'])) {
			$schema['properties'][$k] = moduleDataTables_createSchema($k, $i);
		}
	}
	unset($k, $i);
	
}

//print_r(iniPrepareArray($schema, 1));
//print_r($tbl);

function moduleDataTables_createSchema($k, $i) {
	
	$arrTarget = [];
	
	if (is_array($i) && (!set($i) || objectKeys($i))) {
		
		$arrTarget = [
			'type' => 'object',
			'title' => $k,
			'options' => [
				'collapsed' => empty($sets['simple']) ? true : false
			],
			'properties' => []
		];
		
		if (objectIs($i)) {
			foreach ($i as $ki => $ii) {
				$arrTarget['properties'][$ki] = moduleDataTables_createSchema($ki, $ii);
			}
			unset($ki, $ii);
		}
		
	}
	
	if (objectIs($i)) {
		if (objectKeys($i)) {
			
		} else {
			$arrTarget = [
				'type' => 'array',
				'format' => 'table'
			];
		}
	} elseif ($i === true) {
		$arrTarget['type'] = 'boolean';
	} elseif (set($i)) {
		$arrTarget['type'] = 'string';
	}
	
	return $arrTarget;
	
}

//echo '<pre>' . print_r($tbl, 1) . '</pre>';

//unset($tbl);

?>