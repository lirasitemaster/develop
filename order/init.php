<?php defined('isCMS') or die;
	
	global $template;
	
	// настраиваем дату, приводим ее к нормальному виду
	
	$date = (object) array(
		'sym' => '/',
	);
	if ($lang -> lang === 'ru') {
		$date -> sym = '.';
	}
	
	if (isset($module -> settings -> date -> startdate)) {
		$date -> date = $module -> settings -> date -> startdate;
		$module -> settings -> date -> startdate = dateformatcorrect($date);
	}
	
	if (isset($module -> settings -> date -> stopdate)) {
		$date -> date = $module -> settings -> date -> stopdate;
		$module -> settings -> date -> stopdate = dateformatcorrect($date);
	}
	
	if (isset($module -> settings -> date -> specialdays) && is_array($module -> settings -> date -> specialdays)) {
		foreach ($module -> settings -> date -> specialdays as &$item) {
			if (strlen($item) <= 5) {
				$module -> settings -> date -> specialdays[] = $item . $date -> sym . (date('Y') - 1);
				$module -> settings -> date -> specialdays[] = $item . $date -> sym . (date('Y') + 1);
			}
		}
		foreach ($module -> settings -> date -> specialdays as &$item) {
			$date -> date = $item;
			$item = dateformatcorrect($date);
			//$item = strtotime($item . ' UTC'); // эта строка преобразует дату во временную метку по гринвичу
		}
		
		sort($module -> settings -> date -> specialdays);
	}
	
	//print_r($module -> settings -> date -> specialdays);
	
	if (isset($module -> settings -> date -> holidays) && is_array($module -> settings -> date -> holidays)) {
		foreach ($module -> settings -> date -> holidays as &$item) {
			if (strlen($item) <= 5) {
				$module -> settings -> date -> holidays[] = $item . $date -> sym . (date('Y') - 1);
				$module -> settings -> date -> holidays[] = $item . $date -> sym . (date('Y') + 1);
			}
		}
		foreach ($module -> settings -> date -> holidays as &$item) {
			$date -> date = $item;
			$item = dateformatcorrect($date);
		}
		sort($module -> settings -> date -> holidays);
	}
	
	unset($date);
	
	// настраиваем дни недели, которые нужно заблокировать
	
	if (isset($module -> settings -> week) && is_array($module -> settings -> week)) {
		$module -> settings -> week = (object) array(
			'set' => $module -> settings -> week,
			'days' => array(),
			'disabledays' => array(0, 1, 2, 3, 4, 5, 6)
		);
		
		foreach ($module -> settings -> week -> set as &$item) {
			if (is_array($item)) {
				foreach ($item as &$itempart) {
					$itempart = str_replace('7', '0', $itempart);
					$module -> settings -> week -> days[] = $itempart;
				}
			} else {
				$item = str_replace('7', '0', $item);
				$module -> settings -> week -> days[] = $item;
			}
		}
		
		foreach ($module -> settings -> week -> days as $item) {
			$key = array_search($item, $module -> settings -> week -> disabledays);
			unset ($module -> settings -> week -> disabledays[$key]);
		}
		
		sort($module -> settings -> week -> disabledays);
		
	}
	
	// преобразуем время
	
	if (is_array($module -> settings -> time -> param)) {
		foreach ($module -> settings -> time -> param as &$item) {
			$item = strtotime('01.01.1970 ' . $item . ' UTC');
		}
	} elseif (!isset($module -> settings -> time)) {
		$module -> settings = (object) array_merge( (array) $module -> settings, array(
			'time' => (object) array(
				'param' => 0
			)
		));
	} elseif (!isset($module -> settings -> time -> param)) {
		$module -> settings -> time = (object) array_merge( (array) $module -> settings -> time, array(
			'param' => 0
		));
	} else {
		$module -> settings -> time -> param = strtotime('01.01.1970 ' . $module -> settings -> time -> param . ' UTC');
	}
	
		
	
	// ??? if ($module -> settings -> time -> type === 'line' && empty($module -> settings -> time -> last)) {
	if ($module -> settings -> time -> type === 'line') {
		$module -> settings -> time -> param[1] = $module -> settings -> time -> param[1] - $module -> settings -> time -> param[2];
	}
	
	$module -> settings -> time -> data = array();
	if ($module -> settings -> time -> type === 'line') {
		while ($module -> settings -> time -> param[0] <= $module -> settings -> time -> param[1]) {
			$module -> settings -> time -> data[] = date('H:i', $module -> settings -> time -> param[0] - date('Z', $module -> settings -> time -> param[0]));
			$module -> settings -> time -> param[0] = $module -> settings -> time -> param[0] + $module -> settings -> time -> param[2];
		}
	} elseif ($module -> settings -> time -> type === 'set') {
		foreach ($module -> settings -> time -> param as $time) {
			$module -> settings -> time -> data[] = date('H:i', $time - date('Z', $time));
		}
		unset($time);
	} else {
		$module -> settings -> time -> data[] = date('H:i', $module -> settings -> time -> param[0] - date('Z', $module -> settings -> time -> param[0]));
	}
	$module -> settings -> time -> param = $module -> settings -> time -> data;
	unset($module -> settings -> time -> data);
	
	// преобразуем места
	
	$module -> settings -> place = (object) array_merge( (array) $module -> settings -> place, array(
		'data' => array(),
	));
	//$module -> settings -> place -> data = array();
	if ($module -> settings -> place -> type === 'line') {
		while ($module -> settings -> place -> param[0] <= $module -> settings -> place -> param[1]) {
			$module -> settings -> place -> data[0][] = $module -> settings -> place -> param[0];
			$module -> settings -> place -> param[0] = $module -> settings -> place -> param[0] + $module -> settings -> place -> param[2];
		}
	} elseif ($module -> settings -> place -> type === 'set') {
		foreach ($module -> settings -> place -> param as $key => $place) {
			for ($i = 1; $i <= $place; $i++) {
				$module -> settings -> place -> data[$key][] = $i;
			}
		}
		unset($place, $i, $key);
	} elseif ($module -> settings -> place -> type === 'array') {
		for ($y = 1; $y <= $module -> settings -> place -> param[1]; $y++) {
			for ($x = 1; $x <= $module -> settings -> place -> param[0]; $x++) {
				$module -> settings -> place -> data[$y][] = $x;
			}
		}
		unset($x, $y);
	}
	$module -> settings -> place -> param = $module -> settings -> place -> data;
	unset($module -> settings -> place -> data);
	
	//print_r( $module -> settings -> place -> param );
	
	// преобразуем данные из расписания
	
	if (
		file_exists($module -> path . DS . 'data' . DS . $module -> param . '_schedule.csv') &&
		filesize($module -> path . DS . 'data' . DS . $module -> param . '_schedule.csv') > 0
	) {
		
		$module -> settings -> schedule = dataloadcsv($module -> path . DS . 'data' . DS . $module -> param . '_schedule');
		$module -> settings -> schedule -> key = $module -> settings -> schedule -> data[0];
		$module -> settings -> schedule -> keyrepeat = (object) array_count_values($module -> settings -> schedule -> key);
		unset($module -> settings -> schedule -> data[0]);
		
		foreach(['type', 'param', 'week'] as $p) {
			for ($i = 0, $n = $module -> settings -> schedule -> keyrepeat -> $p - 1; $i <= $n; $i++) {
				$k = array_search($p, $module -> settings -> schedule -> key);
				$module -> settings -> schedule -> key[$k] = $p . $i;
			}
		}
		unset($p, $i, $n, $k);
		
		foreach ($module -> settings -> schedule -> data as &$item) {
			$item = array_combine($module -> settings -> schedule -> key, $item);
			$item = json_encode($item);
			$item = json_decode($item, false);
			/*
			$item -> param = json_decode($item -> param, false);
			$item -> week = json_decode($item -> week, false);
			*/
			
			$item -> dates = [];
			foreach(['type', 'param', 'week'] as $p) {
				for ($i = 0, $n = $module -> settings -> schedule -> keyrepeat -> $p - 1; $i <= $n; $i++) {
					$t = $p . $i;
					$k = array_search($t, $module -> settings -> schedule -> key);
					
					if (!isset($item -> dates[$i] -> $p)) {
						$item -> dates[$i] = (object) array_merge( (array) $item -> dates[$i], array(
							$p => $item -> $t
						));
					} else {
						$item -> dates[$i] -> $p = $item -> $t;
					}
					unset($item -> $t);
				}
			}
			unset($p, $i, $n, $k, $t);
			
			foreach($item -> dates as &$p) {
				
				$p -> param = '[' . $p -> param . ']';
				$p -> param = preg_replace('/(\w{1,2}:\w{1,2})/', '"$1"', $p -> param);
				$p -> param = json_decode($p -> param, false);
				
				$p -> week = '[' . $p -> week . ']';
				if ( strpos($p -> week, '(') !== false ) {
					$p -> week = preg_replace('/(\w{1}\(\w{1}\))/', '"$1"', $p -> week);
				}
				$p -> week = json_decode($p -> week, false);
				
			}
			unset($p);
			
		}
		$module -> settings -> schedule = $module -> settings -> schedule -> data;
		sort($module -> settings -> schedule);
		
	}
	
	if (isset($module -> settings -> schedule) && is_array($module -> settings -> schedule)) {
		
		/* !!!!!!!!!!!!!!!!!!!!!!!!!!!!! */
		/* !!!!!!!!!!!!!!!!!!!!!!!!!!!!! */
		/* !!!!!!!!!!!!!!!!!!!!!!!!!!!!! */
		
		// Подготавливаем массив мест
		// для добавления в пустые ячейки мест и имен
		// для каждого расписания
		
		$module -> settings -> places = array();
				
		foreach ($module -> settings -> place -> param as $k => $i) {
			foreach ($i as $ii) {
				$module -> settings -> places[] = $k + 1 . '-' . $ii;
			}
		}
		
		foreach ($module -> settings -> schedule as $k => &$schedule) {
			
			// Преобразуем или добавляем место для каждого расписания
			
			if (empty($schedule -> place)) {
				$schedule = (object) array_merge( (array) $schedule, array(
					'place' => $module -> settings -> places[$k]
				));
			}
			
			// Преобразуем или добавляем имя для каждого расписания
			
			if (empty($schedule -> name)) {
				$schedule = (object) array_merge( (array) $schedule, array(
					'name' => $k + 1
				));
			}
			
			// Добавляем заблокированные дни для каждого расписания
			
			if (empty($schedule -> disabledays)) {
				$schedule = (object) array_merge( (array) $schedule, array(
					'disabledays' => array(0, 1, 2, 3, 4, 5, 6)
				));
			}
			
			foreach ($schedule -> dates as &$date) {
				
				// Добавляем специальные дни для каждого расписания
				
				if (!isset($date -> special) || !$date -> disabledays) { // <<<<<<<<<<<<< а здесь точно disabledays ????
					$date = (object) array_merge( (array) $date, array(
						'special' => array()
					));
				}
				
				// Преобразуем время для каждого расписания
				// (код полностью повторяет код преобразования времени выше)
				
				if (is_array($date -> param)) {
					foreach ($date -> param as &$item) {
						$item = strtotime('01.01.1970 ' . $item . ' UTC');
					}
				} else {
					$date -> param = array(
						strtotime('01.01.1970 ' . $date -> param . ' UTC')
					);
				}
				
				if ($date -> type === 'line') {
					$date -> param[1] = $date -> param[1] - $date -> param[2];
				}
				
				$date -> data = array();
				if ($date -> type === 'line') {
					while ($date -> param[0] <= $date -> param[1]) {
						$date -> data[] = date('H:i', $date -> param[0] - date('Z', $date -> param[0]));
						$date -> param[0] = $date -> param[0] + $date -> param[2];
					}
				} elseif ($date -> type === 'set') {
					foreach ($date -> param as $time) {
						$date -> data[] = date('H:i', $time - date('Z', $time));
					}
					unset($time);
				} else {
					$date -> data[] = date('H:i', $date -> param[0] - date('Z', $date -> param[0]));
				}
				$date -> param = $date -> data;
				unset($date -> data);
				
				// настраиваем дни недели, которые нужно заблокировать
				// (код полностью повторяет код настройки дней недели выше)
				
				if (isset($date -> week) && is_array($date -> week)) {
					$date -> week = (object) array(
						'set' => $date -> week,
						'days' => array()
					);
					
					foreach ($date -> week -> set as $key => &$item) {
						if (is_array($item)) {
							foreach ($item as &$itempart) {
								$itempart = str_replace('7', '0', $itempart);
								// здесь новый кусок кода, который добавляет проверку на значения в скобках
								// тогда полное значение он переносит в подмассив special,
								// который будет разбираться уже в динамическом скрипте,
								// а также значение чистится и добавляется в дни, иначе этот день заблокируется
								// этот же код нужно вставить выше
								if ( strpos($itempart, '(') !== false && strpos($itempart, ')') !== false ) {
									$date -> special[] = $itempart;
									$itempart = preg_replace('/\(\w\)/', '$1', $itempart);
									unset($date -> week -> set[$key]);
								}
								// конец нового куска кода
								$date -> week -> days[] = $itempart;
							}
						} else {
							$item = str_replace('7', '0', $item);
							// здесь новый кусок кода, который добавляет проверку на значения в скобках
							// тогда полное значение он переносит в подмассив special,
							// который будет разбираться уже в динамическом скрипте,
							// а также значение чистится и добавляется в дни, иначе этот день заблокируется
							// этот же код нужно вставить выше
							if ( strpos($item, '(') !== false && strpos($item, ')') !== false ) {
								$date -> special[] = $item;
								$item = preg_replace('/\(\w\)/', '$1', $item);
								unset($date -> week -> set[$key]);
							}
							// конец нового куска кода
							$date -> week -> days[] = $item;
						}
					}
					
					foreach ($date -> week -> days as $item) {
						$key = array_search($item, $schedule -> disabledays);
						unset ($schedule -> disabledays[$key]);
					}
					
					$date -> week = $date -> week -> set;
					
					// здесь новый кусок кода, который добавляет к каждому расписанию
					// общие заблокированные дни недели (если они есть)
					if (!empty($module -> settings -> week -> disabledays)) {
						$schedule -> disabledays = array_unique(array_merge($schedule -> disabledays, $module -> settings -> week -> disabledays));
					}
					// конец нового куска кода
					
					sort($schedule -> disabledays);
					
				}
			}
		}
		
		//print_r($module -> settings -> schedule);
		//exit;
	}
	
	// читаем файл заказов и преобразуем его в многоуровневый массив
	
	if (empty($module -> settings -> date -> sort)) {
		$module -> settings -> date = (object) array_merge( (array) $module -> settings -> date, array(
			'sort' => 'place,date,time',
		));
		//$module -> settings -> date -> sort = 'place,date,time';
	} else {
		$module -> settings -> date -> sort = str_replace(' ', '', $module -> settings -> date -> sort);
	}
	
	if (empty($module -> settings -> date -> key)) {
		$module -> settings -> date -> key = 'time';
	}
	
	if (
		!file_exists($module -> path . DS . 'data' . DS . $module -> param . '.csv') ||
		filesize($module -> path . DS . 'data' . DS . $module -> param . '.csv') === 0
	) {
		file_put_contents($module -> path . DS . 'data' . DS . $module -> param . '.csv', 'absolute,' . $module -> settings -> date -> sort . ',email,contacts,"datetime order",status');
	}
	
	if (
		file_exists($module -> path . DS . 'data' . DS . $module -> param . '.csv') ||
		filesize($module -> path . DS . 'data' . DS . $module -> param . '.csv') > 0
	) {
		$module -> data = dataloadcsv($module -> path . DS . 'data' . DS . $module -> param);
		$module -> sort = (object) array(
			'place' => array_search('place', $module -> data -> data[0]),
			'date' => array_search('date', $module -> data -> data[0]),
			'time' => array_search('time', $module -> data -> data[0])
		);
		unset($module -> data -> data[0]);
	} else {
		$module -> data = array();
	}
	
	$module -> data -> exclude = array();
	
	foreach ($module -> data -> data as &$item) {
		unset($item[0], $item[4], $item[5], $item[6]);
		if ($module -> settings -> date -> key === 'place') {
			$module -> data -> exclude[$item[ $module -> sort -> time ]][$item[ $module -> sort -> date ]][] = $item[ $module -> sort -> place ];
		} else {
			$module -> data -> exclude[$item[ $module -> sort -> place ]][$item[ $module -> sort -> date ]][] = $item[ $module -> sort -> time ];
		}
	}
	
	//print_r($module -> data -> exclude);
	
	$module -> data = $module -> data -> exclude;
	
