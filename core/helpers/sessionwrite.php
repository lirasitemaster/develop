<?php

// кроме всех этих проверок, возможно, нужно создать переменную сессии с именем пользователя (она уже и так есть, un)
// чтобы затем проверять значение user -> name с этой переменной
// т.к. она записана в сессии, ее невозможно подделать, также она будет гарантировать каждый раз, что пользователь - тот,
// за кого себя выдает
// в противном случае достаточно поменять user -> name и можно будет получить доступ к любой таблице и записи в базе данных,
// назначенных на этого пользователя, если к ней нет ограничения на права

if (!empty($_POST['name'])) {
	
	if (
		!cookie('UID', true) &&
		!isALLOW
	) {
		
		// это условие, на самом деле, для обработчика формы авторизации
		// и еще здесь должна быть куча проверок, в том числе на админку
		// и разрешение доступа к такой-то или такой-то части сайта
		
		if (!cookie('SID', true)) {
			session_start();
			cookie('SID', session_id());
		}
		
		global $userstable;
		global $user;
		global $verification;
		userSet();
		
		$find = userFind($_POST['name']);
		
		if (set($find)) {
			
			$user = objectMerge($user, (object) $find);
			
			if (SECURE_USERS) {
				init('users', 'secure');
				init('users', 'allow');
			}
			
			init('helpers', 'password');
			
		} elseif (
			defined('USERS_ROOT') && USERS_ROOT &&
			defined('USERS_PASSWORD') && USERS_PASSWORD &&
			$_POST['name'] === USERS_ROOT &&
			password_verify($_POST['password'], USERS_PASSWORD)
		) {
			
			if (SECURE_RIGHTS) {
				
				$usersrights = [
					'read' => true,
					'write' => true,
					'create' => true,
					'delete' => true
				];
				
				// записываем в сессию и куки
				// тогда ниже, в init.users.rights не придется их инициировать
				
				$_SESSION['rights'] = md5(json_encode($usersrights));
				cookie('rights', json_encode($usersrights));
				$user -> rights = $usersrights;
				
				unset($usersrights);
				
			}
			
			$verification = true;
			
		}
		
		// ну и здесь все просто - если true, все ок / false - проверку не прошел
		// ошибки там и прочее, но никакой сессии
		// вместо возврата можно сделать обнуление $user
		// а по возвращении смотреть $user и втыкать - пусто или там какая бяка, значит проверку не прошел
		// можно еще через константу смотреть
		
		if ($verification) {
			
			// после всех проверок, при условии, что они пройдены, авторизуем пользователя
			
			cookie('UID', $user -> uid);
			
			if (SECURE_SESSIONTIME) {
				$_SESSION['secure'] = true;
			}
			
			// сюда можно записывать переменные, которые нужно оставить в сессии
			// и таким образом частично защитить от перехвата
			
			// эта переменная указана только лишь для примера
			// $_SESSION['userid'] = time();
			
			$_SESSION['user'] = json_encode($user);
			
			if (empty($_SESSION['user'])) {
				$_SESSION['un'] = $_POST['name'];
			}
			
			// читаем права пользователя и сохраняем их в сессии и в куках
			init('users', 'rights');
			
		}
		
	}
	
	// а это - обновление страницы, ну так, на всякий случай, хотя может и необязательно
	// на первый взгляд кажется, что нужно из-за установки isALLOW
	// однако даже если обновления страницы не произойдет, эта константа будет обновлена при следующем запросе
	
	if (DEFAULT_ADMIN && str_replace('/', '', $_SERVER['REQUEST_URI']) === DEFAULT_ADMIN) {
		header('Location: /' . DEFAULT_ADMIN . '/');
	} else {
		header('Location: /');
	}
	exit;
	
}

if (!empty($_POST['exit']) && !empty($_SESSION)) {
	
	userUnset();
	header('Location: /');
	exit;
	
}

?>