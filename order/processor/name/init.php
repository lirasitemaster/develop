<?php defined('isCMS') or die;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	
	$module = (object) array(
		'form' => (object) array(
			'place' => $_GET['place'],
			'date' => $_GET['date'],
			'time' => $_GET['time'],
			'user' => $_GET['user'],
			'email' => $_GET['email'],
			'phone' => $_GET['phone'],
			'addition' => $_GET['addition'],
			'lang' => $currlang,
			'currdate' => date('d.m.Y H:i (P') . ' GMT)',
		),
		'base' => (object) array(
			'key' => $_GET['key'],
			'sort' => $_GET['sort'],
			'module' => $_GET['module'],
			'path' => PATH_MODULES . DS . $_GET['query'] . DS . 'data',
		),
		'data' => (object) array(),
		'settings' => (object) array(),
		'original' => (object) array(),
		'send' => (object) array(),
		'lang' => (object) array(),
		'status' => 0
	);
	
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
	
	$module = (object) array(
		'form' => (object) array(
			'place' => $_POST['place'],
			'date' => $_POST['date'],
			'time' => $_POST['time'],
			'user' => $_POST['user'],
			'email' => $_POST['email'],
			'phone' => $_POST['phone'],
			'addition' => $_POST['addition'],
			'lang' => $currlang,
			'currdate' => date('d.m.Y H:i (P') . ' GMT)',
		),
		'base' => (object) array(
			'key' => $_POST['key'],
			'sort' => $_POST['sort'],
			'name' => $_POST['name'],
			'path' => PATH_MODULES . DS . $_POST['query'] . DS . 'data',
		),
		'data' => (object) array(),
		'settings' => (object) array(),
		'original' => (object) array(),
		'send' => (object) array(),
		'lang' => (object) array(),
		'status' => 0
	);
	
}

if (
	empty($module -> form -> user) ||
	empty($module -> form -> email) ||
	empty($module -> form -> place) ||
	empty($module -> form -> date) ||
	empty($module -> form -> time)
) {
	header("Location: " . $_SERVER['REDIRECT_URL'] . "?order=error_fields" . "&place=" . $module -> form -> place . "&date=" . $module -> form -> date . "&time=" . $module -> form -> time . "&user=" . $module -> form -> user . "&email=" . $module -> form -> email . "&phone=" . $module -> form -> phone);
	exit;
}

$module -> data = dataloadcsv($module -> base -> path . DS . $module -> base -> name);
$module -> settings = json_decode(json_encode($module -> data -> settings));
$module -> data = json_decode(json_encode($module -> data -> data));
$module -> original = json_decode(json_encode($module -> form));
$module -> send = json_decode(json_encode($module -> settings -> send));
$module -> lang = json_decode(json_encode($module -> settings -> message));
$module -> status = 0;

//objectLang($module -> lang);
unset($module -> settings);

// разбор дат

if (strpos($module -> form -> date, ',') !== false) {
	
	$dates = preg_split('/[\s,]/', $module -> form -> date);
	$module -> form -> date = $dates[0];
	$module -> form -> dates = array();
	
	foreach ($dates as &$date) {
		$date = strtotime($date . ' ' . $module -> form -> time . ' UTC');
	}
	
	while ($dates[0] < $dates[1]) {
		$module -> form -> dates[] = date('d.m.Y', $dates[0]);
		$dates[0] = $dates[0] + TIME_DAY;
	}
	
	unset($dates, $date);
	
}

// сортировка массива

$sort = array();

if (!empty($module -> base -> sort)) {
	$module -> base -> sort = preg_replace('/[\[\]]/' , '', $module -> base -> sort);
	$sort[0] = preg_split('/[\s,]/', $module -> base -> sort);
} else {
	$sort[0] = ['place', 'date', 'time'];
}

$sort[1] = array(
	'place' => array_search('place', $module -> data[0]),
	'date' => array_search('date', $module -> data[0]),
	'time' => array_search('time', $module -> data[0]),
);

// обработка массива

foreach ($module -> data as &$item) {
	
	$item[array_search('place', $sort[0]) + 1] = $item[$sort[1]['place']];
	$item[array_search('date', $sort[0]) + 1] = $item[$sort[1]['date']];
	$item[array_search('time', $sort[0]) + 1] = $item[$sort[1]['time']];

	if ($item[array_search('place', $sort[0]) + 1] === $module -> form -> place && $item[array_search('date', $sort[0]) + 1] === $module -> form -> date && $item[array_search('time', $sort[0]) + 1] === $module -> form -> time) {
	
		if ($item[4] == $module -> form -> email) {
			$module -> status = 'error_double';
			
			// сюда нужно добавить отправление письма тому, кто оформил заказ,
			// с уведомлением о том, что заказ был оформлен повторно и
			// с просьбой о подтверждении, иначе заказ будет отменен в течение 30 мин!
			
		} elseif ($item[7] == 1) {
			$module -> status = 'error_exists';
		} else {
			$module -> status = 'error_noconfirm';
			
			// сюда нужно добавить отправление письма тому, кто оформил заказ,
			// с уведомлением о том, что кто-то другой хочет сделать тот же заказ и
			// с просьбой о подтверждении, иначе заказ будет отменен!
			
		}
	}
}

