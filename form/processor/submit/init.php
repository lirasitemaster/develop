<?php defined('isENGINE') or die;

// не хватает:
// мин-макс числа символов
// мин-макс значения в цифрах
// ввод диапазона и указание прироста
// шаблон ввода

// мы получаем данные в нескольких видах:
// - данные, полученные из полей формы
// - настройки валидации этих данных
// - настройки очистки этих данных
// здесь стоит отметить, что очистка будет проводиться в любом случае,
// просто указанные параметры будут дополнять стандартную очистку
// 
// кроме этих данных, нам нужно получать дополнительную служебную информацию
// - имя модуля
// - токен для капчи
// - настройки модуля

// практичесвки для всего требуются настройки модуля!!!
// капча - вкл/выкл
// поля, тип, валидация
// кому отправлять в случае ок и отправлять ли уведомление
// и т.д. и т.п.

// первым делом собираем данные из запроса

// ЧТО ДОБАВИТЬ
// - в системную функцию send запись ошибок, также как и логов, только при установленной настройке системы записывать только логи ошибок или паническом режиме
// - группировку шаблона формы по папкам
// - очистку, кроме валидации, в настройки модуля
// - опцию отправки уведомления админу
// - опцию отправки уведомления пользователю об успешном заполнении формы на указанную почту или другой канал связи (в таком случае выбор адреса отправки ведется по значению заданного в этих настройках поля)
// - нам нужно отправлять статус
// - если была ошибка, нам нужно указать поля с ошибками
// - фильтрацию спама (через поле 'filter' в настройках формы)

global $uri;

$module = (object) [
	'path' => $uri -> site,
	'settings' => [],
	'captcha' => null
];

// вторым делом проверяем статус запроса
// и если статус завершенный, то сразу переходим к переадресации

if ($process -> set -> status === 'complete') {
	
	if (!empty($module -> settings['redirect'])) {
		$module -> path .= $module -> settings['redirect'];
	} else {
		$module -> path .= $uri -> previous;
	}
	
	if (objectIs($process -> data)) {
		$module -> path .= '?data[' . key($process -> data) . ']=' . htmlentities(reset($process -> data));
		array_shift($process -> data);
		if (objectIs($process -> data)) {
			foreach ($process -> data as $key => $item) {
				$module -> path .= '&data[' . $key . ']=' . htmlentities($item);
			}
			unset($key, $item);
		}
	}
	
	header("Location: " . $module -> path);
	exit;
	
}

// вторым номером читаем настройки и объединяем их с предыдущими данными

$module -> settings = dbUse('modules:' . $process -> source['module'] . ($process -> source['module'] !== 'default' ? ':default' : null), 'select', ['allow' => 'parent:form', 'return' => 'name:data']);

if (objectIs($module -> settings)) {
	$keys = array_keys($module -> settings);
	if (in_array($process -> source['module'], $keys)) {
		$module -> settings = $module -> settings[$process -> source['module']];
	} else {
		$module -> settings = $module -> settings['default'];
	}
	unset($keys);
}

if (empty($module -> settings)) {
	$module -> settings = localFile(PATH_MODULES . 'isengine' . DS . 'form' . DS . 'data' . DS . $process -> source['module'] . '.ini');
	if (!empty($module -> settings)) {
		$module -> settings = iniPrepareJson($module -> settings, true);
	}
}

if (empty($module -> settings)) {
	exit;
}

// читаем капчу

if (!empty($module -> settings['captcha']['enable'])) {
	
	global $user;
	$captcha = str_replace(['.', ':'], '-', $user -> ip) . '-' . $process -> source['hash'];
	$captcha = dbUse('captcha:' . $captcha, 'select');
	if (objectIs($captcha)) {
		$captcha = array_shift($captcha);
		$module -> captcha = set($captcha['data'], true);
	}
	unset($captcha);
	
}

// теперь начинаем эти данные песочить
// проверка на валидность

require PATH_MODULES . 'isengine' . DS . 'form' . DS . 'process' . DS . 'functions.php';

