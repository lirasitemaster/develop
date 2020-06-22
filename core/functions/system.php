<?php defined('isENGINE') or die;

/*
clear		< dataclear - Функция которая производит очистку данных по заданному параметру
crypting	< datacrypt - Функция которая шифрует данные
set			< dataset - Функция которая проверяет, установлено выражение и не пустое ли оно и может возвращать его значение
*/

/* ФУНКЦИЯ ИНИЦИАЛИЗАЦИИ КОМПОНЕНТОВ */

function init($folder, $name) {
	
	if ($folder === 'custom') {
		if (file_exists(PATH_ASSETS . $name . '.php')) {
			require_once PATH_ASSETS . $name . '.php';
			return true;
		} else {
			return false;
		}
	} elseif ($folder === 'class') {
		if (file_exists(PATH_CORE . 'classes' . DS . $name . DS . 'init.php')) {
			require_once PATH_CORE . 'classes' . DS . $name . DS . 'init.php';
			return true;
		} else {
			return false;
		}
	} else {
		if (file_exists(PATH_CORE . $folder . DS . $name . '.php')) {
			require_once PATH_CORE . $folder . DS . $name . '.php';
			return true;
		} else {
			return false;
		}
	}
	
}

/* ФУНКЦИЯ ЛОГИРОВАНИЯ СОБЫТИЙ */