?>

<style>
label {
	color: white;
	padding: 10px;
}
option,
select {
	padding: 2px;
	margin: 0px;
}

<?php if (!empty($module -> settings -> date -> alwaysview)) : ?>
.datepicker-dropdown::before,
.datepicker-dropdown::after {
	content: none;
	display: none;
}
.datepicker-dropdown {
	top: auto!important;
	bottom: auto!important;
	left: auto!important;
	right: auto!important;
	position: relative!important;
}
<?php endif; ?>

.datepicker-dropdown .disabled-date {
	color: red!important;
}


input[type="button"],
button[type="button"] {
	border: 0px;
	border-radius: 4px;
}
input[type="button"].selected,
button[type="button"].selected {
	background-color: blue;
}
input[type="button"].disabled,
button[type="button"].disabled {
	background-color: black;
}
</style>

<?php if ($_GET['order'] === 'error_fields' || $_POST['order'] === 'error_fields') : ?>
	<p>Ошибка в заказе - заполены не все поля!</p>
<?php elseif ($_GET['order'] === 'error_double' || $_POST['order'] === 'error_double') : ?>
	<p>Вы попытались повторить уже существующий заказ! Вам на почту отправлено повторное письмо с подтверждением.</p>
<?php elseif ($_GET['order'] === 'error_exists' || $_POST['order'] === 'error_exists') : ?>
	<p>Ошибка в заказе - такой заказ уже существует!</p>
