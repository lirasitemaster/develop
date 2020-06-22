<?php defined('isCMS') or die;

function userSet() {
	
	global $user;
	
	$user = (object) [
		'ip' => ipReal(),
		'uid' => '',
		'sid' => session_id(),
		'token' => crypting(time()),
		'allow' => [],
		'rights' => []
	];
	
	if (!empty($user -> sid)) {
		
		$user -> uid = md5($user -> sid . $user -> ip . USER_AGENT);
		
		if (!set($_SESSION['token'])) {
			$_SESSION['token'] = $user -> token;
		} else {
			$user -> token = $_SESSION['token'];
		}
		
		if (SECURE_CSRF) {
			csrf(true);
		}
		
	}
	
	if (LOG_MODE === 'panic') {
		logging('userSet data -- ' . json_encode($user));
	}
	
}

function userPID() {
	
	// функция генерирует PID - process id для определения одинаковых запросов
	
	global $user;
	
	if (empty($user) || empty($user -> id)) {
		$pid = md5($user -> ip . USER_AGENT);
	} else {
		$pid = md5($user -> id);
	}
	
	//echo '<br>{{' . print_r($user, true) . '}}<br>';
	
	if (
		empty(cookie('PID', true)) ||
		cookie('PID', true) !== $pid
	) {
		cookie('PID', $pid);
	}
	
	return $pid;
	
}

function userFind($name) {
	
	// пробуем читать по имени пользователя в базе данных
	$try = dbUse('users:' . $name, 'select');
	
	if (empty($try)) {
		
		// если не получилось - не беда
		// теперь мы ищем все поля в данных, по которым разрешена авторизация
		
		global $userstable;
		
		$find = dbUse($userstable, 'filter', ['filter' => 'system:authorise', 'return' => 'name']);
		foreach ($find as &$item) {
			$item .= ':' . $name;
		}
		unset($item);
		$find = objectToString($find);
		
		// а теперь обращаемся к разделу базы данных с пользователями,
		// и вытаскиваем всех пользователей, у которых
		// есть хотя бы одно поле авторизации с искомым значением
		
		$try = dbUse('users', 'select', ['filter' => $find, 'or' => 1]);
		
	}
	
	//print_r($find);
	//echo '<br>';
	//print_r($try);
	
	if (empty($try) || !is_array($try)) {
		// если на этот раз тоже не повезло,
		// значит, пользователь введен неверно
		return false;
	} elseif (count($try) > 1) {
		
		// здесь нам, в общем-то, не важны совпадения по значениям полей
		// если вы задаете поле с разрешением для авторизации,
		// позаботьтесь о том, чтобы в характеристиках оно было уникальным,
		// хотя позже мы наверняка уберем уникальность данных для полей без авторизации,
		// а для полей авторизации реализуем в системе автоматическую проверку уникальности
		
		// тем не менее, уникальное поле влияет лишь на запись данных
		// например, при регистрации пользователя или при редактировании его профиля,
		// или, например, если пользователь забыл логин и пароль
		// в любом случае, проверка должна осуществляться не здесь
		
		// однако сюда мы все же добавляем проверку,
		// что если обнаружено несколько пользователей, то система выдает ошибку -
		// это защитит от какого-либо несанкционированного взлома
		// или, по крайней мере, от случайного доступа к чужому аккаунту
		
		error('403', true, 'more than one user with specified name or value of another authorised field was found in database');
		
	} else {
		// если же мы нашли всего одного пользователя, то возвращаем его данные
		return array_shift($try);
	}
	
}

function userUnset() {
	if (isALLOW) {
		session_destroy();
	}
	unset($_SESSION);
	cookie(['SID', 'UID', 'rights', 'allow']);
}

?>