foreach($module -> settings['form'] as $item) {
	
	$item['options'] = moduleFormOptionsGenerator($item['options']);
	
	if (!empty($module -> settings['required'])) {
		$item['required'] = true;
	}

	moduleFormValidate(
		$process -> data[$item['name']],
		$item,
		$process -> errors,
		$module -> var['message'],
		$module -> captcha
	);
	
	//echo '$process -> data[$item[name]]' . ' : <pre>' . print_r($process -> data[$item['name']], 1) . '</pre><br>';
	//echo '$item' . ' : <pre>' . print_r($item, 1) . '</pre><br>';
	//echo '$process -> errors' . ' : <pre>' . print_r($process -> errors, 1) . '</pre><br>';
	//echo '$module -> var[message]' . ' : <pre>' . print_r($module -> var['message'], 1) . '</pre><br>';
	//echo '<hr>';
	
}
unset($item);

// сюда нужно добавить вызов дополнительного обработчика

if (!empty($module -> settings['customprocess'])) {
	require PATH_ASSETS . 'send' . DS . 'process' . DS . ($module -> settings['customprocess'] === true ? 'default' : str_replace(':', DS, $module -> settings['customprocess'])) . DS . 'init.php';
}

// а здесь проверка ошибок и присвоение статуса

if (empty($process -> errors)) {
	$process -> set -> status = 'ready';
} else {
	$process -> set -> status = 'fail';
}

// если все ок, то запускаем функцию отправки оповещения админу (не пользователю!!!)
// если вы хотите уведомлять пользователя, то делайте это в дополнительном обработчике
// так сделано потому, что уведомления пользователю вы можете рассылать
// как по email, так и по whatsapp, sms, vk и др. сервисам
// при этом контакты каждого пользователя будут меняться или браться из его данных user
// поэтому и сделано такое ограничение
// возможно, мы сделаем авто-уведомление, но позже, когда поймем, как его интегрировать в настройки
// первая мысль - вводить доп.поле для формы, по которому берутся нужные данные

if (!empty($module -> settings['send']['template'])) {
	$module -> settings['send']['template'] = $process -> source['module'];
}

if (
	$process -> set -> status === 'ready' &&
	objectIs($module -> settings['send'])
) {
	
	$send = null;
	
	if (
		objectKeys($module -> settings['send']) &&
		send(
			$module -> settings['send'],
			$labels['message'],
			$labels['subject'],
			$module -> var['message']
		)
	) {
		$send = true;
	} elseif (
		!objectKeys($module -> settings['send'])
	) {
		foreach ($module -> settings['send'] as $item) {
			$send = send(
				$item,
				$labels['message'],
				$labels['subject'],
				$module -> var['message']
			);
		}
		unset($item);
	}
	
	// если в настройках включен лог, то записываем логи о пришедших сообщениях
	// теперь логи записываются автоматически в функции send
	
	if ($send) {
		$process -> set -> status = 'complete';
	}
	
}

if ($process -> set -> status === 'complete') {
	
	if (!empty($module -> settings['redirect'])) {
		$module -> path .= $module -> settings['redirect'];
	} else {
		$module -> path .= $uri -> previous;
	}
	
	$module -> path .= '?status=' . $process -> set -> status;
	
	if (objectIs($module -> settings['cookie'])) {
		foreach ($module -> settings['cookie'] as $key => $item) {
			cookie($key, $item);
		}
		unset($key, $item);
	}
	
} else {
	
	// если отправка не удалась, то мы подготавливаем редирект, а именно добавляем после статуса все введенные данные
	// таким образом, при повторной загрузке, форма получит старые данные, а пользователю не придется вводить их заново
	// можно было бы принять данные и когда все хорошо, но тогда их можно будет перехватить - это раз, и ссылка получится огромной - это два
	
	$module -> path .= $uri -> previous . '?status=' . $process -> set -> status;
	
	if (objectIs($process -> data)) {
		foreach ($process -> data as $key => $item) {
			$module -> path .= '&data[' . $key . ']=' . htmlentities($item);
		}
		unset($key, $item);
	}
	
	if (objectIs($process -> errors)) {
		foreach ($process -> errors as $item) {
			$module -> path .= '&errors[' . htmlentities($item) . ']=1';
		}
		unset($key, $item);
	}
	
}

//echo '[<pre>' . print_r($module, 1) . '</pre>]<br>';
//echo '[<pre>' . print_r($process, 1) . '</pre>]<br>';
//echo '<br>[' . $module -> path . ']<br>';

header("Location: " . $module -> path);
exit;

?>