<?php elseif ($_GET['order'] === 'error_noconfirm' || $_POST['order'] === 'error_noconfirm') : ?>
	<p>Ошибка в заказе - такой заказ был сделан, но еще не подтвержден! Если вам нужны именно эти места, подождите или перезвоните в службу поддержки. Возможна отмена заказа.</p>
<?php elseif ($_GET['order'] === 'complete' || $_POST['order'] === 'complete') : ?>
	<p>Заказ совершен. Теперь вам нужно его подтвердить, иначе заказ будет отменен!</p>
<?php endif; ?>

<?php
	if ($_SERVER['REQUEST_METHOD'] = 'GET') {
		$order = (object) array(
			'place' => $_GET['place'],
			'date' => $_GET['date'],
			'time' => $_GET['time'],
			'user' => $_GET['user'],
			'email' => $_GET['email'],
			'phone' => $_GET['phone']
		);
	} elseif ($_SERVER['REQUEST_METHOD'] = 'POST') {
		$order = (object) array(
			'place' => $_POST['place'],
			'date' => $_POST['date'],
			'time' => $_POST['time'],
			'user' => $_POST['user'],
			'email' => $_POST['email'],
			'phone' => $_POST['phone']
		);
	} else {
		$order = (object) array(
			'place' => '',
			'date' => '',
			'time' => '',
			'user' => '',
			'email' => '',
			'phone' => ''
		);
	}
	
?>