function logging($data = null, $name = null) {
	
	$remote_addr = str_replace('.', '-', $_SERVER['REMOTE_ADDR']);
	$request_time = $_SERVER['REQUEST_TIME'];
	$memory_usage = memory_get_peak_usage() / 1024;
	$microtime = microtime();
	$microtime = substr($microtime, strpos($microtime, ' ') + 1) . substr($microtime, 1, 4);
	$request_microtime = !empty($_SERVER['REQUEST_TIME_FLOAT']) && !empty($microtime) ? number_format($microtime - $_SERVER['REQUEST_TIME_FLOAT'], 3, null, null) : null;
	
	$folder = LOG_MODE . '_by_' . LOG_SORT . DS;
	
	if (!file_exists(PATH_LOG . htmlentities($folder))) {
		mkdir(PATH_LOG . htmlentities($folder));
	}
	
	if (!$name || LOG_MODE === 'panic') {
		if (LOG_MODE === 'warning') {
			$name = htmlentities($data);
		} else {
			$name = str_replace('.', '-', $microtime) . '_' . $remote_addr . '_' . mt_rand();
		}
	}
	
	if (
		LOG_MODE === 'panic' ||
		LOG_MODE === 'warning'
	) {
		
		$data = '{' . "\r\n" . '"information":"' . htmlentities($data) . '",';
		
		foreach ([
			'request' => 'REQUEST_URI',
			'method' => 'REQUEST_METHOD',
			'port' => 'REMOTE_PORT',
			'ip' => 'REMOTE_ADDR',
			'protocol' => 'SERVER_PROTOCOL',
			'referrer' => 'HTTP_REFERER'
		] as $k => $i) {
			if (LOG_DATA && (LOG_DATA === true || strpos(LOG_DATA, $k) !== false)) {
				$data .= "\r\n" . '"' . $k . '" : "' . (!empty($_SERVER[$i]) ? htmlentities($_SERVER[$i]) : null) . '",';
			}
		}
		unset($k, $i);
		
		global $uri;
		global $user;
		
		$data .=
				(LOG_DATA && (LOG_DATA === true || strpos(LOG_DATA, 'agent') !== false) ? "\r\n" . '"agent" : "' . htmlentities(str_replace(['\\', '/'], '-', USER_AGENT)) . '",' : null) .
				(LOG_DATA && (LOG_DATA === true || strpos(LOG_DATA, 'speed') !== false) ? "\r\n" . '"speed" : "' . htmlentities(!empty($request_microtime) ? $request_microtime : time() - $request_time) . ' sec",' : null) .
				(LOG_DATA && (LOG_DATA === true || strpos(LOG_DATA, 'date') !== false) ? "\r\n" . '"date" : "' . htmlentities(date('Y-m-d H:i:s', $request_time)) . '",' : null) .
				(LOG_DATA && (LOG_DATA === true || strpos(LOG_DATA, 'time') !== false) ? "\r\n" . '"time" : "' . $microtime . '",' : null) .
				(LOG_DATA && (LOG_DATA === true || strpos(LOG_DATA, 'memory') !== false) ? "\r\n" . '"memory" : "' . number_format($memory_usage, 3, null, ' ') . ' Kb",' : null) .
				(LOG_DATA && (LOG_DATA === true || strpos(LOG_DATA, 'defines') !== false) ? "\r\n" . '"defines" : "isREQUEST' . (defined('isREQUEST') && isREQUEST ? '+' : '-') . ' isORIGIN' . (defined('isORIGIN') && isORIGIN ? '+' : '-') . ' isALLOW' . (defined('isALLOW') && isALLOW ? '+' : '-') . '",' : null) .
				(LOG_DATA && (LOG_DATA === true || strpos(LOG_DATA, 'session') !== false) ? "\r\n" . '"session" : ' . json_encode($_SESSION, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ',' : null) .
				(LOG_DATA && (LOG_DATA === true || strpos(LOG_DATA, 'cookies') !== false) ? "\r\n" . '"cookies" : ' . json_encode($_COOKIE, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ',' : null) .
				(LOG_DATA && (LOG_DATA === true || strpos(LOG_DATA, 'get') !== false) ? "\r\n" . '"get" : ' . json_encode($_GET, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ',' : null) .
				(LOG_DATA && (LOG_DATA === true || strpos(LOG_DATA, 'post') !== false) ? "\r\n" . '"post" : ' . json_encode($_POST, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ',' : null) .
				(LOG_DATA && (LOG_DATA === true || strpos(LOG_DATA, 'uri') !== false) ? "\r\n" . '"uri" : ' . json_encode($uri, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ',' : null) .
				(LOG_DATA && (LOG_DATA === true || strpos(LOG_DATA, 'user') !== false) ? "\r\n" . '"user" : ' . json_encode($user, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ',' : null)
		;
		
		if (mb_substr($data, -1) === ',') {
			$data = mb_substr($data, 0, -1);
		}
		
		$data .= "\r\n" . '}';
		
	} elseif (LOG_MODE === 'server') {
		$data = json_encode($_SERVER, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	} else {
		$data = htmlentities($data);
	}
	
	if (LOG_SORT) {
		switch (LOG_SORT) {
			case 'ip'      : $folder .= $remote_addr; break;
			case 'agent'   : $folder .= str_replace(['\\', '/'], '-', USER_AGENT); break;
			case 'request' : $folder .= (defined('isREQUEST') && isREQUEST ? 'good_request' : 'bad_request'); break;
			case 'origin'  : $folder .= (defined('isORIGIN') && isORIGIN ? 'good_origin' : 'bad_origin'); break;
			case 'speed'   : $folder .= round(time() - $request_time); break;
			case 'time'    : $folder .= $request_time; break;
			case 'minute'  : $folder .= floor($request_time / TIME_MINUTE) * TIME_MINUTE; break;
			case 'hour'    : $folder .= floor($request_time / TIME_HOUR) * TIME_HOUR; break;
			case 'day'     : $folder .= floor($request_time / TIME_DAY) * TIME_DAY; break;
			case 'week'    : $folder .= floor($request_time / TIME_WEEK) * TIME_WEEK; break;
			case 'month'   : $folder .= floor($request_time / TIME_MONTH) * TIME_MONTH; break;
			case 'memory'  :
				$m = round($memory_usage);
				$folder .= (strlen($m) <= 3) ? substr($m, 0, 1) . '00K' : floor($m / 1000) . 'M';
				unset($m);
				break;
			case 'name'    :
				$folder .= LOG_MODE === 'warning' ? $name : $remote_addr;
				if (LOG_MODE === 'warning') { $name = $remote_addr; }
				break;
			default :
				$folder .= LOG_SORT;
				break;
		}
		$folder .= DS;
	}
	
	if (!file_exists(PATH_LOG . htmlentities($folder))) {
		mkdir(PATH_LOG . htmlentities($folder));
	}
	
	if (LOG_MODE === 'warning' && file_exists(PATH_LOG . htmlentities($folder) . htmlentities($name) . '.ini')) {
		unlink(PATH_LOG . htmlentities($folder) . htmlentities($name) . '.ini');
	}
	
	file_put_contents(PATH_LOG . htmlentities($folder) . htmlentities($name) . '.ini', $data);
	
}

/* ФУНКЦИЯ ВЫЗОВА ОШИБКИ */

function errorlist($code){
	
	$code = (string) $code;
	$type = null;
	$error = null;
	
	switch ($code) {
		case '100' : $type = 'Continue'; break;
		case '101' : $type = 'Switching Protocols'; break;
		case '102' : $type = 'Processing'; break;
		case '200' : $type = 'OK'; break;
		case '201' : $type = 'Created'; break;
		case '202' : $type = 'Accepted'; break;
		case '203' : $type = 'Non-Authoritative Information'; break;
		case '204' : $type = 'No Content'; break;
		case '205' : $type = 'Reset Content'; break;
		case '206' : $type = 'Partial Content'; break;
		case '300' : $type = 'Multiple Choice'; break;
		case '301' : $type = 'Moved Permanently'; break;
		case '302' : $type = 'Found'; break;
		case '303' : $type = 'See Other'; break;
		case '304' : $type = 'Not Modified'; break;
		case '305' : $type = 'Use Proxy'; break;
		case '307' : $type = 'Temporary Redirect'; break;
		case '400' : $type = 'Bad Request'; break;
		case '401' : $type = 'Unauthorized'; break;
		case '402' : $type = 'Payment Required'; break;
		case '403' : $type = 'Forbidden'; break;
		case '404' : $type = 'Not Found'; break;
		case '405' : $type = 'Method Not Allowed'; break;
		case '406' : $type = 'Not Acceptable'; break;
		case '407' : $type = 'Proxy Authentication Required'; break;
		case '408' : $type = 'Request Timeout'; break;
		case '409' : $type = 'Conflict'; break;
		case '410' : $type = 'Gone'; break;
		case '411' : $type = 'Length Required'; break;
		case '412' : $type = 'Precondition Failed'; break;
		case '413' : $type = 'Payload Too Large'; break;
		case '414' : $type = 'URI Too Long'; break;
		case '415' : $type = 'Unsupported Media Type'; break;
		case '416' : $type = 'Range Not Satisfiable'; break;
		case '417' : $type = 'Expectation Failed'; break;
		case '500' : $type = 'Internal Server Error'; break;
		case '501' : $type = 'Not Implemented'; break;
		case '502' : $type = 'Bad Gateway'; break;
		case '503' : $type = 'Service Unavailable'; break;
		case '504' : $type = 'Gateway Timeout'; break;
		case '505' : $type = 'HTTP Version Not Supported'; break;
		case 'php'       : $error = 'Your host needs to use PHP ' . CMS_MINIMUM_PHP . ' or higher to run this version of isENGINE'; break;
		case 'blockip'   : $error = 'Blocking for ip is set, but blacklist or whitelist not found'; break;
		case 'update'    : $error = 'The site is undergoing technical work. Come back later'; break;
		case 'system'    : $error = 'One or more of system components not defined or not found'; break;
		case 'db_driver' : $error = 'Driver for database \'' . DB_TYPE . '\' not found'; break;
		case 'db_noset'  : $error = 'Database type not set on configuration file'; break;
		default :
			$code = '500';
			$type = 'Internal Server Error';
			break;
	}
	
	if (!empty($error)) {
		$code = '503';
		$type = 'Service Unavailable';
	}
	
	return [
		'code' => (int) $code,
		'status' => $type,
		'error' => $error
	];
	
}

function error($code, $refresh = true, $log = false){
	
	$list = errorlist($code);
	$error = &$list['error'];
	
	if (
		!empty($log) ||
		LOG_MODE === 'panic' ||
		LOG_MODE === 'warning' && !empty($error)
	) {
		if (!empty($error)) {
			$info = $error;
		} elseif ($code === '404') {
			$info = $code . ' from ' . str_replace(['/', '?', ':'], '_', $_SERVER['REQUEST_URI']);
		} else {
			$info = $code . ' ' . $list['status'];
		}
		logging(set($log, true), $info);
		unset($info);
	}
	if (DEFAULT_MODE === 'develop') {
		header('Error-Code: ' . $code);
		header('Error-Reason: ' . (!empty($error) ? $error : $status));
	}
	
	header($_SERVER['SERVER_PROTOCOL'] . ' ' . $list['code'] . ' ' . $list['status'], true, $list['code']);
	
	if (defined('isORIGIN') && isORIGIN) {
		if ($refresh) {
			reload(
				'/' . DEFAULT_ERRORS . '/' . $list['code'] . (!empty($error) ? '/' . $code : null) . '/',
				null,
				['Content-Type' => 'text/html; charset=UTF-8']
			);
			//header('Content-Type: text/html; charset=UTF-8');
			//header('Location: /' . DEFAULT_ERRORS . '/' . $list['code'] . (!empty($error) ? '/' . $code : null) . '/');
		} else {
			define('isERROR', $list['code'] . (!empty($error) ? ':' . $code : null));
			require_once PATH_TEMPLATES . DEFAULT_ERRORS . DS . 'template.php';
		}
	}
	
	exit;
	
}

function cookie($name, $set = false){
	
	/*
	*  Функция регистрации/удаления куки
	*  на входе нужно указать имя куки
	*  второй необязательный параметр служит триггером, меняющим поведение функции
	*  
	*  false или не указан - стереть куки, если вместо имени передан массив, будут удалены все указанные в нем куки
	*  true - проверка значения, если задано то возвращает его, если не задано, возвращает false
	*  если указано любое, кроме false и true - присвоить это значение куки
	*  
	*  Функция удобна тем, что сразу присваивает значение куки, без необходимости перезагружать страницу,
	*  а также выполняет все необходимые проверки
	*/
	
	if (is_array($name)) {
		foreach ($name as $item) {
			setcookie($item, '', time() - 3600, '/');
			unset($_COOKIE[$item]);
		}
		unset($item);
	} elseif ($set === true) {
		return $name === true ? $_COOKIE : (!empty($_COOKIE[$name]) ? $_COOKIE[$name] : null);
	} elseif ($set === false) {
		setcookie($name, '', time() - 3600, '/');
		unset($_COOKIE[$name]);
	} else {
		setcookie($name, $set, 0, '/');
		$_COOKIE[$name] = $set;
	}
	
}

function clear($data, $type = false, $skip = true, $special = null) {

	/*
	*  Функция которая производит очистку данных по заданному параметру
	*  например, перед передачей ее для записи в базу данных
	*  на входе нужно указать значение $data и тип преобразования $type
	*  
	*  параметры типов (если нужно несколько, можно перечислять через пробел и/или запятую):
	*    format - оставление в строке только (!) цифр, латинских букв, пробелов, и разрешенных знаков для передачи данных в формате системы
	*    alphanumeric - оставление в строке только (!) цифр, латинских букв и пробелов
	*    numeric - оставление в строке только (!) цифр
	*    datetime - оставление в строке только (!) цифр и знаков, встречающихся в формате даты и времени
	*    phone - приведение строки к телефонному номеру
	*    phone_ru - приведение строки к телефонному номеру россии (+7 заменяется на 8)
	*    login/email - приведение строки к формату логина/email
	*    url - приведение строки к формату url, включая спецсимволы
	*    simpleurl - приведение строки к формату url без спецсимволов, с обрезкой всех параметров
	*    urlencode - приведение строки к формату url, в котором символы кодируются % и hex-кодом
	*    urldecode - приведение строки из формата urlencode в обычный текстовый вид
	*    leavespaces - укажите, чтобы оставить по одному пробелу (если они вообще есть) в начале и в конце сторки
	*    tospaces - приведение всех пробелов, табуляций и символов пробелов к одному пробелу
	*    nospaces - удаление всех пробелов
	*    codespaces - удаление незначащих для кода пробелов, сокращение кода
	*    onestring - приведение данных к однострочному виду
	*    code - htmlspecialchars
	*    entities - htmlentities
	*    notags - удаление всех тегов
	*    cleartags - очищение всех атрибутов внутри тегов
	*    tags - удаление всех тегов, кроме разрешенных
	*      чтобы этот параметр работал корректно, входящие данные должны быть кодированы 
	*      htmlspecialchars, в противном случае теги будут очищены
	*      на предварительном этапе обработки
	*  
	*  теперь, если указать третий параметр 'false', то чистка тегов будет пропущена
	*  т.е. все теги в тексте останутся как есть
	*  если указать 'true', то будут оставлены только теги по-умолчанию
	*  если же задать массив, то будут исключены все теги, кроме указанных
	*  действие этого параметра не распространяется на код php и скрипты, т.к. они будут очищены в любом случае
	*  
	*  в функцию добавился четвертый параметр, который может быть как массивом, так и иметь значение true
	*  в качестве массива он может содержать ключи 'minlen', 'maxlen', 'minnum', 'maxnum' и 'match',
	*  по которым будет идти проверка входной строки, объект же будет преобразован в массив
	*  также данный параметр имеет и еще одно свойство: если этот параметр не пустой
	*  (например, строка или число, хотя мы настоятельно рекомендуем указывать массив или 'true'),
	*  окончательная, очищенная строка будет сравниваться с исходной
	*  в случае совпадения будет возвращаться очищенная строка, в противном случае - false
	*  таким образом, эта функция теперь объединяет в себе очищение и проверку
	*  
	*  также нельзя считать эту функцию полностью безопасной, т.к. она не очистит обфусцированные и шифрованные данные
	*  (т.е. переданные фрагментами), например: '<scr ipt>' или 'PHNjcmlwdD4=' (base64)
	*  однако мы стараемся сделать так, чтобы все файлы системы проходили антивирусную проверку,
	*  в частности через AIBolit, и не выдавать даже подозрений на вирусы,
	*  так чтобы вредоносный код можно было сразу же обнаружить
	*  
	*  на выходе отдает преобразованное значение $data
	*/
	
	// выполняем предварительное очищение - от скриптов, программного кода
	$data = preg_replace('/<\?.+?\?>/u', '', $data);
	$data = preg_replace('/<script.+?\/script>/ui', '', $data);
	
	if (!empty($skip)) {
		// продолжаем предварительное очищение - от всех тегов, кроме разрешенных
		// задаем разрешенные теги
		$tags = is_array($skip) ? $skip : [
			'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'pre', 'span', 'font', 'br', 'hr', 'img', // base elements
			'b', 'i', 's', 'u', 'blockquote', 'code', 'del', 'em', 'ins', 'small', 'strong', 'sub', 'sup', // base formatting
			'ul', 'ol', 'li', 'dl', 'dt', 'dd', 'details', 'summary', // list
			'table', 'thead', 'tbody', 'tfoot', 'th', 'tr', 'td', 'col', 'colgroup', 'caption', // table
			'abbr', 'bdi', 'bdo', 'cite', 'dfn', 'kbd', 'mark', 'q', 'rp', 'rt', 'rtc', 'ruby', 'samp', 'var', 'wbr' // additional
		];
		// подготавливаем список
		$striptags = '';
		foreach ($tags as $tag) {
			$striptags .= '<' . $tag . '>';
		}
		// завершаем
		unset($tags, $tag);
		// очищаем
		$data = strip_tags($data, $striptags);
	}
	
	// продолжаем предварительное очищение - чистим текст от пробелов и отступов в начале и в конце
	if (strpos($type, 'leavespaces') === false) {
		$data = trim($data);
		$data = preg_replace('/^(&nbsp;)+/ui', '', $data);
		$data = preg_replace('/(&nbsp;)+$/ui', '', $data);
	} else {
		$data = preg_replace('/^([\s\t]){2,}/u', '$1', $data);
		$data = preg_replace('/([\s\t]){2,]$/u', '$1', $data);
		$data = preg_replace('/^(&nbsp;){2,}/ui', '$1', $data);
		$data = preg_replace('/(&nbsp;){2,}$/ui', '$1', $data);
	}
	
	// продолжаем предварительное очищение - чистим текст от двойных пробелов
	$data = preg_replace('/(\s|&nbsp;){2,}/ui', '$1', $data);
	
	// создаем исходный результат
	$original = !empty($special) ? $data : null;
	
	if (!empty($type) && is_string($type)) {
		
		// выполняем очищение согласно заданных типов
		
		$qs = preg_split('/\,{0,1}\s|\,|\.\:/u', $type);
		foreach ($qs as $q) {
			
			switch ($q) {
				case 'format':
					$data = preg_replace('/[^a-zA-Z0-9_\- .,:;]/u', '', $data);
				break;
				case 'letters':
					$data = preg_replace('/[^\w]|\d/u', '', $data);
				break;
				case 'words':
					$data = preg_replace('/[^\w ]|\d/u', '', $data);
				break;
				case 'text':
					$data = preg_replace('/^[\w\d\s\-\'\"\.\,\!\?\(\)\:\№\*«»…—‒–]+$/u', '', $data);
				break;
				case 'alphanumeric':
					$data = preg_replace('/[^a-zA-Z0-9_\- ]/u', '', $data);
				break;
				case 'numeric':
					$data = preg_replace('/[^0-9]/u', '', $data);
				break;
				case 'datetime':
					$data = preg_replace('/[^0-9_\-.,:()\\\\\/ ]/u', '', $data);
				break;
				case 'phone':
					$data = preg_replace('/[^0-9]/u', '', $data);
					$original = !empty($special) ? $data : null;
				break;
				case 'phone_ru':
					$dataFirstSymbol = mb_substr($data, 0, 1);
					if (strlen($data) == 10) {
						$data = substr_replace($data, '7', 0, 0);
						$original = !empty($special) ? $data : null;
					} elseif ($dataFirstSymbol == 8) {
						$data = substr_replace($data, '7', 0, 1);
					}
				break;
				case 'login':
				case 'email':
					$data = preg_replace('/[^a-zA-Z0-9\-_.@]/u', '', $data);
				break;
				case 'url':
					$data = preg_replace('/[^a-zA-Z0-9\-_.:\/?&\'\"=#+]/u', '', $data);
					$data = rawurlencode($data);
				break;
				case 'simpleurl':
					$data = preg_replace('/[?&].*$/u', '', $data);
					$data = preg_replace('/[^a-zA-Z0-9\-_.:\/]/u', '', $data);
					$data = htmlspecialchars($data);
				break;
				case 'urlencode':
					$data = rawurlencode($data);
				break;
				case 'urldecode':
					$data = rawurldecode($data);
					$data = preg_replace('/[^a-zA-Z0-9\-_.:\/?&=#+ ]/u', '', $data);
				break;
				case 'tospaces':
					$data = str_replace('&nbsp;', ' ', $data);
					$data = preg_replace('/\s+/u', ' ', $data);
				break;
				case 'nospaces':
					$data = str_replace('&nbsp;', '', $data);
					$data = preg_replace('/\s/u', '', $data);
				break;
				case 'codespaces':
					$data = str_replace('&nbsp;', ' ', $data);
					$data = preg_replace('/\s+/u', ' ', $data);
					$data = preg_replace('/(.)\s(\W)/u', '$1$2', $data);
					$data = preg_replace('/([^\w"])\s(\w)/u', '$1$2', $data);
				break;
				case 'onestring':
					$data = preg_replace('/([^\s\t]|^)[\s\t]*(\r?\n){1,}[\s\t]*([^\s\t]|$)/u', '$1 $3', $data);
				break;
				case 'code':
					$data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5);
				break;
				case 'entities':
					$data = htmlentities($data);
				break;
				case 'tags':
					$data = htmlspecialchars_decode($data);
					$data = strip_tags($data, $striptags);
				break;
				case 'notags':
					//$data = preg_replace('/([^\s\t]|^)[\s\t]*(\r?\n){1,}[\s\t]*([^\s\t]|$)/', '$1 $3', $data);
					$data = preg_replace('/(<\/\w+?>)|(<\w+?\s.+?>)|(<\w+?>)/u', '', $data);
				break;
				case 'notagsspaced':
					//$data = preg_replace('/([^\s\t]|^)[\s\t]*(\r?\n){1,}[\s\t]*([^\s\t]|$)/', '$1 $3', $data);
					$data = preg_replace('/(<\/\w+?>)|(<\w+?\s.+?>)|(<\w+?>)/u', ' ', $data);
				break;
				case 'cleartags':
					//$data = preg_replace('/([^\s\t]|^)[\s\t]*(\r?\n){1,}[\s\t]*([^\s\t]|$)/', '$1 $3', $data);
					$data = preg_replace('/<(\w+)?\s.+?>/u', '<$1>', $data);
				break;
				case 'text':
					$data = preg_replace('/[^\w\d\s\-\'\".,!?():№*«»…—‒–]/u', '', $data);
				break;
			}
			
		}
		
		unset($qs, $q);
		
	}
	
	if (!empty($special)) {
		
		if (is_object($special)) {
			$special = (array) $special;
		}
		
		if (set($special) && is_array($special)) {
			
			// правило, задающее минимальную длину строки
			
			if (
				!empty($special['minlen']) &&
				is_numeric($special['minlen']) &&
				$special['minlen'] > 0 &&
				mb_strlen($data) < $special['minlen']
			) {
				$data = null;
			}
			
			// правило, задающее максимальную длину строки
			
			if (
				!empty($special['maxlen']) &&
				is_numeric($special['maxlen']) &&
				$special['maxlen'] > 0 &&
				mb_strlen($data) > $special['maxlen']
			) {
				$data = mb_substr($data, 0, $special['maxlen']);
			}
			
			// правило, задающее минимальное значение числа
			
			if (
				!empty($special['minnum']) &&
				is_numeric($special['minnum']) &&
				$special['minnum'] > 0 &&
				(float) str_replace(',', '.', $data) < $special['minnum']
			) {
				$data = $special['minnum'];
			}
			
			// правило, задающее максимальное значение числа
			
			if (
				!empty($special['maxnum']) &&
				is_numeric($special['maxnum']) &&
				$special['maxnum'] > 0 &&
				(float) str_replace(',', '.', $data) > $special['maxnum']
			) {
				$data = $special['maxnum'];
			}
			
			// правило, задающее соответствие определенной строке
			
			if (
				!empty($special['match']) &&
				is_string($special['match']) &&
				!preg_match('/' . preg_quote($special['match'], '/') . '/ui', $data)
			) {
				$data = null;
			}
			
		}
		
		if ($data !== $original) {
			$data = null;
		}
		
	}
	
	return $data;
}

function crypting($str, $do = false) {

	/*
	*  Функция которая шифрует данные
	*  на входе нужно указать:
	*    исходную строку
	*    параметы шифрования
	*      false и encode - кодировать
	*      true и decode - декодировать
	*      hash - спец.параметр, сделать хэш
	*  
	*  на выходе отдает готовую строку
	*  
	*  новый алгоритм шифрования:
	*  + более сложный для распознавания
	*  + меньше расчетов
	*  + генератор привязан ко времени
	*  + нет выявленных ошибок кодирования-декодирования
	*    * в том числе кодирует цифру ноль и отбрасывает все, кроме чисел и строк
	*  - строка увеличивается в среднем в 4-5 раз (старый алгоритм - в 3 раза)
	*    * чем больше строка, тем меньше увеличение
	*    * например, один символ увеличивается в 18 раз
	*    * а стих пушкина - в 3,5 раза
	*/
	
	if (!set($str) || !is_string($str) && !is_numeric($str)) {
		return false;
	}
	
	if (!$do || $do === 'encode') {
		
		//$a = '1234567890';
		//$a = 'привет на сто лет';
		//$a = time();
		
		$a0 = substr(time(), -2);
		$a1 = base64_encode($str);
		$a2 = strlen($a1);
		$a3 = '';
		
		$str = '';
		
		$c = 0;
		while ($c < $a2) {
			$a3 .= 999 - (ord($a1[$c]) + $a0);
			$c++;
		}
		
		$a30 = 9 - strlen($a3) % 9;
		
		if ($a30) {
			$a31 = substr($a3, (0 - (9 - $a30)));
			$a32 = substr($a3, 0, (0 - (9 - $a30)));
			$a33 = str_repeat('0', $a30);
			$a3 = $a32 . $a33 . $a31;
		}
		
		$a4 = strlen($a3) / 9;
		$a5 = '';
		
		$c = 0;
		while ($c < $a4) {
			
			$a5 = substr($a3, $c * 9, 9);
			$a5 = strrev($a5);
			$a5 = dechex((int) $a5);
			
			$a50 = 8 - strlen($a5);
			
			if ($a50) {
				$a51 = str_repeat('0', $a50);
				$a5 = $a51 . $a5;
			}
			
			$str .= $a5;
			$c++;
			
		}
		
		$str .= $a0;
		
		//echo $a . ' : ' . strlen($a) . '<br>' . $b . ' : ' . strlen($b) . ' (в ' . (strlen($b) / strlen($a)) . ' раз)<br>';
		
	} elseif ($do && $do !== 'hash') {
		
		$b0 = substr($str, -2);
		$b1 = str_split(substr($str, 0, -2), 8);
		$b2 = '';
		
		$str = '';
		
		foreach ($b1 as $i) {
			$i = (string) hexdec($i);
			$i = strrev($i);
			
			$b20 = 9 - strlen($i);
			if ($b20) {
				$b21 = str_repeat('0', $b20);
				$i = $i . $b21;
			}
			
			$b2 .= $i;
		}
		
		unset($i);
		
		$b3 = array_diff(str_split($b2, 3), ['000']);
		
		foreach ($b3 as $i) {
			$i = 999 - ($i + $b0);
			$i = chr($i);
			$str .= $i;
		}
		
		unset($i);
		
		$str = base64_decode($str);
		
	} else {
		
		$str = [
			'string' => $str,
			'code' => base64_encode($str),
			'temp' => '',
			'len' => ''
		];
		
		$str['len'] = strlen($str['string']);
		$str['len'] = floor($str['len'] / 2);
		
		$str['temp'] = strlen($str['code']);
		$str['temp'] = floor($str['temp'] / 4);
		
		$str['code'] =
			strrev(substr($str['code'], $str['temp'] * 2, $str['temp'])) . 
			substr($str['string'], 0, $str['len']) . 
			substr($str['code'], $str['temp'], $str['temp']) . 
			substr($str['string'], $str['len']);
		
		$str = strlen($str['string']) . strrev(md5($str['code']));
		// данный код может давать предупреждения антивируса, однако он является безопасным
		
	}
	
	return $str;
	
}

function set($item = null, $return = null) {
	
	/*
	*  Функция которая проверяет, установлено выражение и не пустое ли оно
	*
	*  на вход принимаются переменные, массивы, объекты, числа, строки, выражения и любые другие данные
	*  по-сути функция является надстройкой над isset, empty и аналогичными
	*  разница в том, что она отбрасывает проверку на 0,
	*  проверяет массивы на пустые выражения
	*  и еще проверяет существование переменной или выражения
	*  
	*  если указать второй параметр true, то функция после проверки выражения,
	*  если оно существует, вернет его, иначе - false
	*  если второй параметр указан не true и не false, то будет возвращено указанное значение
	*  например:
	*    set($x)                   - вернет true     / false
	*    set($x, true)             - вернет $x       / false
	*    set($x, 'string')         - вернет 'string' / false
	*    set(is_int($x), true)     - вернет true     / false, потому как значение любого выражения true/false
	*    set(is_int($x), 'string') - вернет 'string' / false
	*  
	*  примеры на false:
	*  $x          !empty($x) isset($x) if($x) set($x)   set($x, true) echo set($x, 'return')
	*  null        false      false     false  false     false         ''
	*  false       false      true      false  false     false         ''
	*  ''          false      true      false  false     false         ''
	*  []          false      true      false  false     false         ''
	*  ['']        true       true      true   false     false         ''
	*  примеры на  true:
	*  true        true       true      true   true      true          'return'
	*  0           false      true      false  true      0             'return'
	*  '0'         false      true      false  true      '0'           'return'
	*  1           true       true      true   true      1             'return'
	*  '1'         true       true      true   true      '1'           'return'
	*  'string'    true       true      true   true      'string'      'return'
	*  ['0']       true       true      true   true      ['0']         'return'
	*  ['1']       true       true      true   true      ['1']         'return'
	*  ['string']  true       true      true   true      ['string']    'return'
	*  примеры на выражения:
	*  is_int(1)   true       true      true   true      true          'return'
	*  is_int('1') false      false     false  false     false         ''
	*/
	
	if ($return) {
		return set($item) ? ($return === true ? $item : $return) : null;
	}
	
	if (is_object($item)) {
		$item = (array) $item;
	}
	
	if (
		isset($item) &&
		$item === true
	) {
		return true;
	} elseif (
		!isset($item) ||
		$item === false ||
		$item === null
	) {
		return null;
	} elseif (
		empty($item) &&
		is_numeric($item)
	) {
		return true;
	} elseif (empty($item)) {
		return null;
	} elseif (is_array($item)) {
		foreach ($item as $i) {
			if (set($i)) {
				return true;
			}
		}
		return null;
	}
	
	return true;
	
}

function csrf($token = false) {
	
	if (empty($token)) {
		
		// здесь просто возвращаем значение токена
		// если оно еще не было задано, мы генерируем его
		// очень удобно для использования в шаблоне
		// например, в формах, в передаче в заголовок через js, в полях типа meta
		
		// читаем токен
		if (SECURE_CSRF === 'cookie') {
			// через куки
			$token = cookie('csrf', true);
			//$token .= ':cookie';
		} elseif (SECURE_CSRF === 'header') {
			// через заголовок
			if (!empty($_SERVER['HTTP_X_CSRF_TOKEN'])) {
				$token = $_SERVER['HTTP_X_CSRF_TOKEN'];
				//$token .= ':header';
			} elseif (!empty(apache_response_headers()['X-CSRF-Token'])) {
				$token = apache_response_headers()['X-CSRF-Token'];
				//$token .= ':apache';
			}
		}
		
		if (empty($token)) {
			
			// если токен все еще пуст, значит он не был задан вообще
			// создадим его
			global $user;
			$token = md5($user -> uid);
			//$token .= ':empty';
			
		}
		
		return $token;
		
	} elseif ($token === true) {
		
		// здесь устанавливаем токен, как положено
		
		// сначала создадим его
		$token = csrf();
		
		// записываем токен в сессию как пароль
		if (empty($_SESSION['csrf'])) {
			$_SESSION['csrf'] = password_hash($token, PASSWORD_DEFAULT);
		}
		
		// передаем токен пользователю
		if (SECURE_CSRF === 'cookie') {
			// через куки
			cookie('csrf', $token);
		} elseif (SECURE_CSRF === 'header') {
			// через заголовок
			if (!headers_sent()) {
				header('X-CSRF-Token:' . $token, true);
			}
		}
		// либо не передаем, тогда придется генерировать его позже
		
	} else {
		
		// здесь проверяем токен
		// переданное значение токена сравнивается со значением, сохраненным в сессии
		// если же сессия пуста, то для сравнения берется одно из специальных значений, согласно SECURE_CSRF
		// cookie - значение берется из кук
		// header - значение берется из заголовка
		// если же SECURE_CSRF задан, например, просто true, либо cookie и header пусты по другой причине,
		// то проверяемое значение токена генерируется заново
		
		if (!empty($_SESSION['csrf'])) {
			// проводим сравнение с токеном из сессии
			$verify = $_SESSION['csrf'];
		} else {
			// либо восстанавливаем значение
			$verify = password_hash(csrf(), PASSWORD_DEFAULT);
		}
		
		return password_verify($token, $verify);
		
	}
	
}

function send($arr, $message, $subject = null, $data = [], $clear = null) {
	
	/*
	*  функция принимает данные и отправляет сообщения
	*  на данный момент реализована отправка email, vk, whatsapp, sms
	*  обработки и проверки данных пока нет
	*  
	*  на входе нужно указать:
	*    arr - массив данных (напр. "type" : "mail", "param" : "", "id" : "mail@mail.com", "key" : "")
	*    subject - тема сообщения
	*    data - массив данных [key => item], где key - название, item - значение
	*    message - текстовое сообщение
	*    clear - параметры очистки
	*    template - разрешен ли шаблон и указываем здесь имя шаблона
	*/
	
	if (empty($arr) || !is_object($arr) && !is_array($arr)) {
		return false;
	} elseif (is_array($arr)) {
		$arr = (object) $arr;
	}
	
	$message = clear($message, $clear, false);
	$subject = clear($subject);
	
	if ($arr -> type === 'mail') {
		
		// отправка сообщений по электронной почте
		
		$headers  = "Content-type: text/html; charset=utf-8 \r\n" . 
					"From: no-reply@" . $_SERVER['SERVER_NAME'] . "\r\n" . 
					"Reply-To: no-reply@" . $_SERVER['SERVER_NAME'] . "\r\n" . 
					USER_SENDER;
		
		if (empty($arr -> id)) { $arr -> id = USERS_EMAIL; }
		
		// проверка на наличие шаблона
		
		if (!empty($arr -> template)) {
			$template = PATH_ASSETS . 'send' . DS . 'templates' . DS . $arr -> template . '.php';
		}
		
		if (file_exists($template)) {
			$text = null;
			require $template;
			$message = $text;
		} else {
			$message = '<p>' . $message . '</p>';
			if (!empty($data)) {
				$message = '<p></p>';
				foreach ($data as $key => $item) {
					$message .= '<p>' . $key . ': ' . print_r($item, true) . '</p>';
				}
				unset($key, $item);
			}
		}
		
		$result = mail($arr -> id, $subject, $message, $headers);
		
	} elseif ($arr -> type === 'sms') {
		
		// отправка сообщений по СМС
		
		if (!empty($subject)) {
			$message = '[' . $subject . '] ' . $message;
		}
		
		if (!empty($data)) {
			$message .= ' | ' . key($data) . ': ' . array_shift($data);
			foreach ($data as $key => $item) {
				$message .= ', ' . $key . ': ' . $item;
			}
			unset($key, $item);
		}
		
		$newarr = (object) [
			'key' => json_decode('{"id":"' . $arr -> id . '","message":"' . $message . '",' . ((in_array('mbstring', get_loaded_extensions())) ? mb_substr(json_encode($arr -> key), 1) : substr(json_encode($arr -> key), 1)), true), // EDITED FOR NOT PHP MODULE
			'param' => $arr -> param
		];
		
		foreach ($newarr -> key as $k => $i) {
			$newarr -> param = str_replace( '{' . $k . '}', $i, $newarr -> param);
		}
		
		$result = file_get_contents($arr -> param);
		
	} elseif ($arr -> type === 'whatsapp' || $arr -> type === 'whatsappget') {
		
		// отправка сообщений по WhatsApp
		
		if (!empty($subject)) {
			$message = "[" . $subject . "]\r\n" . $message;
		}
		
		$message .= "\r\n";
		
		if (!empty($data)) {
			foreach ($data as $key => $item) {
				$message .= $key . ': ' . $item . "\r\n";
			}
			unset($key, $item);
		}
		
		if ($arr -> type === 'whatsappget') {
			
			$content = $arr -> param .
				'?' . $arr -> key -> token . '=' . $arr -> key -> key .
				'&' . $arr -> key -> id . '=' . $arr -> id .
				'&' . $arr -> key -> message . '=' . urlencode($message);
			$result = file_get_contents($content);
			
		} else {
			
			$result = file_get_contents($arr -> param . '?' . $arr -> key -> token . '=' . $arr -> key -> key, false, stream_context_create([
				'http' => [
					'method'  => 'POST',
					'header'  => 'Content-type: application/json',
					'content' => json_encode([
						$arr -> key -> id => $arr -> id,
						$arr -> key -> message => $message,
					])
				]
			]));
			
		}
		
	} elseif ($arr -> type === 'vk' || $arr -> type === 'vkontakte') {
		
		// отправка сообщений для вконтакте
		
		if (!empty($subject)) {
			$message = $subject . "\r\n\r\n" . $message;
		}
		
		$message .= "\r\n\r\n";
		
		if (!empty($data)) {
			foreach ($data as $key => $item) {
				$message .= $key . ': ' . $item . "\r\n";
			}
			unset($key, $item);
		}
		
		$result = file_get_contents('https://api.vk.com/method/messages.send', false, stream_context_create([
			'http' => [
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => http_build_query(
					[
						$arr -> param => $arr -> id,
						'message' => $message,
						'access_token' => $arr -> key,
						'v' => '5.37'
					]
				)
			]
		]));
		
	}
	
	// логирование результата
	
	$result = [
		'status' => $result === true ? 'ok' : 'error',
		'sets' => $arr,
		'message' => $message,
		'subject' => $subject,
		'data' => $data,
		'errors' => $result === true ? null : $result
	];
	
	if (!file_exists(PATH_LOG . 'send')) { mkdir(PATH_LOG . 'send'); }
	
	if (
		defined('LOG_MODE') &&
		(LOG_MODE === 'panic' || LOG_MODE === 'warning')
	) {
		if (!file_exists(PATH_LOG . 'send' . DS . 'log_' . $arr -> type)) { mkdir(PATH_LOG . 'send' . DS . 'log_' . $arr -> type); }
		file_put_contents(PATH_LOG . 'send' . DS . 'log_' . $arr -> type . DS . date('Ymd.His') . '.' . mt_rand(1000, 9999) . '.' . str_replace(':', '.', $_SERVER['REMOTE_ADDR']) . '.ini', json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	}
	if (
		$result['status'] === 'error' &&
		defined('LOG_MODE') && LOG_MODE !== 'warning'
	) {
		if (!file_exists(PATH_LOG . 'send' . DS . 'log_' . $arr -> type)) { mkdir(PATH_LOG . 'send' . DS . 'log_' . $arr -> type); }
		file_put_contents(PATH_LOG . 'send' . DS . 'log_' . $arr -> type . DS . date('Ymd.His') . '.' . mt_rand(1000, 9999) . '.' . str_replace(':', '.', $_SERVER['REMOTE_ADDR']) . '.ini', json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	}
	
	// возврат результата
	
	return $result;
	
}

function reload($path = '/', $code = null, $data = null) {
	
	/*
	*  функция перезагружает страницу
	*  
	*  на входе можно указать:
	*    url-адрес (относительный)
	*    массив данных, которые будут добавлены в заголовок
	*/
	
	if (headers_sent()) {
		return;
	}
	
	if (!empty($data) && is_array($data)) {
		foreach ($data as $key => $item) {
			header($key . ': ' . $item);
		}
		unset($key, $item);
	}
	
	if (!empty($code)) {
		$list = errorlist($code);
		header($_SERVER['SERVER_PROTOCOL'] . ' ' . $list['code'] . ' ' . $list['status'], true, $list['code']);
	}
	
	header('Location: ' . $path);
	
	exit;
	
}

?>