if (!$module -> status) {
	
	if (strpos($module -> form -> place, ',') !== false) {
		
		$module -> form -> places = preg_split('/[\s,]/', $module -> form -> place);
		$module -> form -> places = objectClear($module -> form -> places);
		sort($module -> form -> places);
		
		foreach ($module -> form -> places as $place) {
			$module -> form -> place = $place;
			foreach ($sort[0] as $k => $i) {
				$sort[3][$k] = $module -> form -> $i;
			}
			
			$module -> data[] = [(string)strtotime($module -> form -> date . ' ' . $module -> form -> time . ' UTC'), $sort[3][0], $sort[3][1], $sort[3][2], $module -> form -> email, '{user: ' . $module -> form -> user . ', phone: ' . $module -> form -> phone . ', ip: ' . $_SERVER['REMOTE_ADDR'] . ', browser: ' . USER_AGENT . ' lang: ' . $module -> form -> lang . '}', $module -> form -> currdate];
			
		}
		
	} elseif (!empty($module -> form -> dates)) {
		
		foreach ($module -> form -> dates as $date) {
			$module -> form -> date = $date;
			foreach ($sort[0] as $k => $i) {
				$sort[3][$k] = $module -> form -> $i;
			}
			
			$module -> data[] = [(string)strtotime($module -> form -> date . ' ' . $module -> form -> time . ' UTC'), $sort[3][0], $sort[3][1], $sort[3][2], $module -> form -> email, '{user: ' . $module -> form -> user . ', phone: ' . $module -> form -> phone . ', ip: ' . $_SERVER['REMOTE_ADDR'] . ', browser: ' . USER_AGENT . ' lang: ' . $module -> form -> lang . '}', $module -> form -> currdate];
			
		}
		unset($date);
		
	} else {
		
		foreach ($sort[0] as &$i) { $i = $module -> form -> $i; }
		$module -> data[] = [(string)strtotime($module -> form -> date . ' ' . $module -> form -> time . ' UTC'), $sort[0][0], $sort[0][1], $sort[0][2], $module -> form -> email, '{user: ' . $module -> form -> user . ', phone: ' . $module -> form -> phone . ', ip: ' . $_SERVER['REMOTE_ADDR'] . ', browser: ' . USER_AGENT . ' lang: ' . $module -> form -> lang . '}', $module -> form -> currdate];
		
	}
	
	rsort($module -> data);
	
	datasavecsv($module -> data, $module -> base -> path . DS . $module -> base -> name);
	
	$module -> status = 'complete';
	
	if ($module -> base -> key === 'place') {
		$module -> form -> place = '';
	} else {
		$module -> form -> time = '';
	}
	
	//message($module -> send, $module -> lang -> subject, (object) array('value' => $module -> original, 'label' => $module -> lang), $module -> lang -> text, $module -> base -> path . DS . $module -> base -> name . '_error');
	//теперь send
	
	/*
	*  message($arr, $subject, $data, $message, $errors, $status) {
	*  Функция подготовки и вызова отправки сообщения
	*  на входе нужно указать:
	*    arr - массив данных (напр. "type" : "email", "param" : "", "id" : "mail@mail.com", "key" : "")
	*    subject - тема сообщения
	*    data - массив данных ("label" - название данных, "value" - значение данных)
	*    message - текстовое сообщение
	*    errors - путь и название файла с логом ошибок, если письма не были доставлены
	*  
	*  функция примет данные и вызовет messageSend с теми же параметрами
	*/
	
	// сюда нужно добавить отправление письма тому, кто оформил заказ,
	// с просьбой о подтверждении, иначе заказ будет отменен в течение 30 мин!
	
}

header("Location: " . $_SERVER['REDIRECT_URL'] . "?order=" . $module -> status . "&place=" . $module -> form -> place . "&date=" . $module -> form -> date . "&time=" . $module -> form -> time . "&user=" . $module -> form -> user . "&email=" . $module -> form -> email . "&phone=" . $module -> form -> phone);
exit;

?>